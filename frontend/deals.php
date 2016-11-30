<?php
$RESULTS_PER_PAGE=30;

require 'dbCreds.php';

function URLstrip($arg)
{
$end= preg_replace('~(&?)'.$arg.'=[^&]*~','',explode('?', $_SERVER['REQUEST_URI'], 2)[1]);
return 'http://' . $_SERVER['HTTP_HOST'] .explode('?',$_SERVER['REQUEST_URI'], 2)[0]."?". $end;
}

$page=1;
if (isset($_GET['page'])) { //search term
    $page = intval($_GET['page']);
}
if (isset($_GET['sort'])) { //search term
    $sort_what = $_GET['sort'];
    switch($sort_what){
        case "n": //name ascending
            
            $sort="`name` ASC";
            $sort_string="Name ▲";
            break;
        case "pu": //price ascending
            $sort="TheMin ASC";
            $sort_string="Price ▲";
            break;
        case "pd": //price descending
            $sort="TheMin DESC";
            $sort_string="Price ▼";
            break;
        case "id": //id ascencing
            $sort="`id` ASC";
            $sort_string="ID ▲";
            break;
        case "ppva": //price per volume asc
            $sort="ppv ASC";
            $sort_string="$ Per L ▲";
            break;
        case "ppvd": //price per volume desc
            $sort="ppv DESC";
            $sort_string="$ Per L ▼";
            break;
        case "ppvabva": //price per volume asc
            $sort="ppvabv ASC";
            $sort_string="$ Per (ABV) ▲";
            break;
        case "ppvabvd": //price per volume desc
            $sort="ppvabv DESC";
            $sort_string="$ Per (ABV) ▼";
            break;
        case "pdiffa": //percentage difference asc
            $sort="pdiff ASC";
            $sort_string="% Change ▲";
            break;
        case "pdiffd": //percentage difference asc
            $sort="pdiff DESC";
            $sort_string="% Change ▼";
            break;
        default:
            $sort="`id` ASC"; //default
            $sort_string="ID ▲";
}}
else{
    $sort="`id` ASC"; //default
    $sort_string="Sort";
}
if (isset($_GET['s'])) { //search term
    $s = $conn->real_escape_string($_GET['s']);
    $search_term=$s;
}
else{
    $s="";
    $search_term="Search...";
}
//(max(p.the_min)-min(p.the_min))/min(p.the_min)*(min(p.the_min)/s1.the_min) as perc_d 
  $RESULTS_PER_PAGE=50;      
    $sql="SELECT DISTINCT e.*, s1.price, s1.`sale_price`, s1.scrape_date,
        s1.the_min AS TheMin,
        s1.price_per_liter as ppv,
        s1.price_per_abv as ppvabv,
        s1.perc_diff as pdiff,
        s1.avg_dif_perc as avg_dif_perc,
        max(p.the_min)/s1.the_min*(-1/100)*s1.avg_dif_perc as perc_d  
        from Prices p
            inner join Items e on p.id=e.id
          INNER JOIN CurrentPrices s1
            ON (e.id = s1.id)
        group by id
        ORDER BY perc_d desc
        LIMIT ". ($page-1)*$RESULTS_PER_PAGE ." , $RESULTS_PER_PAGE;";
$sql_count="SELECT count(DISTINCT e.id) as c
        FROM Items e
          INNER JOIN CurrentPrices s1
            ON (e.id = s1.id)
        WHERE (e.name LIKE '%$s%' OR e.id Like '%$s%');";
// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $result = $conn->query($sql);
    /* Pagination */
    $result_count = $conn->query($sql_count);
    if ($result_count->num_rows > 0) {
        while ($row2 = $result_count->fetch_assoc()) {
            $num_results=$row2["c"];
            $pagination_str="<ul class=\"pagination\">";
            $i=1;
            if($page>2){
                $i=$page-1;
                $pagination_str=$pagination_str."<li><a href=\"". URLstrip("page")."&page=1\">1</a></li><li><a class=\"disabled\">...</a></li>";
            }    
            $i_init=$i;
            for ($i;$i<=ceil(intval($num_results)/$RESULTS_PER_PAGE) && $i<5+$i_init; $i++)
                {   
                    if($i==$page)
                    {
                        $pagination_str=$pagination_str."<li class=\"active\"><a href=\"". URLstrip("page")."&page=$i\">".($i)."</a></li>";
                    }
                    else{
                        $pagination_str=$pagination_str."<li><a href=\"". URLstrip("page")."&page=$i\">".($i)."</a></li>";
                    }
                }
            }
            if($i<=ceil(intval($num_results)/$RESULTS_PER_PAGE))
            {
                $pagination_str=$pagination_str."<li><a class=\"disabled\">...</a></li><li><a href=\"". URLstrip("page")."&page=".ceil((intval($num_results)+1)/$RESULTS_PER_PAGE)."\">".ceil((intval($num_results)+1)/$RESULTS_PER_PAGE)."</a></li></ul>";
    
            }
            else{
                $pagination_str=$pagination_str."</ul>";
            }
            
            }
    
    if ($result->num_rows > 0) {
        $count=0;
        while ($row = $result->fetch_assoc()) {
            
            if ($count%5==0){
                $grid_str=$grid_str."</div><div class=\"row\">";
            }
            $count=$count+1;
            $grid_str=$grid_str."<div class=\"col-xs-4 col-md-5ths product-item\">
            <a href=\"product.php?id=".$row["id"]."\" STYLE=\"text-decoration: none\">
              <div class=\"panel panel-default \">
                <div class=\"panel-heading\">".$row["name"];
            if (is_null($row["pdiff"]))
            {
                $grid_str=$grid_str." <br><span class=\"label label-warning\">NEW</span>";
            }
            $grid_str=$grid_str."</div>
                <div class=\"panel-body\" align=\"middle\"><img src=\"";
                if($row["image"]=="True" || $row["image"]=="TRUE"){
                    $grid_str=$grid_str."https://raw.githubusercontent.com/ailgup/PriceTracker/master/images/".$row["id"].".jpg";
                }
                else{
                    $grid_str=$grid_str."login/images/bottle.png";
                }

            
                $grid_str=$grid_str."\" class=\"img-responsive\" style=\"height:150px\" alt=\"Image\"></div>
                <div class=\"panel-footer\">
                <table align=\"center\">
                    <tr>
                      <td style=\"padding: 0 5px 0 5px\">
                        <span class=\"label measurments label-default\"><span style=\"font-size: 18px;\">".preg_replace("/[^0-9,.]/", "",$row["volume"])."</span><br>".preg_replace("/[0-9,.]/", "",$row["volume"])."</span>

                      </td>
                      <td style=\"padding: 0 5px 0 5px\">
                        <span class=\"label measurments label-default\"><span style=\"font-size: 18px;\">".intval($row["proof"])."</span><br>Proof</span>
                      </td>
                      <td style=\"padding: 0 5px 0 5px\">
                        <a id=\"popover\" rel=\"popover\" data-html=\"true\" data-trigger=\"hover\" data-content='<div height=250px width=550px><iframe style=\"border:none;\" height=250px width=550px src=\"graph.php?a[]=".$row["id"]."\"></iframe></div>' >
                        <span class=\"label measurments label-default\"><span style=\"font-size: 20px;\" class=\"glyphicon glyphicon-stats\"></span><br>Graph</span>
                        </a>
                    </td>
                      
                    </tr>
                  </table>
                <h3 style=\"margin-top: 10px;\">
                <div style=\"text-align: center\">";
                if($row["pdiff"]>0) //On Sale
                {
                    $grid_str=$grid_str."
                    <span class=\"label label-sale\">
                    $".$row["TheMin"]."  ▼ ".round($row["pdiff"],1) ."%
                    </span>";
                }
                elseif($row["pdiff"]<0) //On Sale
                {
                    $grid_str=$grid_str."
                    <span class=\"label label-danger\">
                    $".$row["TheMin"]."  ▲ ".round($row["pdiff"],1) ."%
                    </span>";
                }
                else{
                    $grid_str=$grid_str."
                    <span class=\"label label-nosale\">
                    $".$row["price"]."
                    </span>";
                }
               
                $grid_str=$grid_str."
                    </div>
                    </h3>
                </div>
              </div>
            </a>
          </div>";
            
        }
    }
?>


<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <meta name="googlebot" content="noindex, nofollow">
  <!--<script type="text/javascript" src="//code.jquery.com/jquery-1.10.1.js"></script>-->
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

  <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
  
  <link rel="stylesheet" type="text/css" href="style.css">
  <script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>


  <title>PriceTracker BEST DEALS</title>

  


  
</head>

<body>
  <?php include "header.php";?>
<div class="container products-grid">
    <img style="padding-bottom: 25px;"src="login/images/hot-deals.png">
    <br>
<div class="row">
<?php echo $grid_str; ?>
</div>
</div>
<br>
<br>
<br>
<div align="center">
<a class="btn btn-info" role="button" id="prev_link" href="">Previous Page</a>
    
<a class="btn btn-info" role="button" id="next_link" href="">Next Page</a>
</div>
<div align="center">
<?php echo $pagination_str ?>
</div>
<?php include 'footer.html';?>
<script type='text/javascript'>

function removeParam(key, sourceURL) {
    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        rtn = rtn + "?" + params_arr.join("&");
    }
    return rtn;
}

function search() {
    if(document.getElementById("search_box").value.length>0)
    {var link = removeParam('page',removeParam('s',window.location.href.toString()));
    if (link.indexOf('?') > -1) {
        var link =link+ '&s='+document.getElementById("search_box").value;
    } else {
        var link =link+ '?s='+document.getElementById("search_box").value;
    }
    window.location.href = link;
    }
    //alert(link);
}
</script>

<script type='text/javascript'>

function sortLinks(){
var elements = document.getElementsByClassName("sort_item");
for(var i=0; i<elements.length; i++) {
    var link = removeParam('page',removeParam('sort',window.location.href.toString()));
    
    if (link.indexOf('?') > -1) {
        var link =link+ '&';
        }
    else {
        var link =link+ '?';
    }
        var link =link+ elements[i].getAttribute('href');
    elements[i].setAttribute('href', link);
}
}

function nextLink(){
        var next_link=removeParam('page',window.location.href.toString());
        
        if (next_link.indexOf('?') > -1) {
            var prev_link=next_link+ '&page='+"<?php echo $page-1; ?>".toString();
            var next_link =next_link+ '&page='+"<?php echo $page+1; ?>".toString();
        
        } else {
            var prev_link=next_link+ '?page='+"<?php echo $page-1; ?>".toString();
            var next_link =next_link+ '?page='+"<?php echo $page+1; ?>".toString();
        }
        if(parseInt(<?php echo $page-1; ?>) <0 ) {
            $('#prev_link').attr('disabled', true);
            }
        if(<?php echo ($num_results/$RESULTS_PER_PAGE)."<=".($page+1); ?>) {
            $('#next_link').attr('disabled', true);
            }
        // FIXME add case for last page will need SqL COUnt(*)   
    document.getElementById("next_link").href=next_link;
        document.getElementById("prev_link").href=prev_link;

}
window.onload=function(){
    nextLink();
    sortLinks();}
    window.onload=function(){
    $('[rel="popover"]').popover({placement:'auto right', container: "body"});
}
</script>