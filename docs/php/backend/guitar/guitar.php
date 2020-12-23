<?php

class Guitar{

    public $id, $name, $price, $summary, $insertDate, $text, $coverImage;
    public $details = array();

    /**
     * costruttore privato, uso pattern factory
     */
    public function __construct($id, $name, $price, $summary, $insertDate, $text, $details, $coverImage)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->summary = $summary;
        $this->insertDate = $insertDate;
        $this->text = $text;
        $this->details = $details;
        $this->coverImage = $coverImage;
    }

}
?>