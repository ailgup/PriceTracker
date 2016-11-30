<?php

if (isset($_POST['action'])&&isset($_POST['prod'])&&isset($_POST['username'])) {
    switch ($_POST['action']) {
        case 'Untrack':
            untrack($_POST['prod']);
            break;
        case 'Track':
            track($_POST['prod']);
            break;
    }
}

function untrack($prod) {
    require 'dbCreds.php';
    $uname=$_POST['username'];
    $sql="DELETE FROM `Tracking` where Tracking.user_id='$uname' and Tracking.item_id=$prod";
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $result=$conn->query($sql);
    if($result===TRUE)
    {
        echo "Track";
        exit;
    }
    else
    {
        echo "Error";
        exit;
    }
}
function track($prod) {
    require 'dbCreds.php';

    $uname=$_POST['username'];
    $sql="INSERT IGNORE INTO `Tracking`(`user_id`, `item_id`) VALUES ('$uname',$prod)";
    
    //echo $sql.PHP_EOL;
    //echo $servername." ".$username." ".$password." ".$dbname.PHP_EOL;

    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $result=$conn->query($sql);
    if($result===TRUE)
    {
        echo "Untrack";
        exit;
    }
    else
    {
        echo "Error";
        exit;
    }


}

?>