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
function stripBackwards($string,$start,$end)
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
function getQuantity($id,$store)
    {
    $goto=getRedir('http://www.liquorandwineoutlets.com/products/detail/'.$id);
    if (is_null($goto))
    {
        return "Not Found";
    }
    $page = getURLContent($goto);

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

    $case1 = $xpath->query('/html[1]/body[1]/div[1]/div[1]/div[1]/div[2]/section[1]/article[1]');
    foreach ($case1 as $entry) {
        $code=$DOM->saveHTML($entry);  
    }
    $str=trim(strip(strip($code,"\#$store), ","</tr>"),"<td class=\"ta_right\">","</td>"));
//    $str2=trim(strip(strip(split("<td class=\"ta_right\">",strip($code,"\#$store), ","</tr>"))[2]),"","</td>"));
    $str2=trim(split("<td class=\"ta_right\">",strip($code,"\#$store), ","</tr>"))[2]);

    if($str=="")
    {
        $str=0;
    }
    if($str2=="")
    {
        $str2=0;
    }       
    return [$str,$str2];
}
?>