<?php

require_once("backend/escapeMarkdown.php");
require_once("backend/article/repoArticle.php");
require_once("backend/article/article.php");
require_once("backend/image/repoImage.php");
header('Content-type: text/html; charset=utf-8');
session_start();
if(!(isset($_SESSION['admin']) && $_SESSION['admin'] === true)) {
	header('Location: adminLogin.php');
}

$html = file_get_contents("../admin/admin-inserisci-articolo.html");

$repoImage = new RepoImage();
$repoArticle = new RepoArticle();

if(isset($_GET["new"])){
	unset($_SESSION["article"]);
}

if(isset($_GET["article_id"])){
	$article = $repoArticle->findArticleById($_GET["article_id"]);
	if($article !== false){
		$_SESSION["article"] = $article;
	} 
}

$isEditing = isset($_SESSION["article"]);
if(isset($_POST["submit"])){
	$validateTitle = validateTitle($_POST["titolo-articolo"]);
	$validateContent = validateContent($_POST["contenuto-articolo"]);
	$validateImageAlt = validateImageAlt($_POST["alt-immagine"]);
	$validateSummary = validateSummary($_POST["sommario-articolo"]);
	$isImageNotChanging = $isEditing && $_FILES["file-immagine"]["error"] !== 0 ;
	echo !isset($_FILES["file-immagine"]["name"]);
	$validateImage =  $isImageNotChanging ? true : validateImage($_FILES["file-immagine"]);
	if ($validateTitle && $validateContent && $validateImage && $validateImageAlt && $validateSummary){
		$imageId = $isImageNotChanging ? $_SESSION["article"]->image : $repoImage->addImage($_FILES["file-immagine"], $_POST["alt-immagine"])->id; 
		$articleId = $isEditing ? $_SESSION["article"]->id : -1;
		$article = new Article($articleId, $_POST["titolo-articolo"], $_POST["contenuto-articolo"], $_POST["sommario-articolo"], $imageId);
		if ($isEditing && !$isImageNotChanging){
			$repoImage->deleteImage($_SESSION["article"]->image);
		}$_SESSION["article"] = $article;
		$result = $isEditing ? $repoArticle->editArticle($article) : $repoArticle->addArticle($article->title,$article->content,$article->summary,$article->image);
		if($result !== false){
			unset($_SESSION["article"]);
			header('Location: editArticles.php');
		}
	}

} 

$html = preg_replace("/%error-\w*%/", "", $html);

if($isEditing){
	$article = $_SESSION["article"];
	$image = $repoImage->findImageById($article->image);
	$html = str_replace("%add-or-modify%", "Modifica di :" . $article->title , $html);
	$html = str_replace("%image-required%", "", $html);
	$html = str_replace("%edit-file-msg%", "Se non si inserisce l'immagine, rimarrà quella inserita precedentemente.", $html);
	$html = str_replace("%value-titolo%",$article->title, $html);
	$html = str_replace("%value-contenuto%",$article->content, $html);
	$html = str_replace("%value-sommario%",$article->summary, $html);
	$html = str_replace("%value-alt%",$image->alt, $html);
} else {
	$html = str_replace("%add-or-modify%", "Nuovo articolo", $html);
	$html = str_replace("%image-required%", 'required="required"', $html);
	$html = str_replace("%edit-file-msg%", "", $html);
	$html = preg_replace("/%value-\w*%/", "", $html);
}

echo $html;
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

function validateContent($contenuto) {
	$errorMessageContenuto = "Il corpo dell'articolo deve essere lungo almeno 30 caratteri e scritto secondo le regole del markdown";
	$valid = validateTextField($contenuto, 30, NULL, true, false);
	handleField($valid, "%error-contenuto%", errorElement($errorMessageContenuto), "%value-contenuto%", $contenuto);
	return $valid;
}

function validateTitle($titolo) {
	// TODO: permettere di inserire markdown lingua nel titolo
	$errorMessageTitolo = "Il titolo dell'articolo è: obbligatorio, al massimo 30 caratteri e va scritto senza markdown";
	$valid = validateTextField($titolo, NULL, 30, false, false);
	handleField($valid, "%error-titolo%", errorElement($errorMessageTitolo), "%value-titolo%", $titolo);
	return $valid;
}

function validateSummary($sommario) {
	$errorMessageSommario = "Il sommario dell'articolo è: obbligatorio, al massimo 200 caratteri e scritto secondo le regole del markdown";
	$valid = validateTextField($sommario, NULL, 200, true, false);
	handleField($valid, "%error-sommario%", errorElement($errorMessageSommario), "%value-sommario%", $sommario);
	return $valid;
}

function validateImageAlt($altImmagine) {
	$errorMessageAlt = "Il testo alternativo non può superare i 70 caratteri o contenere markup";
	$valid = validateTextField($altImmagine, NULL, 70, false, true);
	handleField($valid, "%error-alt%", errorElement($errorMessageAlt), "%value-alt%", $altImmagine);
	return $valid; 
}

function validateImage($file) {
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
