<?php
require_once('dbConnection.php');

class RepoGuitar{
    private $conn;

    public function __construct() {
        $conn = new DbConnection();
    }

    /**
     * esegue il fetch di tutte le chitarre con tutti i loro dettagli dal db
     * ritornando un array con oggetti di tipo Guitars
     */
    public function getImages()
    {
        $query = "SELECT * FROM Images;";
        $stmt = $conn->prepareQuery($query);
        $result = $conn->executePreparedQuery($stmt);
        $images = mysql_fetch_all($result, MYSQLI_ASSOC);
        $result = array();
        foreach ($images as $image)
        {
            $tmp = new Image($image["Id"], $image["FileName"], $image["Alt"], $image["Url"]);
            array_push($result, $tmp);
        }
        $conn->disconnect();
        return $result;
    }

    public function findImageById($imageId)
    {
        $query = "SELECT * FROM Images WHERE Id = ?;";
        $stmt = $conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $imageId);
        $result = $conn->executePreparedQuery($stmt);
        $result = mysqli_fetch_assoc($result);
        $conn->disconnect();
        return new Image($result["Id"], $result["FileName"], $result["Alt"], $result["Url"]);
    }

    public function findImageByName($imageName)
    {
        $query = "SELECT * FROM Images WHERE Name = ?;";
        $stmt = $conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $imageName);
        $result = $conn->executePreparedQuery($stmt);
        $result = mysqli_fetch_assoc($result);
        $conn->disconnect();
        return new Image($result["Id"], $result["Name"], $result["Alt"], $result["Url"]);
    }

    public function addImage($image)
    {
        $query = "INSERT INTO Images (FileName, Alt, Url) VALUES (?, ?, ?);";
        $stmt = $conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "sss", $image->name, $image->alt, $image->url); 
        $result = $conn->executePreparedQuery($stmt);
        if($result === true) // CONTROLLA
        {
            $insertedImage = findImageByName($image->name);
            $conn->disconnect();
            return $insertedImage["Id"];
        }
        else
        {
            $conn->disconnect();
            return false;
        }    
    }

    public function deleteImage($imageId)
    {
        $query = "DELETE FROM Images WHERE Id = ?;";
        $stmt = $conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $imageId); 
        $result = $conn->executePreparedQuery($stmt);
        return $result;
    }

    public function addGuitarsImage($guitarId, $imageId)
    {
        $query = "INSERT INTO GuitarImages (IdGuitar, IdImage) VALUES (?, ?);";
        $stmt = $conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "ss", $guitarId, $imageId); 
        $result = $conn->executePreparedQuery($stmt);
        return $result;
    }

}

?>