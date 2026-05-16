<?php
session_start();

// លុប Session ទាំងអស់
session_unset();
session_destroy();

// បញ្ជូនទៅកាន់ទំព័រ Login
header("Location: login.php");
exit();
?>