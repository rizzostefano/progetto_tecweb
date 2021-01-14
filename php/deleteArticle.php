<?php

require_once("backend/article/repoArticle.php");
require_once("backend/image/repoImage.php");
require_once("backend/article/article.php");
session_start();
if(!(isset($_SESSION['admin']) && $_SESSION['admin'] === true)) {
	header('Location: adminLogin.php');
}

if(isset($_GET["article_id"])) {
    $repoArticle = new RepoArticle();
    $repoImage = new RepoImage();

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
