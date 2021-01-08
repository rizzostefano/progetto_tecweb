<?php

require_once("backend/escapeMarkdown.php");
require_once("backend/article/repoArticle.php");
require_once("backend/image/repoImage.php");
header('Content-type: text/html; charset=utf-8');
session_start();
if(!(isset($_SESSION['admin']) && $_SESSION['admin'] === true)) {
	header('Location: adminLogin.php');
}

$html = file_get_contents("../admin/admin-nuovo-articolo.html");

$repoImage = new RepoImage();
$repoArticle = new RepoArticle();

if(isset($_POST["submit"])){
	if(validaTitolo($_POST["titolo-articolo"]) &&
			validaContenuto($_POST["contenuto-articolo"]) &&
			validaImmagine($_FILES["file-immagine"]) &&
			validaAltImmagine($_POST["alt-immagine"]) &&
			validaSommario($_POST["sommario-articolo"]))
	{
		$file = $_FILES["file-immagine"];
		$repoImage->addImage($file, $_POST["alt-immagine"]);
		$insertedImage = $repoImage->findImageByName($file["name"]);
		$resultInsArticle = $repoArticle->addArticle($_POST["titolo-articolo"],
													 $_POST["contenuto-articolo"], 
													 $_POST["sommario-articolo"], 
													 $insertedImage->id);
		echo "Articolo inserito con successo";
	}
} else {
	$html = preg_replace("/%(.*)%/", "", $html);
	echo $html;
}

$repoImage->disconnect();
$repoArticle->disconnect();

/**
 * @field: valore del campo
 * @validity: booleano che indica la validità del contenuto del campo
 * @error_substitution: valore da cercare in html da sostituire con il messaggio di errore
 * @error_message: messaggio di errore da visualizzare
 * @value_content: contenuto da inserire nel campo
 */
function handleField($validity, $error_substitution, $error_message, $value_substitution, $field){
	global $html;
	$html = substituteError($validity, $error_substitution, errorElement($error_message), $html);
	$html = str_replace($value_substitution, $field, $html);
}

function validateTextField($field, $minlen, $maxlen, $hasMarkdown, $isNotRequired){
	$field  = isset($field) ? $field : "";
	return ($hasMarkdown || validateNoMarkdown($field))
			&& validateLength($field, $minlen, $maxlen)
			&& ($isNotRequired || (validateRequired($field)));
}

function validaContenuto($contenuto) {
	$errorMessageContenuto = "Il corpo dell'articolo deve essere lungo almeno 30 caratteri e scritto secondo le regole del markdown";
	$valid = validateTextField($contenuto, 30, NULL, true, false);
	handleField($valid, "%error-contenuto%", errorElement($errorMessageContenuto), "%value-contenuto%", $contenuto);
	if($valid === false){
		echo "SOMETHING WENT WRONG: CONTENUTO";
	}
	return $valid;
}

function validaTitolo($titolo) {
	// TODO: permettere di inserire markdown lingua nel titolo
	$errorMessageTitolo = "Il titolo dell'articolo è: obbligatorio, al massimo 30 caratteri e va scritto senza markdown";
	$valid = validateTextField($titolo, NULL, 30, false, false);
	handleField($valid, "%error-titolo%", errorElement($errorMessageTitolo), "%value-titolo%", $titolo);
	if($valid === false){
		echo "SOMETHING WENT WRONG: TITOLO";
	}
	return $valid;
}

function validaSommario($sommario) {
	$errorMessageSommario = "Il sommario dell'articolo è: obbligatorio, al massimo 200 caratteri e scritto secondo le regole del markdown";
	$valid = validateTextField($sommario, NULL, 200, true, false);
	handleField($valid, "%error-contenuto%", errorElement($errorMessageSommario), "%value-contenuto%", $sommario);
	if($valid === false){
		echo "SOMETHING WENT WRONG: SOMMARIO";
	}
	return $valid;
}

function validaAltImmagine($altImmagine) {
	$errorMessageAlt = "Il testo alternativo non può superare i 70 caratteri o contenere markup";
	$valid = validateTextField($altImmagine, NULL, 70, false, true);
	handleField($valid, "%error-alt%", errorElement($errorMessageAlt), "%value-alt%", $altImmagine);
	if($valid === false){
		echo "SOMETHING WENT WRONG: ALT";
	}
	return $valid; 
}

function validaImmagine($fileImmagine) {
	global $html;
	global $repoImage;
	$errorMessageFile = "Il file va inserito obbligatoriamente e deve essere un immagine inferiore al megabyte";
	$errorMessageFileDuplicate = "Un file con questo nome è gia stato inserito nella piattaforma";
	$valid = false;
	$isDuplicate = false;
	if(isset($_FILES["file-immagine"])){
		$file = $_FILES["file-immagine"];
		$isDuplicate = $repoImage->checkDouble($file["name"]);
		$valid = $file["size"] <= 1000000
				&& $file["error"] === 0
				&& substr_compare($file["type"], "image/", 0, strlen("image/")) === 0
				&& !$isDuplicate;

	}
	$html = substituteError($valid,
							"%error-file%",
							errorElement($isDuplicate ? $errorMessageFileDuplicate : $errorMessageFile),
							$html);
	if($valid === false){
		echo "SOMETHING WENT WRONG: IMMAGINE";
	}
	return $valid;
}

function validateNoMarkdown($input){
	$valid = true;
	foreach (array_keys(MarkdownConverter::$standardRules) as $regex){
		 $valid = $valid && !preg_match($regex, $input);
	}
	return $valid;
}

function validateLength($input, $min, $max){
	$input = utf8_decode($input);
	$length = strlen($input);
	return (($min === NULL) || ($min <= $length))
	&& (($max === NULL) || ($length <= $max));
	
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
	return '<strong class="error"> - ' . $message . '</strong>';
}
