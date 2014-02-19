#Play By Post

---
###Description:
This is a simple play by post forum for tabletop roleplaying games.
This is primarily meant to be a fun way for me to learn PHP and MySQL.  

To create the tables necessary for the database run
the create_database_tables.sql script in the setup folder.  

To clear the database of existing tables (so that the tables can be updated)
run the drop_database_tables.sql script in the setup folder.  

To update a single table `SET FOREIGN_KEY_CHECKS=0;` and then drop the table
that needs to be updated and run the create_database_tables.sql script.

---
###Goals:
####Completed:
* Log in system
* Basic look for website
* Creating an account
* Database structure for users, characters, and games
* Page for list of games
* Page for list of characters


####Short Term Plans:
* Implement forum
* Create special tag to show text to only particular people in post
* Create special tag for d20 die roll

####Long Term Plans:
* Creating games
* Creating characters
* Editing characters
* Page for user profile
