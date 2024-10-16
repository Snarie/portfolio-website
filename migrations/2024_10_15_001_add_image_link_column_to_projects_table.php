<?php

class AddImageLinkColumnToProjectsTable {
	public function up(PDO $conn): void {
		$sql = "ALTER TABLE projects ADD COLUMN image_link VARCHAR(255) AFTER name";
		$conn->exec($sql);
	}

	public function down(PDO $conn): void {
		$sql = "ALTER TABLE projects DROP COLUMN image_link";
		$conn->exec($sql);
	}
}