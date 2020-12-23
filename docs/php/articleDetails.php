<?php

if (!isset($_GET['article_id'])) 
{
    header('Location: fallback.php');
}

require_once('backend/escapeMarkdown.php');
require_once('backend/article/repoArticle.php');
require_once('backend/image/repoImage.php');

$DOM = file_get_contents('../template.html');

//TODO fare chiamata a db per prendere titolo dell'articolo
$DOM = str_replace('<cs_page_title/>', "Articolo", $DOM);

//TODO chiedere che meta title inserire
$DOM = str_replace('<cs_meta_title/>', '<meta name="title" content="Articolo | Rizzo Guitars"/>', $DOM);

$DOM = str_replace('<cs_meta_description/>', '<meta name="description" content="Scopri di il mondo della liuteria con gli articoli per appassionati su chitarre e altri strumenti di Rizzo guitars ">', $DOM);

//TODO definire keyword per ogni articolo => aggiungerle al db?
$DOM = str_replace('<cs_meta_keyword/>', '<meta name="keywords" content="Chitarra,Corde,Liuteria" />', $DOM);

$repoArticle = new RepoArticle();
$repoImage = new RepoImage();
$article = $repoArticle->findArticleById($_GET["article_id"]);
$title = $article->title;
$title = MarkdownCorverter::render($title);
$content = $article->content;
$content = MarkdownCorverter::render($content);

$articleImages = $repoArticle->getArticleImages($_GET["article_id"]);
$repoArticle->disconnect();
if($articleImages != null)
{
    foreach($articleImages as $image)
    {
        str_replace(sprintf("%s_URL", $image->name), $image->url, $content);
        str_replace(sprintf("%s_ALT", $image->name), $image->alt, $content);
    }
}

$article = "<section>
                <h1>$title</h1>
                $content
            </section>";

$DOM = str_replace('<cs_main_content/>', $article, $DOM);

echo $DOM;

?>