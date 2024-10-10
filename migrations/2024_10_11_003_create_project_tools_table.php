<?php

class CreateProjectToolsTable {
	public function up(PDO $conn): void {
		$sql = "CREATE TABLE project_tools (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT,
            tool_id INT,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (tool_id) REFERENCES  tools(id) ON DELETE CASCADE
        )";
		$conn->exec($sql);
	}

	public function down(PDO $conn): void {
		$sql = "DROP TABLE project_tools";
		$conn->exec($sql);
	}
}