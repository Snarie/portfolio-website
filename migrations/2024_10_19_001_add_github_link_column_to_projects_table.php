<?php

class AddGithubLinkColumnToProjectsTable {
	public function up(PDO $conn): void {
		$sql = "ALTER TABLE projects ADD COLUMN github_link VARCHAR(255) AFTER image_link";
		$conn->exec($sql);
	}

	public function down(PDO $conn): void {
		$sql = "ALTER TABLE projects DROP COLUMN github_link";
		$conn->exec($sql);
	}
}