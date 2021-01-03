<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "dbConnection.php";

class RepoImage{

    private $conn;

    public function __construct() {
        $this->conn = new DbConnection();
    }

    /**
     * esegue il fetch di tutte le chitarre con tutti i loro dettagli dal db
     * ritornando un array con oggetti di tipo Guitars
     */
    public function getImages()
    {
        $query = "SELECT * FROM Images;";
        $stmt = $this->conn->prepareQuery($query);
        $result = $this->conn->executePreparedQuery($stmt);
        $images = mysql_fetch_all($result, MYSQLI_ASSOC);
        $result = array();
        foreach ($images as $image)
        {
            $tmp = new Image($image["Id"], $image["FileName"], $image["Alt"], $image["Url"]);
            array_push($result, $tmp);
        }
        return $result;
    }

    public function findImageById($imageId)
    {
        $query = "SELECT * FROM Images WHERE Id = ?;";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $imageId);
        $result = $this->conn->executePreparedQuery($stmt);
        $result = mysqli_fetch_assoc($result);
        return new Image($result["Id"], $result["FileName"], $result["Alt"], $result["Url"]);
    }

    public function findImageByName($imageName)
    {
        $query = "SELECT * FROM Images WHERE Name = ?;";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $imageName);
        $result = $this->conn->executePreparedQuery($stmt);
        $result = mysqli_fetch_assoc($result);
        return new Image($result["Id"], $result["Name"], $result["Alt"], $result["Url"]);
    }

    public function addImage($image)
    {
        $query = "INSERT INTO Images (FileName, Alt, Url) VALUES (?, ?, ?);";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "sss", $image->name, $image->alt, $image->url); 
        $result = $this->conn->executePreparedQuery($stmt);
        if($result === true) // CONTROLLA
        {
            return mysqli_insert_id($this->conn);
        }
        else
        {
            return false;
        }    
    }

    public function deleteImage($imageId)
    {
        $query = "DELETE FROM Images WHERE Id = ?;";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $imageId); 
        return $this->conn->executePreparedQuery($stmt);
    }

    public function addArticleImage($articleId, $imageId)
    {
        $query = "INSERT INTO ArticleImages (IdArticle, IdImage) VALUES (?, ?);";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "ss", $articleId, $imageId); 
        return $this->conn->executePreparedQuery($stmt);
    }

    public function disconnect()
    {
        $this->conn->disconnect();
    }

}

?>