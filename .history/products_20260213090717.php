<?php

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);


// 1. Database Connection Details
$servername = "localhost";
$username = "skrnchst_products"; // From DirectAdmin MySQL Management
$password = "skrnchst_products";
$dbname = "paFbBecS7XnaerCPfAyA";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
