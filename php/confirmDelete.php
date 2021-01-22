<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . "repoArticle.php";
session_start();

if(!(isset($_SESSION['admin']) && $_SESSION['admin'] === true)) {
	header('Location: adminLogin.php');
}

$html = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR .".." . DIRECTORY_SEPARATOR . "admin" . DIRECTORY_SEPARATOR . "admin-confirm-delete.html");

$articleId = $_GET["article_id"];
$limit = isset($_GET["limit"]) ? $_GET["limit"] : 5;
$repoArticle = new RepoArticle();

$article = $repoArticle->findArticleById($articleId);

$content = "<section><h1>Conferma eliminazione articolo</h1>".
                "<p>Stai per eliminare l'articolo con titolo: " . $article->title . "</p>" .
            "</section>".
            "<div class=\"btn-container\">".
                "<a href=\"deleteArticle.php?article_id={$articleId}limit={$limit}\" class=\"button\">Conferma</a>".
                "<a href=\"editArticles.php?limit={$limit}#article-anchor\" class=\"button\">Annulla</a>".
            "</div>";

$html = str_replace("<cs_main_content/>", $content, $html);
echo $html;
?>