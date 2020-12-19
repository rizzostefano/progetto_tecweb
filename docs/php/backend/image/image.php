<?php
class Guitar{
    public $id, $name, $alt, $url, $cover;

    /**
     * costruttore privato, uso pattern factory
     */
    public function __construct($id, $name, $alt, $url, $cover)
    {
        $this->id = $id;
        $this->name = $name;
        $this->alt = $alt;
        $this->url = $url;
        $this->cover = $cover;
    }

}
?>