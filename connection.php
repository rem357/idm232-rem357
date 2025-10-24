<?php
// database connection file
$host = 'localhost';
$user = 'root';
$password = 'root';   // this is the default for MAMP
$database = 'cookbook';  // change if your phpMyAdmin database name is different

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>
