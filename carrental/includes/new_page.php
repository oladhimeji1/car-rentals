<?php
session_start();

// Retrieve data from session
$authorizationUrl = $_SESSION['authorizationUrl'];
$reference = $_SESSION['reference'];
$vid = $_SESSION['vid'];

// Your verify function
verify($reference, $vid);

echo "<script>console.log('E reach here');</script>";
?>
