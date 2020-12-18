<?php
class Guitar{
    private const $name, $price, $summary, $insertDate, $text, $details;

    /**
     * costruttore privato, uso pattern factory
     */
    private function __construct($name, $price, $summary, $insertDate, $text, $details)
    {
        $this->$name = $name;
        $this->$summary = $summary;
        $this->$insertDate = $insertDate;
        $this->$text = $text;
        $this->$details = $details;
    }
}
?>