from bs4 import BeautifulSoup
import requests
import re
import urllib.request
import os
import json

class Image:

	def get_soup(self,url,header):
		return BeautifulSoup(urllib.request.urlopen(urllib.request.Request(url,headers=header)),'html.parser')

	def __init__(self,query,directory,filename):
		self.query=query
		self.dir=directory
		self.filename=filename
	def search(self):
		image_type="ActiOn"
		self.query= self.query.split()
		self.query='+'.join(self.query)
		url="https://www.google.com/search?q="+self.query+"&source=lnms&tbm=isch"
		print (url)
		#add the directory for your image here
		header={'User-Agent':"Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.134 Safari/537.36"
		}
		soup = self.get_soup(url,header)
		#print soup.text

		ActualImages=[]# contains the link for Large original images, type of  image
		for a in soup.find_all("div",{"class":"rg_meta"}):
			link , Type =json.loads(a.text)["ou"]  ,json.loads(a.text)["ity"]
			ActualImages.append((link,Type))

		print  ("there are total" , len(ActualImages),"images")
		##ActualImages=ActualImages[0] # We only want the first one
		print (self.dir)

		if not os.path.exists(self.dir):
					os.mkdir(self.dir)

		###print images
		
		for i , (img , Type) in enumerate( ActualImages):	
			try:
				req = urllib.request.Request(img, headers=header)
				raw_img = urllib.request.urlopen(req).read()

				cntr = len([i for i in os.listdir(self.dir) if image_type in i]) + 1
				print (cntr)
				if len(Type)==0:
					f = open(os.path.join(self.dir , self.filename+".jpg"), 'wb')
					print("Saved to",self.dir , self.filename,".jpg")
				else :
					f = open(os.path.join(self.dir , self.filename+"."+Type), 'wb')
					print("Saved to",self.dir , self.filename,".",Type)


				f.write(raw_img)
				f.close()
				print("Done")
				break #only want one, hackey I know
			except Exception as e:
				print ("could not load : "+img)
				print (e)
		