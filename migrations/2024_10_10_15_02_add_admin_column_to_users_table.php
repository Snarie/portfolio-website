<?php

class AddAdminColumnToUsersTable {
	public function up(PDO $conn): void {
		$sql = "ALTER TABLE users ADD COLUMN admin BOOLEAN DEFAULT 0 AFTER password";
		$conn->exec($sql);
	}

	public function down(PDO $conn): void {
		$sql = "ALTER TABLE users DROP COLUMN admin";
		$conn->exec($sql);
	}
}