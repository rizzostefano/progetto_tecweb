<?php

if(isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    file_get_contents("admin-nuovo-articolo.html");
}
else {
    header('Location: admin-login.html');
}

?>