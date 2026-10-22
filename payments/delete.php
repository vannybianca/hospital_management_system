<?php
require_once "../config/database.php";

$id = (int)($_GET["id"] ?? 0);
$statement = $conn->prepare("DELETE FROM payments WHERE id = ?");
$statement->bind_param("i", $id);
$statement->execute();

header("Location: index.php");
exit;
