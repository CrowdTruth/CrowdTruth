@section('dynamicButton')
	<a href='#' class="btn btn-success changFiltersButton"><i class="fa fa-filter fa-fw"></i>Filter</a>
@stop

<div class='changFiltersContainer' style='display:none'>
<table class='table table-striped table-condensed changFiltersControls'>
	<tbody>
			@foreach ($lines[0]['filters'] as $filterKey => $filterValue)
				@if($filterKey == "sentenceWordCount")
					<?php continue; ?>
				@endif

				<tr>
					<td>{{ $filterKey }}</td>
					<td>
						<div class="btn-group" id='{{ $filterKey }}'>
						  <button type="button" class="btn btn-info changOn">With</button>
						  <button type="button" class="btn btn-info changOff">Without</button>
						  <button type="button" class="btn btn-success active changNone">Not Applied</button>
						</div>
					</td>
				</tr>

			@endforeach
	</tbody>
</table>
</div>

		{{ Form::open(array('url' => 'files/create?fromURI=' . $entity->_id)) }}
		<div class='table-responsive'>
			<table class='table table-striped table-condensed changContent'>
				<thead>
					<tr>
						<th class="filter-false"><input type="checkbox" class="check_filtered_sentences" /></th>						
						<th>Line Nr.</th>
						<th class="filter-false">TWrex-relation</th>
						<th>Term 1</th>
						<th>B1</th>
						<th>E1</th>
						<th>Term 2</th>
						<th>B2</th>
						<th>E2</th>
						<th>Sentence</th>
						<th>Sentence word count</th>

						@foreach ($lines[0]['filters'] as $filterKey => $filterValue)
							@if($filterKey == "sentenceWordCount")
								<?php continue; ?>
							@endif

							<th class='changFilters {{  $filterKey }}'>
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
						<td>{{ $lineValue['filters']['sentenceWordCount'] }}</td>

						<td class='changFilters'>{{ $lineValue['filters']['relationInSentence'] }}</td>
						<td class='changFilters'>{{ $lineValue['filters']['relationOutsideTerms'] }}</td>
						<td class='changFilters'>{{ $lineValue['filters']['relationBetweenTerms'] }}</td>
						<td class='changFilters'>{{ $lineValue['filters']['semicolonBetweenTerms'] }}</td>
						<td class='changFilters'>{{ $lineValue['filters']['commaSeparatedTerms'] }}</td>
						<td class='changFilters'>{{ $lineValue['filters']['parenthesisBetweenTerms'] }}</td>
						<td class='changFilters'>{{ $lineValue['filters']['overlappingTerms'] }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		<input type="submit" value="Submit" class='changSubmit'>
		{{ Form::close() }}				

@section('extra_js')
	{{ javascript_include_tag('jquery.tablesorter.min.js') }}
	{{ javascript_include_tag('jquery.tablesorter.widgets.min.js') }}

	<script type="text/javascript">
		$(document).ready(function () {
			$('table').bind('filterEnd', function(e){
				console.log($.tablesorter.getFilters( this ));

				$('.tablesorter-headerRow th.changFilters').each(function (){
					// console.log($(".tablesorter-filter input[data-column='" + $(this).attr('data-column') + "']"));
					$("input.tablesorter-filter[data-column='" + $(this).attr('data-column') + "']").parent().hide();
				});

				$('.changContent input:checkbox').prop('checked', false);
				
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

            $(".changFiltersButton").popover({
                trigger: "manual",
                html: true,
                'animation' : false,
                'content' : function(){ return $('.changFiltersContainer').html() },
                'placement' : 'bottom',
                 template: '<div class="popover changFiltersPopover"><div class="arrow"></div><div class="popover-content"></div></div>'
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

			$('body').on('click', '.changFiltersControls button', function(e) {
				var clickedChangFilter = $(this).parent().attr('id');
				$('.changFiltersControls #' + clickedChangFilter + ' button').removeClass('active');

				var dataColumn = $('th.' + clickedChangFilter).attr('data-column');
				var changInputFilter = $(".tablesorter-filter[data-column='" + dataColumn + "']");
				var changInputText = $('th.' + clickedChangFilter + ' input');

				$('.changFiltersControls #' + clickedChangFilter + ' button').removeClass('btn-success active').addClass('btn-info');

				if($(this).hasClass('changOn')){
					$(changInputFilter).add(changInputText).val('1');
				} else if($(this).hasClass('changOff')){
					$(changInputFilter).add(changInputText).val('0');
				} else if($(this).hasClass('changNone')){
					$(changInputFilter).add(changInputText).val('');
				}

				$('.changFiltersControls #' + clickedChangFilter + ' .' + $(this).attr('class').split(' ').join('.')).removeClass('btn-info').addClass('btn-success active');

				console.log(changInputText.val());

			    $('table').trigger('search', false);
			});

			$('input.check_filtered_sentences').click(function(e){
			    $('.changContent td input:checkbox').filter(':visible').prop('checked',this.checked);
			});

			$('a.check_filtered_sentences').click(function(e){
			    $('.changContent input:checkbox').filter(':visible').prop('checked', true);
			});

			$('#create_new_changDocument').click(function(e){
			    $('input.changSubmit').click();
			});

		});
	</script>
@stop		