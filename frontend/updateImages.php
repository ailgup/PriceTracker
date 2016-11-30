<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'dbCreds.php';

    $sql="SELECT *
        FROM Items
        ;";
header( 'Content-type: text/html; charset=utf-8' );
// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $count=0;
        while ($row = $result->fetch_assoc()) {
            
            $file = "https://raw.githubusercontent.com/ailgup/PriceTracker/master/images/".$row["id"].".jpg";
            $file_headers = @get_headers($file);
            if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') { //no image
                if($row["image"]=="False" || $row["image"]=="FALSE"){ //was false before
                $echo_str=$echo_str.$row["id"]." - Still FALSE".PHP_EOL;}
                elseif ($row["image"]=="True" || $row["image"]=="TRUE") { //was true before
                    $echo_str=$echo_str.$row["id"]." - **TRUE to FALSE**".PHP_EOL;
                    if ($conn->query("update Items set image='FALSE' where id=".$row["id"].";") === TRUE) {
                        //echo "Record updated successfully";
                    } else {
                        $echo_str=$echo_str. "Error updating record: " . $conn->error.PHP_EOL;;
                    }
                }
            }
            else { //found image
                if($row["image"]=="False" || $row["image"]=="FALSE"){ //was false before
                    $echo_str=$echo_str.$row["id"]." - *FALSE to TRUE*".PHP_EOL;
                    if ($conn->query("update Items set image='TRUE' where id=".$row["id"].";") === TRUE) {
                        //echo "Record updated successfully";
                    } else {
                        $echo_str=$echo_str. "Error updating record: " . $conn->error.PHP_EOL;;
                    }
                }
                elseif ($row["image"]=="True" || $row["image"]=="TRUE") { //was true before
                    $echo_str=$echo_str.$row["id"]." - Still True".PHP_EOL;
                   
                }
            }
           echo $echo_str;
           ob_implicit_flush(true);
           flush();
    ob_flush();
    ob_implicit_flush(false);
           $echo_str="";
        }
    }
    ?>

    