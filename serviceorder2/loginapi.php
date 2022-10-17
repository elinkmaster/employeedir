<?php 
session_start();

$_SESSION['user_id'] = $_GET['id'];
$_SESSION['first_name'] = $_GET['first_name'];
$_SESSION['last_name'] = $_GET['last_name'];

if (isset($_SESSION['user_id']) && isset($_SERVER['HTTP_REFERER'])) {
	// Redirect to home page 
	header('Location: /' );
} else {
	header('Location: /login.php?error=Incorrect email and password combination!');
}