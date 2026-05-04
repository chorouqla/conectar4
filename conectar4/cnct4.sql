CREATE DATABASE cnct4;
USE cnct4;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
     username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_code VARCHAR(10) UNIQUE NOT NULL,
     player1_name VARCHAR(50) NOT NULL,
    player2_name VARCHAR(50) NULL,
    board VARCHAR(255) DEFAULT '000000000000000000000000000000000000000000',
    turn INT DEFAULT 1,
    winner INT  NULL,
    status VARCHAR(20) DEFAULT 'waiting'
);

CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    user_id INT NOT NULL,
    message VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);



-- UPDATE users
-- SET avatar = ELT(1 + FLOOR(RAND()*10),
-- 'fox','owl','wolf','deer','tiger',
-- 'panda','otter','lynx','hare','falcon')
-- WHERE avatar IS NULL OR avatar = '';