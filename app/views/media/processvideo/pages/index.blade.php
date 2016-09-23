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
                        @if (isset($data['subtitles']))
                            <li class="checklistitem"><span id="cl_status_subs" class="glyphicon glyphicon-ok"></span> Processed subtitles</li>
                        @else
                            <li class="checklistitem"><span id="cl_status_subs" class="glyphicon glyphicon-remove"></span> Processed subtiles
                                <form name="subsform" id="subsform">
                                <input type="file" name="subsfile" id="uploadsubs">
                                    <input type="hidden" name="videounit" value="{{$data['_id']}}">
                                <input type="submit" value="Upload" id="uploadsubssubmit" />
                                </form>
                            </li>
                        @endif

                        @if ($data['doneclarifai'] == "1" && $data['doneimagga'] == "1")
                                <li class="checklistitem"><span id="cl_status_class_img" class="glyphicon glyphicon-ok">
                        @else
                        <li class="checklistitem"><span id="cl_status_class_img" class="glyphicon glyphicon-remove">
                         @endif
                            </span> Image classification
                            @if ($data['doneclarifai'] == "0")
                            <button id="clarifaiall">Clarifai</button>
                            @endif
                            @if ($data['doneimagga'] == "0")
                            <button id="imaggaall">Imagga</button>
                            @endif
                        </li>
                        @if (isset($data['content']['taggeddesc']) && $data['donenerd'] == "1")
                            <li class="checklistitem"><span id="cl_status_class_text" class="glyphicon glyphicon-ok"></span> Text extraction</li>
                        @else
                            <li class="checklistitem"><span id="cl_status_class_text" class="glyphicon glyphicon-remove"></span> Text extraction</li>
                        @endif
                            @if (false)
                                <button id="dbpediasubtitles">DBPedia subtitles</button>
                                @endif
                            @if ($data['donenerd'] == "0")
                                <button id="nerdsubtitles">NERD subtitles</button>
                                @endif
                        @if (!isset($data['content']['description']))
                            <button id="buttonadddesc">Add description</button>
                            <textarea name="" id="textareadesc" cols="30" rows="10"></textarea>
                            <button id="savedesc">Save desciption</button>
                        @else
                            <button id="buttonshowdesc">Show description</button>
                            @if (isset($data['content']['taggeddesc']))
                                <div id="textareadescshow"> {{$data['content']['taggeddesc'];}}</div>
                            @else
                                <div id="textareadescshow"> {{$data['content']['description']}}</div>
                                <button id="processdesc_nerd">Nerd</button>
                            @endif
                        @endif
                        <button id="splitvideobutton">Split video</button>
                        @if(isset($data['content']['downloadedvideo']) && (isset($data['keyframes'])) && (isset($data['subtitles'])) && ($data['doneclarifai'] == "1" && $data['doneimagga'] == "1") && (isset($data['content']['taggeddesc']) && $data['donenerd'] == "1"))
                        <br /><br />    <button id="gotonextstep">Go to step two</button>
                        @endif
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

                                <div class="row">
                                    <div class="singlekfleft">
                                        <img src="{{URL::action('ProcessVideoController@getImage').'?unit='.$keyframe['_id'].'&number=0&width=200&extension=.png'; }}" />
                                        <div class="kfinfo">
                                            <span class="kftime">Time: <pre>{{ $keyframe['content']['humantime']; }}</pre></span>
                                            <span class="clarifaibutton" kfid="{{$keyframe['_id']}}">C</span>
                                            <span class="imaggabutton" kfid="{{$keyframe['_id']}}">I</span>
                                        </div>
                                    </div>
                                    @if(isset($keyframe['content']['subtitles']) && !isset($keyframe['content']['taggedsub']))
                                        <div class="singlekfright">
                                            <button class="dbpediasubtitles" unitid="{{$keyframe['_id']}}">DBP</button>
                                            <div class="subcontainer" id="subcontainer-{{str_replace("/","-",$keyframe['_id'])}}">
                                                @foreach($keyframe['content']['subtitles'] as $datasub)
                                                    {{$datasub}}<BR/>
                                                @endforeach
                                            </div>
                                        </div>
                                        @elseif(isset($keyframe['content']['taggedsub']))
                                        <div class="singlekfright">

                                            <div class="subcontainer" id="subcontainer-{{str_replace("/","-",$keyframe['_id'])}}">
                                                {{$keyframe['content']['taggedsub']}}
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if(isset($keyframe['content']['tags']))

                                    <div class="row">
                                        @foreach($keyframe['content']['tags'] as $tagcontent)
                                            @if ($tagcontent['source'] != 'dbpedia' && $tagcontent['source'] != 'nerd')
                                            <div class="tagcontent">
                                                Source: {{$tagcontent['source']}}
                                                <table class="table-striped tagtable">
                                                    <tr>
                                                        <td class="tdtaghead_tag">Tag</td>
                                                        <td class="tdtaghead_prob">Prob</td>
                                                    </tr>
                                                    @foreach($tagcontent['tags'] as $currenttag)

                                                        <tr>
                                                            <td class="tdtag_tag">{{$currenttag['tag']}}</td>
                                                            <td class="tdtag_prob" title="{{$currenttag['prob']}}">{{sprintf("%.03f",$currenttag['prob'])}}</td>
                                                        </tr>
                                                    @endforeach
                                                </table>

                                            </div>
                                            @endif
                                        @endforeach
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
                dataType: "json"
            });

            });

        function parseSubs()
        {

        }

        $('.singlekeyframe').click(function(){

            if ($(this).width() > 400)
            {
                $(this).animate({width: 210, height: 142}, 500);
            } else {
                $(this).animate({width: 480, height: 350}, 500);
            }
        });

        $('.clarifaibutton').click(function() {
            event.stopPropagation();
            var curvid = $(this).attr('kfid');
            var getdata = {keyframeid: curvid};
            $.get( '{{ URL::action('ProcessVideoController@getClarifai') }}',getdata,function(data,status){
                flashMessage(data.status,data.message);
            },'json');
        });


        $('.imaggabutton').click(function() {
            event.stopPropagation();
            var curvid = $(this).attr('kfid');
            var getdata = {keyframeid: curvid};
            $.get( '{{ URL::action('ProcessVideoController@getImagga') }}',getdata,function(data,status){
                flashMessage(data.status,data.message);
            },'json');
        });




        $('#clarifaiall').click(function() {

           var getdata = {videoid: '{{$data['_id']}}', all: "yes"}
           $.get('{{URL::action('ProcessVideoController@getClarifai')}}',getdata,function(data,status){
               flashMessage(data.status,data.message);
           });

        });

        $('#imaggaall').click(function(){

            var getdata = {videoid: '{{$data['_id']}}', all: "yes"}
            $.get('{{URL::action('ProcessVideoController@getImagga')}}',getdata,function(data,status){
                flashMessage(data.status,data.message);
            });

        });

        $('.dbpediasubtitles').click(function(){
            event.stopPropagation();
            var getid = $(this).attr('unitid');
            var type = "subtitles";
            var getdata = {unitid: getid, type: type};
            $.get('{{URL::action('ProcessVideoController@getDBPediaSpotlight')}}', getdata, function(data,status){
                flashMessage(data.status,data.message);
            });
        });

        $('#dbpediasubtitles').click(function(){
            var unitid = '{{$data['_id']}}';
            var getdata = {unitid: unitid};
            $.get('{{URL::action('ProcessVideoController@getDBPediaSpotlight_allSubtitles')}}',getdata,function(data,status){
                flashMessage(data.status,data.message);
            });

        });
        $('#nerdsubtitles').click(function(){
            var unitid = '{{$data['_id']}}';
            var getdata = {unitid: unitid};
            $.get('{{URL::action('ProcessVideoController@getNerd_allSubtitles')}}',getdata,function(data,status){
                flashMessage(data.status,data.message);
            });

        });

        $('#buttonadddesc').click(function(){
            $('#textareadesc').fadeIn('fast');
            $('#savedesc').fadeIn('fast');
        });

        $('#buttonshowdesc').click(function()
        {
           if ($('#textareadescshow').css('display') != 'block')
           {
               $('#textareadescshow').fadeIn('fast');
               $('#processdesc_dbpedia').fadeIn('fast');
               $('#processdesc_nerd').fadeIn('fast');
               $('#buttonshowdesc').text('Hide description');
           } else {
               $('#textareadescshow').fadeOut('fast');
               $('#processdesc_dbpedia').fadeOut('fast');
               $('#processdesc_nerd').fadeOut('fast');
               $('#buttonshowdesc').text('Show description');
           }
        });

        $('#processdesc_dbpedia').click(function(){
            var type = "description";
            var unitid = '{{$data['_id']}}';
            var getdata = {type:type, unitid:unitid};
            $.get('{{URL::action('ProcessVideoController@getDBPediaSpotlight')}}', getdata, function(data,status){
                flashMessage(data.status,data.message);
            });
        });

        $('#processdesc_nerd').click(function(){
            var type = "description";
            var unitid = '{{$data['_id']}}';
            var getdata = {type:type, unitid:unitid};
            $.get('{{URL::action('ProcessVideoController@getNerd')}}', getdata, function(data,status){
                flashMessage(data.status,data.message);
            });
        });

        $('#savedesc').click(function(){

            var desc = $('#textareadesc').val();
            var postdata = {unitid: '{{$data['_id']}}', description: desc};
            $.post('{{URL::action('ProcessVideoController@postAddDescription')}}', postdata, function(data,status){
                flashMessage(data.status,data.message);
            });

        });

        function flashMessage(type, message)
        {

            $(".flashmessage_text").text(message);
            if (type == "success") {
                $("#flashing_success").fadeIn('fast');

                $("#successmessage_btnreload").click(function() {


                    var form = $('<form action="{{ URL::action("ProcessVideoController@postProcess") }}" method="post"></form>');
                    $('body').append(form);
                    form.append($('<input type="hidden" name="videofile" value="{{$data['_id']}}">'))
                    form.submit();

                });
            } else if (type == "error")
            {
                $("#flashing_error").fadeIn('fast');
            }
        };

        $('#gotonextstep').click(function(){
            var form = $('<form action="{{ URL::action("ProcessVideoController@postStepTwo") }}" method="post"></form>');
            $('body').append(form);
            form.append($('<input type="hidden" name="videofile" value="{{$data['_id']}}">'))
            form.submit();
        });

        $('#splitvideobutton').click(function(){

            var unitid = '{{$data['_id']}}';
            var getdata = {videofile:unitid};
            $.get('{{URL::action('ProcessVideoController@getSplitVideo')}}', getdata, function(data,status){
                flashMessage(data.status,data.message);
            });
        });

    </script>
@stop

@section('head')

    {{javascript_include_tag('jquery-1.10.2.min.js')}}
    {{javascript_include_tag('jwplayer/jwplayer.js')}}
    {{stylesheet_link_tag('processvideo.css')}}
@stop