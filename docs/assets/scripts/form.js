var progressiveId = 0;
const entryImage =
					   '<div class="entry">'
					 +   '<label for="file-immagine-%s" data-error-msg="Il file va inserito, e con estensione .jpg o .png">File immagine</label>'
				 	 +	 '<input type="file" id="file-immagine-%s" required="required" name="file-immagine-%s"/>'
				 	 +	 '<label for="alt-immagine-%s" data-error-msg="Il testo alternativo non può essere vuoto o superare i 70 caratteri">Testo alternativo immagine</label>'
				 	 +	 '<input type="text" id="alt-immagine-%s" name="alt-immagine-%s"'
                 	 +	   'minlength="1" maxlength="70"'
                 	 +	   'placeholder="Inserisci qui il testo alternativo per l\'immagine"'
                 	 +	   'aria-label="Campo per l\'inserimento per il testo alternativo per l\'immagine"/>'
				 	 +	 '<input type="button" class="remove" value="Rimuovi immagine"/>'
					 + '</div>';


const entryAttribute = 
					  '<div class="entry">'
					+   '<label for="nome-attributo-%s" data-error-msg="Il nome non può essere vuoto o superare i 50 caratteri" >Nome Attributo</label>'
					+   '<input type="text" id="nome-attributo-%s" name="nome-attributo-%s"'
                    +  	  'minlength="1" maxlength="50"'
                	+     'placeholder="Inserisci qui il nome dell\'attributo"'
					+	  'required="required"'
                	+     'aria-required="true"'
                	+     'aria-label="Campo per nome dell\'attributo"/>'
					+   '<label for="descrizione-attributo-%s" data-error-msg="La descrizione non può essere vuota o superare i 200 caratteri">Descrizione Attributo</label>'
					+   '<textarea id="descrizione-attributo-%s" name="descrizione-attributo-%s"'
                    +     'minlength="1" maxlength="200"'
                	+     'placeholder="Inserisci qui una descrizione per l\'attributo"'
					+     'required="required"'
                	+     'aria-required="true"'
                	+     'aria-label="Campo per l\'inserimento della descrizione per l\'attributo"/></textarea>'
					+   '<input type="button" class="remove" value="Rimuovi attributo"/>'
					+ '</div>' 
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
			var t = event.target;
			if (t.className=='remove') t.closest('.entry').remove();
			else if (t.id=='aggiungi-immagine') addEntry('immagini', entryImage, progressiveId++);
			else if (t.id=='aggiungi-attributo') addEntry('attributi', entryAttribute, progressiveId++);
	}
	document.onsubmit = validateForm;
})

function validateForm (event){
	deleteErrors();
	var inputs = document.forms['invio'].querySelectorAll("textarea, input[type=text], input[type=file], input[type=email], input[type=password]");
	var valid = true;
	for(var i = 0; i < inputs.length; ++i){
		if(!validateLength(inputs[i])){
			showError(inputs[i]);
			valid = false;
		}
	}
	return valid;
}

function showError(input){
	var label = document.querySelector("label[for=%s]".replace(/%s/, input.id));
	if(label !== null && label.getElementsByClassName("error").length === 0) {
		var error = document.createElement("strong");
		error.className = "error";
		error.appendChild(document.createTextNode(" - " +label.getAttribute("data-error-msg")));
		label.appendChild(error);
	}
		
}

function deleteErrors(){
	var errors = document.getElementsByClassName("error");
	while(0 < errors.length) errors[0].remove();
}

function validateLength(input){
	var min = input.getAttribute("minlength");
	var max = input.getAttribute("maxlength");
	return ((min === null) || (input.value.length >= min))
		   && ((max === null) || (input.value.length <= max));
}


function addEntry (framesetId, entryStructure, entryNumber) {
	var frameset = document.getElementById(framesetId);
	frameset.innerHTML += entryStructure.replace(/%s/g, entryNumber)
}


