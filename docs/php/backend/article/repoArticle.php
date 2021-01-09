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
            $tmp = new Article($article["Id"], $article["Title"], $article["ArticleTextContent"], $article["Summary"], $article["Image"]);
            array_push($result, $tmp);
        }
        return $result;
    }

    public function findArticleById($articleId)
    {
        $query = "SELECT * FROM Articles WHERE Id = ?";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $articleId);
        $result = $this->conn->executePreparedQuery($stmt);
        if(mysqli_num_rows($result) === 0){
            return false;
        }
        else {
            $result = mysqli_fetch_assoc($result);
            return new Article($result["Id"], $result["Title"], $result["ArticleTextContent"], $result["Summary"], $result["Image"]);
        }
    }

    public function findArticleByTitle($articleTitle)
    {
        $query = "SELECT * FROM Articles WHERE Title = ?";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $articleTitle);
        $result = $this->conn->executePreparedQuery($stmt);
        if(mysqli_num_rows($result) === 0) {
            return false;
        }
        else{
            $result = mysqli_fetch_assoc($result);
            return new Article($result["Id"], $result["Title"], $result["ArticleTextContent"], $result["Summary"], $result["Image"]);
        }
    }

    public function addArticle($title, $content, $summary, $image)
    {
        $query = "INSERT INTO Articles (Title, ArticleTextContent, Summary, Image, InsertDate) VALUES (?, ?, ?, ?, CURDATE())";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "sssi", $title, $content, $summary, $image); 
        return $this->conn->executePreparedQueryDML($stmt);
    }

    public function deleteArticle($articleId)
    {
        $query = "DELETE FROM Articles WHERE Id = ?";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $articleId); 
        return $this->conn->executePreparedQueryDML($stmt);
    }

    public function editArticle($article)
    {
        $query = "UPDATE Articles SET Title = ?, ArticleTextContent = ?, Summary = ?, Image = ? WHERE Id = ?";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "ssssi", $article->title, $article->content, $article->summary, $article->image, $article->id); 
        return $this->conn->executePreparedQueryDML($stmt);
    }

    public function checkDouble($titolo){
        return false !== $this->findArticleByTitle($titolo);
    }

    public function disconnect()
    {
        $this->conn->disconnect();
    }

}

?>
