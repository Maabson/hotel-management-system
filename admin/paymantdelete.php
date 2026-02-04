<?php

session_start();
include '../config.php';

// Check if user is logged in as admin
if(!isset($_SESSION['usermail'])) {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'];

$deletesql = "DELETE FROM payment WHERE id = $id";

$result = mysqli_query($conn, $deletesql);

header("Location:payment.php");

?>