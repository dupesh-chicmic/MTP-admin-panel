<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
        <meta http-equiv="cache-control" content="max-age=0" />
        <meta http-equiv="cache-control" content="no-cache" />
        <meta http-equiv="expires" content="0" />
        <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
        <meta http-equiv="pragma" content="no-cache" />
        <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/mobile.css" />
        <script type="text/javascript">var activeRowMw="";</script>
        <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />     
<?php
Yii::app()->getClientScript()->registerCssFile(Yii::app()->request->baseUrl.'/js/jquery-mobile/jquery.mobile-1.4.5.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery-mobile/jquery.mobile-1.4.5.min.js',CClientScript::POS_HEAD);
Yii::app()->clientScript->registerCoreScript('yiiactiveform');
//Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery1.10.min.js',CClientScript::POS_HEAD);
Yii::app()->clientScript->registerCoreScript('jquery');

Yii::app()->clientScript->registerScript('my_script',"
            $(document).ready(function() {
            
                $('#login').focus(function() {
                    if($(this).val() == '') {
                        $(this).removeClass('login')
                    }
                }).blur(function() {
                      if($(this).val() == '') {
                        $(this).addClass('login')
                    } 
                })
                $('#pwd').focus(function() {
                    if($(this).val() == '') {
                        $(this).removeClass('pwd')
                    }
                }).blur(function() {
                      if($(this).val() == '') {
                        $(this).addClass('pwd')
                    } 
                })
                $.cookiesDirective({
                    explicitConsent: true,
                    linkClass: 'tandc cbox cboxElement',
                    privacyPolicyUri: '#',
                    duration: 3600,
                    fontSize: '15px',
                    fontColor: '#68b5c2',
                    linkColor: '#3388cc'
                });   
   
$(function() {
    // WARNING: Extremely hacky code ahead. jQuery mobile automatically
    // sets the current \"page\" height on page resize. We need to unbind the
    // resize function ONLY and reset all pages back to auto min-height.
    // This is specific to jquery 1.8

    // First reset all pages to normal
    $('[data-role=\"page\"]').css('min-height', 'auto');

    // Is this the function we want to unbind?
    var check = function(func) {
        var f = func.toLocaleString ? func.toLocaleString() : func.toString();
        // func.name will catch unminified jquery mobile. otherwise see if
        // the function body contains two very suspect strings
        if(func.name === 'resetActivePageHeight' || (f.indexOf('padding-top') > -1 && f.indexOf('min-height'))) {
            return true;
        }
    };

    // First try to unbind the document pageshow event
    try {
        // This is a hack in jquery 1.8 to get events bound to a specific node
        var dHandlers = $._data(document).events.pageshow;

        for(x = 0; x < dHandlers.length; x++) {
            if(check(dHandlers[x].handler)) {
                $(document).unbind('pageshow', dHandlers[x]);
                break;
            }
        }
    } catch(e) {}

    // Then try to unbind the window handler
    try {
        var wHandlers = $._data(window).events.throttledresize;

        for(x = 0; x < wHandlers.length; x++) {
            if(check(wHandlers[x].handler)) {
                $(window).unbind('throttledresize', wHandlers[x]);
                break;
            }
        }
    } catch(e) {}
});
    $.mobile.resetActivePageHeight();
            })"
    ,CClientScript::POS_HEAD);
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.cookiesdirective.js',CClientScript::POS_HEAD);
?>
        
	<title><?php 
        if(isset($_GET['url'])) {
            $subTitle = CmsPage::model()->getElement('url', $_GET['url'], 'CmsPage', 'title');
            echo Yii::app()->name.' - '.$subTitle;//echo CHtml::encode($this->pageTitle);
        }else {
            echo Yii::app()->name;
        }
        ?></title>
 
</head>
<body>
       <!-- <div id="header">
            <div class="width">
                <h1><a href="#"></a></h1>
            </div>
        </div>-->
        
         <div id="content">
             <div class="width">
                <?php if(Yii::app()->user->hasFlash('mobileError')): ?>                    
                    <div class="flash-error"><?php echo Yii::app()->user->getFlash('mobileError'); ?></div>
                <?php endif; ?>

                    <?php 
                        if(Yii::app()->user->hasFlash('showBackButton')){
                            echo '<a href="'.Yii::app()->createUrl('mobile/mainMenu').'" data-role="button" data-theme="b" data-corners="false">Back to login menu</a>';
                        }
                    ?>
                    
                <?php echo $content; ?>
             </div>
         </div>
        
    <!--<div data-role="footer" class="ui-bar">    
        <div id="footer">
            <img src="images/logo3.png">
        </div>
        <div class="centered">
            <div class="width">
                <?php echo CmsDictionary::model()->dictionaryGetText('front_footer_address'); ?>
            </div>
            <div class="width" style="padding-left:25px;">
               <?php echo CmsDictionary::model()->dictionaryGetText('front_footer_phone'); ?>   
               <br /><?php echo CmsDictionary::model()->dictionaryGetText('front_footer_email'); ?>    
            </div>
        </div>
    </div>-->
    <script>
    $(document).ready(function() {
       
       // alert(2);
// wont work here - place all jquerymobile binding into on pagechange below
        $("div.ui-collapsible-set").on( "collapsibleexpand", function(e) {
            calculateHeightOnAjax();
        });
        $("div.ui-collapsible-set").on( "collapsiblecollapse", function(e) {
             //window.parent.sendHeight();
             
             calculateHeightOnAjax();
        });
        $("a.ui-collapsible-heading-toggle").on( "click", function(e) {
          //  alert(4);
          // window.parent.sendHeight();
           calculateHeightOnAjax();
        });

        $(document).click(function(e) {
             window.parent.sendHeight();
        });
        $('select').change(function(e) {
            window.parent.sendHeight();
        });
        $('#ajaxUpdateDiv').bind('DOMSubtreeModified', function() {
              window.parent.sendHeight();
        });
    });
    
    $(document).on('pagechange', function(){       
            console.log('Page loaded');
           
            $('.ui-page').each(function(){
                if ($( this ).is(":visible") === true) { 
                    
                    //visble_height = $( this ).height();
                    visble_height = $( this ).find('#content').height()+30;
                    
                    console.log('class visible'+visble_height);
                   // window.parent.setHeight($(document).height());
                  // alert('ui-page:'+visble_height)
                    window.parent.setHeight(visble_height);
                }else {
                    un_visble_height = $( this ).height();
                //    console.log('class NOT visible'+un_visble_height);
                }    
            });
           
            $("div.ui-collapsible-set").on( "collapsibleexpand", function(e) {
                //console.log('collapsible set EXPAND');
                //calculateHeightOnAjax();
                window.parent.sendHeight();
            });
            $("div.ui-collapsible-set").on( "collapsiblecollapse", function(e) {
                //console.log('collapsible set COLLAPSED');
                //calculateHeightOnAjax();
                window.parent.sendHeight();
            });

        
    });
    

    
    function calculateHeightOnAjax(){       
            console.log('Ajax action');
            console.log($(document).height());
           // alert('in'+$(document).height());
            
            $('.ui-page').each(function(){
             //   console.log('i');
                if ($( this ).is(":visible") === true) { 
                    
                    //visble_height = $( this ).height();
                    visble_height = $( this ).find('#content').height()+30;
                    
                    console.log('class visible'+visble_height);
                   // window.parent.setHeight($(document).height());
                  // alert('ui-page:'+visble_height)
                    window.parent.setHeight(visble_height);
                }else {
                    un_visble_height = $( this ).height();
                //    console.log('class NOT visible'+un_visble_height);
                }    
            });
        
    };
    
    function calculateHeightOnAjaxAddMore(addMore){       
            console.log('Ajax action add more');
            console.log($(document).height());
           // alert('in'+$(document).height());
            
            $('.ui-page').each(function(){
             //   console.log('i');
                if ($( this ).is(":visible") === true) { 
                    
                    //visble_height = $( this ).height();
                    visble_height = $( this ).find('#content').height()+30+addMore;
                    
                    console.log('class visible'+visble_height);
                   // window.parent.setHeight($(document).height());
                  // alert('ui-page:'+visble_height)
                    window.parent.setHeight(visble_height);
                }else {
                    un_visble_height = $( this ).height();
                //    console.log('class NOT visible'+un_visble_height);
                }    
            });
        
    };
    
//    $(document).on('pageinit', function(){       
// 
//           
//                    visble_height = $('#content').height()+20;
//                    
//                    console.log('class visible'+visble_height);
//                   // window.parent.setHeight($(document).height());
//                  // alert('init:'+visble_height)
//                    window.parent.setHeight(visble_height);
//                  
//            });
        

    
    
//      $mobile.on( "changePage", function() {
//        console.log( 'aabb' );
//      });
 //   ui-page
//    $.mobile.changePage(
//            function(e) {
//                alert('cp');
//            window.parent.sendHeight();
//        }
//            );
    </script>
</body>
</html>