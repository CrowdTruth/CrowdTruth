<!-- START api/nav -->   
<ul class="nav nav-tabs">
	<li{{ (Request::is('api/alchemyapi*') ? ' class="active"' : '') }}>{{ link_to('api/alchemyapi', "AlchemyAPI") }}</li>
	<li{{ (Request::is('api/textrazor*') ? ' class="active"' : '') }}>{{ link_to('#', "TextRazor") }}</li>
</ul>
<!-- END api/nav   --> 