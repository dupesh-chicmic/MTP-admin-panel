<?php
if(Yii::app()->user->isAdmin || Yii::app()->user->isSu()){
?>

<h3>Reset Guide Mobile Tokens for Selected Users</h3>

<p style="color: #666; margin-bottom: 20px;">
    Select one or more users from the list below to reset their Guide mobile tokens. 
    This will invalidate their current tokens and force them to re-authenticate.
</p>

<?php 
    if(Yii::app()->user->hasFlash('errorMsg')){
        echo '<div class="flash-error">';
        echo Yii::app()->user->getFlash('errorMsg');
        echo '</div>';
    }
    if(Yii::app()->user->hasFlash('successMsg')){
        echo '<div class="flash-success">';
        echo Yii::app()->user->getFlash('successMsg');
        echo '</div>';
    }    
?>

<?php echo CHtml::beginForm(Yii::app()->createUrl('site/resetGuideTokensForSelectedUsers'), 'post', array('id'=>'resetTokenForm')); ?>

<div style="margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px;">
    <label for="selectedUsers" style="display: block; margin-bottom: 10px; font-weight: bold;">
        Select Users:
    </label>
    
    <select name="selectedUsers[]" id="selectedUsers" multiple="multiple" style="width: 100%; height: 300px; padding: 5px;">
        <?php foreach($users as $user): ?>
            <option value="<?php echo $user->id; ?>">
                <?php echo CHtml::encode($user->imie . ' ' . $user->nazwisko . ' (' . $user->login . ' / ' . $user->email . ')'); ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <p style="font-size: 12px; color: #999; margin-top: 8px;">
        Hold Ctrl (or Cmd on Mac) to select multiple users
    </p>
</div>

<div style="margin: 20px 0;">
    <?php echo CHtml::submitButton('Reset Tokens for Selected Users', array(
        'class' => 'btn btn-primary',
        'style' => 'padding: 10px 20px; background-color: #0073aa; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;',
        'onclick' => 'return confirm("Are you sure you want to reset tokens for the selected users?");'
    )); ?>
    
    <?php echo CHtml::link('Cancel', array('site/users'), array(
        'class' => 'btn',
        'style' => 'padding: 10px 20px; margin-left: 10px; background-color: #ccc; color: black; text-decoration: none; border-radius: 4px; cursor: pointer; display: inline-block;'
    )); ?>
</div>

<?php echo CHtml::endForm(); ?>

<?php 
}else{
    $this->redirect('index.php');
}
?>
