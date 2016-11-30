<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'dbCreds.php';

    $sql="SELECT distinct(volume) as A,actualVol(volume) as B,count(*) as C FROM `Items` group by volume order by C;";

// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $result = $conn->query($sql);
    echo "<table><tr><th>Volume</th><th>Calculated Volume</th><th>Count</th></tr>";
    if ($result->num_rows > 0) {
        $count=0;
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>".$row["A"]."</td><td>".$row["B"]."</td><td>".$row["C"]."</td></tr>";
        }
    }
    echo "</table>";
    ?>