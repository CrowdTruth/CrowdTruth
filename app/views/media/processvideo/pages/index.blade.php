@extends('layouts.default_new')

@section('container','full-container')


@section('content')

    <!-- START upload_content -->
    <div class="col-xs-10 col-sm-offset-1">
        <div class='maincolumn CW_box_style'>
            @include('media.processvideo.layouts.flashing')
            <div class="row">
            <div class="col-xs-4">
                <div id="checklist">
                    <h3>Checklist</h3>
                    <ul id="checklist_ul">
                        @if (isset($data['content']['downloadedvideo']))
                            <li class="checklistitem"><span id="cl_status_download" class="glyphicon glyphicon-ok"></span> Downloaded video</li>
                        @else
                            <li class="checklistitem"><span id="cl_status_download" class="glyphicon glyphicon-remove"></span> Downloaded video <button class="button" id="downloadvideo">Download</button></li>
                        @endif
                        @if (isset($data['keyframes']))
                            <li class="checklistitem"><span id="cl_status_kf" class="glyphicon glyphicon-ok"></span> Processed keyframes</li>
                        @else
                            <li class="checklistitem"><span id="cl_status_kf" class="glyphicon glyphicon-remove"></span> Processed keyframes <button class="button" id="processkeyframes">Keyframes</button></li>
                        @endif
                        @if (isset($data['subtitles']) && false)
                            <li class="checklistitem"><span id="cl_status_subs" class="glyphicon glyphicon-ok"></span> Processed subtitles</li>
                        @else
                            <li class="checklistitem"><span id="cl_status_subs" class="glyphicon glyphicon-remove"></span> Processed subtiles
                                <form name="subsform" id="subsform">
                                <input type="file" name="subsfile" id="uploadsubs">
                                    <input type="hidden" name="videounit" value="{{$data['videofile']['_id']}}">
                                <input type="submit" value="Upload" id="uploadsubssubmit" />
                                </form>
                            </li>
                        @endif
                        <li class="checklistitem"><span id="cl_status_class_img" class="glyphicon glyphicon-remove"></span> Image classification</li>
                        <li class="checklistitem"><span id="cl_status_class_text" class="glyphicon glyphicon-remove"></span> Text extraction</li>
                    </ul>
                </div>

            </div>
            <div id="videocontiner" class="col-xl-8"><div id="container_jwplayer"></div></div>
            </div>
            <div class="row">
            <div class="clearboth"></div>
                @if(isset($data['keyframes']))

                    <div id="kfcontainer" class="col-xs-12">
                    @foreach($data['keyframes'] as $keyframe)
                        <div class="singlekeyframe" unitid="{{str_replace("/","-",$keyframe['_id']);}}">
                            <div class="singlekfleft">
                            <img src="{{URL::action('ProcessVideoController@getImage').'?unit='.$keyframe['_id'].'&number=0&width=200&extension=.png'; }}" />
                            <div class="kfinfo">
                                <span class="kftime">Time in video: <pre>{{ $keyframe['humantime']; }}</pre></span>
                            </div>
                            </div>
                            @if(isset($data['subtitles']))
                            <div class="singlekfright">
                                <div class="subcontainer" id="subcontainer-{{str_replace("/","-",$keyframe['_id'])}}">

                                </div>

                            </div>
                            @endif
                        </div>
                    @endforeach
                    </div>

            @endif
            </div>
        </div>


    </div>
@stop

@stop

@section('end_javascript')
    <script>
        jwplayer('container_jwplayer').setup({
            file: '{{$data['content']['url']}}',
        });

        $('#downloadvideo').click(function(){
            var getdata = {videounit: '{{$data['_id']}}'};
            $.get( '{{ URL::action('ProcessVideoController@getDownloadFile') }}',getdata,function(data,status){
                flashMessage(data.status,data.message);
            },'json');
        });

        $('#processkeyframes').click(function(){
            var getdata = {videounit: '{{$data['_id']}}'};
            $.get( '{{ URL::action('ProcessVideoController@getProcessKeyframes') }}',getdata,function(data,status){
                flashMessage(data.status,data.message);
            },'json');
        });

        $('#subsform').submit(function(e){
            e.preventDefault();
            var subsform = $('#subsform')[0];
            //console.log(subsform);
            var postdata = new FormData(subsform);
            //console.log(postdata);
            $.ajax({
                url: '{{URL::action('ProcessVideoController@postUploadSubs')}}',
                data: postdata,
                success: function(data){
                    console.log(data);
                    flashMessage(data.status,data.message);
                     },
                processData: false,
                contentType: false,
                type: 'POST',
            });

            });

        function parseSubs()
        {

        }


        function flashMessage(type, message)
        {

            $(".flashmessage_text").text(message);
            if (type == "success") {
                $("#flashing_success").fadeIn('fast');
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