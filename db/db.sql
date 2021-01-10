-- ****************** SqlDBM: MySQL ******************;
-- ***************************************************;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS Administrators;
DROP TABLE IF EXISTS Articles;
DROP TABLE IF EXISTS Images;

-- ************************************** Administrators

CREATE TABLE IF NOT EXISTS Administrators
(
    Id       int PRIMARY KEY AUTO_INCREMENT,
    Username varchar(40) NOT NULL UNIQUE,
    Email    varchar(40) NOT NULL UNIQUE,
    Password varchar(64) NOT NULL
);

-- ************************************** Images

CREATE TABLE IF NOT EXISTS Images
(
    Id   int PRIMARY KEY AUTO_INCREMENT,
    FileName varchar(64) NOT NULL UNIQUE,
    Alt text,
    Url  varchar(128) NOT NULL UNIQUE
);

-- ************************************** Articles

CREATE TABLE IF NOT EXISTS Articles
(
    Id                 int PRIMARY KEY AUTO_INCREMENT,
    Title              text NOT NULL UNIQUE,
    ArticleTextContent text NOT NULL,
    Summary            text NOT NULL,
    InsertDate         datetime NOT NULL,
    Image              int,
    Keywords           text,

    FOREIGN KEY (Image) REFERENCES Images(Id)
 );

INSERT INTO Administrators(Username, Email, Password) 
VALUES ("admin", "admin@mail.com", "8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918");

SET FOREIGN_KEY_CHECKS=1;