<?php

class CreateUsersTable
{
	public function up(PDO $conn) {
		$sql = "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

		$conn->exec($sql);
	}

	public function down(PDO $conn) {
		$sql = "DROP TABLE users";
		$conn->exec($sql);
	}
}