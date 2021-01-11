<?php

if (!isset($_GET['article_id'])) 
{
    header('Location: fallback.php');
}

require_once('backend/escapeMarkdown.php');
require_once('backend/article/repoArticle.php');
require_once('backend/image/repoImage.php');

$repoArticle = new RepoArticle();
$repoImage = new RepoImage();

$article = $repoArticle->findArticleById($_GET["article_id"]);
$title = $article->title;
$title = MarkdownConverter::renderOnlyLanguage($title);
$content = $article->content;
$content = MarkdownConverter::render($content);

$articleImage = $repoImage->findImageById($article->image);

$repoArticle->disconnect();
$repoImage->disconnect();

$DOM = file_get_contents('../visualizzaArticolo.html');

//TODO fare chiamata a db per prendere titolo dell'articolo
$DOM = str_replace('<cs_page_title/>', $title, $DOM);

//TODO chiedere che meta title inserire
$DOM = str_replace('<cs_meta_title/>', "<meta name=\"title\" content=\"$title | Rizzo Guitars\"/>", $DOM);

$DOM = str_replace('<cs_meta_description/>', "<meta name=\"description\" content=\"$article->summary\">", $DOM);

$DOM = str_replace('<cs_meta_keyword/>', "<meta name=\"keywords\" content=\"$article->keywords\" />", $DOM);


$article = "<section>
                <h1>$title</h1>
                $content
                <div class=\"square-image-cropper-large centered\">
                    <img src=\"$articleImage->url\" alt=\"$articleImage->alt\" />
                </div>
            </section>";

$DOM = str_replace('<cs_main_content/>', $article, $DOM);

echo $DOM;

?>
