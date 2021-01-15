<?php

require_once("escapeMarkdown.php");
require_once("repoArticle.php");
require_once("article.php");
require_once("repoImage.php");
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
	$validateKeywords = validateKeywords($_POST["keywords-articolo"]);
	$isImageNotChanging = $isEditing && $_FILES["file-immagine"]["error"] !== 0 ;
	$validateImage =  $isImageNotChanging ? true : validateImage($_FILES["file-immagine"]);
	if ($validateTitle && $validateContent && $validateImage && $validateImageAlt && $validateSummary && $validateKeywords){
		$imageId = $isImageNotChanging ? $_SESSION["article"]->image : $repoImage->addImage($_FILES["file-immagine"], $_POST["alt-immagine"])->id;
		$articleId = $isEditing ? $_SESSION["article"]->id : -1;
		//è brutto ma come pezza per il momento ci sta, buonanotte
		$article = new Article($articleId, trimHtmlCode($_POST["titolo-articolo"]), trimHtmlCode($_POST["contenuto-articolo"]), trimHtmlCode($_POST["sommario-articolo"]), $imageId, trimHtmlCode($_POST["keywords-articolo"]));
		$_SESSION["article"] = $article;
		$result = $isEditing ? $repoArticle->editArticle($article) : $repoArticle->addArticle($article->title,$article->content,$article->summary,$article->image, $article->keywords);
		if ($isEditing && !$isImageNotChanging){
			$repoImage->deleteImage($_SESSION["article"]->image);
		}
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
	$html = str_replace("%add-or-modify%", "Modifica di " . $article->title , $html);
	$html = str_replace("%image-required%", "", $html);
	$html = str_replace("%edit-file-msg%", "Se non si inserisce una nuova immagine, rimarrà quella inserita precedentemente.", $html);
	$html = str_replace("%value-titolo%",$article->title, $html);
	$html = str_replace("%value-contenuto%",$article->content, $html);
	$html = str_replace("%value-sommario%",$article->summary, $html);
	$html = str_replace("%value-alt%",$image->alt, $html);
	$html = str_replace("%value-keywords%", $article->keywords, $html);
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
 * @param field valore del campo
 * @param validity booleano che indica la validità del contenuto del campo
 * @param error_substitution valore da cercare in html da sostituire con il messaggio di errore
 * @param error_message messaggio di errore da visualizzare se $valid è falso
 * @param value_content contenuto da inserire nel campo
 */
function handleField($validity, $error_substitution, $error_message, $value_substitution, $field){
	global $html;
	$html = substituteError($validity, $error_substitution, errorElement($error_message), $html);
	$html = str_replace($value_substitution, $field, $html);
}

/**
 * @param field stringa con del testo
 * @return stringa con spazi eccessivi eleiminati, tags html rimossi e caratteri ambigui sostituiti.
 */
function trimHtmlCode($field){
	return trim(htmlentities(strip_tags($field)));
}

/**
 * @param field stringa contentente il valore del campo di testo
 * @param minlen lunghezza minima di $field, può essere NULL se non ce l'ha
 * @param maxlen lunghezza massima di $field, può essere NULL se non ce l'ha
 * @param isNotRequired booleano che indica se $field può essere vuoto oppure no
 * @param hasMarkdown booleano che indica se $field può contenere il markdown strutturale
 * @param hasLanguage booleano che indica se $field può contenere il markdown per la lingua
 * @return indicazione se $field rispetta le indicazioni fornite attraveso i parametri
 */
function validateTextField($field, $minlen, $maxlen, $isNotRequired, $hasMarkdown, $hasLanguage){
	$field  = isset($field) ? $field : "";
	return (validateNoMarkdown($field, $hasMarkdown, $hasLanguage))
			&& validateLength($field, $minlen, $maxlen)
			&& ($isNotRequired || (validateRequired($field)));
}
/**
 * @param keywords input del campo keywords
 * @return indicazione se $keywords è valido
 * Eventualmente in caso $keywords non fosse valido viene stampato un errore
 */
function validateKeywords($keywords) {
	$errorMessageKeywords = "Le parole chiave sono obbligatorie e scritte senza markdown";
	$valid = validateTextField($keywords, NULL, NULL, false, false, false);
	handleField($valid, "%error-keywords%", errorElement($errorMessageKeywords), "%value-keywords%", $keywords);
	return $valid;
}

/**
 * @param contenuto input del campo contenuto
 * @return indicazione se $contenuto è valido
 * Eventualmente in caso $contenuto non fosse valido viene stampato un errore
 */
function validateContent($contenuto) {
	$errorMessageContenuto = "Il corpo dell'articolo deve essere lungo almeno 30 caratteri e scritto secondo le regole del markdown";
	$valid = validateTextField($contenuto, 30, NULL, false, true, true);
	handleField($valid, "%error-contenuto%", errorElement($errorMessageContenuto), "%value-contenuto%", $contenuto);
	return $valid;
}

/**
 * @param titolo input del campo titolo
 * @return indicazione se $titolo è valido
 * Eventualmente in caso $titolo non fosse valido viene stampato un errore
 */
function validateTitle($titolo) {
	$errorMessageTitolo = "Il titolo dell'articolo è: obbligatorio, al massimo 30 caratteri e va scritto senza markdown";
	$valid = validateTextField($titolo, NULL, 30, false, false, true);
	handleField($valid, "%error-titolo%", errorElement($errorMessageTitolo), "%value-titolo%", $titolo);
	return $valid;
}

/**
 * @param sommario input del campo sommario
 * @return indicazione se $sommario è valido
 * Eventualmente in caso $sommario non fosse valido viene stampato un errore
 */
function validateSummary($sommario) {
	$errorMessageSommario = "Il sommario dell'articolo è: obbligatorio, al massimo 90 caratteri e scritto secondo le regole del markdown";
	$valid = validateTextField($sommario, NULL, 90, false, true, true);
	handleField($valid, "%error-sommario%", errorElement($errorMessageSommario), "%value-sommario%", $sommario);
	return $valid;
}

/**
 * @param altImmagine input del campo altImmagine
 * @return indicazione se $altImmagine è valido
 * Eventualmente in caso $altImmagine non fosse valido viene stampato un errore
 */
function validateImageAlt($altImmagine) {
	$errorMessageAlt = "Il testo alternativo non può superare i 70 caratteri o contenere markup";
	$valid = validateTextField($altImmagine, NULL, 70, true, false, false);
	handleField($valid, "%error-alt%", errorElement($errorMessageAlt), "%value-alt%", $altImmagine);
	return $valid;
}

/**
 * @param file input del campo file
 * @return indicazione se $file è valido
 * Eventualmente in caso $file non fosse valido viene stampato un errore
 */
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

/**
 * @param input stringa da valutare
 * @param hasMarkdown booleano che indica se $input può contenere il markdown strutturale
 * @param hasLanguage booleano che indica se $input può contenere il markdown per la lingua
 * @return indicazione se $input rispetta le indicazioni fornite attraveso i parametri
 */
function validateNoMarkdown($input, $hasMarkdown, $hasLanguage){
	$valid = true;
	if($hasMarkdown === false) {
		foreach (array_keys(MarkdownConverter::$standardRules) as $regex){
			$valid = $valid && !preg_match($regex, $input);
		}
	}

	if($hasLanguage === false) {
		foreach(array_keys(MarkdownConverter::$customRules) as $regex) {
			$valid = $valid && !preg_match($regex, $input);
		}
	}
	return $valid;
}

/**
 * @param input stringa da valutare
 * @param hasMarkdown booleano che indica se $input può contenere il markdown strutturale
 * @param hasLanguage booleano che indica se $inpu può contenere il markdown per la lingua
 * @return indicazione se $input rispetta le indicazioni fornite attraveso i parametri
 */
function validateLength($input, $min, $max){
	$input = utf8_decode($input);
	$length = strlen($input);
	return (($min === NULL) || ($min <= $length))
	&& (($max === NULL) || ($length <= $max));

}

/**
 * @param input stringa da valutare
 * @return indicazione se $input esiste e non è vuota
 */
function validateRequired($input){
	return isset($input) && strlen($input) > 0;
}

/**
 * @summary se $valid è vero sostituisce $pattern con $error
 *          dentro $context, altrimenti elimina $pattern
 * @param valid booleano che indica se fare la sostituzione
 * @param pattern stringa da sostiuire
 * @param error stringa con il messagio d'errore
 * @param context stringa in cui far avvenire la sostituzione
 * @return stringa dov'è avvenuta la sostituzione
 */
function substituteError($valid, $pattern, $error, $context){
	if($valid===true){
		return str_replace($pattern, "", $context);
	} else {
		return str_replace($pattern, $error, $context);
	}
}

/**
 * @param message stringa con il messaggio d'errore
 * @return stringa contenente l'elemento d'errore con $message
 */
function errorElement($message){
	return '<strong class="error" role="alert"> - ' . $message . '</strong>';
}
