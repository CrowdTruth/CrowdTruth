/**
 * Generate DIV for displaying 'json' processor inputs.
 */
function jsonPreprocessor(propId) {
	htmlText = '' + 
		'<div class="row">' +
		'	<label for="column" class="col-md-3 control-label">Column:</label>' + 
		'	<div class="col-xs-3">' +
				getColumnsSelector(propId + '_usecol') +
		'	</div>' +
		'</div>';
	return htmlText;
}
