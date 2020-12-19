<?php
class Guitar{
    public $id, $name, $price, $summary, $insertDate, $text;
    public $details = array();

    /**
     * costruttore privato, uso pattern factory
     */
    public function __construct($id, $name, $price, $summary, $insertDate, $text, $details)
    {
        $this->id = $id;
        $this->name = $name;
        $this->summary = $summary;
        $this->insertDate = $insertDate;
        $this->text = $text;
        $this->details = $details;
    }

}
?>