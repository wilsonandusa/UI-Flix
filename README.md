# Web_Development
Front-End &amp; Back-End Development(UI-Flix)
SP16 CS411 Final Report
UI-Flix
Members: Xiaohao Wang
     Zihao Zhou
     Xiaosheng Wu 

1.Briefly describe what the project accomplished.
	In our project, we build a movie review website, which also incorporates the features to allow user interaction. The main goal of this website is to construct a movie review community. In order to achieve this goal, we use database management system to build data on users and modify the website based on their behaviors as inputs to database. 

2. Discuss the usefulness of your project, i.e. what real problem you solved.
Unlike current popular movie review website, our movie website database system will target each individual user. This feature creates a sense of belong between the user and the website community. In addition, the more the user use the website, the more accurate the view will be modified to fit the user’s taste. This can potentially be used a movie filters for new movies after enough usage. Another distinct feature is that our movie website allow interaction among users. With the “YP” button on each movie's page, a user can find other users who live close and share a interested movie together via E-mail communication. 

3.Discuss the data in your database
The data in our database is consist of two parts that from different sources. First, we implemented crawler program with IMDB package that takes an input file and collects all the info about the target movie. After the program returns the data, it will be stored in our database under the “Movies” table. We stores more than 500 records in our database. The second part of the data comes from users, we have multiple tables in our database to store not only the user information but also the user’s preference towards movie genres and browsing history. These data will be calculated through a formula to reproduce lists of recommended movies on the main page of the website, which will contributes our first advance function. The user’s private data such as contact information was also used to implement our second advanced function. 

4.Include your ER Diagram and Schema

Movies(movie_id, movie_name, year, duration, score, genre, storyline, poster_url, counter)
Favourite_count(email, genre, count)
Favrouite_moive(email, movie_id)
History(email, movie_id, age)
History_count(email, genre, count)
User(email, username, password, phone, address)
Comments(ID,comment,email,movie_name,movie_year) // yet to be implemented

5.Briefly discuss from where you collected data and how you did it (if crawling is automated, explain how and what tools were used)
The crawler program is implemented by python and IMDB package. It takes an input file and collects all the info about the target movie. IMDB package is an open source software for developers and we also implement the crawler so it can find movie posters online. After the program returns the data, it will be stored in our database under the “Movies” table. We stores more than 500 records in our database. 

6.Clearly list the functionality of your application (feature specs)
	1) Create account
	2) Delete account
	3) Login with specific account
	4) Favorite/unfavorite Movie
	5) Recommend movie based on user preference
	6) Select users who live close and loves same type of movie to allow user interaction 

7.Explain one basic function
One basic function is for the user to create an account. It first checks the email for    duplicate, then insert the new user into User table and create the corresponding entries in History_count and Favourite_count.

8.Show the actual SQL code snippet
Register(Including error check and error message):
























User login and deletion:


Search Function:


YP(User Interaction):

View history:


Favorite Function:


Movie Recommendation:





9.List and briefly explain the data flow, i.e. the steps that occur between a user entering the data on the screen and the output that occurs (you can insert a set of screenshots)
1)When user clicks into a movie
When user clicks a movie, it always updates user’s history and history count. If the movie was in the recent history, it rearranges the history so that the current movie has an age 0. If the movie is not in recent history, it adds the movie to history and increment the age of all other movies in user’s recent history. It also deletes all movies that has an age greater than 20 to ensure we don’t have too much data in the table. After that, it increment the history count and view count. Also, it checks if the current movie is in user’s favourite list. If the movie is in user’s favourite, it changes the button to “favourite”, if not, it changes the button to “favourite”.
When user clicks the favourite button, it checks whether the current movie is already in favourite table. If so, there is an error so we displays an error message. Otherwise we add the movie to favourite and increment the favourite count based on the genre of the current movie. Then change the favourite button to unfavourite. The unfavourite button does opposite. It deletes the movie from favourite table and decrement the favourite count based on the genre of the current movie.

2)When user get back to our main page
When user gets back to our main page, our first advanced function runs and produces new recommendations (see the first advanced function for more detail) for movies and genres. Then displays them in the form of posters.

3) When user register an account
User can register an account via the register page through the “register” button from login page. The input information will collected and parse into the database system to check for validity. The validity check includes sanity check and duplication check. If the user input is valid, it will insert a row into the user table. After that, user can login with the registered information to access his or her account. 

4)When user click YP button
	User can click YP button on each movie to indicate that they would like to see this movie with some other user. This button will pull the record of current movie and get its genre. At the same time, it will search the database for users who have the same area code as the current user. Only when either the current movie genre is one of the user’s favorite genre or the user also favorites this particular movie, the user’s email will be returned. With the returned email, our website will generate an email as an invitation to see movie together. The email will be directly sent to the returned email address.


10.Explain your two advanced functions and why they are considered as advanced. Being able to do it is very important both in the report and final presentation.
Advance function 1:Recommend movies and genres:
Our first advanced function is a recommendation algorithm to recommend movies to the users. Every time when a user enters our main page, the algorithm runs and fills the main page with recommended movies. The algorithm has 2 parts. The first part recommend 12 movies to the user based on user’s favourites and history. It also take view count of the movies into consideration and tries to recommend movies that has the most view count. The algorithm also tries to avoid user’s recent history to make sure user get some new movies every time. 
The second part is a genre recommendation based on user’s history and favourites. It will recommend 6 genres to the user. To make sure user gets some new movies every time, 5 of them will be the genres that user like and the other one will always be a random genre. If there is not enough genres for recommendation, the algorithm will fill in random genres.

Advance function 2:
	In order to achieve user interaction, we implement the second advance function. This function is designed to find other users who are nearby and may be interested in seeing the movie based on current login user.This algorithm includes two query tasks. As the current user selects a movie. The first part of the algorithm will search the entire database for the users whose favorite movies include this particular movie selected by current user. Also, the users must have the same area code as the current user. Therefore, the users can actually go to a movie theater together. The second part of the search algorithm comes in when the first part fails to find a user. Based on the selected movie, the algorithm finds users who potentially likes this type of movies. The information comes from the history and favorite records in each user’s database. Again, this user also must have the same phone area code as the current user to ensure they can interaction offline to see movie together. 
	


11.Describe one technical challenge that the team encountered.  This should be sufficiently detailed such that another future team could use this as helpful advice if they were to start a similar project or were to maintain your project. Say you created a very robust crawler - share your knowledge. You learnt how to visualize a graph and make an interactive interface for it - teach all! Know how to minimize time building a mobile app - describe!
I wrote my crawler program in python 2.7 with mysqldb library enabled so that the program can automatically update the information in our database system when it runs. I had a hard time installing mysql lib on my mac, because it was not correctly installed and I would strongly against using python 3.7 because there are people who failed to install mysqldb due to the different version. 
Tips: When you can not solve a technical question, try post the question on stackoverflow.com!



12.State if everything went according to the initial development plan and proposed specifications, if not - why?!
	We originally planned to also include some data scheme for people like directors and actors. But one of our teammate drops the course so we simplified things a little bit and focused more on movie information.  The crawler program was initially designed to collect poster information from wikipedia, but later we changed the path since the program sometimes returns irrelevant pictures. 

13.Describe the final division of labor and how did you manage teamwork.
	Zihao Zhou ---  UI design, frontend decoration, user interaction function, project proposal
	Xiaohao Wang      --- backend setup, movie recommendation function, movie page setup
	Xiaosheng Wu      --- web crawler,frontpage decoration, database setup, youtube video
