<?php
require "login/loginheader.php";
if ($_SESSION['username']!="ailgup")
{
    echo $_SESSION['username']." You're not the admin!";
    exit;
}
require 'dbCreds.php';
$connection = new mysqli($servername, $username, $password, $dbname);
//test if connection failed
if(mysqli_connect_errno()){
    die("connection failed: "
        . mysqli_connect_error()
        . " (" . mysqli_connect_errno()
        . ")");
}
$id=$conn->real_escape_string(intval($_GET['id']));
$SQL1="Delete from Items where Items.id=$id";

        mysqli_query($connection,$SQL1);
        echo $SQL1."<br>Affected rows: " . mysqli_affected_rows($connection);
      
        
        mysqli_close($connection);
        
        ?>