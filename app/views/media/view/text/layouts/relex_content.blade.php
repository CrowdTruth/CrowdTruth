@section('dynamicButton')
	<a href='#' class="btn btn-success relexFiltersButton"><i class="fa fa-filter fa-fw"></i>Filter</a>
@stop

<div class='relexFiltersContainer' style='display:none'>
<table class='table table-striped table-condensed relexFiltersControls'>
	<tbody>
			@foreach ($lines[0]['properties'] as $filterKey => $filterValue)
				@if($filterKey == "sentenceWordCount")
					<?php continue; ?>
				@endif

				<tr>
					<td>{{ $filterKey }}</td>
					<td>
						<div class="btn-group" id='{{ $filterKey }}'>
						  <button type="button" class="btn btn-info relexOn">With</button>
						  <button type="button" class="btn btn-info relexOff">Without</button>
						  <button type="button" class="btn btn-success active relexNone">Not Applied</button>
						</div>
					</td>
				</tr>

			@endforeach
	</tbody>
</table>
</div>

		{{ Form::open(array('url' => 'media/create?fromURI=' . $entity->_id)) }}
		<div class='table-responsive'>
			<table class='table table-striped table-condensed relexContent'>
				<thead>
					<tr>
						<th class="filter-false"><input type="checkbox" class="check_filtered_sentences" /></th>						
						<th>Line Nr.</th>
						<th class="filter-false">RElex-relation</th>
						<th>Term 1</th>
						<th>B1</th>
						<th>E1</th>
						<th>Term 2</th>
						<th>B2</th>
						<th>E2</th>
						<th>Sentence</th>
						<th>Sentence word count</th>

						@foreach ($lines[0]['properties'] as $filterKey => $filterValue)
							@if($filterKey == "sentenceWordCount")
								<?php continue; ?>
							@endif

							<th class='relexFilters {{  $filterKey }}'>
								relationInSentence
								<input type='text' name='{{  $filterKey }}' />
							</th>

						@endforeach
					</tr>								
				</thead>
				<tbody>
					@foreach ($lines as $lineNumber => $lineValue)
					<tr>
						<td><input type="checkbox" name="line_{{ $lineNumber }}" /></td>						
						<td>{{ $lineNumber }}</td>
						<td>{{ $lineValue['relation']['noPrefix'] }}</td>
						<td>{{ $lineValue['terms']['first']['text'] }}</td>
						<td>{{ $lineValue['terms']['first']['startIndex'] }}</td>
						<td>{{ $lineValue['terms']['first']['endIndex'] }}</td>
						<td>{{ $lineValue['terms']['second']['text'] }}</td>
						<td>{{ $lineValue['terms']['second']['startIndex'] }}</td>
						<td>{{ $lineValue['terms']['second']['endIndex'] }}</td>
						<td class='sentence'>{{ $lineValue['sentence']['text'] }}</td>
						<td>{{ $lineValue['properties']['sentenceWordCount'] }}</td>

						<td class='relexFilters'>{{ $lineValue['properties']['relationInSentence'] }}</td>
						<td class='relexFilters'>{{ $lineValue['properties']['relationOutsideTerms'] }}</td>
						<td class='relexFilters'>{{ $lineValue['properties']['relationBetweenTerms'] }}</td>
						<td class='relexFilters'>{{ $lineValue['properties']['semicolonBetweenTerms'] }}</td>
						<td class='relexFilters'>{{ $lineValue['properties']['commaSeparatedTerms'] }}</td>
						<td class='relexFilters'>{{ $lineValue['properties']['parenthesisAroundTerms'] }}</td>
						<td class='relexFilters'>{{ $lineValue['properties']['overlappingTerms'] }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		<input type="submit" value="Submit" class='relexSubmit'>
		{{ Form::close() }}				

@section('extra_js')
	{{ javascript_include_tag('jquery.tablesorter.min.js') }}
	{{ javascript_include_tag('jquery.tablesorter.widgets.min.js') }}

	<script type="text/javascript">
		$(document).ready(function () {
			$('table').bind('filterEnd', function(e){
				console.log($.tablesorter.getFilters( this ));

				$('.tablesorter-headerRow th.relexFilters').each(function (){
					// console.log($(".tablesorter-filter input[data-column='" + $(this).attr('data-column') + "']"));
					$("input.tablesorter-filter[data-column='" + $(this).attr('data-column') + "']").parent().hide();
				});

				$('.relexContent input:checkbox').prop('checked', false);
				
			}).tablesorter({
					theme : 'bootstrap',
					stringTo: "max",

					// initialize zebra and filter widgets
					widgets: ["filter"],

					widgetOptions: {
					// include child row content while filtering, if true
					filter_childRows  : false,
					// class name applied to filter row and each input
					filter_cssFilter  : 'tablesorter-filter',
					// search from beginning
					filter_startsWith : false,
					// Set this option to false to make the searches case sensitive 
					filter_ignoreCase : true
				}
			});

            $(".relexFiltersButton").popover({
                trigger: "manual",
                html: true,
                'animation' : false,
                'content' : function(){ return $('.relexFiltersContainer').html() },
                'placement' : 'bottom',
                 template: '<div class="popover relexFiltersPopover"><div class="arrow"></div><div class="popover-content"></div></div>'
            }).on("mouseenter", function () {
                var _this = this;
                $(this).popover("show");
                $(".popover").on("mouseleave", function () {
                    $(_this).popover('hide');
                });
            }).on("mouseleave", function () {
                var _this = this;
                setTimeout(function () {
                    if (!$(".popover:hover").length) {
                        $(_this).popover("hide");
                    }
                }, 100);
            });   			

			$('body').on('click', '.relexFiltersControls button', function(e) {
				var clickedrelexFilter = $(this).parent().attr('id');
				$('.relexFiltersControls #' + clickedrelexFilter + ' button').removeClass('active');

				var dataColumn = $('th.' + clickedrelexFilter).attr('data-column');
				var relexInputFilter = $(".tablesorter-filter[data-column='" + dataColumn + "']");
				var relexInputText = $('th.' + clickedrelexFilter + ' input');

				$('.relexFiltersControls #' + clickedrelexFilter + ' button').removeClass('btn-success active').addClass('btn-info');

				if($(this).hasClass('relexOn')){
					$(relexInputFilter).add(relexInputText).val('1');
				} else if($(this).hasClass('relexOff')){
					$(relexInputFilter).add(relexInputText).val('0');
				} else if($(this).hasClass('relexNone')){
					$(relexInputFilter).add(relexInputText).val('');
				}

				$('.relexFiltersControls #' + clickedrelexFilter + ' .' + $(this).attr('class').split(' ').join('.')).removeClass('btn-info').addClass('btn-success active');

				console.log(relexInputText.val());

			    $('table').trigger('search', false);
			});

			$('input.check_filtered_sentences').click(function(e){
			    $('.relexContent td input:checkbox').filter(':visible').prop('checked',this.checked);
			});

			$('a.check_filtered_sentences').click(function(e){
			    $('.relexContent input:checkbox').filter(':visible').prop('checked', true);
			});

			$('#create_new_relexDocument').click(function(e){
			    $('input.relexSubmit').click();
			});

		});
	</script>
@stop		