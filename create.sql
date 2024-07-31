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

SELECT * FROM users;