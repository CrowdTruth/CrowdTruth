@extends('layouts.default_new')

@section('container','full-container')


@section('content')
    
    <!-- START upload_content -->
    <div class="col-xs-10 col-sm-offset-1">
        <div class='maincolumn CW_box_style'>
            @include('media.processvideo.layouts.flashing')
            <div class="col-xs-4">
                <div id="checklist">
                    <h3>Checklist</h3>
                    <ul id="checklist_ul">
                        <li class="checklistitem"><span id="cl_status_download" class="glyphicon glyphicon-ok"></span> Downloaded video</li>
                        <li class="checklistitem"><span id="cl_status_kf" class="glyphicon glyphicon-remove"></span> Processed keyframes</li>
                        <li class="checklistitem"><span id="cl_status_subs" class="glyphicon glyphicon-remove"></span> Processed subtitles</li>
                        <li class="checklistitem"><span id="cl_status_class_img" class="glyphicon glyphicon-remove"></span> Image classification</li>
                        <li class="checklistitem"><span id="cl_status_class_text" class="glyphicon glyphicon-remove"></span> Text extraction</li>
                    </ul>
                </div>

            </div>
            <div id="videocontiner" class="col-cs-8"><div id="container_jwplayer"></div></div>



        </div>

        <button class="button" id="downloadvideo">Download</button>
    </div>
@stop

@stop

@section('end_javascript')
    <script>
        jwplayer('container_jwplayer').setup({
            file: '{{$videofile[0]->content->url}}',
        });

        $('#downloadvideo').click(function(){
            var getdata = {videounit: '{{$videofile[0]->_id}}'};
            $.get( '{{ URL::action('ProcessVideoController@getDownloadFile') }}',getdata,function(data,status){
                flashMessage(data.status,data.message);
            },'json');
        });

        function flashMessage(type, message)
        {
            $(".flashmessage_text").text(message);
            if (type == "success") {
                $("#flashing_success").fadeIn('fast').delay(3000).fadeOut('fast');
            } else if (type == "error")
            {
                $("#flashing_error").fadeIn('fast');
            }
        }
    </script>
@stop

@section('head')

    {{javascript_include_tag('jquery-1.10.2.min.js')}}
    {{javascript_include_tag('jwplayer/jwplayer.js')}}
    {{stylesheet_link_tag('processvideo.css')}}
@stop