<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . "guitar.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "dbConnection.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "image" . DIRECTORY_SEPARATOR . "image.php";


class RepoGuitar{
    private $conn;

    public function __construct() {
        $this->conn = new DbConnection();
    }

    /**
     * esegue il fetch di tutte le chitarre con tutti i loro dettagli dal db
     * ritornando un array con oggetti di tipo Guitars
     */
    public function getGuitars()
    {
        $query = "SELECT * FROM Guitars;";
        $stmt = $this->conn->prepareQuery($query);
        $result = $this->conn->executePreparedQuery($stmt);
        $guitars = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $result = array();
        foreach ($guitars as $guitar)
        {
            $tmp = new Guitar($guitar["Id"], $guitar["Name"], $guitar["BasePrize"], $guitar["Summary"], $guitar["InsertDate"], null, null, $guitar["CoverImage"]);
            array_push($result, $tmp);
        }
        return $result;
    }

    public function getGuitarWithDetails($guitarId)
    {
        $query = "SELECT * FROM Guitars JOIN GuitarsDetails WHERE Id = ?;";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $guitarId);
        $result = $this->conn->executePreparedQuery($stmt);
        $queryResult = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $guitar = new Guitar($queryResult[0]["Id"], $queryResult[0]["Name"], $queryResult[0]["BasePrize"], $queryResult[0]["Summary"], $queryResult[0]["InsertDate"], null, null, $queryResult[0]["CoverImage"]);
        $details = array(); 
        foreach($queryResult as $detail)
        {
            if($detail["Name"] === "Descrizione") 
            {
                $guitar->text = $detail["Description"];
            }
            else
            {
                $tmp = array($detail["Name"] => $detail["Description"]);
                array_push($details, $tmp);
            }
        }
        $guitar->details = $details;
        return $guitar;
    }

    public function getArticleImages($articleId)
    {
        $query = "SELECT * from GuitarsImages AS gi JOIN Images AS i ON gi.IdImage = i.Id WHERE gi.IdGuitar = ?";
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

    public function findGuitarById($guitarId)
    {
        $query = "SELECT * FROM Guitars WHERE Id = ?;";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $guitarId);
        $result = $this->conn->executePreparedQuery($stmt);
        $result = mysqli_fetch_assoc($result);
        return new Guitar($result["Id"], $result["Name"], $result["BasePrize"], $result["Summary"], $result["InsertDate"], null, null, $result["CoverImage"]);
    }

    public function findGuitarByName($guitarName)
    {
        $query = "SELECT * FROM Guitars WHERE Name = ?;";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $guitarName);
        $result = $this->conn->executePreparedQuery($stmt);
        $result = mysqli_fetch_assoc($result);
        return new Guitar($result["Id"], $result["Name"], $result["BasePrize"], $result["Summary"], $result["InsertDate"], null, null, $result["CoverImage"]);
    }

    public function addGuitar($guitar)
    {
        $query = "INSERT INTO Guitars (Name, BasePrize, Summary, InsertDate) VALUES (?, ?, ?, ?);";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "ssss", $guitar->name, $guitar->basePrice, $guitar->summary, $guitar->insertDate); 
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
    
    public function addDetail($id, $name, $description) 
    {
        $query = "INSERT INTO GuitarsDetails (IdGuitar, Name, Description) VALUES (?, ?, ?);";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "sss", $id, $name, $description);
        return $this->conn->executePreparedQuery($stmt);
    }

    // TODO: modifica chitarre -> rimozione dettaglio singolo

    public function deleteGuitar($guitarId)
    {
        $query = "DELETE FROM Guitars WHERE Id = ?;";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $guitarId); 
        return $this->conn->executePreparedQuery($stmt);
    }

    public function addGuitarsImage($guitarId, $imageId)
    {
        $query = "INSERT INTO GuitarImages (IdGuitar, IdImage) VALUES (?, ?);";
        $stmt = $this->conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "ss", $guitarId, $imageId); 
        return $this->conn->executePreparedQuery($stmt);
    }

    public function disconnect()
    {
        $this->conn->disconnect();
    }

}

?>
