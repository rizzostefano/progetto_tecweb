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
					"query": "#keywords-articolo",
					"validators": [validateKeywords, validateNoLanguage]
				   },{
					"query": "#alt-immagine",
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

function ready(callback){
    // in case the document is already rendered
    if (document.readyState!='loading') callback();
    else document.addEventListener('DOMContentLoaded', callback);
}

ready(function(){document.onsubmit = validateForm;})

function validateForm (event){
	deleteErrorMessages();
	var form, checks, isLogin = document.getElementById('login') !== null;
	form = isLogin ? document.forms['login'] : document.forms['invio'];
	checks = isLogin ? loginFormChecks : sumbitFormChecks;
	return checks.map((check) => executeCheck(check, form)).reduce((previous, current) => previous && current);
	 
}

function executeCheck(check, form){
	const inputs = Array.from(form.querySelectorAll(check.query));
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
	return ((files.length === 1)
     	      && files[0].type.startsWith("image/")
		   	  && files[0].size < 1000000) 
		   || (files.length === 0);
}

function validateRequired(input) {
	return input.hasAttribute("required")? input.value != "" : true;
}

function validateKeywords(input){
	return  /^(?:\w+,)*\w+$/.test(input.value);
}

function validateNoLanguage(input){
	return !/(\(?)(.+)(\))/.test(input.value);
}
