<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // If user is logged in, redirect to home.php
    header("Location: home.php");
    exit();
} else {
    // If user is not logged in, redirect to login.php
    header("Location: login.php");
    exit();
}
