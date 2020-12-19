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
    public function getGuitars()
    {
        $query = "SELECT * FROM Guitars;";
        $stmt = $conn->prepareQuery($query);
        $result = $conn->executePreparedQuery($stmt);
        $guitars = mysql_fetch_all($result, MYSQLI_ASSOC);
        $result = array();
        foreach ($guitars as $guitar)
        {
            $tmp = new Guitar($guitar["Id"], $guitar["Name"], $guitar["BasePrize"], $guitar["Summary"], $guitar["InsertDate"], null, null);
            array_push($result, $tmp);
        }
        $conn->disconnect();
        return $result;
    }

    public function getGuitarWithDetails($guitarId)
    {
        $query = "SELECT * FROM Guitars JOIN GuitarsDetails WHERE Id = ?;";
        $stmt = $conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $guitarId);
        $result = $conn->executePreparedQuery($stmt);
        $queryResult = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $guitar = new Guitar($details[0]["Id"], $details[0]["Name"], $details[0]["BasePrize"], $details[0]["Summary"], $details[0]["InsertDate"], null, null);
        $details = array(); 
        foreach($queryResult as $detail)
        {
            if($detail["Name"] === "Descrizione") 
            {
                $guitar->text = $detail["Description"];
            }
            else
            {
                array_push($details, $detail["Name"] => $detail["Description"]);
            }
        }
        $guitar->details = $details;
        $conn->disconnect();
        return $guitar;
    }

    public function findGuitarById($guitarId)
    {
        $query = "SELECT * FROM Guitars WHERE Id = ?;";
        $stmt = $conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $guitarId);
        $result = $conn->executePreparedQuery($stmt);
        $result = mysqli_fetch_assoc($result);
        $conn->disconnect();
        return new Guitar($result["Id"], $result["Name"], $result["BasePrize"], $result["Summary"], $guitar["InsertDate"], null, null);
    }

    public function findGuitarByName($guitarName)
    {
        $query = "SELECT * FROM Guitars WHERE Name = ?;";
        $stmt = $conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $guitarName);
        $result = $conn->executePreparedQuery($stmt);
        $result = mysqli_fetch_assoc($result);
        $conn->disconnect();
        return new Guitar($result["Id"], $result["Name"], $result["BasePrize"], $result["Summary"], $guitar["InsertDate"], null, null);
    }

    public function addGuitar($guitar)
    {
        $query = "INSERT INTO Guitars (Name, BasePrize, Summary, InsertDate) VALUES (?, ?, ?, ?);";
        $stmt = $conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "ssss", $guitar->name, $guitar->basePrice, $guitar->summary, $guitar->insertDate); 
        $result = $conn->executePreparedQuery($stmt);
        if($result === true) // CONTROLLA
        {
            $insertedGuitar = findGuitarByName($guitars->name);
            $conn->disconnect();
            return $insertedGuitar["Id"];
        }
        else
        {
            $conn->disconnect();
            return false;
        }
        
    }
    
    public function addDetail($id, $name, $description) 
    {
        $query = "INSERT INTO GuitarsDetails (IdGuitar, Name, Description) VALUES (?, ?, ?);";
        $stmt = $conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "sss", $id, $name, $description);
        return $conn->executePreparedQuery($stmt);
    }

    // TODO: modifica chitarre -> rimozione dettaglio singolo

    public function deleteGuitar($guitarId)
    {
        $query = "DELETE FROM Guitars WHERE Id = ?;";
        $stmt = $conn->prepareQuery($query);
        mysqli_stmt_bind_param($stmt, "s", $guitarId); 
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