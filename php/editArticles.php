<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . "escapeMarkdown.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "repoArticle.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "repoImage.php";
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

$html = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR .".." . DIRECTORY_SEPARATOR . "admin" . DIRECTORY_SEPARATOR . "admin-lista-articoli.html");

$repoArticle = new RepoArticle();
if($repoArticle->getConnectionLastError() !== '')
{
	header('Location: ..' . DIRECTORY_SEPARATOR . '500.html');
}

$articles = $repoArticle->getArticles();
$repoArticle->disconnect();
$tot = count($articles);

$content = '<div class="flex-container">';

if(empty($articles)){
	$content .= "<p>Nessun articolo presente.</p>";
}
else {
	$tabindex = 0;
	for($i=0; $i < $tot && $i < $limit; ++$i)
	{
		$last = false;
        if(($i === $limit - 1) || ($i === $tot - 1)){
            $last = true;
        }
		$article = $articles[$i];
		$article->title = MarkdownConverter::renderOnlyLanguage($article->title);
		$article->summary = MarkdownConverter::render($article->summary);
		$tabindex++;
		$content .= "<article class=\"flex-article\">".
							($last === true ? "<h2 id=\"article-anchor\"" : "<h2") . " tabindex=\"$tabindex\">" . "{$article->title}</h2>";
		$tabindex++;
		$content .= "<section tabindex=\"$tabindex\">{$article->summary}</section>".
							"<div class=\"btn-container three-btn\">";
		$tabindex++;
		$content .= "<a href=\"articleDetailsAdmin.php?article_id={$article->id}\" class=\"button\" tabindex=\"$tabindex\">Leggi!</a>";
		$tabindex++;
		$content .= "<a href=\"insertForm.php?article_id={$article->id}\" class=\"button\" tabindex=\"$tabindex\">Modifica</a>";
		$tabindex++;
		$content .= "<a href=\"deleteArticle.php?article_id={$article->id}\" class=\"button\" tabindex=\"$tabindex\">Elimina</a>".
							"</div>".
						"</article>";
	}
}

$content .= "</div>";

if($tot > $limit){
	$limit += 5;
    $content .= "<div class=\"load-more\"><a href=\"editArticles.php?limit={$limit}#article-anchor\">Carica altro</a></div></section>";
}

$html = str_replace("<cs_main_content/>", $content, $html);
echo $html;

?>
