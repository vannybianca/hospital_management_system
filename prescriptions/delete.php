<?php
require_once "../config/database.php";
$id=(int)($_GET["id"]??0); $s=$conn->prepare("DELETE FROM prescriptions WHERE id=?"); $s->bind_param("i",$id); $s->execute(); header("Location:index.php"); exit;
