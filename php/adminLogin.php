<?php
require_once("backend/dbConnection.php");

$conn = new DbConnection();
$html = file_get_contents("../admin/admin-login.html");
if(isset($_POST['submit'])){
    $username = $_POST['username-admin'];
    $query = "SELECT * FROM Administrators WHERE Username=?";
    $stmt = $conn->prepareQuery($query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    $result = $conn->executePreparedQuery($stmt);
    if(mysqli_num_rows($result) === 1) {
        $result = mysqli_fetch_assoc($result);
        if(strcmp(hash('sha256', $_POST['password-admin']), $result['Password']) === 0){
            ini_set('session.gc_maxlifetime', 3600);
            session_set_cookie_params(3600);
            session_start();
            $_SESSION['admin'] = true;
            header('Location: insertForm.php?new=1');
        }
    } else {
        echo str_replace("%error-login%", "<strong class=\"error\" role=\"alert\"> Credenziali non valide</strong>", $html);
    }

} else {
    echo str_replace("%error-login%", "", $html);
    // TODO: errore apertura connessione
}
?>
