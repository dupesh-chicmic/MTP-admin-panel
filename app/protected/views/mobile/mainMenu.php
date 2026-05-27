<?php
/*
 * Menu glowne aplikacji
 */
?>


<ul id="nav">
  <!-- <?php if(!empty(Yii::app()->user->isGuest)): ?>
        <a href="<?php echo Yii::app()->createUrl('mobile/loginPanel'); ?>" data-role="button" data-theme="b" data-corners="false">Subscriber Login</a>
    <?php else: ?>
        <a href="<?php echo Yii::app()->createUrl('mobile/logout'); ?>" data-role="button" data-theme="b" data-corners="false">Logout</a>
    <?php endif; ?>-->
    <a href="<?php echo Yii::app()->createUrl('mobile/logoutwp'); ?>" data-ajax="false" data-role="button" data-theme="b" data-corners="false">Logout</a>
    <!--<a href="<?php echo Yii::app()->createUrl('registrationService/historyCheckMobile'); ?>" data-role="button" data-theme="b" data-corners="false">Check History</a>-->
       <?php
        
        if(!Yii::app()->user->isGuest){
            echo '<a href='.Yii::app()->createUrl('mobile/chooseType').' data-role="button" data-theme="b" data-corners="false">Used Values</a>';
            echo '<a href='.Yii::app()->createUrl('mobile/chooseNewPrices').' data-role="button" data-theme="b" data-corners="false">New Prices</a>';                
        }
        ?>
<!--
<?php if(!empty($mobileMenu)): ?>        
    <?php foreach($mobileMenu as $menuPos): ?>
        <?php
//            if(empty($menuPos->param_1))
//            {
//                $lvUrl = Yii::app()->createUrl('mobile/view',array('url'=>$menuPos->url));
//            }
//            else
//            {
//                $lvUrl = $menuPos->param_1;
//            }
//            /* Tak jak jest w glownej aplikacji */
//            if($menuPos->id == 108) // USED CARS
//            {
//                if(!Yii::app()->user->isGuest)
//                    echo '<a href='.Yii::app()->createUrl('mobile/chooseType').' data-role="button" data-theme="b" data-corners="false">'.$menuPos->link_name.'</a>';
//                continue;
//            } 
//            else if($menuPos->id == 101) // NEW PRICES
//            {
//                if(!Yii::app()->user->isGuest)
//                    echo '<a href='.Yii::app()->createUrl('mobile/chooseNewPrices').' data-role="button" data-theme="b" data-corners="false">'.$menuPos->link_name.'</a>';                
//                continue;
//            }
//                echo '<a href='.$lvUrl.' data-role="button" data-theme="b" data-corners="false">'.$menuPos->link_name.'</a>';
        ?>
    <?php endforeach; ?>
<?php endif; ?>
-->
</ul>
