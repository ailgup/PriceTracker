import re
import csv
import time
import os
import sys
content=""
rejects=""
count=0
counta=0
countb=0
nf=0
bad=False
with open(sys.argv[1], encoding='utf8') as f:
	content = f.readlines()
	
wine_index=len(content)	
for line in content:
	if "AMERICAN WINE" in str(line):
		wine_index = content.index(line)
		print (wine_index)
		break
"""
Should do this differently, should have a 
first pass in which the table online is looked at and the id's are established

n'th pass in which the prices are scraped and related

"""		
content = content[:wine_index]
with open(sys.argv[1].replace(".csv","")+'_output.csv', 'w', newline='') as csvfile:
	out = csv.writer(csvfile, delimiter=',', quotechar='|', quoting=csv.QUOTE_MINIMAL)
	#out.writerow(["ID","Name","Volume","Proof","Type","Category","url","image","relative_1","relative_2","relative_3"])
	for line in content:
		re1='^(\\d+)'	# Integer Number 1
		re2='.*?'	# Non-greedy match on filler
		re3='(\\d*\\.\\d9)'	# Float 1
		re4='(.*?\\d*\\.\\d9)?'	# Float 2

		rg = re.compile(re1+re2+re3+re4,re.IGNORECASE|re.DOTALL)
		m = rg.search(line)
		rg2 = re.compile(re1,re.IGNORECASE|re.DOTALL)
		m2 = rg2.search(line)
		if m:
			int1=m.group(1)
			float1=m.group(2)
			float2=re.findall("\d+\.\d+",str(m.group(3)))
			if(len(float2) is 0):
				out.writerow([str(int1),str(float1).strip(),"NULL"])
				#print ("("+int1+")"+"("+float1+")"+"\n")
				counta=counta+1
			else:
				out.writerow([str(int1),str(float1).strip(),str(float2[0]).strip()])
				#print ("("+int1+")"+"("+float1+")"+"("+float2+")"+"\n")
				countb=countb+1
			count=count+1
		elif m2:
			if("Items in green are earth-friendly products" not in line and ("|") not in line):
				rejects=rejects+line+"\n"
				nf=nf+1
		else:
			pass
			#rejects=rejects+line+"\n"
			#nf=nf+1

csvfile.close()
with open(sys.argv[1].replace(".csv","")+'_error.txt', 'w', newline='') as errfile:
	errfile.write(rejects)
errfile.close()

print ("count:",count,"not found:",nf,"a;",counta,"b:",countb,"\n\n")
print(rejects)