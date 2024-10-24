Portfolio website.

Requirements<br>
* PHP 8.3.x
* Database that's supported by PDO
* PHP extensions:
  * `GD` extension (required for image manipulation)
  * `PDO_x` extension, depends on your database.


Migration commands:

`php migrate.php migrate`, this will run all migrations that haven't been migrated yet.

`php migrate.php rollback`, this will rollback the last migration.

