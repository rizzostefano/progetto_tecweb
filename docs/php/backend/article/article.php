<?php

/**
 * Modello per l'entità Articolo
 */
class Article{
    
    public $id, $title, $content, $summary, $insertDate;

    /**
     * costruttore privato, uso pattern factory
     */
    public function __construct($id, $title, $content, $summary, $insertDate)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->summary = $summary;
        $this->insertDate = $insertDate;
    }

}
?>