<?php
require "login/loginheader.php";
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function strip($string,$start,$end)
{
   return split($end,split($start,$string)[1])[0];
}
function getImage($query)
{
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://www.google.com/search?q=".str_replace(" ","+",$query)."&source=lnms&tbm=isch");
//curl_setopt($ch, CURLOPT_POST, 1);
//curl_setopt($ch, CURLOPT_POSTFIELDS,$vars);  //Post Fields
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$headers = [
    'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36'
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$server_output = curl_exec ($ch);

curl_close ($ch);
$stripped=strip($server_output,"\"ou\":\"","\"");
sleep(1);
return  $stripped ;
}
require 'dbCreds.php';
$connection = new mysqli($servername, $username, $password, $dbname);
//test if connection failed
if ($_SESSION['username']!="ailgup")
{
    echo $_SESSION['username']." You're not the admin!";
    exit;
}
if(mysqli_connect_errno()){
    die("connection failed: "
        . mysqli_connect_error()
        . " (" . mysqli_connect_errno()
        . ")");
}

//get results from database
$newest_scrape=mysqli_query($connection,"SELECT DISTINCT`scrape_date` as d, count(*) as c FROM `Prices` group by `scrape_date` order by scrape_date DESC limit 1");
while ($row = $newest_scrape->fetch_assoc()) {

    if(intval($row["c"])<100)
    {
        $out=$out. "<h1>Small Sample Size from".$row['d']." of ".$row['c']."items.</h1>";
        $newest=date("Y-m-d");
    }
    else
        $newest=$row['d'];
}
$result = mysqli_query($connection,"SELECT Prices.id,Prices.price_id,Prices.price,Prices.sale_price,Prices.scrape_date,Prices.sale_end,count(*) as count FROM Prices where Prices.scrape_date=DATE('$newest') and not exists (select * from Items where Items.id=Prices.id)
group by id order by count DESC;");
$all_property = array();  //declare an array for saving property
$out=$out. "<h2>Prices without Items</h2>";
//showing property
$out=$out. '<table border="1" style="border-collapse:collapse;" class="data-table">
        <tr class="data-heading">';  //initialize table tag
while ($property = mysqli_fetch_field($result)) {
    $out=$out. '<td><b>' . $property->name . '</b></td>';  //get field name for header
    array_push($all_property, $property->name);  //save those to array
}
$out=$out. '</tr>'; //end tr tag

//showing all data
while ($row = mysqli_fetch_array($result)) {
    $out=$out. "<tr>";
    foreach ($all_property as $item) {
        $out=$out. '<td>' . $row[$item] . '</td>'; //get items using property value
    }
    $out=$out. '<td><a href="getItem.php?id=' . $row[0] . '" target="_blank">Create Item</a></td>';
    $out=$out. '</tr>';
}
$out=$out. "</table>";

$out=$out. "<h2>Deprecated</h2>As of the $newest scrape";
$result = mysqli_query($connection,"SELECT name,id,volume,proof 
FROM  `Items` 
WHERE NOT 
EXISTS (
SELECT * 
FROM Prices
WHERE Prices.id = Items.id
AND Prices.scrape_date =  '$newest');");
$all_property = array();  //declare an array for saving property

//showing property
$out=$out. '<table border="1" style="border-collapse:collapse;" class="data-table">
        <tr class="data-heading">';  //initialize table tag
while ($property = mysqli_fetch_field($result)) {
    $out=$out. '<td><b>' . $property->name . '</b></td>';  //get field name for header
    array_push($all_property, $property->name);  //save those to array
}
$out=$out. '</tr>'; //end tr tag

//showing all data
while ($row = mysqli_fetch_array($result)) {
    $out=$out. "<tr>";
    foreach ($all_property as $item) {
        $out=$out. '<td>' . $row[$item] . '</td>'; //get items using property value
    }
    $out=$out. "<td><a target='_blank' href='product.php?id=".$row[1]."'>Product</a></td>"
            . "<td><a target='_blank' href='dropProduct.php?id=".$row[1]."'>Drop</a></td>";
    $out=$out. '</tr>';
}
$out=$out. "</table>";

$out=$out. "<h2>Imageless</h2>";
$result = mysqli_query($connection,"SELECT name,id,volume,proof 
FROM  `Items` 
WHERE Items.image='False';");
$all_property = array();  //declare an array for saving property

//showing property
$out=$out. '<table border="1" style="border-collapse:collapse;" class="data-table">
        <tr class="data-heading">';  //initialize table tag
while ($property = mysqli_fetch_field($result)) {
    $out=$out. '<td><b>' . $property->name . '</b></td>';  //get field name for header
    array_push($all_property, $property->name);  //save those to array
}
$out=$out. '</tr>'; //end tr tag

//showing all data
while ($row = mysqli_fetch_array($result)) {
    $out=$out. "<tr>";
    foreach ($all_property as $item) {
        $out=$out. '<td>' . $row[$item] . '</td>'; //get items using property value
    }
    $out=$out. '<td><a target="_blank" href="imageScrape.php?q='.$row[0].'">Img</a></td><td><a href="https://www.google.com/search?q='.$row[0]." ".$row[2].'&safe=active&tbm=isch" target="blank">Google Image</a></td>';
    $out=$out. '</tr>';
}
$out=$out. "</table>";

?>