function textToType(propId) {
	htmlText = '' +
		'Use column: <br>' + 
		getColumnsSelector(propId + '_usecol') + '<br>' + 
		'Treat as: <br>' + 
    	'<select name="' + propId + '_as" id="' + propId + '_as">' +
    	'  <option value="text"> Text </option>' +
    	'  <option value="number"> Number </option>' +
    	' </select>' + 
		'';
	return htmlText;
}

function regexPreprocessor(propId) {
	htmlText = '' + 
		'Apply regular expression to column: <br>' + 
		getColumnsSelector(propId + '_usecol') + '<br>' + 
		'Look for: <br>' + 
		'<input type="text" name="' + propId + '_regex" id="' + propId + '_regex"/>' + 
		'<input type="checkbox" name="' + propId + '_regex_split" id="' + propId + '_regex_split"/><small>Multiple expressions</small><br>' +
		'Replace by: <br>' + 
		'<input type="text" name="' + propId + '_replace" id="' + propId + '_replace"/>' + 
		'<input type="checkbox" name="' + propId + '_replace_split" id="' + propId + '_replace_split"/><small>Multiple replacements</small><br>' +
		'<input type="checkbox" name="' + propId + '_uppercase" id="' + propId + '_uppercase"/>Make upper case<br>' +
		'<input type="checkbox" name="' + propId + '_lowercase" id="' + propId + '_lowercase"/>Make lower case<br>' +
		'<br>' + 
		'Example: <br>' + 
		'Look for: /^/,/$/ (Multiple expressions)<br>' + 
		'Replace by: [,] (Multiple replacements)<br>' + 
		'<br>' + 
		'See: http://php.net/manual/en/function.preg-replace.php' + 
		'';
	return htmlText;
}

function wordcountPreprocessor(propId) {
	htmlText = '' + 
		'Perform word count of column: <br>' + 
		getColumnsSelector(propId + '_usecol') + '<br>' + 
		'';
	return htmlText;
}

function stringlengthPreprocessor(propId) {
	htmlText = '' + 
		'Perform string length of column: <br>' + 
		getColumnsSelector(propId + '_usecol') + '<br>' + 
		'';
	return htmlText;
}

function termdifferencePreprocessor(propId) {
	htmlText = '' + 
		'This function returns the Levenshtein-Distance between the two terms or -1, if one of the argument strings is longer than the limit of 255 characters. <br>' + 
		getColumnsSelector(propId + '_col1') + ' and ' + getColumnsSelector(propId + '_col2') + '<br>' + 
		'';
	return htmlText;
}

function replaceTermPreprocessor(propId) {
	htmlText = '' + 
		'Replace terms: <br>' + 
		'In term:<br>' + 
		getPropertySelector(propId + '_repFrom') + '<br>' + 
		'Replace term<br>' + 
		getGroupSelector(propId + '_repBy') + '<br>' + 
		'';
	return htmlText;
}
