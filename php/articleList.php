<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'repoArticle.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'escapeMarkdown.php';

if(isset($_GET["limit"])){
    $limit = $_GET["limit"];
}
else {
    $limit = 5;
}

$DOM = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'listaArticoli.html');

$repoArticle = new RepoArticle();
if($repoArticle->getConnectionLastError() !== '')
{
	header('Location: ..' . DIRECTORY_SEPARATOR . '500.html');
}
$articles = $repoArticle->getArticles();
$repoArticle->disconnect();

$content = '<section>
                <h1>Sei un musicista interessato alla liuteria?</h1>
                <p>Sei nel posto giusto. Qui puoi trovare articoli che troverai sicuramente interessanti.</p>
            </section>
            <section>
            <div class="flex-container">';

$tot = count($articles);
if(empty($articles)){
    $content .= "<p>Nessun articolo presente.</p>";
}
else {
    for($i=0; $i < $limit && $i < $tot; ++$i)
    {
        $last = false;
        if(($i === $limit - 1) || ($i === $tot - 1)){
            $last = true;
        }
        $article = $articles[$i];
        $article->title = MarkdownConverter::renderOnlyLanguage($article->title);
        $article->summary = MarkdownConverter::render($article->summary);
        $content .="<article class=\"flex-article\">" .
                            ($last === true ? "<h2 id=\"article-anchor\"" : "<h2") . " tabindex=\"0\">" . "{$article->title}</h2>".
                        "<div tabindex=\"0\">{$article->summary}</div>".
                            "<div class=\"btn-container\">".
                                "<a href=\"articleDetails.php?article_id={$article->id}\" class=\"button\" tabindex=\"0\">Leggi!</a>".
                            "</div>".
                        "</article>";
    }
}

$content .= "</div>";
if($tot > $limit){
    $limit += 5;
    $content .= "<div class=\"load-more\"><a href=\"articleList.php?limit={$limit}#article-anchor\">Carica altro</a></div></section>";
}




$DOM = str_replace('<cs_main_content/>', $content, $DOM);

echo $DOM;

?>
