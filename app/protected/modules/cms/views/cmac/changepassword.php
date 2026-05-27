<?php
$this->breadcrumbs	=	array(
							'Użytkownik API'=>array('/cms/cmac/searchuser'),
							'Zmień hasło użytkownika API',
						);
$menu_admin=array();
if(Yii::app()->user->isAdmin)
{
    $menu_admin=array(array('label'=>'Dodaj użytkownika API', 'url'=>array('cms/cmac/create')),
	array('label'=>'Wyszukaj użytkownika API', 'url'=>array('cms/cmac/SearchUser')));
}

$this->menu=array_merge($menu_admin, array(
    array('label'=>'Dodaj użytkownika API', 'url'=>array('cms/cmac/create')),
	array('label'=>'Wyszukaj użytkownika API', 'url'=>array('cms/cmac/SearchUser')),
));
?>

<?php //echo $this->renderPartial('cmac/_form.php', array('model'=>$model)); ?>
<div class="form" style="width:90%;">
    <h1>Zmień hasło użytkownika API</h1>
<?php
    // $form=$this->beginWidget('CActiveForm', array(
    //                     'id'=>'new-customer-form-newCustomer-form',
    //                     'enableAjaxValidation'=>true,
    //                 )); 
   $form = $this->beginWidget('CActiveForm', array(
                            'id' => 'chnage-password-form',
                            'enableClientValidation' => true,
                            'htmlOptions' => array('class' => 'well'),
                            'clientOptions' => array(
                                'validateOnSubmit' => true,
                            ),
                    ));

                    // echo "<pre>";
                    // print_r($model->getErrors());
                    // print_r($form->errorSummary($model));
                    // die;

?>

	<p class="note">Pola oznaczone <span class="required">*</span> są wymagane.</p>

	<?php echo $form->errorSummary($model); ?>
    <div class="row"> 
        <?php echo $form->labelEx($model,'old_password'); ?> 
        <?php echo $form->passwordField($model,'old_password'); ?> 
        <?php echo $form->error($model,'old_password'); ?> 
    </div>
    
	<div class="row"> 
        <?php echo $form->labelEx($model,'new_password'); ?> 
        <?php echo $form->passwordField($model,'new_password'); ?> 
        <?php echo $form->error($model,'new_password'); ?> 
    </div>

    <div class="row"> 
        <?php echo $form->labelEx($model,'repeat_password'); ?> 
        <?php echo $form->passwordField($model,'repeat_password'); ?> 
        <?php echo $form->error($model,'repeat_password'); ?> 
    </div>

    <div class="row submit">
        <?php echo CHtml::submitButton('Submit'); ?>            
        <?php //$this->widget('CActiveForm', array('buttonType' => 'submit', 'type' => 'primary', 'label' => 'Change password')); ?>
    </div>
    
    <?php $this->endWidget(); ?>

	<!-- <div class="row buttons">
		<?php //echo CHtml::submitButton('Submit'); ?>
	</div> -->
