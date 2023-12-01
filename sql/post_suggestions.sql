CREATE TABLE IF NOT EXISTS post_suggestions (
    suggestionID BIGINT(44) PRIMARY KEY AUTO_INCREMENT NOT NULL,
    suggestedByUserID BIGINT(44) NOT NULL,
    postType INT(2) NOT NULL,
    postID BIGINT(44) NOT NULL,
    newTitle VARCHAR(256) NOT NULL,
    newBody TEXT(30000) NOT NULL
);