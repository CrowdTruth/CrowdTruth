/*
function textPreprocessor(propId) {
	htmlText = '' + 
		'<div class="row">' +
		'	<label for="column" class="col-md-3 control-label">Column:</label>' + 
		'	<div class="col-xs-3">' +
				getColumnsSelector(propId + '_usecol') +
		'	</div>' +
		'</div>';
	htmlText = 'DO THIS FUNCTION!' + htmlText;
	return htmlText;
}
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
