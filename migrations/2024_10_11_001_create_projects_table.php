<?php

class CreateProjectsTable {
	public function up(PDO $conn): void {
		$sql = "CREATE TABLE projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            start_date DATE,
            end_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
		$conn->exec($sql);
	}

	public function down(PDO $conn): void {
		$sql = "DROP TABLE projects";
		$conn->exec($sql);
	}
}