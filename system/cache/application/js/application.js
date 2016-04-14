/****  Variables Initiation  ****/
var doc = document;
var docEl = document.documentElement;
var page = $("body").data("page");


/****  Break Points Creation  ****/
if ($.fn.setBreakpoints) {
    $(window).setBreakpoints({
        distinct: true,
        breakpoints: [320, 480, 768, 1200]
    });
}

//******************************** MAIN SIDEBAR ******************************//

var $html = $('html');
var $wrapper = $('#wrapper');
var $sidebar = $('#sidebar');
var $sidebar_toggle = $('.sidebar-toggle');
var $sidebar_submenu = $('.submenu');

function manageSidebar() {
    /* We change sidebar type on resize event */
    $(window).bind('enterBreakpoint1200', function () {
        $html.removeClass().addClass('sidebar-large');
        $('.sidebar-nav li.current').addClass('active');
        $('.sidebar-nav li.current .submenu').addClass('in').height('auto');
        $sidebar_toggle.attr('id', 'menu-medium');
        $sidebar.removeClass('collapse');
        sidebarHeight();
        createSideScroll();
    });

    $(window).bind('enterBreakpoint768', function () {
        $html.removeClass('sidebar-hidden').removeClass('sidebar-large').removeClass('sidebar-thin').addClass('sidebar-medium');
        $('.sidebar-nav li.current').removeClass('active');
        $('.sidebar-nav li.current .submenu').removeClass('in');
        $sidebar_toggle.attr('id', 'menu-thin');
        sidebarHeight();
        $sidebar.removeClass('collapse');
        $("#menu-right").trigger("close");
        destroySideScroll();
    });

    $(window).bind('enterBreakpoint480', function () {
        $html.removeClass('sidebar-medium').removeClass('sidebar-large').removeClass('sidebar-hidden').addClass('sidebar-thin');
        $('.sidebar-nav li.current').removeClass('active');
        $('.sidebar-nav li.current .submenu').removeClass('in');
        $sidebar.removeClass('collapse');
        sidebarHeight();
        destroySideScroll();
    });

    $(window).bind('enterBreakpoint320', function () {
        $html.removeClass('sidebar-medium').removeClass('sidebar-large').removeClass('sidebar-thin').addClass('sidebar-hidden');
        sidebarHeight();
        destroySideScroll();
    });

    /* We change sidebar type on click event */
    $(document).on("click", "#menu-large", function () {
        $html.removeClass('sidebar-medium').removeClass('sidebar-hidden').removeClass('sidebar-thin').addClass('sidebar-large');
        $sidebar_toggle.attr('id', 'menu-medium');
        $('.sidebar-nav li.current').addClass('active');
        $('.sidebar-nav li.current .submenu').addClass('in').height('auto');
      
        sidebarHeight();
        createSideScroll();
    });

    $(document).on("click", "#menu-medium", function () {
        $html.removeClass('sidebar-hidden').removeClass('sidebar-large').removeClass('sidebar-thin').addClass('sidebar-medium');
        $sidebar_toggle.attr('id', 'menu-thin');
        $('.sidebar-nav li.current').removeClass('active');
        $('.sidebar-nav li.current .submenu').removeClass('in');
        
        sidebarHeight();
        destroySideScroll();
    });

    $(document).on("click", "#menu-thin", function () {
        $html.removeClass('sidebar-medium').removeClass('sidebar-large').removeClass('sidebar-thin').addClass('sidebar-thin');
        $sidebar_toggle.attr('id', 'menu-large');
        $('.sidebar-nav li.current').removeClass('active');
        $('.sidebar-nav li.current .submenu').removeClass('in');
        sidebarHeight();
        if ($('body').hasClass('breakpoint-768')) $sidebar_toggle.attr('id', 'menu-medium');
        destroySideScroll();
    });

    function destroySideScroll() {
        $sidebar.mCustomScrollbar("destroy");
    }

    function createSideScroll() {
        destroySideScroll();
        if (!(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))) {
            $sidebar.mCustomScrollbar({
                scrollButtons: {
                    enable: false
                },
                autoHideScrollbar: true,
                scrollInertia: 150,
                theme: "light-thin",
                advanced: {
                    updateOnContentResize: true
                }
            });
        }
    }
}

/* Toggle submenu open */
function toggleSidebarMenu() {
    var $this = $('.sidebar-nav');
    $this.find('li.active').has('ul').children('ul').addClass('collapse in');
    $this.find('li').not('.active').has('ul').children('ul').addClass('collapse');
    $this.find('li').has('ul').children('a').on('click', function (e) {
        e.preventDefault();
        $(this).parent('li').toggleClass('active').children('ul').collapse('toggle');
        $(this).parent('li').siblings().removeClass('active').children('ul.in').collapse('hide');
    });
}

/* Adjust sidebar height */
function sidebarHeight() {
    var sidebar_height;
    var mainMenuHeight = parseInt($('#main-menu').height());
    var windowHeight = parseInt($(window).height());
    var mainContentHeight = parseInt($('#main-content').height());
    if (windowHeight > mainMenuHeight && windowHeight > mainContentHeight) sidebar_height = windowHeight;
    if (mainMenuHeight > windowHeight && mainMenuHeight > mainContentHeight) sidebar_height = mainMenuHeight;
    if (mainContentHeight > mainMenuHeight && mainContentHeight > windowHeight) sidebar_height = mainContentHeight;
    if ($html.hasClass('sidebar-large') || $html.hasClass('sidebar-hidden')) {
        $sidebar.height('');
    } else {
        $sidebar.height(sidebar_height);
    }
}


/* Sidebar Statistics */
if ($.fn.sparkline) {
    /* Sparklines can also take their values from the first argument passed to the sparkline() function */
    var myvalues1 = [13, 14, 16, 15, 11, 14, 20, 14, 12, 16, 11, 17, 19, 16];
    var myvalues2 = [14, 17, 16, 12, 18, 16, 22, 15, 14, 17, 11, 18, 11, 12];
    var myvalues3 = [18, 14, 15, 14, 15, 12, 21, 16, 18, 14, 12, 15, 17, 19];
    $('.dynamicbar1').sparkline(myvalues1, {
        type: 'bar',
        barColor: '#159077',
        barWidth: 4,
        barSpacing: 1,
        height: '28px'
    });
    $('.dynamicbar2').sparkline(myvalues2, {
        type: 'bar',
        barColor: '#00699e',
        barWidth: 4,
        barSpacing: 1,
        height: '28px'
    });
    $('.dynamicbar3').sparkline(myvalues3, {
        type: 'bar',
        barColor: '#9e494e',
        barWidth: 4,
        barSpacing: 1,
        height: '28px'
    });
};


//* Search Toggle //
function chatSidebar() {

    /* Manage the right sidebar */
    if ($.fn.mmenu) {
        var $menu = $('nav#menu-right');
        $menu.mmenu({
            position: 'right',
            dragOpen: true,
            counters: false,
            searchfield: {
                add: true,
                search: true,
                showLinksOnly: false
            }
        });
    }

    /* Open / Close right sidebar */
    $('#chat-toggle').on('click', function () {
        $menu.hasClass('mm-opened') ? $menu.trigger("close") : $menu.trigger("open");
        $('#chat-notification, #chat-popup').hide();
        setTimeout(function () {
            $('.mm-panel .badge-danger').each(function () {
                $(this).removeClass('hide').addClass('animated bounceIn');
            });
        }, 1000);
    });

    /* Remove current message when opening */
    $('.have-message').on('click', function () {
        $(this).removeClass('have-message');
        $(this).find('.badge-danger').fadeOut();
    });

    /* Send messages */
    $('.send-message').keypress(function (e) {
        if (e.keyCode == 13) {
            var chat_message = '<li class="img">' +
                '<span>' +
                '<div class="chat-detail chat-right">' +
                '<img src="assets/img/avatars/avatar1.png" data-retina-src="assets/img/avatars/avatar1_2x.png"/>' +
                '<div class="chat-detail">' +
                '<div class="chat-bubble">' +
                $(this).val() +
                '</div>' +
                '</div>' +
                '</div>' +
                '</span>' +
                '</li>';
            $(chat_message).hide().appendTo($(this).parent().parent()).fadeIn();
            $(this).val("");
        }
    });
}



//*********************************** CUSTOM FUNCTIONS *****************************//

/****  Custom Scrollbar  ****/
function customScroll() {
    $('.withScroll').each(function () {
        $(this).mCustomScrollbar("destroy");
        var scroll_height = $(this).data('height') ? $(this).data('height') : 'auto';
        var data_padding = $(this).data('padding') ? $(this).data('padding') : 0;
        if ($(this).data('height') == 'window') scroll_height = $(window).height() - data_padding;

        $(this).mCustomScrollbar({
            scrollButtons: {
                enable: false
            },
            autoHideScrollbar: true,
            scrollInertia: 150,
            theme: "dark-2",
            set_height: scroll_height,
            advanced: {
                updateOnContentResize: true
            }
        });
    });
}

/****  Animated Panels  ****/
function liveTile() {
    $('.live-tile').each(function () {
        $(this).liveTile("destroy", true); /* To get new size if resize event */
        tile_height = $(this).data("height") ? $(this).data("height") : $(this).find('.panel-body').height() + 52;
        $(this).height(tile_height);
        $(this).liveTile({
            speed: $(this).data("speed") ? $(this).data("speed") : 500, // Start after load or not
            mode: $(this).data("animation-easing") ? $(this).data("animation-easing") : 'carousel', // Animation type: carousel, slide, fade, flip, none
            playOnHover: $(this).data("play-hover") ? $(this).data("play-hover") : false, // Play live tile on hover
            repeatCount: $(this).data("repeat-count") ? $(this).data("repeat-count") : -1, // Repeat or not (-1 is infinite
            delay: $(this).data("delay") ? $(this).data("delay") : 0, // Time between two animations
            startNow: $(this).data("start-now") ? $(this).data("start-now") : true, //Start after load or not
        });
    });
}

//~ /****  Full Screen Toggle  ****/
//~ function toggleFullScreen() {
    //~ if (!doc.fullscreenElement && !doc.msFullscreenElement && !doc.webkitIsFullScreen && !doc.mozFullScreenElement) {
        //~ if (docEl.requestFullscreen) {
            //~ docEl.requestFullscreen();
        //~ } else if (docEl.webkitRequestFullScreen) {
            //~ docEl.webkitRequestFullscreen();
        //~ } else if (docEl.webkitRequestFullScreen) {
            //~ docEl.webkitRequestFullScreen();
        //~ } else if (docEl.msRequestFullscreen) {
            //~ docEl.msRequestFullscreen();
        //~ } else if (docEl.mozRequestFullScreen) {
            //~ docEl.mozRequestFullScreen();
        //~ }
    //~ } else {
        //~ if (doc.exitFullscreen) {
            //~ doc.exitFullscreen();
        //~ } else if (doc.webkitExitFullscreen) {
            //~ doc.webkitExitFullscreen();
        //~ } else if (doc.webkitCancelFullScreen) {
            //~ doc.webkitCancelFullScreen();
        //~ } else if (doc.msExitFullscreen) {
            //~ doc.msExitFullscreen();
        //~ } else if (doc.mozCancelFullScreen) {
            //~ doc.mozCancelFullScreen();
        //~ }
    //~ }
//~ }
//~ $('.toggle_fullscreen').click(function () {
    //~ toggleFullScreen();
//~ });

/****  Animate numbers onload  ****/
if ($('.animate-number').length && $.fn.numerator) {
    $('.animate-number').each(function () {
        $(this).numerator({
            easing: $(this).data("animation-easing") ? $(this).data("animation-easing") : 'linear', // easing options.
            duration: $(this).data("animation-duration") ? $(this).data("animation-duration") : 700, // the length of the animation.
            toValue: $(this).data("value"), // animate to this value.
            delimiter: ','
        });
    });
}

/****  Custom Select Input  ****/
if ($('select').length && $.fn.selectpicker) {
    $('select').selectpicker();
}

/****  Show Tooltip  ****/
if ($('[rel="tooltip"]').length && $.fn.tooltip) {
    $('[rel="tooltip"]').tooltip();
}

/****  Show Popover  ****/
if ($('[rel="popover"]').length && $.fn.popover) {
    $('[rel="popover"]').popover();
    $('[rel="popover_dark"]').popover({
        template: '<div class="popover popover-dark"><div class="arrow"></div><h3 class="popover-title popover-title"></h3><div class="popover-content popover-content"></div></div>',
    });
}


/* Adjust panel height */
/*
$('[data-equal-height="true"]').each(function(){
  equalHeight($(this).children().children());
}); 

function equalHeight(group) {
   tallest = 0;
   group.each(function() {
    thisHeight = $(this).height();
    if(thisHeight > tallest) {
     tallest = thisHeight;
    }
   });
   group.height(tallest);
}
*/

/****  Improve Dropdown effect  ****/
if ($('[data-hover="dropdown"]').length && $.fn.popover) {
    $('[data-hover="dropdown"]').popover();
}

/****  Add to favorite  ****/
$('.favorite').on('click', function () {
    ($(this).hasClass('btn-default')) ? $(this).removeClass('btn-default').addClass('btn-danger') : $(this).removeClass('btn-danger').addClass('btn-default');
});

/****  Add to favorite  ****/
$('.favs').on('click', function () {
    event.preventDefault();
    ($(this).hasClass('fa-star-o')) ? $(this).removeClass('fa-star-o').addClass('fa-star c-orange') : $(this).removeClass('fa-star c-orange').addClass('fa-star-o');
});

/****  Custom Checkbox  ****/
if ($('input:checkbox').length && $.fn.iCheck) {
    $('input:checkbox').each(function () {
        // We verify if the checkbox is not a custom switch 
        if (!$(this).hasClass('switch')) {
            var icheck_style = $(this).data('style') ? $(this).data('style') : 'flat-blue';
            $(this).iCheck({
                checkboxClass: 'icheckbox_' + icheck_style
            });
        }
    });

    /* Toggle All Checkbox Function */
    $('.toggle_checkbox').on('ifClicked', function () {
        var icheck_color = $(this).parent().data('color') ? '.icheckbox_flat-' + $(this).parent().data('color') : '.icheckbox_flat-blue';
        if ($(this).parent().hasClass('checked')) {
            $(this).closest('table').find('tr').removeClass('selected');
            $(this).closest('table').find(icheck_color).removeClass('checked');
            $(this).closest('table').find(':checkbox').attr('checked', false);
        } else {
            $(this).closest('table').find('tr').addClass('selected');
            $(this).closest('table').find(icheck_color).addClass('checked');
            $(this).closest('table').find(':checkbox').attr('checked', true);
        }
    });

    /* Toggle Select Class */
    $('.icheck').on('ifClicked', function () {
        if ($(this).parent().hasClass('checked')) {
            $(this).closest('tr').removeClass('selected');
            $(this).closest('tr').find(':checkbox').attr('checked', false);
        } else {
            $(this).parent().addClass('checked');
            $(this).closest('tr').addClass('selected');
            $(this).closest('tr').find(':checkbox').attr('checked', true);
        }
    });
}

/****  Custom Radio Button  ****/
//~ $('input:radio').each(function () {
    //~ var icheck_style = $(this).data('style') ? $(this).data('style') : 'flat-blue';
    //~ $(this).iCheck({
        //~ radioClass: 'iradio_' + icheck_style,
    //~ });
//~ });

/****  Form Validation with Icons  ****/
if ($('.icon-validation').length && $.fn.parsley) {
    $('.icon-validation').each(function () {
        $(this).parsley({
            listeners: {
                onFieldSuccess: function (elem, constraints) {
                    elem.prev().removeClass('fa-exclamation c-red').addClass('fa-check c-green');
                },
                onFieldError: function (elem, constraints) {
                    elem.prev().removeClass('fa-check c-green').addClass('fa-exclamation c-red');
                }
            }
        });
    });
}

/****  Custom Range Slider  ****/
if ($('.range-slider').length && $.fn.rangeSlider) {
    $('.range-slider').each(function () {
        $(this).rangeSlider({
            delayOut: $(this).data('slider-delay-out') ? $(this).data('slider-delay-out') : '0',
            valueLabels: $(this).data('slider-value-label') ? $(this).data('slider-value-label') : 'show',
            step: $(this).data('slider-step') ? $(this).data('slider-step') : 1,
        });
    });
}

/****  Custom Slider  ****/
function handleSlider() {
    if ($('.slide').length && $.fn.slider) {
        $('.slide').each(function () {
            $(this).slider();
        });
    }
}

/**** Custom Switch  ****/
if ($('.switch').length && $.fn.bootstrapSwitch) {
    $('.switch').each(function () {
        var switch_size = $(this).data('size') ? $(this).data('size') : '';
        var switch_on_color = $(this).data('on-color') ? $(this).data('on-color') : 'primary';
        var switch_off_color = $(this).data('off-color') ? $(this).data('off-color') : 'primary';
        $(this).bootstrapSwitch('size', switch_size);
        $(this).bootstrapSwitch('onColor', switch_on_color);
        $(this).bootstrapSwitch('offColor', switch_off_color);
    });
}

/****  Datepicker  ****/
//~ if ($('.datepicker').length && $.fn.datepicker) {
    //~ $('.datepicker').each(function () {
        //~ var datepicker_inline = $(this).data('inline') ? $(this).data('inline') : false;
        //~ $(this).datepicker({
            //~ inline: datepicker_inline
        //~ });
    //~ });
//~ }

/****  Datetimepicker  ****/
if ($('.datetimepicker').length && $.fn.datetimepicker) {
    $('.datetimepicker').each(function () {
        var datetimepicker_inline = $(this).data('inline') ? $(this).data('inline') : false;
        $(this).datetimepicker({
            inline: datetimepicker_inline
        });
    });
}

/****  Pickadate  ****/
if ($('.pickadate').length && $.fn.pickadate) {
    $('.pickadate').each(function () {
        $(this).pickadate();
    });
}

/****  Pickatime  ****/
if ($('.pickatime').length && $.fn.pickatime) {
    $('.pickatime').each(function () {
        $(this).pickatime();
    });
}

/****  Sortable Panels  ****/
if ($('.sortable').length && $.fn.sortable) {
    $(".sortable").sortable({
        connectWith: '.sortable',
        iframeFix: false,
        items: 'div.panel',
        opacity: 0.8,
        helper: 'original',
        revert: true,
        forceHelperSize: true,
        placeholder: 'sortable-box-placeholder round-all',
        forcePlaceholderSize: true,
        tolerance: 'pointer'
    });
}

/****  Sortable Tables  ****/
if ($('.sortable_table').length && $.fn.sortable) {
    $(".sortable_table").sortable({
        itemPath: '> tbody',
        itemSelector: 'tbody tr',
        placeholder: '<tr class="placeholder"/>'
    });
}

/****  Nestable List  ****/
if ($('.nestable').length && $.fn.nestable) {
    $(".nestable").nestable();
}

/****  Sparkline Inline Charts  ****/
if ($('.sparkline').length && $.fn.sparkline) {
    $('.sparkline').each(function () {
        $(this).sparkline(
            $(this).data("sparkline-value"), {
                type: $(this).data("sparkline-type") ? $(this).data("sparkline-type") : 'bar',
                barWidth: $(this).data("sparkline-bar-width") ? $(this).data("sparkline-bar-width") : 5,
                barSpacing: $(this).data("sparkline-bar-spacing") ? $(this).data("sparkline-bar-spacing") : 2,
                height: $(this).data("sparkline-height") ? $(this).data("sparkline-height") : '20px',
                barColor: $(this).data("sparkline-color") ? $(this).data("sparkline-color") : '#7BB2B4',
                enableTagOptions: true
            });
    });
}

/****  Animation CSS3  ****/
if ($('.animated').length) {
    $('.animated').each(function () {
        delay_animation = parseInt($(this).attr("data-delay") ? $(this).attr("data-delay") : 500);
        $(this).addClass('hide').removeClass('hide', delay_animation);
    });
}

/****  Summernote Editor  ****/
if ($('.summernote').length && $.fn.summernote) {
    $('.summernote').each(function () {
        $(this).summernote({
            height: 300,
            toolbar: [
                ["style", ["style"]],
                ["style", ["bold", "italic", "underline", "clear"]],
                ["fontsize", ["fontsize"]],
                ["color", ["color"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["height", ["height"]],
                ["table", ["table"]]
            ]
        });
    });
}

/****  CKE Editor  ****/
if ($('.cke-editor').length && $.fn.ckeditor) {
    $('.cke-editor').each(function () {
        $(this).ckeditor();
    });
}

/****  Tables Dynamic  ****/
if ($('.table-dynamic').length && $.fn.dataTable) {
    $('.table-dynamic').each(function () {
        var opt = {};
        // Tools: export to Excel, CSV, PDF & Print
        if ($(this).hasClass('table-tools')) {
            opt.sDom = "<'row-fluid'<'span6'T><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
            opt.oTableTools = {
                "sSwfPath": "assets/plugins/datatables/swf/copy_csv_xls_pdf.swf",
                "aButtons": ["csv", "xls", "pdf", "print"]
            };
        }
        if ($(this).hasClass('no-header')) {
            opt.bFilter = false;
            opt.bLengthChange = false;
        }
        if ($(this).hasClass('no-footer')) {
            opt.bInfo = false;
            opt.bPaginate = false;
        }
        var oTable = $(this).dataTable(opt);
        oTable.fnDraw();
    });
}

/****  Gallery Images  ****/
if ($('.gallery').length && $.fn.mixItUp) {
    $('.gallery').each(function () {
        $(this).mixItUp({
            animation: {
                duration: 400,
                effects: "fade translateZ(-360px) stagger(34ms)",
                easing: "ease",
                queueLimit: 3,
                animateChangeLayout: true,
                animateResizeTargets: true
            }
        });
    });
}

function getSmsBalance(){
	var nurl="Email/smsBalance";
	var result = null;
	$.ajax({
		url: nurl,
		type: 'get',
		dataType: 'html',
		async: false,
		cache:false,
		success:function(data){
			result = parseInt(data);
		} 
	});
	return result;
}

/****  On Resize Functions  ****/
$(window).bind('resize', function (e) {
    window.resizeEvt;
    $(window).resize(function () {
        clearTimeout(window.resizeEvt);
        window.resizeEvt = setTimeout(function () {
            sidebarHeight();
            liveTile();
            customScroll();
            handleSlider();
        }, 250);
    });
});


/****  Initiation of Main Functions  ****/
jQuery(document).ready(function () {
    manageSidebar();
    toggleSidebarMenu();
    chatSidebar();
    customScroll();
    liveTile();
    handleSlider();
	$("#closepng").hide();
	$("#fileupload1").hide();
	$("#fileupload").hide();
	$("#closepng1").hide();
	$("#delimg").show();
	$("#delimg1").show();
	$("#delvoice").show();
	$("#closevoice1").hide();
	$("#voiceupload").hide();
	$("#connectcall").submit(function(){
		var url="https://mcube.vmc.in/";
		var err='';
		if($('#number').val()==''){
			err='Please enter Mobile number';
		}
		if($('#number').val()!=''){
			if(!$('#number').val().match(/^[0-9]+$/)){
				err+='Please enter Valid mobile Number';
			}else{
				if(confirm('Are you sure you want to connect the call?')){
					$.ajax({
						 type: "POST",
						 url:url+ "api/click2connect/",
						 data:$("#connectcall").serialize()+'&update_system=update_system', 
						 success: function(msg){
							if(msg==0){
								err="Your Number is in DND,please try with Non-DND Number";
								$('#error').html(err);
								return false;
							}else if(msg=='2'){
								err="Insufficent Credites to make Call";
								$('#error').html(err);
								return false;
							}else{
								err="You will get a call soon";
								$('#error').html(err);
								window.setTimeout(function() {
									window.close();
								}, 1500);
							}
						  }
					});
				}
				err='';
			}
		}
		$('#error').html(err);
		return false;
	});		
      $('#asto').live('change',function(event){
		$('#enumber').html('');
		var enumber=null;
		$.ajax({
			url: '/Report/Edetail/'+$('#asto').val(),
			type: 'get',
			dataType: 'html',
			 async: false,
			 cache:false,
			success:function(data){
				enumber = data;
			} 
		 });
		$('#enumber').html(enumber);
	});
     $('#grempId').live('change',function(event){
	  var r=$('#grempId').val();
	  var im = $('#autoAssign').val();
	  if(im =='auto')
		  var suburl = "/"+$('#autoAssign').val();
	  else if(im=='singleLead')
		 var suburl = '';
	  else
		var suburl = '';
	  $.ajax({  
			type: "POST",  
			url: "leads/get_grEmployees/"+r+suburl,  
			success: function(msg){
				$('#assignemp option').each(function(i, option){ $(option).remove(); });
				$('#assignemp').append(msg);
				}
			});
		});
		
	 $('#landingregion').live('change',function(event){
	  var r=$('#landingregion').val();
	  $.ajax({  
			type: "POST",  
			url: "group/getPri/"+r,  
			success: function(msg){
				$('#prinumber option').each(function(i, option){ $(option).remove(); });
				$('#prinumber').append(msg);
				}
			});
		});
		$('#moreclick').live('click',function(event){
		var i = $("#FromTab1 tr").length;
		var j=i+1;
		$("#FromTab1").append('<tr>'
					+'<th valign="top"><label> :</label></th>'
					+'<td><input type="text" name="option[]" class="form-control" id="option'+j+'" /></td>'
					+'<td><input name="action[]" placeholder="Action" class="form-control" /><a href="javascript:void(null)" class="DelRows">Delete</a></td></tr>');
		});	
		$('#addmores').live('click',function(event){
			var i = $("#statcnt").val();
			if(i < 6){
			 j= parseInt(i)+1;
			 $("#suptktTable").append('<tr>'
						+'<th><label> <input type="text" style="width:150px;" name="syslabel_'+j+'" id="syslabel_'+j+'" />:</label></th>'
						+'<td><input type="text" name="stat_'+j+'" id="stat_'+j+'" value="" class="form-control"  />'
						+'<td><input type="checkbox" name="SMS_'+j+'" id="SMS_'+j+'" value="1" class="form-control" />'
						+'<td><textarea name="smscontent_'+j+'" id="smscontent_'+j+'" class="form-control"></textarea>'
						+'&nbsp;<a href="javascript:void(null)" class="delmores">Delete</a><br/><a title="Click to insert Support Ticket Number" class="addVar" rel="@ticketid@">Ticket Number</a></td></tr>');
			
			$("#statcnt").val(j);
		  }
		});
		$('#addmorelevel').live('click',function(event){
			var i = $("#statcnt").val();
			if(i < 6){
				j= parseInt(i)+1;
				$("#suptktLeveltab").append('<tr>'
						+'<th><label> <input type="text" style="width:120px;" name="label_'+j+'" id="label_'+j+'" />:</label></th>'
						+'<td><input type="text" name="level_'+j+'" id="level_'+j+'" value="" style="width:120px;" />'
						+'<td><input type="text" name="time_'+j+'" id="time_'+j+'" value="" style="width:120px;" />'
						+'&nbsp;<a href="javascript:void(null)" class="delmores">Delete</a></td></tr>');
				$("#statcnt").val(j);
		   }
		});
		$('.DelRows').live('click',function(event){
			$(this).parent().parent().remove();
		});
		$('.delmores').live('click',function(event){
			$(this).parent().parent().remove();
			var i = $("#statcnt").val();
			i= i-1;
			$("#statcnt").val(i);
		});
	$('#eprovider').live('change',function(event){
		var val=$('#eprovider').val();
		if(val!=''){
			if(val=='gmail'){
				$('#smtp').val('ssl://smtp.gmail.com');
				$('#port').val('465');
				$('#smtp').attr('readonly', true);
				$('#port').attr('readonly', true);
			}else if(val=='yahoo'){
				$('#smtp').val('ssl://smtp.mail.yahoo.com');
				$('#port').val('465');
				$('#smtp').attr('readonly', true);
				$('#port').attr('readonly', true);
			}else{
				$('#smtp').val('');
				$('#port').val('');
				$('#smtp').attr('readonly', false);
				$('#port').attr('readonly', false);
			}
		}else{
			$('#smtp').val('');
			$('#port').val('');
			$('#smtp').attr('readonly', false);
			$('#port').attr('readonly', false);
		}
	});
	$('#submit').live('click',function(event) {
		var mod_id=$('#moduleid').val();
		if($('#label_list').val()!="text"){
			var str=$('#labeloptions').val();
			var strs=str.split("\n");
			var splt=[];
			var urls=[];
			for(var i=0;i<strs.length;i++){
				var newstr=strs[i];
				var nws=newstr.split('|');
				if(nws.length>0){
					splt.push(nws[0]);
				}
			}
			var x=jQuery.inArray($('#labeldefaultval').val(),splt);
			if(x!=-1){
				$.ajax({ 
					type: "POST",  
					url: "customfield/Addcustom/",  
					data: $('#customfieldforms').serialize(), 
					success: function(msg){ 
						$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
						$("#modulePage").load('customfield/empmod/'+msg);
						$.unblockUI();
						//window.location.reload();
					}
				});
			}
			else{
				$('#err1').html("Default value not in the options ");
				return false;
			}
		}else{
			$.ajax({ 
					type: "POST",  
					url: "customfield/Addcustom/",  
					data: $('#customfieldforms').serialize(), 
					success: function(msg){ 
						$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
						$("#modulePage").load('customfield/empmod/'+msg);
						$.unblockUI();
						//window.location.reload();
					}
			});
		}
			
	});	
	
	$('.blkemail').live('click',function(event){
		var a = new Array();
		var source=$('.blkemail').attr('rel');
		$("input[class=blk_check]").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		if(a.length>0){
			var s='0';
			n = window.open('/Email/compose/'+s+'/'+source, 'Bulk EMail', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=950,height=480,resizable=1');
			return false;
		}else{
			jAlert('Please Select atleast one  to send Email');
			 return false;
		}
	});	
	//~ $('#compose').live('click',function(event){
		//~ var contents = CKEDITOR.instances['content'].getData();
		//~ $('#content').val(contents);
		//~ $.blockUI({ message: $('<img src="/system/application/img/wait.gif">') });
		//~ $.ajax({  
			//~ type: "POST",  
			//~ url: "/Email/SentMail",  
			//~ data:$("#compose").serialize(), 
			//~ success: function(msg){ 
				//~ var url=window.opener.location.href;
				//~ $('.sterror').html(msg);
				//~ $.unblockUI();
			//~ }
		//~ });
		//~ return false;
	//~ });
	$('.DelConfirm').live('click',function(event){
		var nurl=$(this).attr('href');
		jConfirm('<b>Are you sure to Delete the custom field</b>','Alert', function(r) {
			if(r){
				$.ajax({ 
					type: "POST",  
					url: nurl,  
					success: function(msg){ 
						$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
						$("#modulePage").load('customfield/empmod/'+msg);
						$.unblockUI();
					}
				});
			}
		});
		return false;	
	});
	$('#Update_CustomField').live('click',function(event) {
		var hs=$('#hidvals').val();
		var ids=hs.split('~');
		var id=ids[0];
		var modid=ids[1];
		$.ajax({ 
			type: "POST",  
			url: "customfield/Editcustom/"+id+"/"+modid,  
			data: $('#customfieldforms').serialize(), 
			success: function(msg){ 
				$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
				$("#modulePage").load('customfield/empmod/'+modid);
				$.unblockUI();
			}
		});
	});
	$('#Update_supportLevel').live('click',function(event) {
		var hs=$('#hidvals').val();
		var ids=hs.split('~');
		var id=ids[0];
		var modid=ids[1];
		$.ajax({ 
			type: "POST",  
			url: "customfield/EditTktLel/"+id+"/"+modid,  
			data: $('#supportforms').serialize(), 
			success: function(msg){ 
				$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
				$("#modulePage").load('customfield/empmod/'+modid);
				$.unblockUI();
			}
		});
		return false;
	});
	$('#UpdateSuptktStatus').live('click',function(event) {
		var hs=$('#hidvals').val();
		var ids=hs.split('~');
		var id=ids[0];
		var modid=ids[1];
		$.ajax({ 
			type: "POST",  
			url: "customfield/EditTktStatus/"+id+"/"+modid,  
			data: $('#tktstatforms').serialize(), 
			success: function(msg){ 
				$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
				$("#modulePage").load('customfield/empmod/'+modid);
				$.unblockUI();
			}
		});
	});
	$('#Update_leadStatus').live('click',function(event) {
		var hs=$('#hidvals').val();
		var ids=hs.split('~');
		var id=ids[0];
		var modid=ids[1];
		$.ajax({ 
			type: "POST",  
			url: "customfield/EditLeadstat/"+id+"/"+modid,  
			data: $('#leadstatforms').serialize(), 
			success: function(msg){ 
				$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
				$("#modulePage").load('customfield/empmod/'+modid);
				$.unblockUI();
			}
		});
	});
	
    $('#convertlead').live('click',function(event){
		var blk="none";
		if($(this).is(":checked")){
			blk="block";
		}
		$('#grLabel').parent().removeClass('hidden');
		$('#grLabel').css('display',blk);
		$('#grempId').parent().removeClass('hidden');
		$('#grempId').css('display',blk);
		$('#assignLabel').parent().removeClass('hidden');
		$('#assignLabel').css('display',blk);
		$('#assignemp').parent().removeClass('hidden');
		$('#assignemp').css('display',blk);
		$('#alertLabel').parent().removeClass('hidden');
		$('#alertLabel').css('display',blk);
		$('#alerttype').parent().removeClass('hidden');
		$('#alerttype').css('display',blk);
	});
	$('#updatelead').live('click',function(event){
		var blk="none";
		if($(this).is(":checked")){
			blk="block";
		}
		$('#alertLabel').parent().removeClass('hidden');
		$('#alertLabel').css('display',blk);
		$('#alerttype').parent().removeClass('hidden');
		$('#alerttype').css('display',blk);
		
	});
	$('#convertsuptkt').live('click',function(event){
		var blk="none";
		if($(this).is(":checked")){
			blk="block";
		}
		if(blk == "block"){
			$('#supgrLabel').parent().removeClass('hidden');
			$('#supgrId').parent().removeClass('hidden');
			$('#supassignLabel').parent().removeClass('hidden');
			$('#supEmpid').parent().removeClass('hidden');
			$('#suplevelLabel').parent().removeClass('hidden');
			$('#tkt_level').parent().removeClass('hidden');
			$('#suptimeLabel').parent().removeClass('hidden');
			$('#tkt_esc_time').parent().removeClass('hidden');
			$('#supalertLabel').parent().removeClass('hidden');
			$('#supalerttype').parent().removeClass('hidden');
		}else{
			$('#supgrLabel').parent().addClass('hidden');
			$('#supgrId').parent().addClass('hidden');
			$('#supassignLabel').parent().addClass('hidden');
			$('#supEmpid').parent().addClass('hidden');
			$('#suplevelLabel').parent().addClass('hidden');
			$('#tkt_level').parent().addClass('hidden');
			$('#suptimeLabel').parent().addClass('hidden');
			$('#tkt_esc_time').parent().addClass('hidden');
			$('#supalertLabel').parent().addClass('hidden');
			$('#supalerttype').parent().addClass('hidden');
		}
		$('#supgrLabel').css('display',blk);
		$('#supgrId').css('display',blk);
		$('#supassignLabel').css('display',blk);
		$('#supEmpid').css('display',blk);
		$('#suplevelLabel').css('display',blk);
		$('#tkt_level').css('display',blk);
		$('#suptimeLabel').css('display',blk);
		$('#tkt_esc_time').css('display',blk);
		$('#supalertLabel').css('display',blk);
		$('#supalerttype').css('display',blk);
		
	});
	$('#updatesuptkt').live('click',function(event){
		var blk="none";
		if($(this).is(":checked")){
			blk="block";
		}
		$('#supalertLabel').parent().removeClass('hidden');
		$('#supalertLabel').css('display',blk);
		$('#supalerttype').parent().removeClass('hidden');
		$('#supalerttype').css('display',blk);
	});
	  $('#supgrId').live('change',function(event){
	  var r=$('#supgrId').val();
	  var im = $('#autoAssign').val();
	  if(im =='auto')
		  var suburl = "/"+$('#autoAssign').val();
	  else if(im=='single')
		 var suburl = '';
	  else
		var suburl = '';
	  $.ajax({  
			type: "POST",  
			url: "support/getSupGrpEmp/"+r+suburl,  
			success: function(msg){
					$('#supEmpid option').each(function(i, option){ $(option).remove(); });
					$('#supEmpid').append(msg);
				}
			});
	});
	$('#tkt_level').live('change',function(event){
	  var r=$('#tkt_level').val();
	  $.ajax({  
			type: "POST",  
			url: "support/getSupLevelTime/"+r,  
			success: function(msg){
					$('#tkt_esc_time').val(msg);
				}
			});
	});
	
    $('.clickToConnect').live('click',function(event){
		event.preventDefault();
		var links=this.href;
		$.alerts.okButton = 'Yes';
		$.alerts.cancelButton = 'No';
		jConfirm('Are you sure you want to connect the Number?','Connect Conformation', function(r) {
			if(r){
				$.blockUI({ message: $('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b><br><br><br><img src="system/application/img/loading.gif"><br><br><br><h2>Connecting...</h2>') });
				$.get(links, function(data){
					if(data==1){
						$.blockUI({ message: $('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b><br><br><br><img src="system/application/img/loading.gif"><br><br><br><h2>Your call is in progress...</h2>') });
					}else{
						$.blockUI({ message: $('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b><br><br><h2>Request failed,'+ data +'</h2><br><br>') });
					}
				});
			}
		});
		return false;	
	});
	
	/* add custom fields */
	$('#showtext').hide();
	$('#label_list').live('click',function(event){
		if($('#label_list').val()!=""){
			if($('#label_list').val()!="text"){
				if($('#label_list').val()!="textarea" && $('#label_list').val()!="datetime"){
					$('#labeloptions').addClass('required');
					$('#showtext').show();
				}
				else{
					$('#labeloptions').removeClass('required');
					$('#showtext').hide();
				}
			}else{
				$('#labeloptions').removeClass('required');
				$('#showtext').hide();
			}
		}else{
			$('#labeloptions').removeClass('required');
			$('#showtext').hide();
		}	
	});
	/* add custom fields */
	/* Blk SMS */
	$('.blkSMs').live('click',function(event){
		var a = new Array();
		var nurl=$('.blkSMs').attr('href');
		var source=$('.blkSMs').attr('rel');
		$("input[class=blk_check]").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		if(a.length>0){
			var s='0'; 
			var sms_balance=100;
			sms_balance=getSmsBalance();
			if(a.length<=sms_balance){
					event.preventDefault();
		        	event.stopPropagation();
			        $('embed').hide();
					$.get(nurl+"/"+source, function(data){
						$('#modal-blksms').html(data);
					    $('#to').val(a);
					    $('#source').val(source);
					});
		
				return true;
			}else{
				$("#modal-blksms").modal("hide");
				jAlert("You Don't have sufficient Credit to Send SMS");
				return false;
			}
			return false;
		}else{
		    $("#modal-blksms").modal("hide");
			jAlert('Please Select atleast one  to send SMS');
			 return false;
		}
	});	
	$('.blkStatus').live('click',function(event){
		var a = new Array();
		$(".blk_check").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		if(a.length==0){
			 $("#modal-blkStatus").modal("hide");
			jAlert('Please Select atleast one item to Assign');
			 return false;
		}else{
			$.get(this.href, function(data){
				$("#modal-blkStatus").html(data);
				$('#ids').val(a);
			});
		
			return false;
		}
	});
	$('.add_more_adv').live('click',function(event){
		var j=($('#fsizec').val()!='')?$('#fsizec').val():1;
		var i = parseInt($("#mod_Id").val());
		j++;
		$('#fsizec').val(j);
		if($('#fsizec').val()>=	i){
			$("#sbtn").before('<tr>'+$('#searchF').html()+'<td><a href="javascript:void(0)" class="remRow">Remove</a></td></tr>');		
		}
		if($('#fsizec').val()==i){
			$("#addmore").css('display','none');
		}
	});
	$('.remRow').live('click',function(event){
		//$("#addmore").css('display','inline');
		var j=$('#fsizec').val();
		j--;
		$('#fsizec').val(j);
			$(this).parent().parent().remove();
	});
	/* List View Related JS */
	var cnt=1;
	$('#c_all').live('click',function(event){
		$(".blk_check").each(function(){
			if((cnt%2) == 0){
				$(this).attr('checked',false);
			}else if((cnt%2) == 1){
				if($('input[type=checkbox]').not(':checked')){
					$(this).attr('checked',true);
				}
			}
		});
		cnt++;
	});
	
	$('.blk_calls').live('click',function(event){
		var a = new Array();
		$(".blk_check").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		if(a.length==0){
		   $("#modal-pop").modal("hide");
			jAlert('Please Select atleast one Download');
			 return false;
		}else{
			event.preventDefault();
			event.stopPropagation();
			$('embed').hide();
			$.get(this.href, function(data){
				$("#modal-pop").html(data);
				$('#call_ids').val(a);
			});
			return false;
		}
	});
	$('.blk_assign').live('click',function(event){
		var a = new Array();
		$(".blk_check").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		if(a.length==0){
		    $("#modal-responsive").modal("hide");
			jAlert('Please Select atleast one item to Assign');
			 return false;
		}else{
			event.preventDefault();
			event.stopPropagation();
			$('embed').hide();
			$.get(this.href, function(data){
				$("#modal-responsive").html(data);
				$('#ids').val(a);
			});
		
			return false;
		}
	});
	/*=====BULK DELETE====*/
	$('.blkDeletecall').live('click',function(event){
		var a = new Array();
		var nurl=$('.blkDeletecall').attr('href');
		$(".blk_check").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		if(a.length>0){
			$.alerts.okButton = 'Yes';
			$.alerts.cancelButton = 'No';
			jConfirm('<b>Are you sure to Delete the Selected Information</b>','Alert', function(r) {
				if(r){
					$.ajax({  
							type: "POST",  
							url: nurl, 
							data: "callid="+a, 
							dataType:"text",
							success: function(msg){ 
							  window.location.href=window.location.href
							}
						});
				}
			});
			return false;
		}else{
			jAlert('Please Select atleast one item to delete');
			 return false;
			}
	});
	
		/*=====BULK DELETE====*/
		/*=====LEAD BULK DELETE====*/
	$('.blkDel').live('click',function(event){
		var a = new Array();
		var nurl=$('.blkDel').attr('href');
		$(".blk_check").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		if(a.length>0){
			$.alerts.okButton = 'Yes';
			$.alerts.cancelButton = 'No';
			jConfirm('<b>Are you sure to Delete the Selected Information</b>','Alert', function(r) {
					if(r){
					$.ajax({  
							type: "POST",  
							url: nurl, 
							data: "leadids="+a, 
							dataType:"text",
							success: function(msg){ 
							  window.location.href=window.location.href
							}
						});
				}
			});
			return false;
		}else{
			jAlert('Please Select atleast one item to delete');
			 return false;
			}
	});
			/*=====LEAD BULK DELETE====*/
/*==========================add employee to group===========*/
$('.grp_emp').live('click',function(event){
		var str=$(this).attr('id');
		if($(this).is(":checked")){
			$('#eid'+str).removeAttr('disabled');
			$('#starttime'+str).removeAttr('disabled');
			$('#endtime'+str).removeAttr('disabled');
			$('#isfailover'+str).removeAttr('disabled');
			$('#area_code'+str).removeAttr('disabled');
			$('#empweight'+str).removeAttr('disabled');
			$('#empPriority'+str).removeAttr('disabled');
			$('#pcode'+str).removeAttr('disabled');
		}else{
			$('#eid'+str).attr('disabled',true);
			$('#starttime'+str).attr('disabled',true);
			$('#endtime'+str).attr('disabled',true);
			$('#isfailover'+str).attr('disabled',true);
			$('#area_code'+str).attr('disabled',true);
			$('#empweight'+str).attr('disabled',true);
			$('#empPriority'+str).attr('disabled',true);
			$('#pcode'+str).attr('disabled',true);	
		}
		
	});
	$('.addVar').live('click',function(event){
			$(this).parent().parent().find('textarea').val($(this).parent().parent().find('textarea').val()+this.rel);
	});
	$('.timepicker').timepicker({'minTime': '00:00am', 'timeFormat': 'H:i' });
	$( ".datepicker" ).datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		minDate: -60,
		maxDate: +0
	});
	$( ".datepicker_leads" ).datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
	});
	$("#closeId").live('click',function(event){
		$('embed').show();
		$.unblockUI();
		$("#popupDiv").html('');
	});
	$('.lead_owner').live('click',function(event){
		var a = new Array();
		$(".blk_check").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		if(a.length==0){
			   $("#modal-leadowner").modal("hide");
			jAlert('Please Select atleast one item to Assign');
			 return false;
		}else{
			$.get(this.href, function(data){
				$("#modal-leadowner").html(data);
				$('#ids').val(a);
			});
			return false;
		}
	});
	$('.blkAssign').live('click',function(event){
		var a = new Array();
		$(".blk_check").each(function(){
			if($(this).is(":checked")){
				a.push($(this).val());
			}
		});
		if(a.length==0){
			 $("#modal-blkAssign").modal("hide");
			jAlert('Please Select atleast one item to Assign');
			 return false;
		}else{
			$.get(this.href, function(msg){
				  msg = msg.replace(/(\r\n|\n|\r)/gm,"");
                    $('#modal-blkAssign').html(msg);
				$('#ids').val(a);
				 return true;
			});
		}
	});
	$('.clickToSMS').live('click',function(event){
		event.preventDefault();
		var balance=0;
		balance=getSmsBalance();
		if(balance>0){
			$.get(this.href, function(data){
				$("#modal-empl").html(data);
			});
			return false;
		}else{
			$("#modal-empl").modal("hide");
			jAlert("You Don't have enough credit to send SMS please contact Administrator");
			return false;
		}	
	});
	$('#temp_id').live('change',function(event){
		var tid=$('#temp_id').val();
		$('#sms_content').val('');
		$.get("Email/smsContent/"+tid, function(data){
			$('#sms_content').val($.trim(data));
		});
	});
	$('#convertlead').live('click',function(event){
		var blk="none";
		if($(this).is(":checked")){
			blk="block";
		}
		$('#grLabel').css('display',blk);
		$('#grempId').css('display',blk);
		$('#assignLabel').css('display',blk);
		$('#assignemp').css('display',blk);
		$('#alertLabel').css('display',blk);
		$('#alerttype').css('display',blk);
		
	});
	$('#updatelead').live('click',function(event){
		var blk="none";
		if($(this).is(":checked")){
			blk="block";
		}
		$('#alertLabel').css('display',blk);
		$('#alerttype').css('display',blk);
		
	});
	$('#convertsuptkt').live('click',function(event){
		var blk="none";
		if($(this).is(":checked")){
			blk="block";
		}
		$('#supgrLabel').css('display',blk);
		$('#supgrId').css('display',blk);
		$('#supassignLabel').css('display',blk);
		$('#supEmpid').css('display',blk);
		$('#suplevelLabel').css('display',blk);
		$('#tkt_level').css('display',blk);
		$('#suptimeLabel').css('display',blk);
		$('#tkt_esc_time').css('display',blk);
		$('#supalertLabel').css('display',blk);
		$('#supalerttype').css('display',blk);
	});
	$('#updatesuptkt').live('click',function(event){
		var blk="none";
		if($(this).is(":checked")){
			blk="block";
		}
		$('#supalertLabel').css('display',blk);
		$('#supalerttype').css('display',blk);
	});
	$('.ChangeStatus').click(function(){
			var url="Employee/change_status/"+this.id
			$.get(url, function(data){
				window.parent.location.href = window.parent.location.href;
			});
	});
	$('.changeStatus').click(function(){
		var url="Masteradmin/change_status/"+this.id
			$.get(url, function(data){
				window.parent.location.href = window.parent.location.href;
			});
	});
	$(".deleteClass").live('click',function(event) {
		event.preventDefault();
		event.stopPropagation();
		$('embed').hide();
		var links=this.href;
		$.alerts.okButton = 'Yes';
		$.alerts.cancelButton = 'No';
		jConfirm('Are you sure?','Confirmation Dialog', function(r) {
			if(r){
				$.blockUI({ message: $('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b><img src="system/application/img/wait.gif">') });		
				$.get(links, function(data){
					$.unblockUI();
					window.parent.location.href = window.parent.location.href;
				});
			}
		});
	});
	$('#targettype').live('change',function(event){
		var optval = $("#targettype option:selected").val();
		var nurl = $("#extURL").val();
		$('#ext').val('');
		$('#ext').removeAttr('readonly',false);
		$.ajax({  
			type: "POST",  
			url: nurl,  
			data: "optVal="+optval,
			success: function(msg){
				if(msg == ''){
					$("#tlabel").html(msg);
					$("#testlabel").hide();
				}else{
					$("#testlabel").show();
					$("#tlabel").html(msg);
				}
				return false;
			}
		});
		return false;
	});
	$('.employees').live('change',function(event){
		$('#ext').val('');
		$('#ext').removeAttr('readonly',false);
		$.ajax({  
			type: "POST",  
			url: "pbx/EmpByid/"+$(this).val(),  
			data:'update_system=update_system', 
			success: function(msg){ 
				msg=$.trim(msg);
				if(msg!=''){
					$('#ext').val(msg);
					$('#ext').attr('readonly',true);
				}else{
					$('#ext').val('');
					$('#ext').removeAttr('readonly',false);
				}
			}
		});
	});
	/* Admin Module JS */
	$(".modTitle").change(function(){
		$('.chk_'+$(this).val()).attr('checked',$(this).is(':checked'));
	});
		$('#addmore').live('click',function(event){
			var i = $("#FromTab tr").length;
			$("#FromTab").append('<tr>'
						+'<th valign="top" colspan="2">&nbsp;<input name="emp['+i+'][empname]" placeholder="Name" class="required" style="width:100px;" type="text">&nbsp;&nbsp;'
						+'<input name="emp['+i+'][empemail]" placeholder="Email" class="required email" style="width:150px;" type="text">&nbsp;&nbsp;'
						+'<input name="emp['+i+'][number]" placeholder="Number" class="required number" style="width:100px;" type="text">&nbsp;&nbsp;'
						+'<input name="emp['+i+'][ext]" placeholder="Ext" class="required number" style="width:50px;" type="text">&nbsp;&nbsp;'
						+'<input name="owner" value="'+i+'" class="required" type="radio"> as Owner</th><td><a href="javascript:void(null)" class="DelRow">Delete</a></td></tr>');		
		});
			
		$('.DelRow').live('click',function(event){
			$(this).parent().parent().remove();
		});
		$('#button1').live('click',function(event){
				$("#FrmError").html('');
				$('#configure').validate({
					 errorPlacement: function(error, element) {
						$("#FrmError").append(error);
					}
				});
		});
	   $('#labeldefaultval').removeClass('required');
	   $("#modulePage").load('customfield/empmod/1');
	   $('#modulename').change(function(){
		   $.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
		   $("#modulePage").load('customfield/empmod/'+$('#modulename').val());
		   $.unblockUI();
	   });
	   $("#button3").live('click',function(event) {
			$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
			var modid=parseInt($('#moduleid').val());
			var nxt=parseInt($('#nexT').val());
			$.ajax({  
					type: "POST",  
					url: "customfield/custommange/"+modid,  
					data:$("#customform").serialize()+'&update_system=update_system', 
					success: function(msg){ 
					$("#modulePage").load('customfield/empmod/'+nxt);
					$('#modulename').val(nxt);
					$.unblockUI();	
					}
				});
	});
	$("#button4").live('click',function(event) {
		$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
		var modid=parseInt($('#moduleid').val());
		var pre=parseInt($('#preV').val());
		$.ajax({  
				type: "POST",  
				url: "customfield/custommange/"+modid,  
				data:$("#customform").serialize()+'&update_system=update_system', 
				success: function(msg){ 
				$("#modulePage").load('customfield/empmod/'+pre);
				$('#modulename').val(pre);
				$.unblockUI();	
				}
		});
	});
	
	$("#button5").live('click',function(event) {
		$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
		var modid=parseInt($('#moduleid').val());
		$.ajax({  
			type: "POST",  
			url: "customfield/custommange/"+modid,  
			data:$("#customform").serialize()+'&update_system=update_system', 
			success: function(msg){ 
			$("#modulePage").load('customfield/empmod/'+modid);
			$('#modulename').val(modid);
			$.unblockUI();	
			}
		});
	});
	$('.skip').live('click',function(){
		window.location.href = 'Home';
	});
	$(".modTitle").live('change',function(){
		$('.chk_'+$(this).val()).attr('checked',$(this).is(':checked'));
	});
 
 	$(".deleteClass1").live('click',function(event) {
		event.preventDefault();
		event.stopPropagation();
		$('embed').hide();
		var id=$(".deleteClass1").attr('id');
		var links=this.href;
		$.alerts.okButton = 'Yes';
		$.alerts.cancelButton = 'No';
		$("#modal-responsive").modal("hide");
		jConfirm('Are you sure to undelete group?','Confirmation Dialog', function(r) {
			if(r){
				//$.blockUI({ message: $('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b><img src="system/application/img/wait.gif">') });		
				$.get(links, function(data){
					if(data!=1){
				
						jConfirm(data,'Confirmation Dialog', function(r) {
							if(r){
									event.preventDefault();
									event.stopPropagation();
									$('embed').hide();
									$.blockUI({ message:$('#modal-responsive').load("group/Undelte_emplist/"+id)});
								
							}
							
						});
						
					}else{
						window.location="group/manage_group";
						
					}
				});
			}
		});
		return false;
	
	});
	//~ $(".deleteClass1").live('click',function(event) {
		//~ event.preventDefault();
		//~ event.stopPropagation();
		//~ $('embed').hide();
		//~ var id=$(".deleteClass1").attr('id');
		//~ var links=this.href;
		//~ $.alerts.okButton = 'Yes';
		//~ $.alerts.cancelButton = 'No';
	    //~ $("#modal-responsive").modal("hide");
		//~ jConfirm('Are you sure to undelete group?','Confirmation Dialog', function(r) {
			//~ if(r){
				//~ $.get(links, function(data){
					//~ if(data!=1){
						//~ jConfirm(data,'Confirmation Dialog', function(r) {
							//~ if(r){
									//~ ('#modal-responsive').load("group/Undelte_emplist/"+id);
						     	//~ }
						//~ });
						//~ 
					//~ }else{
						//~ window.location="group/manage_group";
					//~ }
				//~ });
			//~ }
		//~ });
		//~ return false;
	//~ });
	$('.clicktoFields').live('click',function(event){
		$.get(this.href, function(data){
			$("#popupDiv").html('<b id="closeId">&nbsp;&nbsp;&nbsp;&nbsp;</b>' + data);
			$("#popupDiv").css( {
				backgroundColor: '#FFF', 
				borderColor: '#ccc',
				'border-radius': '10px', 
				opacity: .9,
				color: '#000',
				overflow:'auto',
				cursor:		'default'
			});
		});
		$.blockUI({ message: $('#popupDiv') });
		return false;
	});
	$('.SenDSMS').live('click',function(event){
		var smsBal=parseInt($('#smsBal').val());
		$("#sendField").validate({ 
			rules: { 
					"formfields[]": { 
						required: true, 
						minlength: 1 
					} 
			}, 
			messages: { 
					"formfields[]": "Please select at least One field to send SMS"
			},errorPlacement: function(error, element) {
                        error.appendTo( element.parent().next() );
                }
		}); 
		if(smsBal>0){
			return true;
		}else{
			jAlert("You Don't have enough credit to send SMS please contact Administrator");
			return false;
		}
	});
	$('.sendEmail').live('click',function(event){
		var Emailconfig=parseInt($('#Emailconfig').val());
		$("#sendField").validate({ 
			rules: { 
					"formfields[]": { 
							required: true, 
							minlength: 1 
					} 
			}, 
			messages: { 
					"formfields[]": "Please select at least One field to send Email"
			},errorPlacement: function(error, element) {
                        error.appendTo( element.parent().next() );
                }
		}); 
		if(Emailconfig>0){
			return true;
		}else{
			jAlert("Your Email Configuration is not Complete ,Please contact adminstrator");
			return false;
		}
		
	});
	/* Bulk Email End */
	/* List View functions End */
	$('#listView tr').live('click',function(event){
		if($(this).attr('rel')=='1'){
			$(this).find("input[type=checkbox]").attr('checked',false);
			$(this).attr('rel','0');
			$(this).removeClass("danger");
		}else{
			$(this).find("input[type=checkbox]").attr('checked',true);
			$(this).attr('rel','1');
			$(this).addClass("danger");
		}
	});
 /* New functions need to add here */
	$('.sentDelete').live('click',function(event){
		var nurl=$(this).attr('href');
		$.alerts.okButton = 'Yes';
		$.alerts.cancelButton = 'No';
		jConfirm('<b>Are you sure to Delete </b>','Alert', function(r) {
			if(r){
				$.ajax({ 
					type: "POST",  
					url: nurl,  
					success: function(msg){ 
						$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
						$.unblockUI();
						window.location.href=window.location.href
					}
				});
			}
		});
		return false;		
	});	
	$('.EmailTemp').live('click',function(event){
		var nurl=$(this).attr('href');
		$.alerts.okButton = 'Yes';
		$.alerts.cancelButton = 'No';
		jConfirm('<b>Are you sure to Delete </b>','Alert', function(r) {
			if(r){
				$.ajax({ 
					type: "POST",  
					url: nurl,  
					success: function(msg){ 
						$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
						$.unblockUI();
						window.location.href=window.location.href
					}
				});
			}
		});
		return false;		
	});	
	$('.unconfirmEmP').live('click',function(event){
		var nurl=$(this).attr('href');
		$.alerts.okButton = 'Yes';
		$.alerts.cancelButton = 'No';
		jConfirm('<b>Are you sure to Delete Unconfirm Employee</b>','Alert', function(r) {
			if(r){
				$.ajax({ 
					type: "POST",  
					url: nurl,  
					success: function(msg){ 
						$.blockUI({ message: $('<img src="system/application/img/wait.gif">') });
						$.unblockUI();
						window.location.href=window.location.href
					}
				});
			}
		});
		return false;		
	});
});
/* Jquery Document Ready Function end. */
function setMaster(){
	var rate_value = document.addaudio.hdayaudio.value;
	document.form.fileupload1.value = rate_value;
	if(document.form.fileupload1.value ){
		$('#fileupload1').attr('name', 'hdayaudio');
		$('#fileupload1').attr('id', 'hdayaudio');
		$('#hdayaudio').attr('id', 'fileupload1');
		$("#delimg").hide();
		$('#closepng').css('display', 'block');
		$('#closepng').css('margin', '-10px');
		$("#fileupload1").hide();
		$("#closeimg").hide();
		$('#hdayaudio').css('display', 'block');
		$('#hdayaudio').css('float', 'left');
		$("#addimg").hide();
	}
}
function setMaster1(){
	document.form.fileupload.value = document.greetings.greetings.value;
	if(document.form.fileupload.value ){
		$('#fileupload').attr('name', 'greetings');
		$('#fileupload').attr('id', 'greetings');
		$('#greetings').attr('id', 'fileupload');
		$('#closepng1').css('display', 'block');
		$('#closepng1').css('margin', '-10px');
		$("#delimg1").hide();
		$("#fileupload").hide();
		$("#closeimg1").hide();
		$('#greetings').css('display', 'block');
		$('#greetings').css('float', 'left');
		$("#addimg1").hide();
		
	}
}
function setVoice(){
	document.form.voiceupload.value = document.voicemessage.voicemessage.value;
	if(document.form.voiceupload.value ){
	   $('#voiceupload').attr('name', 'voicemessage');
	   $('#voiceupload').attr('id', 'voicemessage');
		$('#voicemessage').attr('id', 'voiceupload');
		$('#closevoice1').css('display', 'block');
		$('#closevoice1').css('margin', '-10px');
		$("#delvoice").hide();
		$("#voiceupload").hide();
		$("#closevoice").hide();
		$('#voicemessage').css('display', 'block');
		$('#voicemessage').css('float', 'left');
		$("#addvoice").hide();
	}
}
function deletefile(){
	document.form.fileupload1.value = '';
	$('#hdayaudio').attr('name', 'fileupload1');
	$('#hdayaudio').attr('id', 'fileupload1');
	$('#fileupload1').attr('id', 'hdayaudio');
	$("#fileupload1").hide();
	$("#hdayaudio").show();
	$("#addimg").show();
	$("#closepng").css("display","none");
}
function deletefile1(){
	document.form.fileupload.value = '';
	$('#greetings').attr('name', 'fileupload');
	$('#greetings').attr('id', 'fileupload');
	$('#fileupload').attr('id', 'greetings');
	$("#fileupload").hide();
	$("#greetings").show();
	$("#addimg1").show();
	$("#closepng1").css("display","none");
}
function deletevoice(){
	document.form.fileupload.value = '';
	$('#voicemessage').attr('name', 'voiceupload');
	$('#voicemessage').attr('id', 'voiceupload');
	$('#voiceupload').attr('id', 'voicemessage');
	$("#voiceupload").hide();
	$("#voicemessage").show();
	$("#addvoice").show();
	$("#closevoice1").css("display","none");
}
$('.btn-list').live('click',function(event){
	var nurl=$(this).attr('href');
	var msg = '';
	$.ajax({
		type: "POST",
		url: nurl,
		success: function(msg){
			msg = msg.replace(/(\r\n|\n|\r)/gm,"");
			$('#modal-list').html(msg);
			return true;
		}
	});
	return true;
});

$('.btn-empl').live('click',function(event){
	var nurl=$(this).attr('href');
	var msg = '';
	$.ajax({
		type: "POST",
		url: nurl,
		success: function(msg){
			msg = msg.replace(/(\r\n|\n|\r)/gm,"");
			$('#modal-empl').html(msg);
			return true;
		}
	});
	return true;
});
$('.btn-followup').live('click',function(event){
	var nurl=$(this).attr('href');
	var msg = '';
	$.ajax({
		type: "POST",
		url: nurl,
		success: function(msg){
			msg = msg.replace(/(\r\n|\n|\r)/gm,"");
			$('#modal-followup').html(msg);
			return true;
		}
	});
	return true;
});
$('.btn-leadhis').live('click',function(event){
	var nurl=$(this).attr('href');
	var msg = '';
	$.ajax({
		type: "POST",
		url: nurl,
		success: function(msg){
			msg = msg.replace(/(\r\n|\n|\r)/gm,"");
			$('#modal-leadhis').html(msg);
			return true;
		}
	});
	return true;
});
 $('.btn-csv').live('click',function(event){
        var nurl=$(this).attr('href');
        $.ajax({
                type: "POST",
                url: nurl,
                success: function(msg){
                    msg = msg.replace(/(\r\n|\n|\r)/gm,"");
                    $('#modal-csv').html(msg);
                    return true;
                }
            });
        return true;
    });
$('.btn-danger').live('click',function(event){
	$('#showtext').hide();
	var nurl=$(this).attr('href');
	$.ajax({
		type: "POST",
		url: nurl,
		success: function(msg){
			msg = msg.replace(/(\r\n|\n|\r)/gm,"");
			$('#modal-responsive').html(msg);
			return true;
		}
	});
	return true;
});
$('.btn-search').live('click',function(event){
	var nurl = $(this).attr('href');
	$.ajax({
		type: "POST",
		url: nurl,
		data:'search=search', 
		success: function(msg){
			msg = msg.replace(/(\r\n|\n|\r)/gm,"");
			$('#modal-search').html(msg);
		}
	});
	return true;
});
$('.btn-advsearch').live('click',function(event){
	var nurl = $(this).attr('href');
	$.ajax({
		type: "POST",
		url: nurl,
		data:'search=advsearch', 
		success: function(msg){
			msg = msg.replace(/(\r\n|\n|\r)/gm,"");
			$('#modal-advsearch').html(msg);
		}
	});
	return true;
});
