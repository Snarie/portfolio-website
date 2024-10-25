<?php
namespace App\Requests;

abstract class Request
{
	abstract function rules(): array;

	private array $errors = [];
	private array $validatedData = [];

	public function validate(): bool
	{
		$rules = $this->rules();
		$this->errors = [];

		foreach ($rules as $field => $ruleString) {
			$value = $_POST[$field] ?? null;
			$rules = explode('|', $ruleString);


			if (in_array('required', $rules) && (is_null($value) || $value === '')) {
				$this->errors[] = "The $field field is required";
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

				if (!$this->checkRule($value, $rule, $parameter)) {
					$this->errors[$field][] = "Validation failed for $field: rule $rule";
				}
			}

			if (!isset($this->errors[$field])) {
				$this->validatedData[$field] = $value;
			}
		}

		return empty($this->errors);
	}

	protected function checkRule($value, string $rule, $parameter = null): bool
	{
		return match ($rule) {
			'string' => is_string($value),
			'int' => filter_var ($value, FILTER_VALIDATE_INT) !== false,
			'max' => strlen($value) <= (int)$parameter,
			'min' => strlen($value) >= (int)$parameter,
			'date' => strtotime($value) !== false,
			'array' => is_array($value),
			'after' => strtotime($value) >= strtotime($_POST[$parameter] ?? null),
			'before' => strtotime($value) <= strtotime($_POST[$parameter] ?? null),
			default => true,
		};
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