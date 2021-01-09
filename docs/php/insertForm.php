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

print_r($_POST);
print_r($_GET);

if(isset($_GET["article_id"]) || isset($_POST["articleId"]))
{
	$article = $repoArticle->findArticleById(isset($_GET["article_id"]) ? $_GET["article_id"] : $_POST["articleId"]);
	$image = $repoImage->findImageById($article->image);
	if($article === false){
		header('Location: insertForm.php');
	}
	if(isset($_POST["submit"])){
		// caso modifica articolo submit premuto
		$validaTitolo = validaTitolo($_POST["titolo-articolo"]);
		$validaContenuto = validaContenuto($_POST["contenuto-articolo"]);
		$validaAltImmagine = validaAltImmagine($_POST["alt-immagine"]);
		$validaSommario = validaSommario($_POST["sommario-articolo"]);
		$validaImmagine = true;
		$imageIsChanging = false;
		if(isset($_FILES)){
			$validaImmagine = validaImmagine($_FILES["file-immagine"]);
			$imageIsChanging = true;
		}
		if($validaTitolo && $validaContenuto && $validaImmagine &&
				$validaAltImmagine && $validaSommario && $validaSommario)
		{
			if($imageIsChanging === true) {
				$repoImage->deleteImage($image->id);
				$repoImage->addImage($_FILES["file-immagine"], $_POST["alt-immagine"]);
				$image = $repoImage->findImageByName($_FILES["file-immagine"]["name"]);
				$article->image = $image->id;
			}
			else{
				$repoImage->editAltImage($image->id, $_POST["alt-immagine"]);
			}
			
			$repoArticle->editArticle($article);
			echo "inserimento non andato";
		} else {
			echo $html;
		}
	}
	else{
		// CASO MODIFICA ARTICOLO submit non premuto
		$html = str_replace("%article-id%", '<input type="hidden" name="articleId" value="'. $_GET["article_id"].'"/>', $html);
		$html = str_replace("%image-required%", "", $html);
		$html = str_replace("%edit-file-msg%", "Se non si inserisce l'immagine, rimarrà quella inserita precedentemente.", $html);
		$html = preg_replace("/%error-(.*)%/", "", $html);
		$html = str_replace("%value-titolo%",$article->title, $html);
		$html = str_replace("%value-contenuto%",$article->content, $html);
		$html = str_replace("%value-sommario%",$article->summary, $html);
		$html = str_replace("%value-file%",$image->name, $html);
		$html = str_replace("%value-alt%",$image->alt, $html);
		echo $html;
	}
}
else{

	if(isset($_POST["submit"])){
		echo "1";
	// CASO INSERIMENTO ARTICOLO SUBMIT PREMUTO
		$validaTitolo = validaTitolo($_POST["titolo-articolo"]);
		$validaContenuto = validaContenuto($_POST["contenuto-articolo"]);
		$validaImmagine = validaImmagine($_FILES["file-immagine"]);
		$validaAltImmagine = validaAltImmagine($_POST["alt-immagine"]);
		$validaSommario = validaSommario($_POST["sommario-articolo"]);

		if($validaTitolo && $validaContenuto && $validaImmagine &&
				$validaAltImmagine && $validaSommario && $validaSommario)
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
		else {
			// qualcosa va storto in inserimento, stampo errori
			echo $html;
		}
	}
	else{
		echo "2";
		// caso inserimento submit non premuto
		$html = str_replace("%image-required%", 'required="required"', $html);
		$html = preg_replace("/%(.*)%/", "", $html);
		echo $html;
	}
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
	return $valid;
}

function validaTitolo($titolo) {
	// TODO: permettere di inserire markdown lingua nel titolo
	$errorMessageTitolo = "Il titolo dell'articolo è: obbligatorio, al massimo 30 caratteri e va scritto senza markdown";
	$valid = validateTextField($titolo, NULL, 30, false, false);
	handleField($valid, "%error-titolo%", errorElement($errorMessageTitolo), "%value-titolo%", $titolo);
	return $valid;
}

function validaSommario($sommario) {
	$errorMessageSommario = "Il sommario dell'articolo è: obbligatorio, al massimo 200 caratteri e scritto secondo le regole del markdown";
	$valid = validateTextField($sommario, NULL, 200, true, false);
	handleField($valid, "%error-sommario%", errorElement($errorMessageSommario), "%value-sommario%", $sommario);
	return $valid;
}

function validaAltImmagine($altImmagine) {
	$errorMessageAlt = "Il testo alternativo non può superare i 70 caratteri o contenere markup";
	$valid = validateTextField($altImmagine, NULL, 70, false, true);
	handleField($valid, "%error-alt%", errorElement($errorMessageAlt), "%value-alt%", $altImmagine);
	return $valid; 
}

function validaImmagine($file) {
	global $html;
	global $repoImage;
	$errorMessageFile = "Il file va inserito obbligatoriamente se non precedentemente inserito e deve essere un'immagine inferiore al megabyte";
	$errorMessageFileDuplicate = "Un file con questo nome è gia stato inserito nella piattaforma";
	$valid = false;
	$isDuplicate = false;
	if(isset($file)){
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
