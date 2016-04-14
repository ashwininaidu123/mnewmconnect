<?php
include_once("GoogleMap.php");
include_once("JSMin.php");
$MAP_OBJECT = new GoogleMapAPI(); $MAP_OBJECT->_minify_js = isset($_REQUEST["min"])?FALSE:TRUE;

$marker_web_location = "http://www.bradwedell.com/phpgooglemapapi/demos/img/";
$default_icon = $marker_web_location."triangle_icon.png";
$default_icon_key = $MAP_OBJECT->setMarkerIcon($default_icon);
$blue_marker = $MAP_OBJECT->addMarkerByAddress($qr_info->qraddress,$qr_info->qraddress);
?>



<!doctype html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>MCube QRTrack</title>
        <base href="<?=base_url()?>">
        <style type="text/css" media="screen">@import "jqt/jqtouch/jqtouch.css";</style>
        <style type="text/css" media="screen">@import "jqt/themes/jqt/theme.css";</style>
        <script src="jqt/jqtouch/jquery-1.4.2.js" type="text/javascript" charset="utf-8"></script>
        <script src="jqt/jqtouch/jqtouch.js" type="application/x-javascript" charset="utf-8"></script>
        <?=$MAP_OBJECT->getHeaderJS();?>
		<?=$MAP_OBJECT->getMapJS();?>
        <script type="text/javascript" charset="utf-8">
            var jQT = new $.jQTouch({
                icon: 'jqtouch.png',
                icon4: 'jqtouch4.png',
                addGlossToIcon: false,
                startupScreen: 'jqt_startup.png',
                statusBar: 'black',
                preloadImages: [
                    'jqt/themes/jqt/img/activeButton.png',
                    'jqt/themes/jqt/img/back_button.png',
                    'jqt/themes/jqt/img/back_button_clicked.png',
                    'jqt/themes/jqt/img/blueButton.png',
                    'jqt/themes/jqt/img/button.png',
                    'jqt/themes/jqt/img/button_clicked.png',
                    'jqt/themes/jqt/img/grayButton.png',
                    'jqt/themes/jqt/img/greenButton.png',
                    'jqt/themes/jqt/img/redButton.png',
                    'jqt/themes/jqt/img/whiteButton.png',
                    'jqt/themes/jqt/img/loading.gif'
                    ]
            });
            // Some sample Javascript functions:
            $(function(){
                // Show a swipe event on swipe test
                $('#swipeme').swipe(function(evt, data) {
                    $(this).html('You swiped <strong>' + data.direction + '/' + data.deltaX +':' + data.deltaY + '</strong>!');
                    $(this).parent().after('<li>swiped!</li>')

                });
                $('#tapme').tap(function(){
                    $(this).parent().after('<li>tapped!</li>')
                })
                $('a[target="_blank"]').click(function() {
                    if (confirm('This link opens in a new window.')) {
                        return true;
                    } else {
                        return false;
                    }
                });
                // Page animation callback events
                $('#pageevents').
                    bind('pageAnimationStart', function(e, info){ 
                        $(this).find('.info').append('Started animating ' + info.direction + '&hellip; ');
                    }).
                    bind('pageAnimationEnd', function(e, info){
                        $(this).find('.info').append(' finished animating ' + info.direction + '.<br /><br />');
                    });
                // Page animations end with AJAX callback event, example 1 (load remote HTML only first time)
                $('#callback').bind('pageAnimationEnd', function(e, info){
                    // Make sure the data hasn't already been loaded (we'll set 'loaded' to true a couple lines further down)
                    if (!$(this).data('loaded')) {
                        // Append a placeholder in case the remote HTML takes its sweet time making it back
                        // Then, overwrite the "Loading" placeholder text with the remote HTML
                        $(this).append($('<div>Loading</div>').load('ajax.html .info', function() {        
                            // Set the 'loaded' var to true so we know not to reload
                            // the HTML next time the #callback div animation ends
                            $(this).parent().data('loaded', true);  
                        }));
                    }
                });
                // Orientation callback event
                $('#jqt').bind('turn', function(e, data){
                    $('#orient').html('Orientation: ' + data.orientation);
                });
                $('#play_movie').bind('tap', function(){
                    $('#movie').get(0).play();
                    $(this).removeClass('active');
                });
                
                $('#video').bind('pageAnimationStart', function(e, info){
                    $('#movie').css('display', 'none');
                }).bind('pageAnimationEnd', function(e, info){
                    if (info.direction == 'in')
                    {
                        $('#movie').css('display', 'block');
                    }
                })
                $('#callnow').change(function(){
					if($(this).is(':checked')){
						$('#number').attr('disabled','disabled');
					}else{
						$('#number').removeAttr('disabled');
					}
				});
            });
        </script>
    </head>
    <body>
        <div id="jqt">
			<? if(strlen($qr_info->qruse)>'1'){ ?>
            <div id="home" class="current">
                <div class="toolbar">
                    <h1><?=$qr_info->qrtitle?></h1>
                    <a class="button slideleft" href="#contact">Contact Us</a>
                </div>
                <div>
					<ul class="rounded"><li>
					<?=$qr_info->description?><br>
					<?if($qr_info->video!=''){?><iframe width="100%" height="345" src="<?=$qr_info->video?>" frameborder="0" allowfullscreen></iframe><?}?>
					</li></ul>
                </div>
                <ul class="rounded">
					<?=(in_array('1',explode(',',$qr_info->qruse)))?'<li class="forward"><a href="#callus">Schedule A Call</a></li>':''?>
					<?=(in_array('2',explode(',',$qr_info->qruse)))?'<li class="forward"><a target="_blank" href="'.$qr_info->webaddress.'">Visit Us</a></li>':''?>
					<?=(in_array('4',explode(',',$qr_info->qruse)))?'<li class="forward"><a href="#deal">To days Deal</a></li>':''?>
					<?=(in_array('6',explode(',',$qr_info->qruse)))?'<li class="forward"><a href="#map">View Map</a></li>':''?>
                </ul>
            </div>
            <? } ?>
            <div id="contact" class="selectable">
                <div class="toolbar">
					<a class="back" href="#">Back</a>
                    <h1><?=$qr_info->qrtitle?></h1>
                </div>
                <form name="qrlead" method="POST" action="q/savelead" class="form">
                    <ul class="rounded">
						<? foreach($form['fields'] as $field){?>
						<li><?=$field['label']?><?=$field['field']?></li>
						<? }?>
						<input type="hidden" name="submit" value="submit"/>
						<input type="hidden" name="bid" value="<?=$bid?>"/>
						<input type="hidden" name="qrid" value="<?=$qrid?>"/>
						<input type="hidden" name="gid" value="<?=$gid?>"/>
						<input type="hidden" name="scanid" value="<?=$scanid?>"/>
                    </ul>
                    <a style="margin:0 10px;color:rgba(0,0,0,.9)" href="#" class="submit whiteButton">Submit</a>
                </form>
            </div>
            <div id="map" class="selectable">
                <div class="toolbar">
					<a class="back" href="#">Back</a>
                    <h1><?=$qr_info->qrtitle?></h1>
                    <a class="button slideleft" href="#contact">Contact Us</a>
                </div>
                <div>
                <?=$MAP_OBJECT->printOnLoad();?> 
				<?=$MAP_OBJECT->printMap();?>
				<? //=$MAP_OBJECT->printSidebar();?>
                </div>
            </div>
            <div id="callus" class="selectable">
                <div class="toolbar">
					<a class="back" href="#">Back</a>
                    <h1><?=$qr_info->qrtitle?></h1>
                    <a class="button slideleft" href="#contact">Contact Us</a>
                </div>
                <form name="qrlead" method="POST" action="q/callus" class="form">
                    <ul class="rounded">
						<li><input type="text" name="number" placeholder="Enter Your Mobile No." value=""/></li>
						<li>Call Now<span class="toggle"><input id="callnow" name="callnow" type="checkbox" checked="checked" /></span></li>
						<li><input disabled="disabled" type="text" name="datetime" id="datetime" placeholder="Schedule Call as <?=date('Y-m-d H:i:s')?>" value=""/></li>
						<input type="hidden" name="submit" value="submit"/>
						<input type="hidden" name="bid" value="<?=$bid?>"/>
						<input type="hidden" name="gid" value="<?=$gid?>"/>
						<input type="hidden" name="scanid" value="<?=$scanid?>"/>
                    </ul>
                    <a style="margin:0 10px;color:rgba(0,0,0,.9)" href="#" class="submit whiteButton">Submit</a>
                </form>
            </div>
            <div id="deal" class="selectable">
                <div class="toolbar">
					<a class="back" href="#">Back</a>
                    <h1><?=$qr_info->qrtitle?></h1>
                    <a class="button slideleft" href="#contact">Contact Us</a>
                </div>
                <div>
					<ul class="rounded">
					<li>Deal Title:<?=$deal_info->dealtitle?></li>
					<li>Deal value:<?=$deal_info->dealvalue?></li>
					<li>Description:<?=$deal_info->description?></li>
					<li>Address:<?=$deal_info->address ?></li>
					<li>Phone:<?=$deal_info->phone?></li>
					</ul>
                </div>
            </div>
        </div>
    </body>
</html>
