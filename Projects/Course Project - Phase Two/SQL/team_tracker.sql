/*
    team_tracker.sql
    Creates the players table and the users table.
    The users table is new in Phase 2 and stores registered user accounts.
    I learned how to do this from one of the other courses i'm in (COMP 2003 - Relational Database)
*/

-- Players table
CREATE TABLE IF NOT EXISTS players (
    id          INT AUTO_INCREMENT PRIMARY KEY,  
    first_name  VARCHAR(100)  NOT NULL,           
    last_name   VARCHAR(100)  NOT NULL,           
    position    VARCHAR(50)   NOT NULL,           
    phone       VARCHAR(20)   NOT NULL,           
    email       VARCHAR(150)  NOT NULL UNIQUE,
    team_name   VARCHAR(100)  NOT NULL,
    photo       VARCHAR(255)  DEFAULT NULL,        
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP  
);

-- Users table
-- Stores registered user accounts for login and authentication
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(100) NOT NULL UNIQUE,       
    email      VARCHAR(150) NOT NULL UNIQUE,      
    password   VARCHAR(255) NOT NULL,            
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
