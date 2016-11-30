<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function strip($string,$start,$end)
{
   return split($end,split($start,$string)[1])[0];
}
if (isset($_GET['q'])) { //used for plotting sale prices, do we need this? !!NO!!
    $query = $_GET['q'];
$ch = curl_init();
echo str_replace(" ","+",$query);
curl_setopt($ch, CURLOPT_URL,"https://www.google.com/search?q=".str_replace(" ","+",$query)."&sout=1&source=lnms&tbm=isch");
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
echo  "<img height=100px src=\"".$stripped."\">" ;
}
else
{
    echo "Invalid";
}