<?php
echo '<div class="ui-body ui-body-a ui-corner-all" data-theme="a" data-form="ui-body-a">';
echo '<!--<div class="emphasize2">Your Vehicle</div>-->';


echo '<div class="emphasize4" style="text-align:center;">'.$_POST['VehicleRegNumber'].'</div>';

?>

<?php 
echo ''.$vehicle['year'].'<br/>';
echo ''.$vehicle['make'].'<br/>';
echo ''.$vehicle['model'].'<br/>';
echo ''.$vehicle['colour'].'<br/>';
echo ''.$vehicle['engine'].'<br/>';
echo ''.$vehicle['fuel'].'<br/>';
echo ''.$vehicle['transmission'].'<br/>';
echo ''.$vehicle['CO2'].'<br/>';
echo ''.$vehicle['roadTax'].'<br/>';
?>
<br/>
<div class="results">Guide price&nbsp;<span class='emphasize3'>&euro;XX,777</span></div>
<div class="results">With&nbsp;<span class='emphasize3'>XX,000&nbsp;Kms</span></div>

</div>

<br/>
 <img class="buttonBack" onclick="goBack()" style="height: 100px; width: 100px;" id="carGo" 
     data-role="button" src="images/mobile/back.png" data-shadow="false" data-iconpos="notext" data-theme="none" />
    
  
<?php
$carResults = array_merge($main_coreWithAssociatedCarsModel,$rest_coreWithAssociatedCarsModel);
$calculatedArray = $calculatedMain_coreWithAssociatedCars+$calculatedRest_coreWithAssociatedCars;

var_dump($carResults);
echo '---------------------------------------';
//var_dump($calculatedArray);
$filteredCars = RegistrationService::getValidVehicles($carResults, $vehicle);
var_dump($filteredCars);


?>

    <script type="text/javascript">
    function goBack(){
        window.location.href='<?php echo Yii::app()->createUrl('mobile/gSelectReg');?>';
    }
    </script>

</div>