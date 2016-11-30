<?php
require 'dbCreds.php';
include 'quantity.php';
#require "login/loginheader.php";

session_start();
$conn = new mysqli($servername, $username, $password, $dbname);
            // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
if (isset($_GET['id'])) { //search term
    $id = $conn->real_escape_string($_GET['id']);
    
    // Dealing with the tracking of items
    
    $button_str="";
    $curr_store=0;
    if (isset($_SESSION['username'])) {
        
        $result = $conn->query("Select item_id from Tracking where Tracking.user_id='".$_SESSION['username']."' and Tracking.item_id='$id'");
        $result2=$conn->query("SELECT store from members where username='".$_SESSION['username']."';");
        if ($result2->num_rows > 0) {
            while ($row = $result2->fetch_assoc()) {
                $curr_store=$row["store"];
            }
        }
        if ($result->num_rows > 0) {
            $set="Untrack";
        }
        else
        {
            $set="Track";
        }
        mysqli_close($conn);
        $button_str="<button type=\"submit\" id=track class=\"button\" name=\"insert\" value=\"$set\">$set</button>";
    }
    //end Item tracking

    $sql="SELECT DISTINCT e.*, s1.price, s1.`sale_price`, s1.scrape_date,s1.sale_end,
        s1.the_min AS TheMin,
        s1.price_per_liter as ppv,
        s1.price_per_abv as ppvabv,
        s1.perc_diff as perc_diff,
        s1.avg_dif_perc as avg_dif_perc,
        getName(e.rel_1) as rel_1n,
        getName(e.rel_2) as rel_2n,
        getName(e.rel_3) as rel_3n
        FROM Items e
          INNER JOIN CurrentPrices s1
            ON (e.id = s1.id)
        WHERE e.id =$id
        LIMIT 0 , 1;";
    
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
            $prod_name=$row["name"];
            if (is_null($row["perc_diff"]))
            {
                $prod_name=$prod_name." <span class=\"label label-warning\">NEW</span>";
            }
            if ($count%5==0){
                // $grid_str=$grid_str."</div><div class=\"row\">";
            }
            $perc_diff=$row["perc_diff"];
            $rel_id = array($row["rel_1"],$row["rel_2"],$row["rel_3"]);
            $rel_name=array($row["rel_1n"],$row["rel_2n"],$row["rel_3n"]);
            $relatives="";
            for ($i=0;$i<3;$i++)
            {
                if(!is_null($rel_id[$i])&&$rel_id[$i]!=0)
                {
                    $relatives=$relatives."<div class=\" col-md-4\" > <div class=\"panel panel-default\" "
                            . "style=\"text-align: center\"><div class=\"panel-heading\">"
                            . "<a align=\"middle\" href=\"product.php?id=$rel_id[$i]\" STYLE=\"text-decoration: none\"> "
                            . "$rel_name[$i] </div><div class=\"panel-body\"><img height=80px "
                            . "src=\"https://raw.githubusercontent.com/ailgup/PriceTracker/master/images/$rel_id[$i]"
                            . ".jpg\"></div></div></div></a>";
                }
            }
            
            $count=$count+1;
            $grid_str=$grid_str."<div class=\"col-md-4 product-item\">
            
              <div class=\"panel panel-default \">
                <div class=\"panel-heading\">".$row["name"]."</div>
                <div class=\"panel-body\" align=\"middle\"><img src=\"";
                if($row["image"]=="True" || $row["image"]=="TRUE"){
                    $grid_str=$grid_str."https://raw.githubusercontent.com/ailgup/PriceTracker/master/images/".$row["id"].".jpg";
                }
                else{
                    $grid_str=$grid_str."login/images/bottle.png";
                }
            
                $grid_str=$grid_str."\" class=\"img-responsive\" style=\"height:300px\" alt=\"Image\"></div>
                <div class=\"panel-footer\">
                <table width=\"100%\">
                <tr><td>
                <b>Size:</b> ".$row["volume"]."</td>".
                "<td align='right'><b>Type:</b> ".$row["type"]."</td></tr>".
                "<tr><td><b>Category:</b> ".$row["category"]."</td>". 
                "<td align='right'><b>Proof:</b> ".$row["proof"]."</td></tr>".
                    
                "<tr><td><b>$/L:</b> ".$row["ppv"]."</td>".
                       
                "<td align='right'><b>$/L(Alc):</b> ".$row["ppvabv"]."</td></tr></table>";
                
                if($row["sale_price"]!=0) //On Sale
                {
                    $grid_str=$grid_str.
                    "<table width=\"100%\">
                <tr><td><b>Sale Ends:</b> ".$row["sale_end"]."</td>".
                    "<td align='right'><b>List Price: </b>$<del>".$row["price"]."</del></td></tr></table>";
                            
                }
                $grid_str=$grid_str."<h2><div style=\"text-align: center\">";
                    if($row["perc_diff"]>0) //On Sale
                    {
                        $grid_str=$grid_str."
                        <span class=\"label label-sale\">
                        $".$row["TheMin"]."  ▼ ".round($row["perc_diff"],1) ."%
                        </span>";
                    }
                    elseif($row["perc_diff"]<0) //On Sale
                    {
                        $grid_str=$grid_str."
                        <span class=\"label label-danger\">
                        $".$row["TheMin"]."  ▲ ".abs(round($row["perc_diff"],1)) ."%
                        </span>";
                    }
                    else{
                        $grid_str=$grid_str."
                        <span class=\"label label-nosale\">
                        $".$row["TheMin"]."
                        </span>";
                    }
                

                $grid_str=$grid_str."
                    </div>
                    </h2>
                   <h3>
                    <div style=\"text-align: center\">
                    <span class=\"label label-primary\">
                    Relative to Avg.: ";
                if($row["avg_dif_perc"]>0)
                {
                    $grid_str=$grid_str."▲";
                }
                elseif($row["avg_dif_perc"]<0)
                {
                    $grid_str=$grid_str."▼";
                }
                else
                    {
                    $grid_str=$grid_str."";
                    }   
                $quantity=getQuantity($row["id"],$curr_store);
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
                $grid_str=$grid_str.abs(round($row["avg_dif_perc"],2))."%
                    </span>
                    </div>
                    </h3>
                    <br>
                <div style=\"text-align: center\">
                <a target=\"_blank\" class=\"btn btn-danger\" role=\"button\" id=\"prev_link\" href=\"http://liquorandwineoutlets.com".$row["url"]."\">NHLiquor  <span class=\"glyphicon glyphicon-new-window\"></span></a>
                $button_str
                <br>";
                if($curr_store>0)
                {
                    $grid_str=$grid_str."<br>Store #$curr_store: <span data-toggle=\"tooltip\" title=\"Number In Stock\" class=\"badge $color[0]\">$quantity[0]</span> | <span data-toggle=\"tooltip\" title=\"Number On Order\" class=\"badge $color[1]\">$quantity[1]</span>";
                }
                $grid_str=$grid_str."</div>
                </div>
              </div>
          </div>";
            
        }
    }
    else
    {
        $prod_name="Product Not Found";
    }
    }
else{//no ID
    echo "Error ";
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


  <title>PriceTracker Product</title>

  


  
</head>

<body>
  <?php include "header.php";?>

    <div class="product container" style="margin-left:100px; margin-right:100px">
        <h1><?php echo $prod_name;?></h1>
        <div class="row">
            <div class="panel panel-default col-md-8" style="text-align: center">
                
                <iframe style="border:none;" height=400px class="col-md-12" src="graph.php?a[]=<?php echo $id;?>"></iframe>
                <br>
                <div class="col-md-12" style="text-align: left">
                    <h3>Related Products</h3>
                <div class="row" id="rel_tab" style="text-align: center">
                    <?php echo $relatives; ?>
                </div> </div>
            </div>
        <?php echo $grid_str; ?>
        </div> 
    </div>
<br>
<br>
<br>

<footer class="container-fluid text-center">
  <p>2016 - C.Puglia</p>
</footer>

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
$(document).ready(function(){
    $('#track').click(function(){
        var clickBtnValue = $(this).val();
        //alert(clickBtnValue);
        var ajaxurl = 'track.php',
        data =  {'action': clickBtnValue,'prod':<?php echo $id ?>,'username':'<?php echo $_SESSION['username'] ?>'};
        $.post(ajaxurl, data, function (response) {
            // Response div goes here.
            $("#track").text(response);
            $("#track").prop('value', response);
            //alert(response);
        });
    });

});
</script>
</body>

</html>


