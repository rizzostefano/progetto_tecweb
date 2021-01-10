<?php

require_once('backend/article/repoArticle.php');
require_once('backend/escapeMarkdown.php');

$DOM = file_get_contents('../template.html');

//TODO fare chiamata a db per prendere titolo dell'articolo
$DOM = str_replace('<cs_page_title/>', "Lista articoli", $DOM);

//TODO chiedere che meta title inserire
$DOM = str_replace('<cs_meta_title/>', '<meta name="title" content="Articolo | Rizzo Guitars"/>', $DOM);

$DOM = str_replace('<cs_meta_description/>', '<meta name="description" content="Scopri di il mondo della liuteria con gli articoli per appassionati su chitarre e altri strumenti di Rizzo guitars ">', $DOM);

//TODO definire keyword per ogni articolo => aggiungerle al db?
$DOM = str_replace('<cs_meta_keyword/>', '<meta name="keywords" content="Chitarra,Corde,Liuteria" />', $DOM);

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