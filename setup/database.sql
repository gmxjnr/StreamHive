CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    username VARCHAR(50)
);

CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    filename VARCHAR(255) NOT NULL,
    views INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    video_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    FOREIGN KEY (video_id)
    REFERENCES videos(id)
    ON DELETE CASCADE
);

CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    video_id INT NULL,
    comment_id INT NULL,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    FOREIGN KEY (video_id)
    REFERENCES videos(id)
    ON DELETE CASCADE,

    FOREIGN KEY (comment_id)
    REFERENCES comments(id)
    ON DELETE CASCADE
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE video_category (
    video_id INT NOT NULL,
    category_id INT NOT NULL,

    PRIMARY KEY (video_id, category_id),

    FOREIGN KEY (video_id)
    REFERENCES videos(id)
    ON DELETE CASCADE,

    FOREIGN KEY (category_id)
    REFERENCES categories(id)
    ON DELETE CASCADE
);

CREATE TABLE password_reset (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);



-- =========================
-- DUMMY USERS
-- =========================

INSERT INTO users (email, password, role, created_at, username)
VALUES
('admin@example.com', 'hashedpassword1', 'admin', NOW(), 'adminUser'),
('john@example.com', 'hashedpassword2', 'user', NOW(), 'johnDoe'),
('emma@example.com', 'hashedpassword3', 'user', NOW(), 'emmaSmith'),
('lucas@example.com', 'hashedpassword4', 'user', NOW(), 'lucasGamer'),
('sophie@example.com', 'hashedpassword5', 'user', NOW(), 'sophieDev');



-- =========================
-- DUMMY CATEGORIES
-- =========================

INSERT INTO categories (name)
VALUES
('Gaming'),
('Programming'),
('Tutorial'),
('Music'),
('Technology');



-- =========================
-- DUMMY VIDEOS
-- =========================

INSERT INTO videos
(user_id, title, description, filename, views, created_at)
VALUES
(2, 'Minecraft Survival Ep1', 'Starting a new survival world', 'minecraft1.mp4', 120, NOW()),
(3, 'Learn SQL Basics', 'Beginner SQL tutorial', 'sql_tutorial.mp4', 340, NOW()),
(4, 'Top 10 Gaming Keyboards', 'Reviewing gaming keyboards', 'keyboards.mp4', 89, NOW()),
(5, 'How to build APIs', 'REST API development guide', 'api_guide.mp4', 210, NOW()),
(2, 'Funny Gaming Moments', 'Best funny clips compilation', 'funnymoments.mp4', 500, NOW());



-- =========================
-- VIDEO CATEGORY LINKS
-- =========================

INSERT INTO video_category (video_id, category_id)
VALUES
(1, 1),
(2, 2),
(2, 3),
(3, 1),
(3, 5),
(4, 2),
(4, 3),
(5, 1);



-- =========================
-- DUMMY COMMENTS
-- =========================

INSERT INTO comments
(user_id, video_id, content, created_at)
VALUES
(3, 1, 'Nice video bro!', NOW()),
(4, 1, 'Can’t wait for episode 2!', NOW()),
(5, 2, 'This SQL explanation helped a lot.', NOW()),
(2, 4, 'Very useful API tutorial.', NOW()),
(3, 5, 'Hahaha this was funny 😂', NOW());



-- =========================
-- DUMMY LIKES
-- =========================

INSERT INTO likes
(user_id, video_id, comment_id)
VALUES
(3, 1, NULL),
(4, 1, NULL),
(5, 2, NULL),
(2, 4, NULL),
(3, 5, NULL),

(2, NULL, 1),
(4, NULL, 3),
(5, NULL, 5);



-- =========================
-- DUMMY PASSWORD RESETS
-- =========================

INSERT INTO password_reset
(user_id, token, expires_at)
VALUES
(2, 'reset_token_123', DATE_ADD(NOW(), INTERVAL 1 DAY)),
(3, 'reset_token_456', DATE_ADD(NOW(), INTERVAL 1 DAY));