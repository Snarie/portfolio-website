<?php

namespace App\Models;

class User extends Model
{
	public string $email;
	public string $name;
	public string $password;
	public bool $admin;

	protected array $guarded = ['created_at', 'updated_at', 'admin'];

	/**
	 * Constructor for the User model.
	 * Can optionally initialize model properties.
	 * @param array|null $data Optional data to instantiate the model properties.
	 */
	public function __construct(?array $data = null)
	{
		$this->table = 'users';
		if ($data) {
			$this->fill($data);
		}
	}
}