<?php
require "newItems.php";
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <meta name="googlebot" content="noindex, nofollow">
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">  
  <link rel="stylesheet" type="text/css" href="style.css">
  <script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
  
  <title>PriceTracker Product</title>

</head>

<body>
  <?php include "header.php";?>
    
  <div class="container">
    <h1>Admin Page</h1>
    <a class="btn btn-info" role="button" href="updateImages.php">Update Image Tags (N.B. takes ~5 mins)</a> <br><br>
    <a class="btn btn-info" role="button" href="volumeCalcs.php">Check Volume Calcs</a> <br><br>
    <a class="btn btn-info" role="button" href="updateAllPriceCalcs.php">Update All Price Calcs</a> <br><br>
    <?php echo $out?>
  </div>
<?php include 'footer.html';?>