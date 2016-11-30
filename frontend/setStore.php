<?php

if (isset($_POST['store'])&&isset($_POST['username'])) {
    require 'dbCreds.php';
    $uname=$_POST['username'];
    $store= intval($_POST['store']);
    $sql="UPDATE `members` SET `store`=$store where members.username='$uname';";
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $result=$conn->query($sql);
    if($result===TRUE)
    {
        echo "Updated";
        exit;
    }
    else
    {
        echo "Error";
        exit;
    }
}
else
{
    echo "Big Error";
}
?>