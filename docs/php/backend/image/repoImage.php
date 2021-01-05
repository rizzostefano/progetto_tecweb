<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "dbConnection.php";

class RepoImage{

    private $conn;
    private $path = "../../../assets/images/";
    
    public function __construct() {
        $this->conn = new DbConnection();
    }

    /**
     * esegue il fetch di tutte le chitarre con tutti i loro dettagli dal db
     * ritornando un array con oggetti di tipo Guitars
     */
    public function getImages()
    {
        $query = "SELECT * FROM Images";
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
        $query = "SELECT * FROM Images WHERE Id = ?";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $imageId);
        $result = $this->conn->executePreparedQuery($stmt);
        if(mysqli_num_rows($result) === 0) {
            return false;
        } else{
            $result = mysqli_fetch_assoc($result);
            return new Image($result["Id"], $result["FileName"], $result["Alt"], $result["Url"]);
        }
    }

    public function findImageByName($imageName)
    {
        $query = "SELECT * FROM Images WHERE FileName = ?";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $imageName);
        $result = $this->conn->executePreparedQuery($stmt);
        if(mysqli_num_rows($result) === 0) {
            return false;
        } else{
            $result = mysqli_fetch_assoc($result);
            return new Image($result["Id"], $result["FileName"], $result["Alt"], $result["Url"]);
        }
    }

    public function addImage($fileImage, $alt)
    {
        $filePath = $this->path . $fileImage['name'];
        $query = "INSERT INTO Images (FileName, Alt, Url) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "sss", $fileImage["name"], $alt, $filePath); 
        $result = $this->conn->executePreparedQueryDML($stmt);
        if($result === true) {
            $resultMove = move_uploaded_file($fileImage["tmp_name"], $filePath);
            return true;
        } else {
            return false;
        }
    }

    public function checkDouble($imageName) {
        return false !== $this->findImageByName($imageName);
    }

    public function deleteImage($imageId)
    {
        $query = "DELETE FROM Images WHERE Id = ?";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $imageId); 
        return $this->conn->executePreparedQueryDML($stmt);
    }

    public function addArticleImage($articleId, $imageId)
    {
        $query = "INSERT INTO ArticleImages (IdArticle, IdImage) VALUES (?, ?)";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "ss", $articleId, $imageId); 
        return $this->conn->executePreparedQueryDML($stmt);
    }

    public function disconnect()
    {
        $this->conn->disconnect();
    }

}

?>