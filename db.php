<?php
/**
 * Private.php format:
 *
 *  return [
 *      "database" => [
 *          'servername' => "{host}:{port}",
 *          'username' => "{username}",
 *          'password' => "{password}",
 *          'dbname' => "{database}"
 *      ]
 *  ]
 */
$data = require('private.php');

$db = $data["database"];
$dsn = "mysql:host=".$db['servername'].";dbname=".$db['dbname'].";charset=utf8mb4";
try {
	$conn = new PDO(
		$dsn,
		$db['username'],
		$db['password']);
	// set the PDO error mode to exception
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $conn;
} catch(PDOException $e) {
	abort("Connection failed:" . $e->getMessage());
}