<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . "dbConnection.php";

class RepoImage{

    private $conn;
    private $path = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "assets" . 
                    DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "uploaded" . DIRECTORY_SEPARATOR;
    
    public function __construct() {
        $this->conn = new DbConnection();
    }

    /**
     * @summary Ritorna l'ultimo errore loggato avvenuto nella connessione al db. 
     * @return stringa contenente una descrizione dell'errore avvenuto
     */
    public function getConnectionLastError()
    {
        return $this->conn->getLastError();
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
            move_uploaded_file($fileImage["tmp_name"], $filePath);
            return new Image($this->conn->getInsertID(), $fileImage["name"], $alt, $filePath);
        } else {
            return false;
        }
    }

    public function editAltImage($id, $alt) {
        $query = "UPDATE Images SET Alt = ? WHERE Id = ?";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "ss", $alt, $id);
        return $this->conn->executePreparedQuery($stmt);
    }

    public function checkDouble($imageName) {
        return false !== $this->findImageByName($imageName);
    }

    public function deleteImage($imageId)
    {
        $image = $this->findImageById($imageId);
        unlink($image->url);
        $query = "DELETE FROM Images WHERE Id = ?";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $imageId); 
        return $this->conn->executePreparedQueryDML($stmt);
    }

    public function disconnect()
    {
        $this->conn->disconnect();
    }

}

?>
