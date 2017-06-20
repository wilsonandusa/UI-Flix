## Since the old server is down, the images are gone, but the rest of the website works fine
## http://cs411uiflix.web.engr.illinois.edu
Front-End &amp; Back-End Development(UI-Flix)
SP16 CS411 Final Report
UI-Flix
Members: Xiaohao Wang
     Zihao Zhou
     Xiaosheng Wu 

Register(Including error check and error message):


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


13.Describe the final division of labor and how did you manage teamwork.
	Zihao Zhou ---  UI design, frontend decoration, user interaction function, project proposal
	Xiaohao Wang      --- backend setup, movie recommendation function, movie page setup
	Xiaosheng Wu      --- web crawler,frontpage decoration, database setup, youtube video
