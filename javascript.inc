<?php
// DESC: JavaScript values, functions, and classes
//
// values
//
	$js_RCM = <<<EOV
var selectRCM = new Array(
	new Option('Relate to','RELATE'),
	new Option('Copy to','COPY'),
	new Option('Move to','MOVE')
);

EOV;
	$js_check = <<<EOV

// check for valid input

var_digits = "0123456789";

function checkValidChars(field,type,alertMessage,fieldname) {
   
	var input = field.value;
	alertMessage = fieldname +" -  "+ alertMessage; 

	flag=0;
	for (var i=0;i<input.length;i++){
		var tst=input.substring(i,i+1);
		if (type.indexOf(tst) < 0){
			flag++;
		}
	}
	if (flag!=0){
		alert(alertMessage+"\n("+flag+" bad characters)");
		field.focus();	
		return false;
	}
	return true;
}

function isInteger(field,fieldname) {
	var alertMessage = "must be an integer only!";
	return checkValidChars(field,var_digits,alertMessage,fieldname);
}
EOV;

// shared menu choices
	$js_loadSelect = <<<EOV

function loadSelect (select_thing, select_choices) {
	if (! select_thing.options[0]) {
		select_thing.options[0] = new Option(' ','none');
		for (var i = 0; i < select_choices.length; i++) {
			select_thing.options[i+1] = new Option (
				select_choices[i].text,
				select_choices[i].value); }}
	return true;
};
EOV;

//
// functions
	function javascript($q) {
		return "<script language=\"javascript\"><!--{\n$q\n//}--></script>\n";
	}

	// create a javascript validate function wrapper
	function js_validate($q, $name="validate") {
		return "\nfunction $name() {\n".$q."	return true;}\n";
	}
	// validate stanzas
	function js_isInteger($doc_form_input, $field_name) {
		return "if (! isInteger($doc_form_input,\"$field_name\")) "
		. "{return false;}\n";
	}
	// tag ids should not have []'s
	function js_debracket ($q) {
		return preg_replace("/[\[\]]/","",$q);
	}
	// share menu select
	function js_select($select_name, $doc_form_select, $selectArray) {
		return "<select id=".js_debracket($select_name)
			." name=\"$select_name\" onClick=\"return loadSelect("
			.js_debracket($doc_form_select).
			",$selectArray);\" > </select>\n";
	}
?>
