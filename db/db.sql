-- ****************** SqlDBM: MySQL ******************;
-- ***************************************************;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS GuitarsModify;
DROP TABLE IF EXISTS ArticlesImages;
DROP TABLE IF EXISTS ArticlesModify;
DROP TABLE IF EXISTS Administrators;
DROP TABLE IF EXISTS GuitarsImages;
DROP TABLE IF EXISTS GuitarsDetails;
DROP TABLE IF EXISTS Articles;
DROP TABLE IF EXISTS Guitars;
DROP TABLE IF EXISTS Images;

-- ************************************** Administrators

CREATE TABLE IF NOT EXISTS Administrators
(
    Id       int PRIMARY KEY AUTO_INCREMENT,
    Username varchar(40) NOT NULL UNIQUE,
    Email    varchar(40) NOT NULL UNIQUE,
    Password varchar(64) NOT NULL
);

-- ************************************** Articles

CREATE TABLE IF NOT EXISTS Articles
(
    Id                 int PRIMARY KEY AUTO_INCREMENT,
    Title              text NOT NULL,
    ArticleTextContent text NOT NULL,
    InsertDate         datetime NOT NULL 
 );

 -- ************************************** Guitars

CREATE TABLE IF NOT EXISTS Guitars
(
    Id         int PRIMARY KEY AUTO_INCREMENT,
    Name       varchar(40) NOT NULL,
    BasePrize  double NULL,
    InsertDate datetime NOT NULL,
    Decription text NOT NULL
);

-- ************************************** Images

CREATE TABLE IF NOT EXISTS Images
(
    Id   int PRIMARY KEY AUTO_INCREMENT,
    FileName varchar(64) NOT NULL UNIQUE,
    Alt text,
    Url  varchar(128) NOT NULL UNIQUE
);

-- ************************************** GuitarsDetails

CREATE TABLE IF NOT EXISTS GuitarsDetails
(
    IdGuitar int NOT NULL,
    Name varchar(64) NOT NULL,
    Description text,

    PRIMARY KEY(IdGuitar, Name),
    FOREIGN KEY (IdGuitar) REFERENCES Guitars(Id) ON DELETE CASCADE
);

-- ************************************** ArticlesImages

CREATE TABLE IF NOT EXISTS ArticlesImages
(
    IdImage  int NOT NULL,
    IdArticle int NOT NULL,

    PRIMARY KEY (IdImage, IdArticle),
    FOREIGN KEY (IdImage) REFERENCES Images(Id) ON DELETE CASCADE,
    FOREIGN KEY (IdArticle) REFERENCES Articles(Id) ON DELETE CASCADE
);

-- ************************************** ArticlesModify

CREATE TABLE IF NOT EXISTS ArticlesModify
(
    IdArticle       int NOT NULL,
    IdAdministrator int NOT NULL,
    ModifyDate      datetime NOT NULL,
    CommentChanges  text,

    PRIMARY KEY (IdArticle, IdAdministrator, ModifyDate),
    FOREIGN KEY (IdArticle) REFERENCES Articles(Id) ON DELETE CASCADE,
    FOREIGN KEY (IdAdministrator) REFERENCES Administrators(Id) ON DELETE CASCADE
);


-- ************************************** GuitarImages

CREATE TABLE IF NOT EXISTS GuitarsImages
(
 IdGuitar int NOT NULL,
 IdImage  int NOT NULL,

 PRIMARY KEY (IdGuitar, IdImage),
 FOREIGN KEY (IdGuitar) REFERENCES Guitars(Id) ON DELETE CASCADE,
 FOREIGN KEY (IdImage) REFERENCES Images(Id) ON DELETE CASCADE
);


-- ************************************** GuitarsModify

CREATE TABLE IF NOT EXISTS GuitarsModify
(
 IdGuitar        int NOT NULL,
 IdAdministrator int NOT NULL,
 ModifyDate      datetime NOT NULL,
 CommentChanges  text NULL,

 PRIMARY KEY (IdGuitar, IdAdministrator, ModifyDate),
 FOREIGN KEY (IdGuitar) REFERENCES Guitars(Id) ON DELETE CASCADE,
 FOREIGN KEY (IdAdministrator) REFERENCES Administrators(Id) ON DELETE CASCADE
);

SET FOREIGN_KEY_CHECKS=1;
