<?php
namespace App\Requests;

use App\Models\Model;
use App\Models\Project;

abstract class Request
{
	abstract function authorize(): bool;

	abstract function rules(): array;

	private array $errors = [];
	private array $validatedData = [];

	public function validate(): bool
	{
		if (!$this->authorize()) {
			$this->errors = [];
			$this->errors['auth'] = ["User is not authorized to make this request."];
			return false;
		}

		$rules = $this->rules();
		$this->errors = [];

		foreach ($rules as $field => $ruleString) {
			$value = $_POST[$field] ?? null;
			$rules = explode('|', $ruleString);


			if (in_array('required', $rules) && (is_null($value) || $value === '')) {
				$this->errors[$field][] = "The $field field is required";
				continue; // skip further validation if it's missing.
			}

			if (is_null($value)) {
				continue; // skip further validation if optional field is null.
			}
 			// handles parameters like max, min.
			foreach ($rules as $rule) {
				if (str_contains($rule, ':')) {
					[$rule, $parameter] = explode(':', $rule);
				} else {
					$parameter = null;
				}


				$message = $this->checkRule($value, $rule, $field, $parameter);
				if ($message) {
					$this->errors[$field][] = $message;
				}
			}

			if (!isset($this->errors[$field])) {
				$this->validatedData[$field] = $value;
			}
		}

		return empty($this->errors);
	}

	protected function checkRule($value, string $rule, string $field, $parameter = null): ?string
	{
		switch ($rule) {
			case 'string':
				if (is_string($value)) break;
				return "field must be a string.";
			case 'int':
				if (filter_var($value, FILTER_VALIDATE_INT)) break;
				return "field must be a integer.";
			case 'date':
				if (strtotime($value)) break;
				return "field must be a valid date.";
			case 'array':
				if (is_array($value)) break;
				return "field must an array";
			case 'max':
				if (strlen($value) <= (int)$parameter) break;
				return "field cannot exceed $parameter characters.";
			case 'min':
				if (strlen($value) >= (int)$parameter) break;
				return "field must be at least $parameter characters.";
			case 'after':
				if (strtotime($value) >= strtotime($_POST[$parameter] ?? null)) break;
				return "field date must be after $parameter.";
			case 'before':
				if (strtotime($value) <= strtotime($_POST[$parameter] ?? null)) break;
				return "field date must be before $parameter.";
			case 'matches':
				if ($value == $_POST[$parameter]) break;
				return "field must match $parameter.";
			case 'unique':
				/** @var Model $modelName */
				$modelName = $parameter;
				$model = $modelName::where($field, $_POST[$field]);
				if (!$model->exists()) break;
				return "$field is already in use.";
			default:
				return null;
		}
		return null;
	}

	public function getErrors(): array
	{
		return $this->errors;
	}

	public function has(string $field): bool
	{
		return array_key_exists($field, $this->validatedData);
	}
	public function get(string $field): mixed
	{
		return $this->validatedData[$field] ?? null;
	}
}