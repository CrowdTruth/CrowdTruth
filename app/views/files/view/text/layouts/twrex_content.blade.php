@section('dynamicButton')
	<a href='#' class="btn btn-success twrexFiltersButton"><i class="fa fa-filter fa-fw"></i>Filter</a>
@stop

<div class='twrexFiltersContainer' style='display:none'>
<table class='table table-striped table-condensed twrexFiltersControls'>
	<tbody>
			@foreach ($lines[0]['properties'] as $filterKey => $filterValue)
				@if($filterKey == "sentenceWordCount")
					<?php continue; ?>
				@endif

				<tr>
					<td>{{ $filterKey }}</td>
					<td>
						<div class="btn-group" id='{{ $filterKey }}'>
						  <button type="button" class="btn btn-info twrexOn">With</button>
						  <button type="button" class="btn btn-info twrexOff">Without</button>
						  <button type="button" class="btn btn-success active twrexNone">Not Applied</button>
						</div>
					</td>
				</tr>

			@endforeach
	</tbody>
</table>
</div>

		{{ Form::open(array('url' => 'files/create?fromURI=' . $entity->_id)) }}
		<div class='table-responsive'>
			<table class='table table-striped table-condensed twrexContent'>
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

						@foreach ($lines[0]['properties'] as $filterKey => $filterValue)
							@if($filterKey == "sentenceWordCount")
								<?php continue; ?>
							@endif

							<th class='twrexFilters {{  $filterKey }}'>
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

						<td class='twrexFilters'>{{ $lineValue['properties']['relationInSentence'] }}</td>
						<td class='twrexFilters'>{{ $lineValue['properties']['relationOutsideTerms'] }}</td>
						<td class='twrexFilters'>{{ $lineValue['properties']['relationBetweenTerms'] }}</td>
						<td class='twrexFilters'>{{ $lineValue['properties']['semicolonBetweenTerms'] }}</td>
						<td class='twrexFilters'>{{ $lineValue['properties']['commaSeparatedTerms'] }}</td>
						<td class='twrexFilters'>{{ $lineValue['properties']['parenthesisBetweenTerms'] }}</td>
						<td class='twrexFilters'>{{ $lineValue['properties']['overlappingTerms'] }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		<input type="submit" value="Submit" class='twrexSubmit'>
		{{ Form::close() }}				

@section('extra_js')
	{{ javascript_include_tag('jquery.tablesorter.min.js') }}
	{{ javascript_include_tag('jquery.tablesorter.widgets.min.js') }}

	<script type="text/javascript">
		$(document).ready(function () {
			$('table').bind('filterEnd', function(e){
				console.log($.tablesorter.getFilters( this ));

				$('.tablesorter-headerRow th.twrexFilters').each(function (){
					// console.log($(".tablesorter-filter input[data-column='" + $(this).attr('data-column') + "']"));
					$("input.tablesorter-filter[data-column='" + $(this).attr('data-column') + "']").parent().hide();
				});

				$('.twrexContent input:checkbox').prop('checked', false);
				
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

            $(".twrexFiltersButton").popover({
                trigger: "manual",
                html: true,
                'animation' : false,
                'content' : function(){ return $('.twrexFiltersContainer').html() },
                'placement' : 'bottom',
                 template: '<div class="popover twrexFiltersPopover"><div class="arrow"></div><div class="popover-content"></div></div>'
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

			$('body').on('click', '.twrexFiltersControls button', function(e) {
				var clickedtwrexFilter = $(this).parent().attr('id');
				$('.twrexFiltersControls #' + clickedtwrexFilter + ' button').removeClass('active');

				var dataColumn = $('th.' + clickedtwrexFilter).attr('data-column');
				var twrexInputFilter = $(".tablesorter-filter[data-column='" + dataColumn + "']");
				var twrexInputText = $('th.' + clickedtwrexFilter + ' input');

				$('.twrexFiltersControls #' + clickedtwrexFilter + ' button').removeClass('btn-success active').addClass('btn-info');

				if($(this).hasClass('twrexOn')){
					$(twrexInputFilter).add(twrexInputText).val('1');
				} else if($(this).hasClass('twrexOff')){
					$(twrexInputFilter).add(twrexInputText).val('0');
				} else if($(this).hasClass('twrexNone')){
					$(twrexInputFilter).add(twrexInputText).val('');
				}

				$('.twrexFiltersControls #' + clickedtwrexFilter + ' .' + $(this).attr('class').split(' ').join('.')).removeClass('btn-info').addClass('btn-success active');

				console.log(twrexInputText.val());

			    $('table').trigger('search', false);
			});

			$('input.check_filtered_sentences').click(function(e){
			    $('.twrexContent td input:checkbox').filter(':visible').prop('checked',this.checked);
			});

			$('a.check_filtered_sentences').click(function(e){
			    $('.twrexContent input:checkbox').filter(':visible').prop('checked', true);
			});

			$('#create_new_twrexDocument').click(function(e){
			    $('input.twrexSubmit').click();
			});

		});
	</script>
@stop		