<?php

class CreateToolsTable {
	public function up(PDO $conn): void {
		$sql = "CREATE TABLE tools (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
		$conn->exec($sql);
	}

	public function down(PDO $conn): void {
		$sql = "DROP TABLE tools";
		$conn->exec($sql);
	}
}