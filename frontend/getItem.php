<?php
function strip($string,$start,$end)
{
   return split($end,split($start,$string)[1])[0];
}

function getURLContent($url){
    $doc = new DOMDocument;
    $doc->preserveWhiteSpace = FALSE;
    @$doc->loadHTMLFile($url);
    return $doc->saveHTML();
}
function getRedir($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $a = curl_exec($ch); // $a will contain all headers
    if (strpos($a,"500 Internal Server Error"))
    {
        return NULL;}
    $url = curl_getinfo($ch, CURLINFO_REDIRECT_URL); // This is what you need, it will return you the last effective URL
    return $url; // Voila
}
if (isset($_GET['id'])) { //search term
    $id = intval($_GET['id']);
$goto=getRedir('http://www.liquorandwineoutlets.com/products/detail/'.$id);
if (is_null($goto))
{
    echo "Invalid";
    exit();
}
$page = getURLContent($goto);

//curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
//#curl_setopt($curl,CURLOPT_FOLLOWLOCATION, true);
//$page = curl_exec($curl);
//if(curl_errno($curl)) // check for execution errors
//{
//    echo 'Scraper error: ' . curl_error($curl);
//    exit;
//}
//curl_close($curl);
 
$DOM = new DOMDocument;
 
libxml_use_internal_errors(true);
if (!$DOM->loadHTML($page))
	{
		$errors="";
	    foreach (libxml_get_errors() as $error)  {
			$errors.=$error->message."<br/>"; 
		}
		libxml_clear_errors();
		print "libxml errors:<br>$errors";
		return;
	}
$xpath = new DOMXPath($DOM);
 
$case1 = $xpath->query('/html[1]/body[1]/div[1]/div[1]/div[1]/div[2]/section[1]/article[1]/h1[1]');
$case2 = $xpath->query('//div[@class="tk-chaparral-pro"]/p[1]'); //size and proof
$case3 = $xpath->query('/html[1]/body[1]/div[1]/div[1]/div[1]/div[2]/section[1]/article[1]/div[1]/p[2]/strong[1]/em[1]'); //price
$case4 = $xpath->query('/html[1]/body[1]/div[1]/div[1]/div[1]/div[2]/section[1]/article[1]/div[1]');
$case5 = $xpath->query('/html[1]/body[1]/div[1]/div[1]/div[1]/div[2]/aside[1]/div[2]/em[1]/a[1]');
$case6 = $xpath->query('/html[1]/body[1]/div[1]/div[1]/div[1]/div[2]/aside[1]/div[3]/em[1]/a[1]');
$case7 = $xpath->query('/html[1]/body[1]/div[1]/div[1]/div[1]/div[2]/aside[1]/div[4]/em[1]/a[1]');
$case8 = $xpath->query('/html[1]/body[1]/div[1]/div[1]/div[1]/div[2]/section[1]/article[1]/div[1]/form[1]/input[3]');
foreach ($case1 as $entry) {
    $name=str_replace("*","",str_replace("&amp;","&",$entry->firstChild->nodeValue));
}
foreach ($case2 as $entry) {
    $code=$DOM->saveHTML($entry);
    
}

    
$split_code=split("<br>",$code);
$volume= trim((strip($split_code[0],"</strong>","</p>"))); //volume
$proof= trim(str_replace("°","",(strip($split_code[1],"</strong>","</p>")))); //proof


foreach ($case3 as $entry) {
    $price=$entry->firstChild->nodeValue;
    
}

foreach ($case4 as $entry) {
    $code=$DOM->saveHTML($entry);
    
}
if(substr_count ($code,"<br>")==6) #sale
{
    $split_code=split("br>",$code);
$item_num= trim((strip($split_code[3],"Item Number:</strong>","<"))); //item_num
$type= trim(str_replace("&amp;","&",(strip($split_code[4],"Type:</strong>","<")))); //item_num
$category= trim(str_replace("&amp;","&",(strip($split_code[5],"Category:</strong>","<")))); //item_num
}
else{
    $split_code=split("br>",$code);
$item_num= trim((strip($split_code[1],"Item Number:</strong>","<"))); //item_num
$type= trim(str_replace("&amp;","&",(strip($split_code[2],"Type:</strong>","<")))); //item_num
$category= trim(str_replace("&amp;","&",(strip($split_code[3],"Category:</strong>","<")))); //item_num

}

foreach ($case5 as $entry) {
    $code=$DOM->saveHTML($entry);
}
$rel[0]=trim((strip($code,"href=\"/products/detail/","/")));
foreach ($case6 as $entry) {
    $code=$DOM->saveHTML($entry);
}
$rel[1]=trim((strip($code,"href=\"/products/detail/","/")));
foreach ($case7 as $entry) {
    $code=$DOM->saveHTML($entry);
}
$rel[2]=trim((strip($code,"href=\"/products/detail/","/")));
foreach ($case8 as $entry) {
    $code=$DOM->saveHTML($entry);
}
$url=trim((strip($code,".com","\"")));
    //$url=strip($code[0],".com","");


for($i=0;$i<count($rel); $i++)
{
    if($rel[$i]=="")
    {
        $rel[$i]="NULL";
        echo $rel[$i];
    }
}
echo "Item Num: ".$item_num."<br>";
echo "Name: ".$name."<br>";
echo "URL: ".$url."<br>";
echo "Volume: ".$volume."<br>";
echo "Proof: ".$proof."<br>";
echo "Category: ".$category."<br>";
echo "Type: ".$type."<br>";
echo "Rel's: ".$rel[0].",".$rel[1].",".$rel[2]."<br>";
$sql="INSERT IGNORE INTO `Items` (`id`, `name`, `volume`, `proof`, `type`, `category`, `url`, `image`, `rel_1`, `rel_2`, `rel_3`) "
        . "VALUES ($item_num,\"$name\",\"$volume\",$proof,\"$type\",\"$category\",\"$url\",\"FALSE\",\"$rel[0]\",\"$rel[1]\",\"$rel[2]\")";
$sql2="Select * from Items where id=$item_num";
echo "<br>".$sql;
require 'dbCreds.php';
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$vals=$conn->query($sql2);
if($vals->num_rows == 0)
{
    if ($conn->query($sql) === TRUE) {
    echo "<br><br><h2>New record created successfully</h2>";
    echo "<a href=product.php?id=".$item_num.">Product Page</a>";
    } else {
        echo "<br><b>Error: " . $sql . "</b><br>" . $conn->error;
    }
}
else
{
    echo "<br><br><h2>Record Already Exists</h2><br>Not Added";
}

$conn->close();
}
else
{
    echo "Invalid";
}

?>