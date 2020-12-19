<?php

require_once('backend/guitar/guitar.php');
require_once('backend/guitar/repoGuitar.php');
require_once('backend/image/image.php');
require_once('backend/image/repoImage.php');

$DOM = file_get_contents('../template.html');

//TODO fare chiamata a db per prendere titolo dell'articolo
$DOM = str_replace('<cs_page_title/>', "Lista strumenti", $DOM);

//TODO chiedere che meta title inserire
$DOM = str_replace('<cs_meta_title/>', '<meta name="title" content="Strumenti | Rizzo Guitars"/>', $DOM);

$DOM = str_replace('<cs_meta_description/>', '<meta name="description" content="Rizzo Guitars Laboratorio di liuteria in provincia di Padova - Costruzione - Set Up - Riparazione Strumenti Musicali" />', $DOM);

//TODO definire keyword per ogni articolo => aggiungerle al db?
$DOM = str_replace('<cs_meta_keyword/>', '<meta name="keywords" content="Liutaio Liuteria Padova Chitarre di liuteria Strumenti costruiti Strumenti" />', $DOM);

$repoGuitar = new RepoGuitar();
$guitars = $repoGuitar->getGuitars();
$repoImage = new RepoImage();


$content = '<section>
                <h1>Interessato ad un nuovo strumento?</h1>
                <p>Lo sappiamo entrambi. I musicisti sanno essere molto schizzinosi quando si parla dei loro strumenti. Farsi realizzare uno <em>strumento su misura</em> è il modo migliore per ottenere quella sensazione di puro piacere quando si suona.</p>
            </section>
            <section>
                <h2>Qui potrai trovare le specifiche di ogni strumento costruito.</h2>';

foreach($guitars as $guitar)
{
    $nameHtml = MarkdownCorverter::render($guitar->name);
    $summaryHtml = MarkdownCorverter::render($guitar->summary);
    $coverImage = $repoImage->findImageById($guitar->id);
    $content .= "<div class='flex-container'>
                    <article class='column'>
                        <h3>{$nameHtml}</h3>
                        <p>{$summaryHtml}</p>
                        <div class='rectangle-image-cropper centered'>
                            <img src='{$coverImage->url}' alt='{$coverImage->alt}' />
                        </div>
                        <div class='btn-container'>
                            <a href='guitarDetails.php?guitar_id={$guitar->id}'class='button'>Esplora</a>
                        </div>
                    </article>
                </div>"
}

$content .= "</section>";

$DOM = str_replace('<cs_main_content/>', $content, $DOM);

echo $DOM;

?>