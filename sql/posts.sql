CREATE TABLE posts (
    postID BIGINT(44) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    userID BIGINT(44) NOT NULL,
    type INT(1) NOT NULL,
    title VARCHAR(128) NOT NULL,
    text TEXT(30000) NOT NULL,
    imageArray TEXT(150000),
    views BIGINT(44) DEFAULT 0 NOT NULL,
    upvotes BIGINT(44) DEFAULT 0 NOT NULL,
    downvotes BIGINT(44) DEFAULT 0 NOT NULL,
    datetime VARCHAR(256) NOT NULL,
    tagOne VARCHAR(50),
    tagTwo VARCHAR(50),
    tagThree VARCHAR(50),
    tagFour VARCHAR(50),
    tagFive VARCHAR(50)
);