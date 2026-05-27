<?php
if(Yii::app()->user->isAdmin || Yii::app()->user->isSu()){
?>

<h3>Guide Token Reset Results</h3>

<?php 
    if(Yii::app()->user->hasFlash('successMsg')){
        echo '<div class="flash-success">';
        echo Yii::app()->user->getFlash('successMsg');
        echo '</div>';
    }    
?>

<div style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
    <h4 style="margin-top: 0;">Execution Log:</h4>
    
    <pre style="background-color: #fff; padding: 15px; border: 1px solid #ddd; border-radius: 4px; overflow-x: auto; font-size: 12px; line-height: 1.6;">
<?php 
    echo CHtml::encode(implode("\n", $results));
?>
    </pre>
</div>

<div style="margin: 20px 0;">
    <?php echo CHtml::link('Back to User Management', array('site/users'), array(
        'class' => 'btn btn-primary',
        'style' => 'padding: 10px 20px; background-color: #0073aa; color: white; text-decoration: none; border-radius: 4px; cursor: pointer; display: inline-block;'
    )); ?>
    
    <?php echo CHtml::link('Reset More Tokens', array('site/resetGuideTokensUI'), array(
        'class' => 'btn',
        'style' => 'padding: 10px 20px; margin-left: 10px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px; cursor: pointer; display: inline-block;'
    )); ?>
</div>

<?php 
}else{
    $this->redirect('index.php');
}
?>
