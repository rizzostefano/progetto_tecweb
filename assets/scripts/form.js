var sumbitFormChecks = [{
					"query": "input[type=text]",
					"validators": [validateRequired, validateLength, validateNoMarkdownButLanguage]
				   }, {
					"query": "input[type=file]",
					"validators": [validateRequired, validateImageFile]
				   }, {
					"query": "textarea",
					"validators": [validateRequired, validateLength]
				   },{
					"query": "#keywords-articolo, #alt-immagine",
					"validators": [validateNoLanguage]
				   }]
				  

var loginFormChecks = [{
						"query": "input[type=text], input[type=password]",
						"validators": [validateRequired, validateLength]
					}]

var regexes = [/(#+)(.*)/,                  // headers
				/\[([^\[]+)\]\(([^\)]+)\)/, // links
				/(\*\*|__)(.*?)\1/,         // bold
				/(\*|_)(.*?)\1/,            // emphasis
				/\~\~(.*?)\~\~/,	        // del
				/\:\"(.*?)\"\:/,            // quote
				/`(.*?)`/,                  // inline code
				/\n\*(.*)/,                 // ul lists
				/\n[0-9]+\.(.*)/,	        // ol lists
				/\n(&gt;|\>)(.*)/,          // blockquotea
				/\n-{5,}/,                  // horiziontal rule
				/\n([^\n]+)\n/,             // add paragraphs
				/(\(!)(.+)(\))/]            // image

/**
 * @summary funzione che chiama $callback quando document è caricato
 * @param callback, funzione senza parametri
 */
function ready(callback){
    // in case the document is already rendered
    if (document.readyState!='loading') callback();
    else document.addEventListener('DOMContentLoaded', callback);
}

ready(function(){document.onsubmit = validateForm;})

/**
 * @summary funzione che effettua il controllo e visualizza i messaggi d'errore
 *          sui campi del form, adattandosi al tipo di form: invio oppure login.
 * @param event, evento che scatena l'avvio della funzione. Non usato
 * @return indicazione se il form è valido oppure no.
 */
function validateForm (event){
	deleteErrorMessages();
	var form, checks, isLogin = document.getElementById('login') !== null;
	form = isLogin ? document.forms['login'] : document.forms['invio'];
	checks = isLogin ? loginFormChecks : sumbitFormChecks;
	return checks.map((check) => executeCheck(check, form)).reduce((previous, current) => previous && current);
	 
}

/**
 * @summary funzione che prende in input un controllo e lo applica a tutti i campi del form
 *          consoni e ne mostra gli eventuali errori
 * @param check, oggetto con i seguenti campi
 *             check.query:      query css che individua quali campi vanno controllati 
 *             check.validators: lista di funzioni che rappresentano i controlli da effettuare
 *                               per i campi individuati da $check.query.
 * @return indicazione se tutti i campi trovati da $check.query rispettano i controlli di $check.validators
 */
function executeCheck(check, form){
	const inputs = Array.from(form.querySelectorAll(check.query));
	const wrong_inputs = inputs.filter((input) => !checkInput(input, check.validators));
	wrong_inputs.forEach(input => showErrorMessage(input));
	return wrong_inputs.length === 0;
}

/**
 * @summary funzione che mostra il messagio d'errore, se non già presente pensato per $input
 * @param $input, campo di cui mostrarne l'errore
 */
function showErrorMessage(input){
	var label = document.querySelector("label[for=%s]".replace(/%s/, input.id));
	if(label !== null && label.getElementsByClassName("error").length === 0) {
		var error = document.createElement("strong");
		error.className = "error";
		error.setAttribute("role", "alert");
		error.appendChild(document.createTextNode(" - " +label.getAttribute("data-error-msg")));
		label.appendChild(error);
	}
}

/**
 * @summary funzione che elimina tutti i messaggi d'errore presenti nel form
 */
function deleteErrorMessages(){
	var errors = document.getElementsByClassName("error");
	while(0 < errors.length) errors[0].remove();
}

/**
 * @summary funzione che esegue tutti i controlli su uno specifico input
 * @param input, campo da controllare
 * @param validators, lista di funzioni che dicono se $input è valido oppure no
 * @return indicazione se $input è valido per tutti i $validators 
 */
function checkInput(input, validators){
	return validators.reduce((previousValidity, validator) => previousValidity && validator(input),true);
}

/**
 * @param input campo da controllare
 * @return indicazione se $input non contiene nessuna delle regex di $regexes
 */
function validateNoMarkdownButLanguage(input){
	return regexes.map( re => !re.test(input.value))
				  .reduce((previuos, current) => previuos && current, true);
}

/**
 * @param input campo da controllare
 * @return indicazione se $input rispetta la lunghezza indicata negli attributi minlength, maxlength
 */
function validateLength(input){
	const min = input.getAttribute("minlength");
	const max = input.getAttribute("maxlength");
	return ((min === null) || (input.value.length >= min))
		   && ((max === null) || (input.value.length <= max));
}

/**
 * @param input campo da controllare
 * @return indicazione se l'eventuale file inserito
 *         in $input è un immagine sotto il MB
 */
function validateImageFile(input) {
	const files = input.files;
	return ((files.length === 1)
     	      && files[0].type.startsWith("image/")
		   	  && files[0].size < 1000000) 
		   || (files.length === 0);
}

/**
 * @param input campo da controllare
 * @return indicazione se $input l'obbligatorietà indicata con l'attributo required
 */
function validateRequired(input) {
	return input.hasAttribute("required")? input.value != "" : true;
}

/**
 * @param input campo da controllare
 * @return indicazione se l'input contiene stringe per specificare il linguaggio
 */
function validateNoLanguage(input){
	return !/(\(?)(.+)(\))/.test(input.value);
}
