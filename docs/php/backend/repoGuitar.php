<?php
require_once('dbConnection.php');

class RepoGuitar{
    private $conn;

    public function __construct($DbConnection) {
        $conn = $DbConnection;
    }

    /**
     * esegue il fetch di tutte le chitarre con tutti i loro dettagli dal db
     * ritornando un array con oggetti di tipo Guitars (costruiti con GuitarBuilder)
     */
    public function getGuitars() {
        
    }
}

?>