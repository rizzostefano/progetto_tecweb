<?php

require_once('backend/article/repoArticle.php');
require_once('backend/escapeMarkdown.php');

$DOM = file_get_contents('../listaArticoli.html');

$repo = new RepoArticle();
$articles = $repo->getArticles();

$content = '<section>
                <h1>Sei un musicista interessato alla liuteria?</h1>
                <p>Sei nel posto giusto. Qui puoi trovare articoli che troverai sicuramente interessanti.</p>
            </section>
            <div class="flex-container">';

foreach($articles as $article)
{
    $article->title = MarkdownConverter::renderOnlyLanguage($article->title);
    $article->summary = MarkdownConverter::render($article->summary);
    $content .= "<div class='flex-container'>
                    <article class='column'>
                        <h2>{$article->title}</h2>
                        <p>{$article->summary}</p>
                        <div class='btn-container'>
                            <a href='articleDetails.php?article_id={$article->id}' class='button'>Leggi!</a>
                        </div>
                    </article>
                </div>";
}

$content .= "</div>";

$DOM = str_replace('<cs_main_content/>', $content, $DOM);

echo $DOM;

?>
