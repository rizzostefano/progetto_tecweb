<?php
require_once("backend/dbConnection.php");

$conn = new DbConnection();

if(isset($_POST['submit'])){
    $username = $_POST['username-admin'];
    $query = "SELECT * FROM Administrators WHERE Username=?;";
    $stmt = $conn->prepareQuery($query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    $result = $conn->executePreparedQuery($stmt);
    $result = mysqli_fetch_assoc($result);    
    if($result->num_rows === 1 && $_POST['password-admin'] === $result['Password']) {
        ini_set('session.gc_maxlifetime', 3600);
        session_set_cookie_params(3600);
        session_start();
        $_SESSION['admin'] = true;
    }
    else{
        // TODO: errore utente admin non trovato
    }

} else {
    // TODO: errore apertura connessione
}





?>