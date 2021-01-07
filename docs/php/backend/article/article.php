<?php

/**
 * Modello per l'entità Articolo
 */
class Article{
    
    public $id, $title, $content, $summary, $insertDate, $image;

    /**
     * costruttore privato, uso pattern factory
     */
    public function __construct($id, $title, $content, $summary, $insertDate, $image)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->summary = $summary;
        $this->insertDate = $insertDate;
        $this->image = $image;
    }

}
?>