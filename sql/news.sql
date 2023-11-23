CREATE TABLE IF NOT EXISTS news (
    newsID BIGINT(44) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    mainTitle varchar(24) NOT NULL,
    topics text(30000) NOT NULL,
    topicSubjects text(100000) NOT NULL,
    datetime varchar(256) NOT NULL
);