<?php
require_once "../config/database.php";
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: index.php"); exit; }
$id = (int)($_POST['id'] ?? 0);
$statement = $conn->prepare("DELETE FROM doctors WHERE id=?");
$statement->bind_param("i", $id);
$statement->execute();
header("Location: index.php");
exit;
