CREATE TABLE IF NOT EXISTS users_archive (
    userID BIGINT(22) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    rank INT(1) DEFAULT 0 NOT NULL,
    username VARCHAR(24) NOT NULL,
    email VARCHAR(128) NOT NULL,
    password VARCHAR(128) NOT NULL,
    image text(30000) DEFAULT "/web_images/defaults/default_pfp.jpg" NOT NULL,
    banner text(30000) DEFAULT "/web_images/defaults/banner_default.png" NOT NULL,
    seeds BIGINT(44) DEFAULT 5 NOT NULL,
    posts BIGINT(44) DEFAULT 0 NOT NULL,
    replies BIGINT(44) DEFAULT 0 NOT NULL,
    questions BIGINT(44) DEFAULT 0 NOT NULL,
    answers BIGINT(44) DEFAULT 0 NOT NULL,
    joined VARCHAR(128) NOT NULL,
    time VARCHAR(128) NOT NULL
);