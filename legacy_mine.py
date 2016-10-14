"""

WARNING 

Code is broken as is, here as a refference

"""


import re
import csv
from bs4 import BeautifulSoup
import requests
import time
import urllib.request
import os
import json


def googleImage(query):
	#import urllib2
	import json
	#import cStringIO

	#fetcher = urllib2.build_opener()
	searchTerm = 'parrot'
	startIndex = 0
	searchUrl = "https://www.google.com/search?tbm=isch&q=" + searchTerm + "&start=" + str(startIndex)
	f = requests.get(searchUrl)
	print (f.text)
	deserialized_output = json.loads(f.text)
	print (deserialized_output)
	imageUrl = deserialized_output['responseData']['results'][0]['unescapedUrl']
	print (imageUrl)
	return imageUrl
	
content=""
rejects=""
count=0
nf=0
with open("import.txt", encoding='utf8') as f:
	content = f.readlines()
	
wine_index=len(content)	
for line in content:
	if "WINE" in str(line):
		wine_index = content.index(line)
		print (wine_index)
		break
"""
Should do this differently, should have a 
first pass in which the table online is looked at and the id's are established

n'th pass in which the prices are scraped and related

"""		
content = content[:wine_index]
with open('spirits.csv', 'w', newline='') as csvfile:
	out = csv.writer(csvfile, delimiter=',', quotechar='|', quoting=csv.QUOTE_MINIMAL)
	out.writerow(["ID","Name","Volume","Proof","Type","Category","url","image","relative_1","relative_2","relative_3"])
	for line in content:
		x=re.findall("^\d+",line)
		x+=re.findall("\d+\.\d\d(?!\d)(?!\L)",line)
		if len(x)>1 and len(x)<4:
			
			request = requests.get("http://www.liquorandwineoutlets.com/products/detail/"+str(x[0]))
			
			if request.status_code == 200:
				soup = BeautifulSoup((request.text), 'html.parser')
				title = soup.h1.string
				
				reg_price = x[1]
				if len(x) is 3:
					sale_price = x[2]
				else:
					sale_price = reg_price
					
				### Scrape Prices
				# price = str(soup.find_all("p","big red")[0])
				# if "Sale" in price:
					# sale_price = re.findall("(\$\d+(\.\d+)?)",price)[0][0].strip("$")
					# reg_price = re.findall("(\$\d+(\.\d+)?)",((str(soup.find_all('div','col col_1')[0]).split("\"strike\">")[1]).split("</")[0]).strip())[0][0].strip("$")
					# sale_ends = re.findall("(\$\d+(\.\d+)?)",((str(soup.find_all('div','col col_1')[0]).split("Sale Ends:</strong>")[1]).split("<")[0]).strip())[0][0].strip()
				# else:
					# reg_price = re.findall("(\$\d+(\.\d+)?)",price)[0][0].strip("$")
					# sale_price = reg_price
					# sale_ends =""
				### End Scrape Prices
					
				volume = ((str(soup.find_all('div','tk-chaparral-pro')[1]).split("</strong>")[1]).split("<br>")[0]).strip()
				#print(volume)
				proof = ((str(soup.find_all('div','tk-chaparral-pro')[1]).split("</strong>")[2]).split(u'\N{DEGREE SIGN}')[0]).strip()
				type = str((request.text.split("Type:</strong>")[1]).split("<")[0])
				category = str((request.text.split("Category:</strong>")[1]).split("<")[0])
				relative_1 = ((str(soup.find_all('div','sep')[0]).split("detail/")[1]).split("/")[0]).strip()
				relative_2 = ((str(soup.find_all('div','sep')[1]).split("detail/")[1]).split("/")[0]).strip()
				relative_3 = ((str(soup.find_all('div','sep')[2]).split("detail/")[1]).split("/")[0]).strip()
				url = "http://www.liquorandwineoutlets.com/products/detail/"+str(x[0])
				image = str(((str(soup.find_all('img','product_details_image')[0]).split("src=\"")[1]).split("\"")[0]).strip())
				if "spirit_silo" in image:
					print ("Need to Google Image")
					image = False
				else:
					if not (os.path.isfile("images\\"+x[0]+".jpg")):
						urllib.request.urlretrieve(image,"images\\"+x[0]+".jpg")
					image =True
				print ("\nID: ",str(x[0]),"\nTitle: ",title,"\nReg Price: ", reg_price, "\nSale Price: ", sale_price, "\nVol: ",volume,"\nProof: ",proof,"\nType",type,"\nCategory",category,"\nRelatives: ",[relative_1,relative_2,relative_3],"\nURL: ",url,"\nImage: ", image)
				
				
				count+=1
			else:
				nf+=1
			time.sleep(5)
		else:
			rejects+=line


print (count,nf)

