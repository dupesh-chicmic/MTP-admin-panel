<?php
/*
 * Widok wyboru samochodu z listy CARS
 */
?>
<!--<span class="accordionContent">Don't have reg plate? Search by <a href="<?php echo Yii::app()->createUrl('mobile/gSelectMake');?>">Make/Model</a></span>-->
<?php
//echo $this->renderPartial('//mobile/_usedCarsMobileMenu', 
//            array(//'model'=>$model,
//                  'usedCarsMark'=>$usedCarsMark,                  
//                )); 




    
    echo $this->renderPartial('//registrationService/_checkPlateNumber', 
            array(//'model'=>$model,
                  'usedCarComModel'=>'UsedCarsModel',
                  'importId'=>$usedCarsMark[0]['id_import'],
                  'checkByRegLookUp'=>false
                )); 
    echo '</div>';
    ?>
<div id="cars_model_details"></div>

