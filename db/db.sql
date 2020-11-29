-- ****************** SqlDBM: MySQL ******************;
-- ***************************************************;

DROP TABLE IF EXISTS `Amministrators`;
DROP TABLE IF EXISTS `ArticleImages`;
DROP TABLE IF EXISTS `Articles`;
DROP TABLE IF EXISTS `ArticlesWriting`;
DROP TABLE IF EXISTS `GuitarDetails`;
DROP TABLE IF EXISTS `GuitarImages`;
DROP TABLE IF EXISTS `Guitars`;
DROP TABLE IF EXISTS `GuitarsModifi`;
DROP TABLE IF EXISTS `Images`;



DROP SCHEMA IF EXISTS `TecWebProject`;

CREATE SCHEMA IF NOT EXISTS `TecWebProject`;


-- ************************************** `Amministrators`

CREATE TABLE IF NOT EXISTS `Amministrators`
(
 `Id`       int ai NOT NULL ,
 `Username` varchar(40) NOT NULL ,
 `Email`    varchar(40) NULL ,
 `Password` varchar(64) NOT NULL ,

PRIMARY KEY (`Id`),
UNIQUE KEY `UsernameUnique` (`Username`)
);

-- ************************************** `ArticleImages`

CREATE TABLE IF NOT EXISTS `ArticleImages`
(
 `IdImages`  int ai NOT NULL ,
 `IdArticle` int ai NOT NULL ,

PRIMARY KEY (`IdImages`, `IdArticle`),
KEY `fkIdx_158` (`IdImages`),
CONSTRAINT `FK_158` FOREIGN KEY `fkIdx_158` (`IdImages`) REFERENCES `Images` (`Id`),
KEY `fkIdx_162` (`IdArticle`),
CONSTRAINT `FK_162` FOREIGN KEY `fkIdx_162` (`IdArticle`) REFERENCES `Articles` (`Id`)
);


-- ************************************** `Articles`

CREATE TABLE IF NOT EXISTS `Articles`
(
 `Id`                 int ai NOT NULL ,
 `Title`              tinytext NOT NULL ,
 `ArticleTextContent` longtext NOT NULL ,
 `InsertDate`         datetime NOT NULL ,

PRIMARY KEY (`Id`),
UNIQUE KEY `ArticleTitleUnique` (`Title`)
);



-- ************************************** `ArticlesWriting`

CREATE TABLE IF NOT EXISTS `ArticlesWriting`
(
 `IdArticle`       int ai NOT NULL ,
 `IdAmministrator` int ai NOT NULL ,
 `ModifiDate`      datetime NOT NULL ,
 `CommentChanges`  text NULL ,

PRIMARY KEY (`IdArticle`, `IdAmministrator`, `ModifiDate`),
KEY `fkIdx_142` (`IdArticle`),
CONSTRAINT `FK_142` FOREIGN KEY `fkIdx_142` (`IdArticle`) REFERENCES `Articles` (`Id`),
KEY `fkIdx_213` (`IdAmministrator`),
CONSTRAINT `FK_213` FOREIGN KEY `fkIdx_213` (`IdAmministrator`) REFERENCES `Amministrators` (`Id`)
);



-- ************************************** `GuitarDetails`

CREATE TABLE IF NOT EXISTS `GuitarDetails`
(
 `IdGuitars`  int ai NOT NULL ,
 `Decription` text NOT NULL ,
 `Name`       varchar(40) NOT NULL ,

PRIMARY KEY (`IdGuitars`, `Name`),
KEY `fkIdx_190` (`IdGuitars`),
CONSTRAINT `FK_190` FOREIGN KEY `fkIdx_190` (`IdGuitars`) REFERENCES `Guitars` (`Id`)
);


-- ************************************** `GuitarImages`

CREATE TABLE IF NOT EXISTS `GuitarImages`
(
 `IdGuitar` int ai NOT NULL ,
 `IdImage`  int ai NOT NULL ,

PRIMARY KEY (`IdGuitar`, `IdImage`),
KEY `fkIdx_171` (`IdGuitar`),
CONSTRAINT `FK_171` FOREIGN KEY `fkIdx_171` (`IdGuitar`) REFERENCES `Guitars` (`Id`),
KEY `fkIdx_175` (`IdImage`),
CONSTRAINT `FK_175` FOREIGN KEY `fkIdx_175` (`IdImage`) REFERENCES `Images` (`Id`)
);


-- ************************************** `Guitars`

CREATE TABLE IF NOT EXISTS `Guitars`
(
 `Id`         int ai NOT NULL ,
 `Name`       varchar(40) NOT NULL ,
 `BasePrize`  double precision NULL ,
 `InsertDate` datetime NOT NULL ,

PRIMARY KEY (`Id`)
);



-- ************************************** `GuitarsModifi`

CREATE TABLE IF NOT EXISTS `GuitarsModifi`
(
 `IdGuitar`        int ai NOT NULL ,
 `IdAmministrator` int ai NOT NULL ,
 `ModifiDate`      datetime NOT NULL ,
 `CommentChanges`  text NULL ,

PRIMARY KEY (`IdGuitar`, `IdAmministrator`, `ModifiDate`),
KEY `fkIdx_183` (`IdGuitar`),
CONSTRAINT `FK_183` FOREIGN KEY `fkIdx_183` (`IdGuitar`) REFERENCES `Guitars` (`Id`),
KEY `fkIdx_216` (`IdAmministrator`),
CONSTRAINT `FK_216` FOREIGN KEY `fkIdx_216` (`IdAmministrator`) REFERENCES `Amministrators` (`Id`)
);



-- ************************************** `Images`

CREATE TABLE IF NOT EXISTS `Images`
(
 `Id`   int ai NOT NULL ,
 `Name` varchar(64) NOT NULL ,
 `URL`  varchar(128) NOT NULL ,

PRIMARY KEY (`Id`)
);

