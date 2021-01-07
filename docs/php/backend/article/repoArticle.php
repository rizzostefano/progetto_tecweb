<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . "article.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "dbConnection.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "image" . DIRECTORY_SEPARATOR . "image.php";

class RepoArticle{

    private $conn;

    public function __construct() {
        $this->conn = new DbConnection();
    }

    /**
     * esegue il fetch di tutte le chitarre con tutti i loro dettagli dal db
     * ritornando un array con oggetti di tipo Guitars
     */
    public function getArticles()
    {
        $query = "SELECT * FROM Articles ORDER BY InsertDate";
        $stmt = $this->conn->prepareQuery($query);
        $result = $this->conn->executePreparedQuery($stmt);
        $articles = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $result = array();
        foreach ($articles as $article)
        {
            $tmp = new Article($article["Id"], $article["Title"], $article["ArticleTextContent"], $article["Summary"], $article["InsertDate"]);
            array_push($result, $tmp);
        }
        return $result;
    }

    public function findArticleById($articleId)
    {
        $query = "SELECT * FROM Articles WHERE Id = ?;";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $articleId);
        $result = $this->conn->executePreparedQuery($stmt);
        $result = mysqli_fetch_assoc($result);
        return new Article($result["Id"], $result["Title"], $result["ArticleTextContent"], $result["Summary"], $result["InsertDate"]);
    }

    public function findArticleByTitle($articleTitle)
    {
        $query = "SELECT * FROM Articles WHERE Title = ?;";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $articleTitle);
        $result = $this->conn->executePreparedQuery($stmt);
        $result = mysqli_fetch_assoc($result);
        return new Article($result["Id"], $result["Title"], $result["ArticleTextContent"], $result["Summary"], $result["InsertDate"]);
    }

    public function getArticleImages($articleId)
    {
        $query = "select * from ArticlesImages as ai join Images as i on ai.IdImage = i.Id where ai.IdArticle = ?";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $articleId);
        $result = $this->conn->executePreparedQuery($stmt);
        $images = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $result = array();
        foreach ($images as $image)
        {
            $tmp = new Image($image["Id"], $image["Name"], $image["Alt"], $image["Url"]);
            array_push($result, $tmp);
        }
        return $result;
    }

    public function addArticle($title, $content, $summary, $insertDate, $image)
    {
        $query = "INSERT INTO Article (Title, ArticleTextContent, Summary, InsertDate, Image) VALUES (?, ?, ?, ?, ?);";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "ssss", $title, $content, $summary, $insertDate, $image); 
        return $result = $this->conn->executePreparedQuery($stmt);
        /*if($result === true) // controlla l'esito della query
        {
            return mysqli_insert_id($this->conn);
        }
        else
        {
            return false;
        }*/
    }

    public function deleteArticle($articleId)
    {
        $query = "DELETE FROM Articles WHERE Id = ?;";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $articleId); 
        return $this->conn->executePreparedQuery($stmt);
    }

    public function editArticle($article)
    {
        $query = "UPDATE Articles SET Title = ?, ArticleTextContent = ? WHERE Id = ?";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "sss", $article->title, $article->content, $article->id); 
        return $this->conn->executePreparedQuery($stmt);
    }

    public function disconnect()
    {
        $this->conn->disconnect();
    }

}

?>
