import csv
import sys
import pymysql
import pymysql.cursors
pw = input('Enter Password: ')
conn= pymysql.connect(host='69.65.0.221',user='chrispug_pricetr',password=pw,db='chrispug_pricetracker',charset='utf8mb4',autocommit=True,cursorclass=pymysql.cursors.DictCursor)
a=conn.cursor()
with open(sys.argv[1], 'r') as f:
    reader = csv.reader(f)
    your_list = list(reader)
#output_str="INSERT INTO `Prices`(`id`,`price`,`sale_price`,`scrape_date`,`sale_end`) VALUES "
for l in your_list:
	output_str="("+l[1]+","+l[4]+","+l[6]+",DATE'"+l[5]+"',DATE'"+l[7]+"')"
#(1,0,45.00,40.00,'2016-05-01','2016-05-31')
#0,2156,(ri) 1 Kentucky Straight Rye Whiskey,750mL,39.99,16-10-28,36.99,16-10-30
#0   1                    2                     3    4      5       6     7

	sql="INSERT INTO `Prices`(`id`,`price`,`sale_price`,`scrape_date`,`sale_end`) VALUES "+output_str+";"
	print(sql)
	a.execute(sql)
	print("Insert: ",str(l[1])," ",l[0],"/",len(your_list))
#result = a.fetchone()
#print (result)

# target = open(sys.argv[2], 'w')
# target.write(output_str)
# target.close()
print("Complete")
