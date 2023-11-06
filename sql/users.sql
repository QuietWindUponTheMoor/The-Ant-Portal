CREATE TABLE users (
    userID BIGINT(22) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    rank INT(1) DEFAULT 0 NOT NULL, -- 0 = user, 1 = moderator, 2 = administrator
    username VARCHAR(24) NOT NULL,
    email VARCHAR(128) NOT NULL,
    password VARCHAR(128) NOT NULL,
    image text(30000) DEFAULT "/web_images/defaults/default_pfp.jpg" NOT NULL, -- User's profile image
    seeds BIGINT(44) DEFAULT 5 NOT NULL, -- User gets 5 free seeds when signing up. Seeds are another word for rep (for example, Reddit Karma)
    posts BIGINT(44) DEFAULT 0 NOT NULL, -- Amount of general posts the user has, not including replies to other posts or answers to questions
    replies BIGINT(44) DEFAULT 0 NOT NULL, -- Amount of replies to general posts the user has posted
    questions BIGINT(44) DEFAULT 0 NOT NULL, -- Amount of questions the user has asked
    answers BIGINT(44) DEFAULT 0 NOT NULL, -- Amount of answers to questions the user has answered
    joined VARCHAR(128) NOT NULL,
    time VARCHAR(128) NOT NULL
);