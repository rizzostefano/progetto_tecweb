<?php
require_once("backend/escapeMarkdown.php");
require_once("backend/article/repoArticle.php");
require_once("backend/image/repoImage.php");

session_start();
if(!(isset($_SESSION['admin']) && $_SESSION['admin'] === true)) {
	header('Location: adminLogin.php');
}


$html = file_get_contents("../admin/admin-nuovo-articolo.html");

$errorMessageTitolo = "Il titolo dell'articolo è: obbligatorio, al massimo 30 caratteri e va scritto senza markdown";
$errorMessageContenuto = "Il corpo dell'articolo deve essere lungo almeno 30 caratteri e scritto secondo le regole del markdown";
$errorMessageSommario = "Il sommario dell'articolo è: obbligatorio, al massimo 200 caratteri e scritto secondo le regole del markdown";
$errorMessageFile = "Il file va inserito obbligatoriamente e deve essere un immagine inferiore al megabyte";
$errorMessageFileDuplicate = "Un file con questo nome è gia stato inserit nella piattaforma";
$errorMessageAlt = "Il testo alternativo non può superare i 70 caratteri o contenere markup";

$repoImage = new RepoImage();
$repoArticle = new RepoArticle();

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
	$html = substituteError($validTitolo, "%error-titolo%", errorElement($errorMessageTitolo), $html);
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
	$html = substituteError($validContenuto, "%error-contenuto%", $errorMessageContenuto, $html);
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
	$html = substituteError($validSommario, "%error-sommario%", $errorMessageSommario, $html);
	$html = str_replace("%value-sommario%", $sommario, $html);
	
	// check file immagine caricato (dimensione e tipo file)
	if(isset($_FILES["file-immagine"])){
		$file = $_FILES["file-immagine"];
		$isDuplicate = $repoImage->checkDouble($file["name"]);
		$validFile = $file["size"] <= 1000000
				&& $file["error"] === 0
				&& substr_compare($file["type"], "image/", 0, strlen("image/")) === 0
				&& !$isDuplicate;

	} else {
		$validFile = false;
		$isDuplicate = false;
	}
	$html = substituteError($validFile, "%error-file%", $isDuplicate ? $errorMessageFileDuplicate : $errorMessageFile , $html);
	
	// check alt immagine (lunghezza massima e niente markdown)
	if(isset($_POST["alt-immagine"])){
		$alt = $_POST["alt-immagine"];
		$validAlt = validateNoMarkdown($alt)
				&& validateLength($alt, NULL, 70);
	} else {
		$validAlt = true;
		$alt = "";
	}
	$html = substituteError($validAlt, "%error-alt%", $errorMessageAlt, $html);
	$html = str_replace("%value-alt%", $alt, $html);
	
	if($validTitolo && $validContenuto && $validFile && $validAlt && $validSommario){
		$resultInsImage = $repoImage->addImage($file, $alt);
		if($resultInsImage === true) {
			$insertedImage = $repoImage->findImageByName($file["name"]);
			$resultInsArticle = $repoArticle->addArticle($titolo, $contenuto, $sommario, $insertedImage->id);
			echo "Articolo inserito";
		}
		else {
			echo "IMMAGINE NON INSERITA";
		}
	}else{
		echo $html;
	}
	
} else {
	$substitutions = array("%error-alt%", "%error-file%", "%error-contenuto%", "%error-titolo%","%error-sommario%", 
	                       "%value-alt%", "%value-contenuto%", "%value-titolo%", "%value-sommario%");
	echo str_replace($substitutions, "",$html);
}

$repoImage->disconnect();
$repoArticle->disconnect();

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

function errorElement($message){
	return '<strong class="error">' . $message . '</strong>';
}