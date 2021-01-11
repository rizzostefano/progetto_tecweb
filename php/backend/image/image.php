<?php
class Image{
    
    public $id, $name, $alt, $url;

    /**
     * costruttore privato, uso pattern factory
     */
    public function __construct($id, $name, $alt, $url)
    {
        $this->id = $id;
        $this->name = $name;
        $this->alt = $alt;
        $this->url = $url;
    }

}
?>