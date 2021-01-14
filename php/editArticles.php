<?php
require_once("backend/escapeMarkdown.php");
require_once("backend/article/repoArticle.php");
require_once("backend/image/repoImage.php");
header('Content-type: text/html; charset=utf-8');
session_start();
if(!(isset($_SESSION['admin']) && $_SESSION['admin'] === true)) {
	header('Location: adminLogin.php');
}

$html = file_get_contents("../admin/admin-lista-articoli.html");

$repoArticle = new RepoArticle();
$repoImage = new RepoImage();

$articles = $repoArticle->getArticles();

$content = '<div class="flex-container">';

foreach($articles as $article)
{
	$article->title = MarkdownConverter::renderOnlyLanguage($article->title);
	$article->summary = MarkdownConverter::render($article->summary);
	$content .= "<article class=\"column\">
						<h2>{$article->title}</h2>
						<p>{$article->summary}</p>
						<div class=\"btn-container\">
							<a href=\"insertForm.php?article_id={$article->id}\" class=\"button\">Modifica</a>
							<a href=\"deleteArticle.php?article_id={$article->id}\" class=\"button\">Elimina</a>
						</div>
					</article>";
}

$content .= "</div>";

$html = str_replace("<cs_main_content/>", $content, $html);
echo $html;

$repoArticle->disconnect();
$repoImage->disconnect();

?>
