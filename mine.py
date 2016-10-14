
import re
import csv
from bs4 import BeautifulSoup
import requests
import time
import urllib.request
import os
import json
import datetime
import sys

"""
Extract function

Returns text between the first instance of start
and the first instance of end after start, exclusive.

ex. extract("x","y","hello x this is x but also y y")
	would return "this is also x but also y"
"""
def extract(start,end,string):
	return str(((string.split(start)[1]).split(end)[0]).strip().encode('utf-8').decode('ascii'))
def endFirstExtract(start,end,string):
	first=string.split(end)[0]
	return str((first.split(start)[len(first.split(start))-1]).strip().encode('utf-8').decode('ascii')).replace(end,"")

def getNumPages(url):
	request2 = requests.get(url)
	if request2.status_code == 200:
		soup = BeautifulSoup((request2.text), 'html.parser')
		s=soup.findAll("div",{"class":"pagination"})[0]
		for row in s.findAll("a"):
			if row.text == "Last":
				return(extract("page=","&",str(row)))
	return 0	
	
"""
Scrape Function

Used to scrape website for both price data as well as
product data. The function creates a .csv file with the
data exported

Parameters:
 - mode (int)
	determines how much detail we want by the scrape
	1 = Price data only (~5 min)
	2 = Price and Product data (~3 hrs)
 - start (int) 
	determines which page to begin the scrape on
	1 = default
"""
def scrape(mode,start=1):
	mode=int(mode)
	if not((mode is 1) or (mode is 2)):
		print (mode)
		raise ValueError('Illegal mode value')
	filename="spirits"+(datetime.datetime.now()).strftime("%m-%d-%y-%H_%M_%S")+".csv"
	with open(filename, 'w', newline='') as csvfile:
		out = csv.writer(csvfile, delimiter=',', quotechar='|', quoting=csv.QUOTE_MINIMAL)
		out.writerow(["Scraped", datetime.date.today()])
		if mode is 2:
			out.writerow(["ID","Name","Volume","Price","Sale Price","Sale Ends","Proof","Type","Category","url","image","relative_1","relative_2","relative_3"])
		if mode is 1:
			out.writerow(["ID","Name","Volume","Price","Sale Price","Sale Ends"])
		index=0
		numPages=int(getNumPages("http://www.liquorandwineoutlets.com/products?type[]=spirit"))
		for i in range(int(start),numPages):
			request = requests.get("http://www.liquorandwineoutlets.com/products?page="+str(i)+"&type%5B0%5D=spirit")
					
			if request.status_code == 200:
				soup = BeautifulSoup((request.text), 'html.parser')
				
				for s in soup.findAll("tr", {"class" : re.compile('product_row_*')}):
					tds=s.findAll("td")
					link=extract("<a href=\"","\"",str(tds[0]))
					id=extract("\">","</a>",str(tds[0]))
					name=extract("\">","</a>",str(tds[1])).replace("\"","") #removes quotes
					
					vol=extract(">","<",str(tds[2]))
					price=str(tds[3].text).strip().replace(",","") #removes commas on prices >999
					sale_price=str(tds[4].text).strip()
					sale_ends=str(tds[5].text).strip()
					#Now looking into the individual item
					#print (link)
					if mode is 2:
						request2 = requests.get("http://www.liquorandwineoutlets.com"+link)
						if request2.status_code == 200:
							soup = BeautifulSoup((request2.text), 'html.parser')
							proof = ((str(soup.find_all('div','tk-chaparral-pro')[1]).split("</strong>")[2]).split(u'\N{DEGREE SIGN}')[0]).strip()
							type = extract("Type:</strong>","<",str(soup.findAll("div",{"class":"tk-chaparral-pro"})[1]))
							#print(type)
							category = extract("Category:</strong>","<",str(soup.findAll("div",{"class":"tk-chaparral-pro"})[1]))
							try:
								relative_1 = extract("detail/","/",str(soup.findAll("div",{"class":"sep"})[0]))
							except:
								relative_1="-"
							try:
								relative_2 = extract("detail/","/",str(soup.findAll("div",{"class":"sep"})[1]))
							except:
								relative_2="-"
							try:
								relative_3 = extract("detail/","/",str(soup.findAll("div",{"class":"sep"})[2]))
							except:
								relative_2="-"
							image = str(((str(soup.find_all('img','product_details_image')[0]).split("src=\"")[1]).split("\"")[0]).strip()).replace("http://","")
							if "spirit_silo" in image:
								#print ("Need to Google Image")
								image = False
							else:
								if not (os.path.isfile("images\\"+id+".jpg")):
									urllib.request.urlretrieve(("http://"+urllib.parse.quote(image)),"images\\"+id+".jpg")
								image =True
						print (index," ",id," ",name," ",vol," ",price," ",sale_price," ",sale_ends," ",proof," ",type," ",category," ",link," ",image," ",relative_1," ",relative_2," ",relative_3)
						out.writerow([index,id,name,vol,price,sale_price,sale_ends,proof,type,category,link,image,relative_1,relative_2,relative_3])
					else:
						print (index," ",id," ",name," ",vol," ",price," ",sale_price," ",sale_ends)
						out.writerow([index,id,name,vol,price,sale_price,sale_ends])
					csvfile.flush()
					time.sleep(5)
					index+=1
					#id=
			else:
				raise("Error probably overwhelming server")
			time.sleep(5) #slows things down so we don't overwhelm the server
				
scrape(sys.argv[1])
