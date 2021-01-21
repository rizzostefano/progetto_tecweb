<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . "repoArticle.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "repoImage.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "article.php";
session_start();
if(!(isset($_SESSION['admin']) && $_SESSION['admin'] === true)) {
	header('Location: adminLogin.php');
}

if(isset($_GET["article_id"])) {
    $repoArticle = new RepoArticle();
    $repoImage = new RepoImage();

    if($repoArticle->getConnectionLastError() !== '' || $repoImage->getConnectionLastError() !== '')
    {
	    header('Location: ..' . DIRECTORY_SEPARATOR . '500.html');
    }

    $idArticolo = $_GET["article_id"];

    $articolo = $repoArticle->findArticleById($idArticolo);
    $idImmagine = $articolo->image;
    $repoArticle->deleteArticle($idArticolo);
    $repoImage->deleteImage($idImmagine);

    header('Location: editArticles.php');

    $repoArticle->disconnect();
    $repoImage->disconnect();
}


?>
