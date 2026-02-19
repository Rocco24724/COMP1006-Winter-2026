/*

Creating SQL script that stores all the players in on the team with the following info (First Name, Last Name, Position, Phone Number, Email, and the date they were added to the team).
I learned how to do this from one of the other courses i'm in (COMP 2003 - Relational Database)

*/
CREATE TABLE IF NOT EXISTS players (
    id          INT AUTO_INCREMENT PRIMARY KEY,  
    first_name  VARCHAR(100)  NOT NULL,           
    last_name   VARCHAR(100)  NOT NULL,           
    position    VARCHAR(50)   NOT NULL,           
    phone       VARCHAR(20)   NOT NULL,           
    email       VARCHAR(150)  NOT NULL UNIQUE,
    team_name   VARCHAR(100)  NOT NULL,               
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP  
)