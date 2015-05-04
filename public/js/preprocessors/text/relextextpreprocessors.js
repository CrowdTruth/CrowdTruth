/**
 * Generate DIV for displaying 'Relation in sentence' processor inputs.
 */
function relationInSentence(propId) {
	htmlText = '' + 
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Relation:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_relation') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Sentence:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_sentence') +
	'	</div>' +
	'</div>';
	return htmlText;
}

/**
 * Generate DIV for displaying 'Relation outside terms' processor inputs.
 */
function relationOutsideTerms(propId) {
	htmlText = '' + 
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Relation:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_relation') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Sentence:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_sentence') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Start index first term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_startTerm1') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Start index second term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_startTerm2') +
	'	</div>' +
	'</div>';
	return htmlText;
}

/**
 * Generate DIV for displaying 'Relation between terms' processor inputs.
 */
function relationBetweenTerms(propId) {
	htmlText = '' + 
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Relation:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_relation') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Sentence:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_sentence') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Start index first term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_startTerm1') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">End index first term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_endTerm1') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Start index second term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_startTerm2') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">End index second term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_endTerm2') +
	'	</div>' +
	'</div>';
	return htmlText;
}

/**
 * Generate DIV for displaying 'Has semicolon' processor inputs.
 */
function hasSemicolon(propId) {
	htmlText = '' + 
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Sentence:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_sentence') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Start index first term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_startTerm1') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">End index first term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_endTerm1') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Start index second term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_startTerm2') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">End index second term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_endTerm2') +
	'	</div>' +
	'</div>';
	return htmlText;
}

/**
 * Generate DIV for displaying 'Has comma' processor inputs.
 */
function hasComma(propId) {
	htmlText = '' + 
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Relation:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_relation') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Sentence:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_sentence') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">First term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_term1') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Start index first term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_startTerm1') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">End index first term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_endTerm1') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Second term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_term2') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Start index second term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_startTerm2') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">End index second term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_endTerm2') +
	'	</div>' +
	'</div>';
	return htmlText;
}

/**
 * Generate DIV for displaying 'Has parenthesis' processor inputs.
 */
function hasParenthesis(propId) {
	htmlText = '' + 
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Sentence:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_sentence') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">First term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_term1') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Start index first term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_startTerm1') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Second term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_term2') +
	'	</div>' +
	'</div>' + 
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Start index second term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_startTerm2') +
	'	</div>' +
	'</div>';
	return htmlText;
}

/**
 * Generate DIV for displaying 'Has overlapping terms' processor inputs.
 */
function hasOverlappingTerms(propId) {
	htmlText = '' + 
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Start index first term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_startTerm1') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">End index first term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_endTerm1') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">Start index second term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_startTerm2') +
	'	</div>' +
	'</div>' +
	'<div class="row">' +
	'	<label for="column" class="col-md-3 control-label">End index second term:</label>' + 
	'	<div class="col-xs-3">' +
			getColumnsSelector(propId + '_endTerm2') +
	'	</div>' +
	'</div>';
	return htmlText;
}
