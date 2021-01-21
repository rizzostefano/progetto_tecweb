<?php

if (!isset($_GET['article_id']))
{
    header('Location: fallback.php');
}

require_once('escapeMarkdown.php');
require_once('repoArticle.php');
require_once('repoImage.php');

$repoArticle = new RepoArticle();
$repoImage = new RepoImage();

$article = $repoArticle->findArticleById($_GET["article_id"]);

if($article === false)
{
    $repoArticle->disconnect();
    $repoImage->disconnect();
    header('Location: ../404.html');
}

$title = $article->title;
$title = MarkdownConverter::renderOnlyLanguage($title);

$tag_title = $article->title;
$tag_title = MarkdownConverter::removeLanguage($tag_title);

$tag_summary = $article->summary;
$tag_summary = MarkdownConverter::removeLanguage($tag_summary);

$tag_keywords = $article->keywords;
$tag_keywords = MarkdownConverter::removeLanguage($tag_keywords);

$content = $article->content;
$content = MarkdownConverter::render($content);

$articleImage = $repoImage->findImageById($article->image);

$repoArticle->disconnect();
$repoImage->disconnect();

$DOM = file_get_contents('../visualizzaArticolo.html');

//TODO fare chiamata a db per prendere titolo dell'articolo
$DOM = str_replace('<cs_page_title/>', $tag_title, $DOM);

//TODO chiedere che meta title inserire
$DOM = str_replace('<cs_meta_title/>', "<meta name=\"title\" content=\"$tag_title | Rizzo Guitars\"/>", $DOM);

$DOM = str_replace('<cs_meta_description/>', "<meta name=\"description\" content=\"$tag_summary\">", $DOM);

$DOM = str_replace('<cs_meta_keyword/>', "<meta name=\"keywords\" content=\"$tag_keywords\" />", $DOM);


$article = "<article>
                <h1>$title</h1>
                <div class=\"rectangle-image-cropper-large centered\">
                    <img src=\"$articleImage->url\" alt=\"$articleImage->alt\" />
                </div>
                $content
            </article>";

$DOM = str_replace('<cs_main_content/>', $article, $DOM);

echo $DOM;

?>
