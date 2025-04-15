<?php

// localhost
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "courier";

// cloud
// $dbuser = "litzp5lx_finnest";
// $dbpass = "Litztech!123";
// $dbname = "litzp5lx_finnest";

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
