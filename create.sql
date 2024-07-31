# CALLING DATABASE:
USE pacman;

# CREATING USERS TABLE:
CREATE TABLE users (
    username VARCHAR(15) NOT NULL PRIMARY KEY,
    password VARCHAR(30) NOT NULL,
    country VARCHAR(20) NOT NULL,
    highscore INT
);

# CREATING LEADERBOARD TABLE:
CREATE TABLE leaderboard (
    username VARCHAR(15) NOT NULL,
    highscore INT NOT NULL,
    FOREIGN KEY (username) REFERENCES users(username)
);

# INSERTING ADMIN LOGIN INTO DATABASE
INSERT INTO users(username, password, country) VALUES ("admin", "adminpassword", "adminoffice");

SELECT * FROM users;

SELECT * FROM leaderboard;