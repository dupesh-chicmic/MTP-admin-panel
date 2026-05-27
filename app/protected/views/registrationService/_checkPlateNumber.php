<?php /**
 * Widok w ktorym user wpisuje numer tablicy rejestracyjnej - Made for mobile only
 */
$style = "";
//if(!Yii::app()->mobileDetect->isMobile() && !Yii::app()->mobileDetect->isTablet()){
    // DESKTOP version
?>
   <!-- <div class="contentContainer" style="width: 100%;">
        
    <?php
    if(Yii::app()->user->hasFlash('errorMsg')){
        echo '<div class="flash-error" role="alert">';
        echo Yii::app()->user->getFlash('errorMsg');
        echo '</div>';
    }
?>
    
    <div id="carInfo" style="width: auto; float:left; margin-left: 50px;"><h1 style="text-align: right; padding: 20px 0 0 0;">Enter Irish Registration</h1></div>
        <?php
            echo $this->renderPartial('//registrationService/_checkPlateNumberByRegLookUp', 
                    array('usedCarComModel'=>$usedCarComModel,'useAjax'=>false));
        ?>
    </div>-->
<?php
//} else { ?>
<?php 
//Was in mobile - as a result only first check from RI.
//echo CHtml::beginForm(Yii::app()->createUrl('registrationService/checkPlateNumber'), 'POST', array('style'=>$style,'autocomplete'=>'off','target'=>'_blank')); //'data-ajax'=>'false'
echo CHtml::beginForm(Yii::app()->createUrl('registrationService/checkPlateNumberByRegLookUpMobile'), 'POST', array('style'=>$style,'autocomplete'=>'off' ));
?>
<div class="checkPlateForm">
    <div class="checkPlateNumber">
        <?php echo CHtml::textField('VehicleRegNumber', '', array('id'=>'check_ri_field', 'class'=>"reg_field", 'maxlength'=>12)); ?>
    </div>
    <?php echo CHtml::hiddenField('usedCarComModel', $usedCarComModel, array('maxlength'=>12)); ?>
    <?php echo CHtml::hiddenField('importId', $importId, array('maxlength'=>12)); ?>        
    <?php echo CHtml::hiddenField('useAjax', 1); ?>
</div>
   <?php echo CHtml::submitButton('Check',array('class'=>'button1')); ?>
   
<?php echo CHtml::endForm(); ?>

<script type="text/javascript">
    function setRegNumber()
    {
        var lvRegNr = $("#check_ri_field").val();        
        document.getElementById("colorBoxLinkRegNumber").href="<?php echo Yii::app()->createUrl('registrationService/checkPlateNumberByRegLookUpMobile',array('usedCarComModel'=>$usedCarComModel, 'useAjax'=>1, 'importId'=>$importId, 'VehicleRegNumber'=>"")); ?>"+lvRegNr;
    }
  //  alert(2);
    $('.reg_field').click(function(){
     //   alert(1);
        $(".WrapperPadding").remove();
    });
    
</script>
<?php
//} 
?>
