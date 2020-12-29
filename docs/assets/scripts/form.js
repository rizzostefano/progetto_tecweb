var progressiveId = 0;
const entryImage =
					   '<div class="entry">'
					 +   '<label for="file-immagine-%s" data-error-msg="Il file va inserito obbligatoriamente e deve essere un immagine inferiore al megabyte">File immagine</label>'
				 	 +	 '<input type="file" id="file-immagine-%s"'
					 +      'required="required"'
					 +  	'accept="image/*"'
					 +      'name="file-immagine-%s"/>'
				 	 +	 '<label for="alt-immagine-%s" data-error-msg="Il testo alternativo non può superare i 70 caratteri o contenere markup">Testo alternativo immagine</label>'
				 	 +	 '<input type="text" id="alt-immagine-%s" name="alt-immagine-%s"'
                 	 +	   'maxlength="70"'
                 	 +	   'placeholder="Inserisci qui il testo alternativo per l\'immagine"'
                 	 +	   'aria-label="Campo per l\'inserimento per il testo alternativo per l\'immagine"/>'
				 	 +	 '<input type="button" class="remove" value="Rimuovi immagine"/>'
					 + '</div>';


const entryAttribute = 
					  '<div class="entry">'
					+   '<label for="nome-attributo-%s" data-error-msg="Il nome non può: essere vuoto, superare i 50 caratteri e contenere il markup" >Nome Attributo</label>'
					+   '<input type="text" id="nome-attributo-%s" name="nome-attributo-%s"'
                    +  	  'minlength="1" maxlength="50"'
                	+     'placeholder="Inserisci qui il nome dell\'attributo"'
					+	  'required="required"'
                	+     'aria-required="true"'
                	+     'aria-label="Campo per nome dell\'attributo"/>'
					+   '<label for="descrizione-attributo-%s" data-error-msg="La descrizione non può essere vuota o superare i 200 caratteri">Descrizione Attributo</label>'
					+   '<textarea id="descrizione-attributo-%s" name="descrizione-attributo-%s"'
                    +     'maxlength="200"'
                	+     'placeholder="Inserisci qui una descrizione per l\'attributo"'
					+     'requied="required"'
                	+     'aria-required="true"'
                	+     'aria-label="Campo per l\'inserimento della descrizione per l\'attributo"/></textarea>'
					+   '<input type="button" class="remove" value="Rimuovi attributo"/>'
					+ '</div>'

var formChecks = [ {
					"query": "input[type=text]",
					"validators": [validateRequired, validateLength, validateNoMarkdownButLanguage]
				   }, {
					"query": "input[type=file]",
					"validators": [validateRequired, validateImageFile]
				   }, {
					"query": "textarea, input[type=password]",
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
function ready(callback){
    // in case the document is already rendered
    if (document.readyState!='loading') callback();
    // modern browsers
    else if (document.addEventListener) document.addEventListener('DOMContentLoaded', callback);
    // IE <= 8
    else document.attachEvent('onreadystatechange', function(){
        if (document.readyState=='complete') callback();
    });
}

ready(function(){
	document.getElementById('invio').onclick = function (event){
			const t = event.target;
			if (t.className=='remove') t.closest('.entry').remove();
			else if (t.id=='aggiungi-immagine') addEntry('immagini', entryImage, progressiveId++);
			else if (t.id=='aggiungi-attributo') addEntry('attributi', entryAttribute, progressiveId++);
	}
	document.onsubmit = validateForm;
})

function validateForm (event){
	deleteErrorMessages();
	return formChecks.map((check) => executeCheck(check))
					 .reduce((previous, current) => previous && current);
}

function executeCheck(check){
	// From nodelist to array
	const inputs = Array.apply(null, document.forms['invio'].querySelectorAll(check.query));
	const wrong_inputs = inputs.filter((input) => !checkInput(input, check.validators));
	wrong_inputs.forEach(input => showErrorMessage(input));
	return wrong_inputs.length === 0;

}

function showErrorMessage(input){
	var label = document.querySelector("label[for=%s]".replace(/%s/, input.id));
	if(label !== null && label.getElementsByClassName("error").length === 0) {
		var error = document.createElement("strong");
		error.className = "error";
		error.appendChild(document.createTextNode(" - " +label.getAttribute("data-error-msg")));
		label.appendChild(error);
	}
		
}

function deleteErrorMessages(){
	var errors = document.getElementsByClassName("error");
	while(0 < errors.length) errors[0].remove();
}

function checkInput(input, validators){
	return validators.reduce((previousValidity, validator) => previousValidity && validator(input),true);
}

function validateNoMarkdownButLanguage(input){
	return regexes.map( re => !re.test(input.value))
				  .reduce((previuos, current) => previuos && current, true);
}

function validateLength(input){
	const min = input.getAttribute("minlength");
	const max = input.getAttribute("maxlength");
	return ((min === null) || (input.value.length >= min))
		   && ((max === null) || (input.value.length <= max));
}

function validateImageFile(input) {
	const files = input.files;
	return (files.length === 1)
           && files[0].type.startsWith("image/")
           && files[0].size < 1000000; 
}

function validateRequired(input) {
	return input.hasAttribute("required")? input.value != "" : true;
}


function addEntry (framesetId, entryStructure, entryNumber) {
	var frameset = document.getElementById(framesetId);
	frameset.innerHTML += entryStructure.replace(/%s/g, entryNumber)
}


