<?php
require_once("escapeMarkdown.php");
require_once("repoArticle.php");
require_once("repoImage.php");
session_start();

if(!(isset($_SESSION['admin']) && $_SESSION['admin'] === true)) {
	header('Location: adminLogin.php');
}

if(isset($_GET["limit"])){
    $limit = $_GET["limit"];
}
else {
    $limit = 5;
}

$html = file_get_contents("../admin/admin-lista-articoli.html");

$repoArticle = new RepoArticle();
$articles = $repoArticle->getArticles();
$repoArticle->disconnect();
$tot = count($articles);

$content = '<div class="flex-container">';

if(empty($articles)){
	$content .= "<p>Nessun articolo presente.</p>";
}
else {
	for($i=0; $i < $tot && $i < $limit; ++$i)
	{
		$last = false;
        if(($i === $limit - 1) || ($i === $tot - 1)){
            $last = true;
        }
		$article = $articles[$i];
		$article->title = MarkdownConverter::renderOnlyLanguage($article->title);
		$article->summary = MarkdownConverter::render($article->summary);
		$content .= "<article class=\"column\">".
							($last === true ? "<h2 id=\"article-anchor\">" : "<h2>") . "{$article->title}</h2>".
							"{$article->summary}".
							"<div class=\"btn-container\">".
								"<a href=\"insertForm.php?article_id={$article->id}\" class=\"button\">Modifica</a>".
								"<a href=\"deleteArticle.php?article_id={$article->id}\" class=\"button\">Elimina</a>".
							"</div>".
						"</article>";
	}
}

$content .= "</div>";

if($tot > $limit){
	$limit += 5;
    $content .= "<div class=\"load-more\"><a href=\"articleList.php?limit={$limit}#article-anchor\">Carica altro</a></div></section>";
}

$html = str_replace("<cs_main_content/>", $content, $html);
echo $html;

?>
