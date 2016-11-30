import os
import csv
import sys
import pymysql
import pymysql.cursors

pw = input('Enter Password: ')
conn= pymysql.connect(host='69.65.0.221',user='chrispug_pricetr',password=pw,db='chrispug_pricetracker',charset='utf8mb4',autocommit=True,cursorclass=pymysql.cursors.DictCursor)
a=conn.cursor()

for file in os.listdir(sys.argv[1]):
	count=0
	if file.endswith(".csv"):
		with open(sys.argv[1]+"\\"+file, 'r') as f:
			reader = csv.reader(f)
			your_list = list(reader)
		for l in your_list:
			output_str="("+str(l[0])+","+str(l[1])+","+str(l[2])+",DATE'"+"20"+file[2:4]+"-"+file[:2]+"-01"+"',DATE'"+"20"+file[2:4]+"-"+file[:2]+"-28"+"')"
			count=count+1
			sql="INSERT INTO `Prices`(`id`,`price`,`sale_price`,`scrape_date`,`sale_end`) VALUES "+output_str+";"
			#print(sql)
			a.execute(sql)
			print("Insert: ",file[:4],"- ",count,"/",len(your_list))

		print("Complete")
