# PriceTracker 
###![screenshot](https://github.com/ailgup/PriceTracker/blob/master/readme/shot.png?raw=true) [Live Demo :arrow_upper_right:](http://chrispuglia.com/pt)



Product price tracker, built as a framework, but implemented to track historical prices from [LiquorandWineOutlets.com](http://LiquorandWineOutlets.com). Frontend is a PHP based web-app, and data is stored in a MySQL database. Legacy data was scraped from PDF's using python, and current pricing data scraped from the web using PHP.
## Frontend
PHP based frontend currently hosted [HERE](http://chrispuglia.com/pt)

### To Deploy
On a LAMP server running PHP 5.5+ run 
```
git clone https://github.com/ailgup/PriceTracker.git
cd frontend

# create dbCreds.php which contains database credentials

echo "<?php
\$servername = \"localhost\";
\$host = \"localhost\";
\$username = \"username\";
\$password = \"password\";
\$dbname = \"dbname\";
\$db_name = \"dbname\";
\$tbl_prefix = \"\";
\$tbl_members = $tbl_prefix.\"members\";
\$tbl_attempts = $tbl_prefix.\"loginAttempts\";" >> dbCreds.php
```
Now navigating to ```frontend/grid.php``` will display the frontpage, if it is desired to have the ```frontend``` directory automatically point to this page then simply run the following from the ```frontend``` directory
```
echo  "<?php header( 'Location: grid.php' ) ;?> " >> index.php
```
###Pages

####Grid
<table>
<tr><td width="200">
<img src="https://github.com/ailgup/PriceTracker/blob/master/readme/grid.png?raw=true" width="200"></td><td>
Main page of the site, features items visually with images, allows for search, and ordering based on many criteria.
Is not exhaustive in filtering as the table does a much better job in this area. Provides basic info about the product and offers an option to mouseover to preview the graph. Clicking on an item will take you to it's page. Features pagination with a default of 60 products per page.</td></tr></table>

####Table
<table>
<tr><td width="200"><img src="https://github.com/ailgup/PriceTracker/blob/master/readme/table.png?raw=true" width="200"></td><td>
Data-centric hub of the site, does not feature all the graphical elements present in the grid, but makes up for it with very powerful sorting and searching tools, in addition to the display of a number of more advanced fields such as Price Per Liter, and Price Per ABV*L. Allowing the user to sort by these gives a much more powerful experience than the grid.</td></tr></table>

####Deals
<table>
<tr><td width="200"><img src="https://github.com/ailgup/PriceTracker/blob/master/readme/deals.png?raw=true" width="200"></td><td>
Since prices are tracked historically the natural question arises, what are the best deals this month. To handle this the deals page uses an algorithm to rank products given how much the price has fallen from it's maximum value while also seeing how much it has changed in the past month. Items at the top of the list will have often fallen 30+% in the past month and will often be at all time lows, a great time to buy. You can also go to the other end of the spectrum and see the 'worst' deals, items that would not be recommended to purchase this month as the price is at a high point. The layout of the page is very similar to the Grid page.</td></tr></table>

####Product
<table>
<tr><td width="200"><img src="https://github.com/ailgup/PriceTracker/blob/master/readme/product.png?raw=true" width="200"></td><td>
This page gives all the details about a given product in a suscinct manner. On the right side of the page the items details are listed along with an image of the product. If the user is logged in the quantity of that product available at the given location is also listed. Important data like the price change in the last month, and the price relative to the average are also listed for easy comparison. On the left side of the page the price history graph takes up the majority of the page. With related products listed below for possible consideration.</td></tr></table>

####Account
<table>
<tr><td width="200"><img src="https://github.com/ailgup/PriceTracker/blob/master/readme/user.png?raw=true" width="200"></td><td>
This page allows users to view their "tracked" items, see their availability at their seleted store, as well as modify their selected store, or tracked products. This is very benefical as it serves as a single place for a user to view the products that that they are interested in without the clutter of uninteresting products.
</td></tr></table>


####Admin Account
<table>
<tr><td width="200"><img src="https://github.com/ailgup/PriceTracker/blob/master/readme/admin.png?raw=true" width="200"></td><td>
If the user is logged in as an admin they will see the admin button on their user page. Clicking this button leads to the admin acount page where more details concerning the backend of the site, especially items and prices that do not fit nicely into the database. The admin can add Items that have appeared in the most recent scrape but have not yet been added to the Items table. This is done by performing a live PHP-based scrape of the merchants site. 
</td></tr></table>


## Backend
### To Deploy
On a MySQL server run ```SQL\pricetracker.sql```

### To perform new Scrape
Run ```Mine.py``` as detailed below, can be run as a CRON job monthly to automate the process.

Once ```Mine.py``` has generated a CSV file ```csvToSQL.py``` can be run to upload the scrape to the MySQL database
### Mine.py
Used to mine product data from the website, currently stored in .csv, ultimatly will live in SQL
```
#Price Mine ~5min
python mine.py 1
``` 
will do **Price** mine and will save to a .csv
```
# Item Mine ~3hrs
python mine.py 2
``` 
will do **Item** mine and will save to a .csv (Takes much longer)
### csvToSQL.py
Used to upload legacy .CSV files to the MySQL database upon completion of scrape

**Note**: IP address and database name will need to be modified
```
# upload test.csv to Prices table
python csvToSQL.py test.csv
```
### ImageScrape.py
Finds the items which lack images and does a scrape of Google Images saving the first image found by searching Product_name+Volume. After running this program and uploading the images to GitHub the Update Images script should be run from the Admin Panel to update the SQL database with which products contain images and which do not.
```
# run google image scrape
python ImageScrape.py dir_to_save_to
```
After running this script is may be wise to manually go through and delete images which are not accurate.
