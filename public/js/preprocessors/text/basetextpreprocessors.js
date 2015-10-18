/**
 * Generate DIV for displaying 'As Text' processor inputs.
 */
function textPreprocessor(propId) {
	htmlText = '' + 
		'<div class="row">' +
		'	<label for="column" class="col-md-3 control-label">Column:</label>' + 
		'	<div class="col-xs-6">' +
				getColumnsSelector(propId + '_usecol') +
		'	</div>' +
		'</div>';
	return htmlText;
}

/**
 * Generate DIV for displaying 'As Number' processor inputs.
 */
function numberPreprocessor(propId) {
	htmlText = '' + 
		'<div class="row">' +
		'	<label for="column" class="col-md-3 control-label">Column:</label>' + 
		'	<div class="col-xs-6">' +
				getColumnsSelector(propId + '_usecol') +
		'	</div>' +
		'</div>';
	return htmlText;
}

/**
 * Generate DIV for displaying 'Regular Expression' processor inputs.
 */
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

/**
 * Generate DIV for displaying 'Word count' processor inputs.
 */
function wordcountPreprocessor(propId) {
	htmlText = '<div class="row">' +
		'<label for="column" class="col-md-3 control-label">Column:</label>' + 
		'<div class="col-xs-3">' +
		getColumnsSelector(propId + '_usecol') + '</div></div>' + 
		'';
	return htmlText;
}

/**
 * Generate DIV for displaying 'String length' processor inputs.
 */
function stringlengthPreprocessor(propId) {
	htmlText = '<div class="row">' +
		'<label for="column" class="col-md-3 control-label">Column:</label>' + 
		'<div class="col-xs-3">' +
		getColumnsSelector(propId + '_usecol') + '</div></div>' + 
		'';
	return htmlText;
}

/**
 * Generate DIV for displaying 'Term difference' processor inputs.
 */
function termdifferencePreprocessor(propId) {
	htmlText = '' + 
		'Compute the Levenshtein-Distance between the terms in the following two columns. Will return -1 if one of the argument strings is longer than the limit of 255 characters. <br><br>' + 
		'<div class="row">' +
		'<label for="_col1" class="col-md-3 control-label">First term:</label>' + 
		'<div class="col-xs-3">' +
		getColumnsSelector(propId + '_col1') + '</div></div>' + 
		'<div class="row">' +
		'<label for="_col2" class="col-md-3 control-label">Second term:</label>' + 
		'<div class="col-xs-3">' +
		getColumnsSelector(propId + '_col2') + '</div></div>' + 
		'';
	return htmlText;
}

/**
 * Generate DIV for displaying 'Replace term' processor inputs.
 */
function replaceTermPreprocessor(propId) {
	htmlText = '' +		
		'<div class="row">' +
		'<label for="column" class="col-md-3 control-label">In term:</label>' + 
		'<div class="col-xs-3">' +
		getPropertySelector(propId + '_repFrom') + '</div></div>' + 
		'<div class="row">' +
		'<label for="column" class="col-md-3 control-label">Replace Terms (Group):</label>' + 
		'<div class="col-xs-3">' +
		getGroupSelector(propId + '_repBy') + '</div></div>' + 
		'';
	return htmlText;
}
