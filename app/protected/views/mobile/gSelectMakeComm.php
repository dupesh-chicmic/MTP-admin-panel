<?php
/*
 * Widok syboru samochodu z listy CARS
 */
?>
<!--<span class="accordionContent">Have a Reg Plate Instead? Search by <a href="<?php echo Yii::app()->createUrl('mobile/gSelectReg');?>">Reg Plate</a></span>-->

<?php
$style = "";

echo CHtml::beginForm(Yii::app()->createUrl('mobile/gSelectMakeComms'), 'POST', array('id'=>'commsForm', 'style'=>$style,'autocomplete'=>'off' ));

//echo '<h3 style="text-align:center;">Check by Make/Model</h3>';
?>
<!--<div class="ui-field-contain" data-role="fieldcontain">
<fieldset class="ui-controlgroup ui-controlgroup-horizontal ui-corner-all" data-role="controlgroup" data-type="horizontal">
<div class="ui-controlgroup-controls ">
<div class="ui-radio">
<label class="ui-btn ui-corner-all ui-btn-a ui-first-child ui-radio-off" for="radio-view-a-a" data-form="ui-btn-up-a">Passenger</label>
<input id="radio-view-a-a" data-theme="a" name="radio-view-a" value="list" checked="checked" data-cacheval="false" type="radio">
</div>
<div class="ui-radio">
<label class="ui-btn ui-corner-all ui-btn-a ui-last-child ui-btn-active ui-radio-on" for="radio-view-b-a" data-form="ui-btn-up-a">Commercial</label>
<input id="radio-view-b-a" data-theme="a" name="radio-view-a" value="grid" data-cacheval="true" type="radio">
</div>
</div>
</fieldset><div>-->

<!--    
<fieldset data-role="controlgroup" data-type="horizontal">	
     	<input type="radio" name="vehicle_type" id="radio-choice-1" value="cars" checked="checked" />
     	<label for="radio-choice-1">Passenger</label>

     	<input type="radio" name="vehicle_type" id="radio-choice-2" value="comms"  />
     	<label for="radio-choice-2">Commercial</label>

     	
</fieldset>-->
<div id="dw-control-group"  data-role="controlgroup" data-type="horizontal">
  <a href="<?php echo Yii::app()->createUrl('mobile/gSelectMake');?>" class="dw-radio-btn ui-btn ui-corner-all " >Passenger</a>
  <a href="#" class="dw-radio-btn ui-btn ui-corner-all ui-btn-active">Commercial</a>  
</div>
<!--</div>-->
<?php
echo CHtml::hiddenField('vehicle_type', 'comm');
echo CHtml::hiddenField('import_id', $usedCarsMark[0]['id_import']);
//echo CHtml::textField('year', '', array('placeholder'=>'Year', 'id'=>'user_kms', 'class'=>"reg_field", 'maxlength'=>12));
$years = Mobile::getDisplayYears('Yrs2Display_ByMake.xml');

            echo CHtml::dropDownList('year', 'year', 
                $years,
                array(
                  'prompt'=>'Year',
                  'data-iconpos'=>"noicon",
                    
//                  'onchange'=>'document.getElementById("comms_model_details").innerHTML = "";
//                               document.getElementById("comms_model-button").firstChild.innerHTML="Select Model";',
                  'ajax' => array(
                    'type'=>'POST', 
                    'url'=>Yii::app()->createUrl('mobile/loadCommercialMake'),
                    'update'=>'#comms_mark_name', 
                    'data'=>array('year'=>'js:this.value',
                        'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken,
                        'vehicle_type'=>'js:document.getElementById(\'vehicle_type\').value'),
                    'cache'=>false,
                    'beforeSend' => "js:function(data)
                                    {
                                       unselect('#comms_mark_name'); unselect('#comms_ranges');unselect('#comms_model'); unselect('#comms_fuel'); unselect('#comms_transmission'); unselect('#comms_body'); unselect('#comms_doors'); unselect('#cars_badge');
                                       singleSelect('#comms_mark_name');
                                    }",
                    
                ))

            );
    if(!empty($usedCarsMark))
    {
        echo CHtml::dropDownList('comms_mark_name', 'comms_make_s', 
                CHtml::listData($usedCarsMark,'id', 'name'),
                array(
                  'prompt'=>'Select Make',
                  'data-iconpos'=>"noicon",
                  'onchange'=>'document.getElementById("comms_model_details").innerHTML = "";
                               document.getElementById("comms_model-button").firstChild.innerHTML="Select Model";',
                  'ajax' => array(
                    'type'=>'POST', 
                    'url'=>Yii::app()->createUrl('mobile/loadCommercialRanges'),
                    'update'=>'#comms_ranges', 
                    'data'=>array('year'=>'js:document.getElementById(\'year\').value','mark_id'=>'js:this.value','YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                    'cache'=>false,
                    'beforeSend' => "js:function(data)
                                    {
                                       unselect('#comms_ranges');unselect('#comms_model'); unselect('#comms_fuel'); unselect('#comms_transmission'); unselect('#comms_body'); unselect('#comms_doors'); unselect('#cars_badge');
                                       singleSelect('#comms_ranges');
                                    }",
                    
                ))
            );
        
        echo CHtml::dropDownList('comms_ranges','', array(), 
                array(
                  'prompt'=>'Select Range',
                    'data-iconpos'=>"noicon",
                  'ajax' => array(
                  'type'=>'POST', 
                  'url'=>Yii::app()->createUrl('mobile/loadCommercialModel'),
                  'update'=>'#comms_model',
                  'cache'=>false,
                  'dataType'=>'html',
                  'data'=>array('range_id'=>'js:this.value',
                                'year'=>'js:document.getElementById(\'year\').value',
                                'import_nazwa'=>$usedCarsMark[0]['nazwa'],
                                'import_id'=>$usedCarsMark[0]['id_import'],
                                'mark_id'=>'js:document.getElementById(\'comms_mark_name\').value',
                                'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                  'complete' => "js:function(data)
                                    {
                                    unselect('#comms_model'); unselect('#comms_fuel'); unselect('#comms_transmission'); unselect('#comms_body'); unselect('#comms_doors'); unselect('#cars_badge');
                                    singleSelect('#comms_model');
                                    }",
                ))
              );
        
        //loadCarsModel

         echo CHtml::dropDownList('comms_model','', array(), 
                array(
                  'prompt'=>'Select Model',
                    'data-iconpos'=>"noicon",
                  'ajax' => array(
                  'type'=>'POST', 
                  'url'=>Yii::app()->createUrl('mobile/loadCommsFuel'),
                  'update'=>'#comms_fuel',
                  'cache'=>false,
                  'dataType'=>'html',
                  'data'=>array('range_id'=>'js:document.getElementById(\'comms_ranges\').value',
                                'import_nazwa'=>$usedCarsMark[0]['nazwa'],
                                'import_id'=>$usedCarsMark[0]['id_import'],
                                'mark_id'=>'js:document.getElementById(\'comms_mark_name\').value',
                                'model_txt'=>'js:this.value',
                                
                                'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                                'complete' => "js:function(data)
                                    {
                                        unselect('#comms_fuel'); unselect('#comms_transmission'); unselect('#comms_body'); unselect('#comms_doors'); unselect('#cars_badge');
                                        singleSelect('#comms_fuel');
                                    }",
                ))
              );
         //Load fuel
         echo CHtml::dropDownList('comms_fuel','', array(), 
                array(
                  'prompt'=>'Select Fuel',
                    'data-iconpos'=>"noicon",
                  'ajax' => array(
                  'type'=>'POST', 
                  'url'=>Yii::app()->createUrl('mobile/loadCommercialTransmission'),
                  'update'=>'#comms_transmission',
                  'cache'=>false,
                  'dataType'=>'html',
                  'data'=>array('range_id'=>'js:document.getElementById(\'comms_ranges\').value',
                                'import_nazwa'=>$usedCarsMark[0]['nazwa'],
                                'import_id'=>$usedCarsMark[0]['id_import'],
                                'mark_id'=>'js:document.getElementById(\'comms_mark_name\').value',
                                'model_txt'=>'js:document.getElementById(\'comms_model\').value',
                                'comms_fuel'=>'js:this.value',
                                
                                'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                  'complete' => "js:function(data)
                                    {
                                        unselect('#comms_transmission'); unselect('#comms_body'); unselect('#comms_doors'); unselect('#cars_badge');
                                        singleSelect('#comms_transmission');
                                    }",
                ))
              );
         
         //Load Transmission
         echo CHtml::dropDownList('comms_transmission','', array(), 
                array(
                  'prompt'=>'Select Transmission',
                    'data-iconpos'=>"noicon",
                  'ajax' => array(
                  'type'=>'POST', 
                  'url'=>Yii::app()->createUrl('mobile/loadCommercialBody'),
                  'update'=>'#comms_body',
                  'cache'=>false,
                  'dataType'=>'html',
                  'data'=>array('range_id'=>'js:document.getElementById(\'comms_ranges\').value',
                                'import_nazwa'=>$usedCarsMark[0]['nazwa'],
                                'import_id'=>$usedCarsMark[0]['id_import'],
                                'mark_id'=>'js:document.getElementById(\'comms_mark_name\').value',
                                'model_txt'=>'js:document.getElementById(\'comms_model\').value',
                                'comms_fuel'=>'js:document.getElementById(\'comms_fuel\').value',
                                'comms_transmission'=>'js:this.value',
                                
                                'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                  'complete' => "js:function(data)
                                    {
                                        unselect('#comms_body'); unselect('#comms_doors'); unselect('#cars_badge');
                                        singleSelect('#comms_body');
                                    }",
                ))
              );
         
         //body
         echo CHtml::dropDownList('comms_body','', array(), 
                array(
                  'prompt'=>'Select Body',
                    'data-iconpos'=>"noicon",
                  'ajax' => array(
                  'type'=>'POST', 
                  'url'=>Yii::app()->createUrl('mobile/loadCommercialDoors'),
                  'update'=>'#comms_doors',
                  'cache'=>false,
                  'dataType'=>'html',
                  'data'=>array('range_id'=>'js:document.getElementById(\'comms_ranges\').value',
                                'import_nazwa'=>$usedCarsMark[0]['nazwa'],
                                'import_id'=>$usedCarsMark[0]['id_import'],
                                'mark_id'=>'js:document.getElementById(\'comms_mark_name\').value',
                                'model_txt'=>'js:document.getElementById(\'comms_model\').value',
                                'comms_fuel'=>'js:document.getElementById(\'comms_fuel\').value',
                                'comms_transmission'=>'js:document.getElementById(\'comms_transmission\').value',
                                'comms_body'=>'js:this.value',
                                
                                'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                  'complete' => "js:function(data)
                                    {
                                         unselect('#comms_doors'); unselect('#cars_badge');
                                         singleSelect('#comms_doors');
                                    }",
                ))
              );
         
         //load doors
         echo CHtml::dropDownList('comms_doors','', array(), 
                array(
                  'prompt'=>'Select Doors',
                    'data-iconpos'=>"noicon",
                  'ajax' => array(
                  'type'=>'POST', 
                  'url'=>Yii::app()->createUrl('mobile/loadCommsBadgeType'),
                  'update'=>'#comms_badge',
                  'cache'=>false,
                  'dataType'=>'html',
                  'data'=>array('range_id'=>'js:document.getElementById(\'comms_ranges\').value',
                                'import_nazwa'=>$usedCarsMark[0]['nazwa'],
                                'import_id'=>$usedCarsMark[0]['id_import'],
                                'mark_id'=>'js:document.getElementById(\'comms_mark_name\').value',
                                'model_txt'=>'js:document.getElementById(\'comms_model\').value',
                                'comms_fuel'=>'js:document.getElementById(\'comms_fuel\').value',
                                'comms_transmission'=>'js:document.getElementById(\'comms_transmission\').value',
                                'comms_body'=>'js:document.getElementById(\'comms_body\').value',                       
                                'comms_doors'=>'js:this.value',                          
                      
                                'YII_CSRF_TOKEN' => Yii::app()->request->csrfToken),
                  'complete' => "js:function(data)
                                    {
                                        unselect('#cars_badge');
                                        singleSelect('#cars_badge');
                                    }",
                )
                    )
              );
         //load type (badge)
         echo CHtml::dropDownList('comms_badge','', array(), 
                array(
                  'prompt'=>'Select Type',
                    'data-iconpos'=>"noicon",

                    )
              );
         
    }
 echo CHtml::textField('userKms', '', array('placeholder'=>'Enter Kms (if known)', 'id'=>'user_kms', 'class'=>"reg_field", 'maxlength'=>12));
 echo '<br/>';
 //echo CHtml::submitButton('GO',array('class'=>'button1'));
 ?>

<img class="buttonGo" onclick="submitForm()"style="height: 100px; width: 100px;" id="carGo" 
        data-role="button" src="images/mobile/go.png" data-shadow="false" data-iconpos="notext" data-theme="none" />
<br/>
 <?php
    
 echo CHtml::endForm();    
echo '</div>
</div>';     
?>
<script type="text/javascript">
function submitForm(){    
    $('#commsForm').submit();    
}

function singleSelect(child){
    
    size = $(child+' option').size();
    val = $(child+' option').eq(1).val();
    //alert(child+size+' - '+val);
    if(size === 2){
        $(child).val($(child+' option').eq(1).val());
        $(child).selectmenu("refresh");
        $(child).trigger("change");
    }
}

function unselect(child){
    //alert(child);
    $(child+" option:selected").removeAttr("selected");
    $(child).selectmenu("refresh");
}
    
$("input[type='radio']").bind( "change", function(event, ui) {
      $.ajax({
        url: "<?php echo Yii::app()->createUrl('mobile/loadMake');?>",
        type:'POST',
        cache:false,
        update: "#comms_mark_name",
        dataType:'html',
        data: {'vehicle_type':this.value},
        complete : function(data){
            //console.log(data.response);
                                   $('#comms_mark_name').html('<option>make</option>');   
                                   $("option:selected").removeAttr("selected");
                                   //$('#comms_mark_name').html('<option>ala</option>');   
                                 }
        
        
      });
});
</script>

<div id="comms_model_details"></div>

