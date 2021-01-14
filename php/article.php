<?php

/**
 * Modello per l'entità Articolo
 */
class Article{
    
    public $id, $title, $content, $summary, $image, $keywords;

    /**
     * costruttore privato, uso pattern factory
     */
    public function __construct($id, $title, $content, $summary, $image, $keywords)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->summary = $summary;
        $this->image = $image;
        $this->keywords = $keywords;
    }

}
?>