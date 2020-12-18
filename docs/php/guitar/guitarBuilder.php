<?php

namespace Guitar;

class GuitarBuilder{
    private static $name, $price, $summary, $insertDate, $text, $details;

    /**
     * azzera tutti i campi dati di questa classe
     */
    public static function reset()
    {
        unset($name, $price, $summary, $insertDate, $text, $details);
    }

    public static function withName($name)
    {
        GuitarBuilder::$name = $name;
    }

    public static function withPrice($price)
    {
        GuitarBuilder::$price = $price;
    }

    public static function withSummary($summary)
    {
        GuitarBuilder::$summary = $summary;
    }

    public static function withInsertDate($insertDate)
    {
        GuitarBuilder::$insertDate = $insertDate;
    }

    public static function withText($text)
    {
        GuitarBuilder::$text = $text;
    }

    public static function withDetails($details)
    {
        GuitarBuilder::$details = $details;
    }

    /**
     * se tutti i campi hanno un valore valido costruisco la chitarra
     * altrimenti non ritorno nulla, chi chiama la funzione dovrà fare un
     * controllo isset() sul valore ritornato
    */
    public static function buildGuitar()
    {
        if(isset($name, $price, $summary, $insertDate, $text, $details))
        {
            return new Guitar($name, $price, $summary, $insertDate, $text, $details);
        }
    }
}

?>