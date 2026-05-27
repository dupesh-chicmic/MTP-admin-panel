<?php

echo '<div class="ui-body ui-body-a ui-corner-all" data-theme="a" data-form="ui-body-a">';
echo '<div class="emphasize2">Your Vehicle</div>';
//echo '<div class="tiny_text">(MyWheeles.ie)</div>';
//echo '<div class="emphasize4" style="text-align:center;">151D1234</div>';
$car=null;
if(!empty($data) && is_array($data)){
    if(sizeof($data)==1){
        $car = $data[0];
    }else {
        echo 'more than one result';
    }
}else {    
    echo 'Nothing to display';
    Yii::app()->end();
}
?>
<div class="emphasize2">
<?php 
echo Mobile::displayFullYearForRegYear($_POST['year']).'<br/>';// convert
echo $car['maker'].'<br>';
echo $car['vehicle'].'<br>';
echo $car['badge'].'<br>';
echo Mobile::getFuelText($car['fuel']).'<br>';
echo $car['transmission'].'<br>';
echo $car['bod'].'<br>';
echo $car['drs'].' doors<br>';
echo '</div>';
if(empty($_POST['userKms'])){
    $info = $car->getValueAndKmsForYear($_POST['year']);    
}else {
    $info = $car->getValueAndKmsForYear($_POST['year']);  
        $input = array();
            $kms =  $_POST['userKms']/1000;
            $input['km']=$kms;
            $input['year']=$_POST['year'];
            $input['fuel']=$car['fuel'];            
            $input['guide']=$info['value']; // ze znakiem euro            
            $input['guideKm']=$info['kms']; // km z tabeli          
            $input['import']=$_POST['import_id'];
            $adjustedValue = RegistrationService::getAdjustedValue($input);
            if(Mobile::isNumber($adjustedValue)){
                $info['kms']=$kms;
                $info['value']=$adjustedValue;
            }else {
               echo '<br><div class="results emphasize2"><span class=\'emphasize2\'>'.$adjustedValue.'</span></div>';

            }
            
    
}



?>


<br/>
<div class="results emphasize3">Guide price&nbsp;<span class='emphasize3'>&euro;<?php echo Mobile::displayValue($info['value']); ?></span></div>
<div class="results emphasize2">With&nbsp;<span class='emphasize2'><?php echo Mobile::displayKms($info['kms']); ?>&nbsp;Kms</span></div>

</div>

<br/>
 <img class="buttonBack" onclick="goBack()" style="height: 100px; width: 100px;" id="carGo" 
     data-role="button" src="images/mobile/back.png" data-shadow="false" data-iconpos="notext" data-theme="none" />
    
  


    <script type="text/javascript">
    function goBack(){
        window.location.href='<?php echo Yii::app()->createUrl('mobile/gSelectMake');?>';
    }
    </script>



