<?php
session_start();
require_once("../../config/database.php");
$database = new Database();
$db = $database->getConnection();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../login.php");
    exit;
}
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $stmt = $db -> prepare("DELETE FROM products WHERE id=? ");
    if($stmt ->execute([$id])){
        header("Location:index.php");
    }else{
        echo "Error: Can not delete this item.";
    }
} else{
    header("Location:index.php");
}

?>