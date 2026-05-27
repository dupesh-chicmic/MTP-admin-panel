<?php
if (Yii::app()->user->hasFlash('errorMsg')) {
    echo '<div class="flash-error" role="alert">';
    echo Yii::app()->user->getFlash('errorMsg');
    echo '</div>';
}

$arch = null;
        if(!empty($_POST['arch'])){
            $arch = $_POST['arch'];
        }
?>

<?php 
//if (!empty($vehicle)): 
?>
<?php 
////looking for a car the mobile (the guide) way.
//if(Yii::app()->params['is_test_version']){
//
//    if(!empty($vehicle['code'])){
//	//echo ''.$vehicle['body'].'(verisk)<br/>';
//    
//        echo ''.$vehicle['code'].'(verisk)<br/>';       
//    }
//    }
//$carResults = array_merge($main_coreWithAssociatedCarsModel,$rest_coreWithAssociatedCarsModel);
//$calculatedArray = $calculatedMain_coreWithAssociatedCars+$calculatedRest_coreWithAssociatedCars;
//$textQuerriedUCars = array();
//
////adding cars found by text
//
//if(empty($carResults) || $vehicle['body']=='MPV'){    
//if(empty($carResults)){
//    //try to get range by teh first part of teh Versik make:
//    $temp = explode(' ', $vehicle['model']);
//    if(isset($temp[0])){
//        $rangeVeriskCandidate = $temp[0];        
//    }else {
//        $rangeVeriskCandidate = $vehicle['model'];
//    }
//    if(!empty($rangeVeriskCandidate)){
//        $range = UsedCarsRanges::model()->find('rangedesc=:range_desc ORDER BY id desc', array('range_desc'=>$rangeVeriskCandidate));
//        if(empty($range)){
//             $range = UsedCommsRanges::model()->find('rangedesc=:range_desc ORDER BY id desc', array('range_desc'=>$rangeVeriskCandidate));
//        }
//       // var_dump($range);
//    }
//}else {
//    $rangeVeriskCandidate = $carResults[0]['rangecode'];
//    //get range of found car and apply to search
//    $range = UsedCarsRanges::model()->find('rangecode=:range_code ORDER BY id desc', array('range_code'=>$rangeVeriskCandidate));
//        if(empty($range)){
//             $range = UsedCommsRanges::model()->find('rangecode=:range_code ORDER BY id desc', array('range_code'=>$rangeVeriskCandidate));
//        }
//   // var_dump($range);
//}
//    $textQuerriedUCars = RegistrationServiceMob::getCarsByTextValuesFromVerisk($vehicle, 'UsedCarsModel', $range);
//    if($vehicle['body']!='MPV'){
//        echo ' going for COMMS ';
//        $textQuerriedUComms = RegistrationServiceMob::getCarsByTextValuesFromVerisk($vehicle, 'UsedComCarsModel', $range);
//    }
//    if(!empty($textQuerriedUComms)){
//        if(is_array($textQuerriedUCars)){
//            $carResultsText = array_merge($textQuerriedUCars, $textQuerriedUComms);
//        }else {
//            $carResultsText = $textQuerriedUComms;
//        }
//    }else {
//        $carResultsText = $textQuerriedUCars;
//    }
//    
//    $carResults = array_merge($carResults, $carResultsText);
//}else {
//    
//}    
////----    
////echo 'cars to filter:';
//
//$filteredCars = RegistrationServiceMob::getValidVehicles($carResults, $vehicle);
////var_dump($filteredCars);
////---
//
//$valuationArray = array();
//if(!empty($filteredCars)){
//   // echo '->a';
//    $rawResults = RegistrationServiceMob::getScoreForValidVehicles($filteredCars, $vehicle);
//    $results = $rawResults['score'];
//    $valuationArray = $rawResults['values'];
//}else {
//   // echo '->b';
//    $rawResults = RegistrationServiceMob::getScoreForNOTValidVehicles($carResults, $vehicle);
//    $results = $rawResults['score'];
//    $valuationArray = $rawResults['values'];
//}
//
////---
//$skip=0;
//if(!empty($results) && is_array($results)){
//    //echo 'case 1->'.array_shift(array_keys($results)).'<-';
//    
//    //$arrVal = array_keys($results);
//    $bestCarId = RegistrationServiceMob::getFirstValuedVehicle($results, $valuationArray);
//    if(empty($bestCarId)){
//        $arrVal = array_keys($results);
//        $bestCarId = array_shift($arrVal);
//    }
//    
//    $car = UsedComCarsModel::model()->findByPk($bestCarId);
//    if(empty($car)){
//        $arrVal = array_keys($results);
//        $car = UsedCarsModel::model()->findByPk($bestCarId);
//    }
//    //var_dump($car);
//}else {    
//    ///echo 'case 2';
//    if(!empty($carResults) && is_array($carResults)){
//        $arrVal = array_values($carResults);
//        $car = array_shift($arrVal);
//    }else {
//        //Yii::app()->
//        //echo 'Nothing to display';
//        $skip = 1;
//    }
//}
//if(!empty($car)){
//    if(!empty($car->corecode)){
//            if(isset($car->id_used_cars)){
//                $coreCar = UsedCarsModel::model()->find('codenumber=:codenumber AND id_used_cars=:idUsedCars', array('codenumber'=>$car->corecode, 'idUsedCars'=>$car->id_used_cars));
//            }else {
//                $coreCar = UsedComCarsModel::model()->find('codenumber=:codenumber AND id_used_com_cars=:idUsedCars', array('codenumber'=>$car->corecode, 'idUsedCars'=>$car->id_used_com_cars));
//            }
//            
//            $scoreCoreCar = RegistrationServiceMob::scoreTags($coreCar, $vehicle);
//            $arrVal = array_values($results);
//            $topValue = array_shift($arrVal);
//            
//            //var_dump($results);
//         //   echo 'coreCarScore:'.$scoreCoreCar.'>='.$topValue;
//            if($scoreCoreCar>=$topValue){
//                $car = $coreCar;
//            }
//                    
//    }
//}
//
//$model = $car;
//$vehicle ["kmsForYear"]= RegistrationService::getFieldValueForYear("kms", $vehicle['year'], $model);
//echo "MTP (post mob):".$model['codenumber'];
//    echo 'All cars found:'.sizeof($carResults);
////    foreach($carResults as $key=>$val){
////        $veh = UsedComCarsModel::model()->findByPk($key);
////        if(empty($veh)){
////            $veh = UsedCarsModel::model()->findByPk($key);
////        }
////        echo 'Unfiterd veh:'.$veh->codenumber. 'score:'.$val.' ID:'.$veh->id.'<BR>';
////    }
//
//    echo '<br>';
//    echo 'Filtered to:'.sizeof($filteredCars);
//    echo '<br>';
//    //$displayResults = $filteredCars;
//    if(sizeof($filteredCars)==0){
//        //$displayResults = $filteredCars;
//        echo " UN filtered cars scores:";
//    }
//    echo '<br>';
//    foreach($results as $key=>$val){
//        $veh = UsedComCarsModel::model()->findByPk($key);
//        if(empty($veh)){
//            $veh = UsedCarsModel::model()->findByPk($key);
//        }
//        if(array_key_exists($veh->id, $valuationArray)){
//            echo 'veh code:'.$veh->codenumber. ' score:'.$val.' ID:'.$veh->id.' valuation for year:'.$valuationArray[$veh->id].'<BR>';
//        }else {
//            echo 'veh code:'.$veh->codenumber. ' score:'.$val.' ID:'.$veh->id.' <BR>';
//        }
//        
//        
//    }
//    echo '<br>';
//    echo 'Unfitered cars:';
//    if(Yii::app()->params['is_test_version_deep_debug']){
//      
//    }
//    //var_dump($carResults);
?>

<?php 
//car details from API.
$singleCarAPIResults = RegistrationService::getMtpInternalAPIResults($_POST['VehicleRegNumber']);
//var_dump($singleCarAPIResults);
       $model = UsedComCarsModel::model()->findByPk($singleCarAPIResults['vehicles']['car']);
                if(empty($veh)){
                    $model = UsedCarsModel::model()->findByPk($singleCarAPIResults['vehicles']['car']);
                }
                
    var_dump($singleCarAPIResults);
              // var_dump($model);
               // exit;
       //$car = $oneCarResults['car'];
       $vehicle = $singleCarAPIResults['vehicles']['vehicle'];
       $adjustedValue = $singleCarAPIResults['adjustedValue'];
       $returnKms = $singleCarAPIResults['returnKms'];
?>

    <?php
//echo var_dump($vehicle);'MTP (codenumber)='.$model['codenumber'] .' RI (code)='.$vehicle['code']; 
//var_dump($model);
//echo '<br/>';
//var_dump($vehicle);
    ?>
    <h1>Vehicle Data for <?php echo $vehicle['registerVehicleNumber']; ?>
        <?php if(Yii::app()->params['used_car_com_code_column_visibility']){
               echo '(code '.$vehicle['code'].')';
            }
			?>
        <span style="font-size:9px;">powered by <a target="_blank" href="http://www.mywheels.ie">MyWheels.ie</a></span></h1>

    <table class="column1">
        <tr>
            <td><span class="text">Vehicle Year</span></td><td><?php echo $vehicle['year']; ?></td>
        </tr>
        <tr>
            <td><span class="text">Make</span></td><td><?php echo $vehicle['make']; ?></td>
        </tr>
        <tr>
            <td><span class="text">Model</span></td><td><?php echo $vehicle['model']; ?></td>
        </tr>
        <tr>
            <td><span class="text">Colour</span></td><td><?php echo $vehicle['colour']; ?></td>
        </tr>
        <tr>
            <td><span class="text">Body</span></td><td><?php echo $vehicle['body']; ?></td>
        </tr>
    </table>

    <table class="column2">
        <tr>
            <td><span class="text">Engine</span></td><td><?php echo $vehicle['engine']; ?></td>
        </tr>
        <tr>
            <td><span class="text">Fuel</span></td><td><?php echo $vehicle['fuel']; ?></td>
        </tr>
        <tr>
            <td><span class="text">Transmission</span></td><td><?php echo $vehicle['transmission']; ?></td>
        </tr>
        <tr>
            <td><span class="text">Co2</span></td><td><?php echo $vehicle['CO2']; ?></td>
        </tr>
        <tr>
            <td><span class="text">Motor Tax</span></td><td><?php echo $vehicle['roadTax']; ?></td>
        </tr>
    </table>

    <?php if (!empty($model)): ?>
        <div class="associatedContainer">
        <?php $changedModelVehicleData = RegistrationService::getChangeRiDataResults($model, $vehicle); ?>
            <div>MTP Associated Values for: <span class="text"><?php echo $changedModelVehicleData['make'] . ' ' . $changedModelVehicleData['model']; ?></span></div>
            <span class="subTitle">
        <?php echo $vehicle['importTitle']; ?>
            </span>
            <br />
            Average Kilometre Reading <span id="defaultUserGuideKm" class="text"><?php echo $vehicle['kmsForYear']*1000 / 1000; ?></span> km (x1000).
            <br />
            <script type="text/javascript">
                $("#adjustKmByRegLookup").keypress(function (e) {
                    var key = e.which;
                    if (key == 13) {
                        $("#adjustRegLookupBtn").click();
                        return false;
                    }
                });
            </script>
        <?php
       
        
        echo CHtml::beginForm('', '', array('onsubmit' => 'return valid();'));
        echo 'Adjusted Kilometre Reading';
        echo '<div class="form1">' . CHtml::textField('userGuideKm', '', array('class' => 'km', 'id' => 'adjustKmByRegLookup', 'maxlength' => '3', 'autocomplete' => 'off')) . '</div>';
        echo 'km (x1000)';
        echo CHtml::hiddenField('VehicleRegNumber', $vehicle['registerVehicleNumber']);
        echo CHtml::hiddenField('vehicleYear', $vehicle['year']);
        echo CHtml::hiddenField('usedCarComModel', $type);
        echo CHtml::hiddenField('defaultKmsForYear', ($vehicle['kmsForYear']*1000 / 1000));
        echo CHtml::hiddenField('checkedAllCheckboxes', '');
        echo CHtml::hiddenField('customValueGrp', '');
        echo CHtml::hiddenField('mainVehicleMtpCode', $model['codenumber']);
        echo CHtml::hiddenField('coreCodenumberForCustomValue', '');
        echo CHtml::hiddenField('YII_CSRF_TOKEN', Yii::app()->request->csrfToken);
        if(!empty($arch)){
            echo CHtml::button('ADJUST', array('class' => 'button1',
            'id' => 'adjustRegLookupBtn',
            'onclick' => CHtml::ajax(array('type' => 'POST', 'url' => array("registrationService/ajaxCalculateByRegLookUpArch", array('arch'=>$arch)),
                'update' => '#associatedCarTable',
                'beforeSend' => 'doActionsBeforeSend()',
                'complete' => 'showAdjustedNotification()',
            ))
        ));
        }else {
            echo CHtml::button('ADJUST', array('class' => 'button1',
            'id' => 'adjustRegLookupBtn',
            'onclick' => CHtml::ajax(array('type' => 'POST', 'url' => array("registrationService/ajaxCalculateByRegLookUpAlt"),
                'update' => '#associatedCarTable',
                'beforeSend' => 'doActionsBeforeSend()',
                'complete' => 'showAdjustedNotification()',
            ))
        ));
        }
        
        echo Chtml::endForm();
        ?>

            <br />
        </div>

        <!--<div style="display: none;" id="odometerError" class="flash-error" role="alert">Please enter the correct Kms number.</div>-->

        <?php
        echo '<div style="height:50px; margin-bottom: 5px;"><div id="adjustedNotice" style="width: 940px; display:none;" class="flash-warning" role="alert">';
        echo 'Data updating. Please wait...';
        echo '</div></div>';
        ?>
        <?php
        $years = $model['years'];
//$vehicle['year']=11;
        if (!empty($years)) {
            $yearsArr = explode('/', $years);
            if (sizeof($yearsArr) > 1) {
                $yearFrom = $yearsArr[0];
                $yearTo = $yearsArr[1];
            } else {
                $yearFrom = $years;
                $yearTo = $years;
            }
            if ($vehicle['year'] == $yearFrom || $vehicle['year'] == $yearTo) {
                echo '<div style="height:50px; margin-bottom: 5px;"><div id="borderYearWarning" style="width: 940px;" class="flash-warning" role="alert">';
                echo "**Note** Model change may have occurred in this year. MTP Associated values below correspond to model year " . $years . ". 
                        <br/><br/>
                        <B>Please check the exact model of your vehicle and search by Make or model if it differs from the model listed.</b>";
                echo '</div>';
            }
        }
        ?>

        <?php // generowanie PDF z $RIdata ?>
        <div style="width:970px; float:left;">
            <!--<a style="float:right; text-decoration: none; color:#2D8296; font-weight: bolder;" id="pdfByRegLookup" href="<?php echo Yii::app()->createUrl('registrationService/pdfByRegLookUp', array('vnumber' => $vehicle['registerVehicleNumber'], 'type' => $type, 'defaultKmsForYear' => ($vehicle['kmsForYear']*1000 / 1000), 'userGuideKm' => $vehicle['kmsForYear']*1000 / 1000, 'grpCustomValeResult' => '', 'coreCodeNumber' => '', 'checkedCheckboxes' => '', 'end' => 1)) ?>">Download PDF <img src="./images/pdf.png" style="    vertical-align: middle; width:28px; height:28px; border: none;" alt="[pdf]" /></a>-->
            <a style="float:right; text-decoration: none; color:#2D8296; font-weight: bolder;" onclick="openPdf();" id="pdfByRegLookup" href="#">Download PDF <img src="./images/pdf.png" style="    vertical-align: middle; width:28px; height:28px; border: none;" alt="[pdf]" /></a>
        </div>
        <div id="associatedCarTable" style="width:970px;">
        <?php
        //based on mobile app style found car:
        $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
        $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
        ///////////////////////////////////////
        
        
        
        
        $this->renderPartial('_associatedValuesByRegLookUp', array(
            'model' => $model,
            'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
            'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
            'calculatedMain_coreWithAssociatedCars' => $calculatedMain_coreWithAssociatedCars,
            'calculatedRest_coreWithAssociatedCars' => $calculatedRest_coreWithAssociatedCars,
            'vehicleYear' => $vehicle['year'],
            'checkedAllCheckboxes' => '',
            'grpCustomValeResult' => '',
            'calculatedCustomValue' => '',
        ));
        ?>
        </div>
            <?php $searchByMakeModelLink = ($type == 'UsedCarsModel') ? 'member/usedCars' : 'member/usedCommercial'; ?>
        <h1 class="custom" style="text-transform: none;">Not your vehicle? Search <a class="buttonImitation" href="<?php echo Yii::app()->createUrl($searchByMakeModelLink.'IFrame'); ?>">By Make/Model</a></h1>
        <?php else: // empty model ?>

        <div class="associatedContainer" style="padding-bottom: 40px;">
            <div>MTP valuation unavailable at this time. This may be due to:</div>
            <ol type="a">
                <li>Insufficient market activity.</li>
                <li>The age of the vehicle falls outside our normal parameters.</li>
            </ol>
            <span class="text">Please contact our office for a valuation on this vehicle. Phone 01 8775460</span>
        </div>

    <?php endif; ?>

<?php 
//endif; //empty vehicle - data from RI  
?>


<?php // spinner ?>
<script>
    
    function openPdf(){
        var userKms = $('#adjustKmByRegLookup').val();
        var url = '<?php echo Yii::app()->createUrl('registrationService/pdfByRegLookUp', array('vnumber' => $vehicle['registerVehicleNumber'], 'type' => $type, 'defaultKmsForYear' => ($vehicle['kmsForYear']*1000 / 1000), 'grpCustomValeResult' => '', 'coreCodeNumber' => '', 'checkedCheckboxes' => '', 'end' => 1)); ?>';
        var newUrl = url+'&userGuideKm='+userKms;
        //alert(newUrl);
        window.location.href = newUrl;
        
    }
    
    $body = $("body");

    $(document).on({
        ajaxStart: function () {
            $body.addClass("loading");
        },
        ajaxStop: function () {
            $body.removeClass("loading");
        }
    });
</script>
<div class="modal"></div>