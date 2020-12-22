const entryImage = 
					   '<div class="entry">'
					 +   '<label for="file-immagine-%s" data-msg-required="Per favore, inserisci il file %s">File immagine %s</label>'
				 	 +	 '<input type="file" id="file-immagine-%s" name="file-immagine-%s"/>'
				 	 +	 '<label for="alt-immagine-%s" data-msg-required="Per favore, inserisci un testo alternativo per l\'immagine %s">Testo alternativo immagine %s</label>'
				 	 +	 '<input type="text" id="alt-immagine-%s" name="alt-immagine-%s"'
                 	 +	   'minlength="1" maxlength="70"'
                 	 +	   'placeholder="Inserisci qui il testo alternativo per l\'immagine %s"'
                 	 +	   'aria-label="Campo per l\'inserimento per il testo alternativo per l\'immagine %s"/>'
				 	 +	 '<input type="button" class="remove" value="Rimuovi immagine %s"/>'
					 + '</div>';


const entryAttribute = 
					  '<div class="entry">'
					+   '<label for="nome-attributo-%s" data-msg-required="Per favore, inserisci un nome per questo attributo" >Nome Attributo %s</label>'
					+   '<input type="text" id="nome-attributo-%s" name="nome-attributo-%s"'
                    +  	  'minlength="1"'
                	+     'placeholder="Inserisci qui il nome dell\'attributo %s"'
					+	  'required="required"'
                	+     'aria-required="true"'
                	+     'aria-label="Campo per nome dell\'attributo %s"/>'
					+   '<label for="descrizione-attributo-%s" lang data-msg-required="Per favore, inserisci una descrizione per l\'attributo %s" >Descrizione Attributo %s</label>'
					+   '<textarea id="descrizione-attributo-%s" name="descrizione-attributo-%"'
                    +     'minlength="1"'
                	+     'placeholder="Inserisci qui una descrizione per l\'attributo %s"'
					+     'required="required"'
                	+     'aria-required="true"'
                	+     'aria-label="Campo per l\'inserimento della descrizione per l\'attributo %s"/></textarea>'
					+   '<input type="button" id="rimuovi-attributo-%s" class="remove" value="Rimuovi attributo %s"/>'
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
			console.log(t.id)
			if (t.className=='remove') t.closest('.entry').remove();
			else if (t.id=='aggiungi-immagine') addEntry('immagini', entryImage, 0);
			else if (t.id=='aggiungi-attributo') addEntry('attributi', entryAttribute, 0);
	} 
})

function addEntry (framesetId, entryStructure, entryNumber) {
	var frameset = document.getElementById(framesetId);
	frameset.innerHTML += entryStructure.replace(/%s/g, entryNumber)
}


