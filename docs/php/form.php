<?php
require_once("backend/escapeMarkdown.php");
require_once("backend/article/repoArticle.php");
require_once("backend/image/repoImage.php");

$html = file_get_contents("../admin/admin-nuovo-articolo.html");

if(isset($_POST["submit"])){
	// check titolo aritcolo (niente markdown, lunghezza massima e required)
	if(isset($_POST["titolo-articolo"])){
		$titolo = $_POST["titolo-articolo"];
		$validTitolo = validateNoMarkdown($titolo)
				&& validateLength($titolo, NULL, 30)
				&& validateRequired($titolo);
	} else {
		$validTitolo = false;
		$titolo = "";
	}
	$html = substituteError($validTitolo, "%error-titolo%", "o no sbaliato", $html);
	$html = str_replace("%value-titolo%", $titolo, $html);
	
	// check contenuto articolo (lunghezza minima e required)
	if(isset($_POST["contenuto-articolo"])){
		$contenuto = $_POST["contenuto-articolo"];
		$validContenuto = validateLength($contenuto, 30, NULL)
					&& validateRequired($contenuto);
	} else {
		$contenuto = "";
		$validContenuto = false;
	}
	$html = substituteError($validContenuto, "%error-contenuto%", "o no sbaliato", $html);
	$html = str_replace("%value-contenuto%", $contenuto, $html);
	
		// check summary articolo (lunghezza massima e required)
	if(isset($_POST["sommario-articolo"])){
		$sommario = $_POST["sommario-articolo"];
		$validSommario = validateLength($sommario, NULL, 200)
					&& validateRequired($sommario);
	} else {
		$sommario = "";
		$validSommario = false;
	}
	$html = substituteError($validSommario, "%error-sommario%", "o no sbaliato", $html);
	$html = str_replace("%value-sommario%", $sommario, $html);
	
	// check file immagine caricato (dimensione e tipo file)
	if(isset($_FILES["file-immagine"])){
		$file = $_FILES["file-immagine"];
		$validFile = $file["size"] <= 1000000
				&& $file["error"] === 0
				&& substr_compare($file["type"], "image/", 0, strlen("image/")) === 0;
	} else {
		$validFile = false;
	}
	$html = substituteError($validFile, "%error-file%", "o no sbaliato", $html);
	
	// check alt immagine (lunghezza massima e niente markdown)
	if(isset($_POST["alt-immagine"])){
		$alt = $_POST["alt-immagine"];
		$validAlt = validateNoMarkdown($alt)
				&& validateLength($alt, NULL, 70);
	} else {
		$validAlt = true;
		$alt = "";
	}
	$html = substituteError($validAlt, "%error-alt%", "o no sbaliato", $html);
	$html = str_replace("%value-alt%", $alt, $html);
	
	if($validTitolo && $validContenuto && $validFile && $validAlt && $validSommario){
		$repoImage = new RepoImage();
		$resultInsImage = $repoImage->addImage($file, $alt);
		if($resultInsImage === true) {
			$repoArticle = new RepoArticle();
			$resultInsArticle = $repoArticle->addArticle($titolo, $contenuto, NULL, date("Y-m-d"), $file["name"]);
		}
		echo "ole";
	}else{
		echo $html;
	}
	
} else {
	$substitutions = array("%error-alt%", "%error-file%", "%error-contenuto%", "%error-titolo%","%error-sommario%", 
	                       "%value-alt%", "%value-contenuto%", "%value-titolo%", "%value-sommario%");
	echo str_replace($substitutions, "",$html);
}


function validateNoMarkdown($input){
	$valid = true;
	foreach (array_keys(MarkdownConverter::$standardRules) as $regex){
		 $valid = $valid && !preg_match($regex, $input);
	}
	return $valid;
}

function validateLength($input, $min, $max){
	return (($min === NULL) || ($min <= strlen($input)))
	&& (($max === NULL) || ($max >= strlen($input)));
}

function validateRequired($input){
	return isset($input) && strlen($input) > 0;
}

function substituteError($valid, $pattern, $error, $context){
	if($valid===true){
		return str_replace($pattern, "", $context);
	} else {
		return str_replace($pattern, $error, $context);
	}
}