<?php
require "login/loginheader.php";
require "dbCreds.php";
include 'quantity.php';
$connection = new mysqli($servername, $username, $password, $dbname);

$uname=$connection->real_escape_string($_SESSION['username']);


$filename = 'stores.txt';
$eachlines = file($filename, FILE_IGNORE_NEW_LINES);//create an array
$dropdown= '<select name="value" id="store_dropdown">';
foreach($eachlines as $lines){
    $dropdown=$dropdown. "<option>{$lines}</option>";
}
$dropdown=$dropdown. '</select>';

$out="<h1>Hello $uname&nbsp;&nbsp;&nbsp;";
if($uname=="ailgup")
{
 $out=$out."<a class=\"btn btn-info\" role=\"button\" href=\"admin.php\">Admin Home</a></h1>";   
}


//begin grid

    $sql="SELECT DISTINCT Tracking.*, e.*, s1.price, s1.`sale_price`, s1.scrape_date,
        s1.the_min AS TheMin,
        s1.price_per_liter as ppv,
        s1.price_per_abv as ppvabv,
        s1.perc_diff as pdiff,
        s1.avg_dif_perc as avg_dif_perc
        FROM Items e
          INNER JOIN CurrentPrices s1
            ON (e.id = s1.id)
          INNER Join Tracking on Tracking.item_id=e.id
where Tracking.user_id='$uname'";
    $sql2="SELECT store from members where username='$uname';";
// Create connection
    
// Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $result2 = $connection->query($sql2);
        if ($result2->num_rows > 0) {
            while ($row = $result2->fetch_assoc()) {
                $curr_store=$row["store"];
            }
        }
    $result = $connection->query($sql);
        if ($result->num_rows > 0) {
        $count=0;
        $grid_str="<h2>Tracking</h2><div>";
        if ($result->num_rows > 15) { //if too many tracked items to not scrape too slow
                $grid_str=$grid_str."Note: Since tracking more than 15 items, item stock not shown<br>";
            }
        while ($row = $result->fetch_assoc()) {
            if ($result->num_rows > 15) { //if too many tracked items to not scrape too slow
                $quantity=NULL;
            }
            else
            {
                $quantity=getQuantity($row["id"],$curr_store);
            }
                $color=[];
                for ($i=0;$i<sizeof($quantity);$i++)
                {
                    if (intval($quantity[$i])==0)
                    {
                        $color[$i]="progress-bar-danger";
                    }
                    elseif (intval($quantity[$i])<5)
                    {
                        
                        $color[$i]="progress-bar-warning";
                    }
                    else
                    {
                        $color[$i]="progress-bar-success";

                    }
                }
            if ($count%5==0){
                $grid_str=$grid_str."</div><div class=\"row\">";
            }
            $count=$count+1;
            $grid_str=$grid_str."<div class=\"col-xs-4 col-md-5ths product-item\">
            
              <div class=\"panel panel-default \">
                <div class=\"panel-heading\">
                              <a href=\"product.php?id=".$row["id"]."\" style=\"text-decoration: none; color: white\">".$row["name"];
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

            
                $grid_str=$grid_str."\" class=\"img-responsive\" style=\"height:150px\" alt=\"Image\"></a></div>
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
                    <div style=\"text-align: center\" data-toggle=\"tooltip\" title='' data-original-title=\"Current price, Change from last month\">";
                if($row["pdiff"]>0) //On Sale
                {
                    $grid_str=$grid_str."
                    <span class=\"label label-sale\">
                    $".$row["TheMin"]." ▼ ".$row["pdiff"] ."%
                    </span>";
                }
                elseif($row["pdiff"]<0) //On Sale
                {
                    $grid_str=$grid_str."
                    <span class=\"label label-danger\">
                    $".$row["TheMin"]." ▲ ".abs($row["pdiff"]) ."%
                    </span>";
                }
                else{
                    $grid_str=$grid_str."
                    <span class=\"label label-nosale\">
                    $".$row["TheMin"]."
                    </span>";
                }
                $grid_str=$grid_str."</div></h3>
                    
                     <div style=\"text-align: center\"> 
                     #$curr_store: <span data-toggle=\"tooltip\" title=\"Number In Stock\" class=\"badge $color[0]\">$quantity[0]</span> | <span data-toggle=\"tooltip\" title=\"Number On Order\" class=\"badge $color[1]\">$quantity[1]</span>
                    </div>
                    <br>
 <div style=\"text-align: center\">                   
<button type=\"submit\" id=".$row["id"]." class=\"track button\" item=\"".$row["id"]."\" name=\"insert\" value=\"Untrack\">Untrack</button>
</div>
                </div>
              </div>
            
          </div>
          ";
            
        }
    }

//end grid


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
  
  <title>PriceTracker User</title>

</head>

<body>
  <?php include "header.php";?>

  <div class="container ">
    <?php echo $out ?>
      <br>
    <div class="panel">
        <div class="panel-heading">  
        Primary Store
        </div>
        <div style="font-size: large;margin:10px">
           Home Store: Store #<?php echo $curr_store ?>&nbsp;&nbsp;&nbsp; 
          <button class="btn" data-toggle="collapse" data-target="#store_hidden">
              <span class="glyphicon glyphicon-edit"</span></button>
          <div id="store_hidden" class="collapse">
            <?php echo $dropdown ?>
            <button id="store" type="" class="">Update</button>
          </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php echo $grid_str?>
            <br>
        </div>
    </div>
        
        
    </div>
        

</div>
<?php include 'footer.html';?>

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
    $('[rel="popover"]').popover({placement:'auto right', container: "body"});
});


 $(document).ready(function(){
    $('#store').click(function(){
        $('#store').prop('disabled', true);
        var clickBtnValue = $('#store_dropdown :selected').text();
        clickBtnValue=clickBtnValue.slice(clickBtnValue.indexOf("#")+1,clickBtnValue.indexOf(")"));
        //alert(clickBtnValue);
        var ajaxurl = 'setStore.php',
        data =  {'store': clickBtnValue,'username':'<?php echo $_SESSION['username'] ?>'};
        $.post(ajaxurl, data, function (response) {
            // Response div goes here.
            $("#store").text(response);
            location.reload();
            //alert(response);
        });

    });
});
$(document).ready(function(){
    $('.track').click(function(){
        var id = $(this).attr('id');
        $('#' + id).prop('disabled', true);
        var clickBtnValue = $(this).val();
        var item=$(this).attr('item');
        //alert(clickBtnValue);
        var ajaxurl = 'track.php',
        data =  {'action': clickBtnValue,'prod':item,'username':'<?php echo $_SESSION['username'] ?>'};
        $.post(ajaxurl, data, function (response) {
            // Response div goes here.
            $('#' + id).text(response);
            $('#' + id).prop('value', response);
            $('#' + id).prop('disabled', false);    
            //alert(response);
        });
    });
    
    });
</script>
