CREATE DATABASE IF NOT EXISTS sns
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  firstname VARCHAR(50) NOT NULL,
  lastname VARCHAR(50) NOT NULL,
  email VARCHAR(150) NOT NULL,
  password VARCHAR(150) NOT NULL,
  profile_picture VARCHAR(50),
  banner VARCHAR(50),
  is_active BOOLEAN,
  created_at DATETIME
);

CREATE TABLE posts (
  id INT PRIMARY KEY AUTO_INCREMENT,
  content TEXT NOT NULL,
  published_at DATETIME,
  user_id INT,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE TABLE `groups` (
  id INT PRIMARY KEY AUTO_INCREMENT,
  is_private BOOLEAN,
  created_at DATETIME
);


CREATE TABLE messages_group (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50),
  created_at DATETIME
);

CREATE TABLE messages (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_from_id INT,
  user_to_id INT,
  content TEXT,
  group_id INT,
  published_at DATETIME,
  FOREIGN KEY (group_id) REFERENCES messages_group (id) ON DELETE CASCADE,
  FOREIGN KEY (user_from_id) REFERENCES users (id) ON DELETE NO ACTION,
  FOREIGN KEY (user_to_id) REFERENCES users (id) ON DELETE NO ACTION
);

CREATE TABLE groups_posts (
  group_id INT,
  post_id INT,
  PRIMARY KEY (group_id, post_id),
  FOREIGN KEY (group_id) REFERENCES `groups` (id) ON DELETE CASCADE,
  FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE
);

CREATE TABLE comments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  content TEXT NOT NULL,
  published_at DATETIME
);

CREATE TABLE comments_posts_users (
  post_id INT,
  comment_id INT,
  user_id INT,
  PRIMARY KEY (post_id, comment_id, user_id),
  FOREIGN KEY (post_id) REFERENCES posts (id) ON DELETE CASCADE,
  FOREIGN KEY (comment_id) REFERENCES comments (id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE TABLE comments_users (
  comment_id INT,
  user_id INT, 
  PRIMARY KEY (comment_id, user_id),
  FOREIGN KEY (comment_id) REFERENCES comments (id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE TABLE reactions (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50),
  number INT
);

CREATE TABLE comments_reactions (
  comment_id INT,
  reaction_id INT, 
  PRIMARY KEY (comment_id, reaction_id),
  FOREIGN KEY (comment_id) REFERENCES comments (id) ON DELETE CASCADE,
  FOREIGN KEY (reaction_id) REFERENCES reactions (id) ON DELETE CASCADE
);

CREATE TABLE posts_reactions (
  post_id INT,
  reaction_id INT, 
  PRIMARY KEY (post_id, reaction_id),
  FOREIGN KEY (post_id) REFERENCES comments_posts_users (post_id) ON DELETE CASCADE,
  FOREIGN KEY (reaction_id) REFERENCES reactions (id) ON DELETE CASCADE
);


CREATE TABLE followers_users (
  follower_id INT,
  user_id INT,
  PRIMARY KEY (follower_id, user_id),
  FOREIGN KEY (follower_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE TABLE pages (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  description TEXT NOT NULL, 
  image VARCHAR(50),
  created_at DATETIME
);

CREATE TABLE admins_pages (
  admin_id INT,
  page_id INT,
  PRIMARY KEY (admin_id, page_id),
  FOREIGN KEY (admin_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (page_id) REFERENCES pages (id) ON DELETE CASCADE
);


CREATE TABLE admins_groups (
  admin_id INT,
  group_id INT,
  PRIMARY KEY (admin_id, group_id),
  FOREIGN KEY (admin_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (group_id) REFERENCES `groups` (id) ON DELETE CASCADE
);

CREATE TABLE groups_users (
  group_id INT,
  user_id INT,
  PRIMARY KEY (group_id, user_id),
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (group_id) REFERENCES `groups` (id) ON DELETE CASCADE
);

CREATE TABLE messages_groups_users (
  messages_group_id INT,
  user_id INT,
  PRIMARY KEY (messages_group_id, user_id),
  FOREIGN KEY (messages_group_id) REFERENCES messages_group (id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

CREATE TABLE followings_users (
  following_id INT,
  user_id INT,
  PRIMARY KEY (following_id, user_id),
  FOREIGN KEY (following_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);