from bs4 import BeautifulSoup
import requests
import re
import urllib.request
import os
import json
import sys
import pymysql
import pymysql.cursors
class Image:
	def isInt(self,s):
		try: 
			int(s)
			return True
		except ValueError:
			return False
			
	def findImageless(self):
		import csv

		host = "69.65.0.221" #input('Enter host: ')
		db= "chrispug_pricetracker"#input('Enter db')
		user = "chrispug_pricetr"#input('Enter user: ')
		pw = "booze4days" # input('Enter Password: ')
		conn= pymysql.connect(host=host,user=user,password=pw,db=db,charset='utf8mb4',autocommit=True,cursorclass=pymysql.cursors.DictCursor)
		a=conn.cursor()
		a.execute("SELECT name,id,volume FROM  `Items` WHERE Items.image='False';")
		result = a.fetchall()
		for row in result:
			print(row['name'])
			self.search(row['name']+" "+str(row['volume']),str(row['id']))
		print("Completed")
	def get_soup(self,url,header):
		return BeautifulSoup(urllib.request.urlopen(urllib.request.Request(url,headers=header)),'html.parser')

	def __init__(self,directory):
		
		self.dir=directory
		
	def search(self,query,id):
		self.filename=id
		self.query=query
		image_type="ActiOn"
		self.query= self.query.split()
		self.query='+'.join(self.query)
		url="https://www.google.com/search?q="+self.query+"&source=lnms&tbm=isch"
		#print (url)
		#add the directory for your image here
		header={'User-Agent':"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36"
		}
		soup = self.get_soup(url,header)
		#print soup.text

		ActualImages=[]# contains the link for Large original images, type of  image
		for a in soup.find_all("div",{"class":"rg_meta"}):
			try:
				link , Type =json.loads(a.text)["ou"]  ,json.loads(a.text)["ity"]
			except:
				break
			ActualImages.append((link,Type))

		#print  ("there are total" , len(ActualImages),"images")
		##ActualImages=ActualImages[0] # We only want the first one
		#print (self.dir)

		if not os.path.exists(self.dir):
					os.mkdir(self.dir)

		###print images
		
		for i , (img , Type) in enumerate( ActualImages):	
			try:
				req = urllib.request.Request(img, headers=header)
				raw_img = urllib.request.urlopen(req).read()

				cntr = len([i for i in os.listdir(self.dir) if image_type in i]) + 1
				#print (cntr)
				if len(Type)==0:
					f = open(os.path.join(self.dir , self.filename+".jpg"), 'wb')
					print("Saved to",self.dir ,"\\", self.filename,".jpg")
				else :
					f = open(os.path.join(self.dir , self.filename+"."+Type), 'wb')
					print("Saved to",self.dir ,"\\", self.filename,".",Type)


				f.write(raw_img)
				f.close()
				#print("Done")
				break #only want one, hackey I know
			except Exception as e:
				print ("could not load : "+img)
				print (e)
i=Image(sys.argv[1])
i.findImageless()