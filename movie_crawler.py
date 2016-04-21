import urllib
import urllib2
from bs4 import BeautifulSoup
import re
import imdb
import mysql.connector 
from selenium import webdriver
imdb_access = imdb.IMDb()
con = mysql.connector.connect(user="cs411uif_wxh",password="12345",host="engr-cpanel-mysql.engr.illinois.edu",database="cs411uif_database")

movie_name_list = open("return_imdb/input.txt").read();
movie_name_list = movie_name_list.split("\n")
'''
myfile_movieID = open("return_imdb/output_movieID.txt",'r+')
myfile_poster = open("return_imdb/output_poster.txt",'r+')
myfile_year = open("return_imdb/output_year.txt",'r+')
myfile_runtime = open("return_imdb/output_runtime.txt",'r+')
myfile_rating = open("return_imdb/output_rating.txt",'r+')
myfile_storyline = open("return_imdb/output_storyline.txt",'r+')
myfile_genre = open("return_imdb/output_genre.txt",'r+')
'''



cursor = con.cursor()

for name in movie_name_list:


	contain = imdb_access.search_movie(name)#(movie_list[x])
	if len(contain)!=0:
		ID = contain[0].movieID #str type
	summary = imdb_access.get_movie(ID)
	#print "http://www.movieposterdb.com/Movie/"+str(ID)
	driver = webdriver.Firefox()
	#htmltext = (urllib.urlopen("http://www.movieposterdb.com/Movie/"+str(ID))).read()
	driver.get("http://www.movieposterdb.com/Movie/"+str(ID))
	flag = 0
	links = driver.find_elements_by_tag_name('img')
	for i in links:
		if flag == 1:
				continue
		if 'posters' in i.get_attribute('src'):
			url_links = i.get_attribute('src')
			flag = 1
			
	driver.close()
	#soup = BeautifulSoup(htmltext, 'html.parser')
	#links = soup.findAll(class="mpdb-movie-thumbnails-img-link") 
	#links = soup.find("a", { "class" : "mpdb-movie-thumbnails-img" })
	
	'''
	if not links:
		
		htmltext = (urllib.urlopen("https://en.wikipedia.org/wiki/"+name+" (film)")).read()

		soup = BeautifulSoup(htmltext, 'html.parser')

		link_2 = soup.find(width="220")
	
		if not link_2:
			
			htmltext = (urllib.urlopen("https://en.wikipedia.org/wiki/"+name+" ("+str(summary.get('year'))+" film)")).read()

			soup = BeautifulSoup(htmltext, 'html.parser')

			link_3 = soup.find(width="220")

			if not link_3:
				continue 
	'''
	
	# links = str(soup.find(width="220") )
	# start = links.find("srcset")+10
	# end = links.find("1.5x,")

	# url_links = "https://"+(str(links))[start:end]
	
	print url_links

	year = int(summary.get('year'))
	runtime = str((summary.get('runtime'))[0]) #get a list we use [0] to get the time
	rating = float(summary.get('rating'))
	storyline = str((summary.get('plot'))[0])
	genre = str(' '.join(summary.get("genre")))
	print storyline
	#addEntry = """ INSERT INTO Movies
			#	
			#	VALUES (ID,    name   ,  year,  runtime  , rating,   storyline,   genre , links) 
	

	args = {

		"ID": ID,
		"name": name,
		"year": year,
		"runtime": runtime,
		"rating": rating,
		"storyline": storyline,
		"genre": genre,
		"url_links": url_links
	}
	
	query = ("INSERT INTO Movies" "(movie_id,movie_name,movie_year,duration,score,storyline,genre,poster)" 
		"VALUES (%(ID)s,%(name)s,%(year)s,%(runtime)s,%(rating)s,%(storyline)s,%(genre)s,%(url_links)s)")	#args = (ID,name,year,runtime,rating,storyline,genre,links) 
	
	cursor.execute(query,args)
	con.commit()

cursor.close()
con.close()


print "Done!"
