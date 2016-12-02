# MySQL File Details

## Grading Criteria
1. Tables
  1. Items - Products to be displayed
  2. Prices - Legacy price data
  3. CurrentPrices - Current price data, includes advanced calculated values
  4. members - users of the application, and their attributes
  5. Tracking - associates a user with an Item that they are tracking
  6. loginAttempts - tracks the number of unsuccessful login attempts from a user to prevent a brute force attack
2. Primary and Foreign Keys
  * Items - Primary key (id), cannot make Prices.id a foreign key because it is not the primary key of the table
  * Prices - Primary key (price_id), cannot make Items.id a foreign key because there exist Prices that do not yet have Items created for them, hence the 'Prices without Items' section of the admin page
  * CurrentPrices - Primary key (price_id), same as Prices in regard to foreign keys
  * members - Primary key (id), no foreign keys
  * Tracking - Primary key (track_id), FOREIGN KEY (`user_id`) REFERENCES `members` (`username`), FOREIGN KEY (`item_id`) REFERENCES `Items` (`id`)
  * loginAttempts - Primary key (ID), cannot have foreign key of members.username because login attempts can be made by "fake" usernames and we don't want attackers to know the username is fake so the same max attempt policy is used
3. Integrity and Additional Constraints
  * Integrity - All foreign keys have integrity constraints built in
    * `members` (`username`) ON DELETE CASCADE ON UPDATE CASCADE, since if a member is deleted they should be dropped from tracking
    * `Items` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION, if an Item is deleted (no longer sold) we want to alert the user with a warning, not just delete the Item from their Tracking list, therefore it is kept.
  * Additional Constraints
    * Items
      `id`: NOT NULL,
      `name`: NOT NULL,
      `volume`: NOT NULL,
      `proof`: NOT NULL,
      `type`: NOT NULL,
      `category`: NOT NULL,
      `url`: NOT NULL,
      `image`: NOT NULL,
      `rel_1`: NOT NULL,
      `rel_2`: NOT NULL,
      `rel_3`:DEFAULT NULL --some Items only have two related items, therefore this third one is optional
    * Prices
      * `price_id`: NOT NULL AUTO_INCREMENT,
      * `id`: NOT NULL,
      * `price`: NOT NULL,
      * `sale_price`: DEFAULT NULL,
      * `scrape_date`: NOT NULL,
      * `sale_end`: DEFAULT NULL,
      * `the_min`: DEFAULT NULL,
      * UNIQUE KEY `price_id` (`price_id`), -- each there should be no duplication of the primary key!
    * CurrentPrices
      * `price_id`: NOT NULL AUTO_INCREMENT,
      * `id`: NOT NULL,
      * `price`: NOT NULL,
      * `sale_price`: DEFAULT NULL,
      * `scrape_date`: NOT NULL,
      * `sale_end`: DEFAULT NULL,
      * `price_per_liter`: DEFAULT NULL,
      * `price_per_abv`: DEFAULT NULL,
      * `the_min`: DEFAULT NULL,
      * `perc_diff`: DEFAULT NULL,
      * `avg_dif_perc`: DEFAULT NULL,
      * UNIQUE KEY `price_id` (`price_id`), -- each there should be no duplication of the primary key!
      * UNIQUE KEY `id` (`id`)-- each item should only appear in CurrentPrices once, since there can be only one CurrentPrice 
    * members
      * `id` : NOT NULL,
      * `username` : NOT NULL DEFAULT '',
      * `password` : NOT NULL DEFAULT '',
      * `email` : NOT NULL,
      * `verified` : NOT NULL DEFAULT '0',
      * `mod_timestamp` : NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      * `store` : DEFAULT NULL,
      * UNIQUE KEY `username_UNIQUE` (`username`),
      * UNIQUE KEY `id_UNIQUE` (`id`),
      * UNIQUE KEY `email_UNIQUE` (`email`)
    * Tracking
      * `track_id`: NOT NULL AUTO_INCREMENT
      * `user_id`: NOT NULL
      * `item_id`: NOT NULL
      * UNIQUE KEY `unique_index` (`user_id`,`item_id`)
    * loginAttempts
      * `IP`: NOT NULL,
      * `Attempts`: NOT NULL,
      * `LastLogin`: NOT NULL,
      * `Username`: DEFAULT NULL,
      * `ID`: NOT NULL AUTO_INCREMENT,
4. Complexity of Schema 
  See above, also of note is the size of the Tables, especially Prices which contains 65,000+ rows
5. Secondary Indices
  Since the data is as minimized as possible to both save storage space and crucial computation time given the large size of the database, there are few secondary keys in the data. This is to be expected in a well condensed schema 
  Other Possible secondary indices include
  * Items Table:  Name + Volume + Proof 
  * members Table:  username + password 
6. User Defined Functions/Procedures/Triggers
  * Triggers are used extensively to handle the input of data into the database triggers used in the 
    * ```CurrentPrices``` table calculate the price_per_liter, price_per_abv, the_min (the sale price if on sale, otherwise the list price), perc_diff (percent difference from last month), and avg_dif_perc (the percent difference from the average price)
    * ```Prices``` table to calculate the_min (the sale price if on sale, otherwise the list price), which is crucial for graphing
	  * ```Items``` table to ensure that if a new Item is created it's associated CurrentPrice is updated with the price_per_liter and price_per_abv
  * Functions are also used in many of these calculations to make it easier on the frontend for these repeated tasks such as 
    * ```actualVol```- given a Item id and which contains a string based volume eg. (750mL or 1.75L or 750mL 2 Pk) but it needs to be in Liters for the price_per_volume calculation, this function uses regex to extract the actual volume	
    * ```avgPrice```- given an Item id return the average price of the item over time
    * ```getName```- given an Item id return the name of the item
    * ```pricePerABV``` - given an Item id return the price per liter of pure alcohol eg.((current price)/(item volume))*(200/proof)
    * ```pricePercDiff``` - given an Item id return the percent difference from the previous price to the current price eg. (current-past)/current*100
    * ```pricePerLiter``` - given an Item id return the unit price in $/L of a given item
