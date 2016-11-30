<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require 'dbCreds.php';
$connection = new mysqli($servername, $username, $password, $dbname);
//test if connection failed
if(mysqli_connect_errno()){
    die("connection failed: "
        . mysqli_connect_error()
        . " (" . mysqli_connect_errno()
        . ")");
}
$SQL1="
update `Prices` INNER JOIN Items ON Items.id = CurrentPrices.id SET  `price_per_liter` = pricePerLiter(
		CurrentPrices.sale_price,
		CurrentPrices.price,
		Items.volume
		),`price_per_abv`=pricePerABV(Items.proof,CurrentPrices.sale_price,CurrentPrices.price,Items.volume) ,
		`the_min`= CASE WHEN  CurrentPrices.sale_price <  CurrentPrices.price
				   THEN  CurrentPrices.sale_price 
				   ELSE  CurrentPrices.price
					END;";
        
        $SQL2="update CurrentPrices as p1,(
			SELECT 
					mt1.price_id as q,( mt1.the_min - mt2.the_min
					) / mt1.the_min *100
					AS real_perc_diff
					FROM Prices mt1
					LEFT JOIN Prices mt2 ON mt2.scrape_date = ( 
					SELECT MAX( mt3.scrape_date ) 
					FROM Prices mt3
					WHERE mt3.id = mt1.id
					
					AND mt1.price_id != mt3.price_id
					AND mt3.scrape_date < mt1.scrape_date ) 
					WHERE mt2.id = mt1.id ) as p2
			 set p1.perc_diff = p2.real_perc_diff
			where p2.id = p1.id and p1.id=2100;";
        
        $SQL3="UPDATE `Prices` SET avg_dif_perc=(Prices.the_min-avgPrice(Prices.id))/Prices.the_min*100;";  

        mysqli_query($connection,$SQL1);
        echo "<br>price_per_liter,price_per_ABV, the_min <br> Affected rows: " . mysqli_affected_rows($connection);
        
        mysqli_query($connection,$SQL2);
        echo "<br>perc_diff <br> Affected rows: " . mysqli_affected_rows($connection);
        
        mysqli_query($connection,$SQL3);
        echo "<br>SET avg_dif_perc <br> Affected rows: " . mysqli_affected_rows($connection);
        
        mysqli_close($connection);
        
        ?>