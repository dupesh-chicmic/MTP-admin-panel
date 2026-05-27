<?php
$this->breadcrumbs	=	array(
							'Użytkownik API'=>array('/cms/cmac/searchuser'),
							'Tworzenie konta użytkownika API',
						);
$menu_admin=array();
if(Yii::app()->user->isAdmin)
{
    $menu_admin=array(array('label'=>'Dodaj użytkownika API', 'url'=>array('cms/cmac/create')),
	array('label'=>'Wyszukaj użytkownika API', 'url'=>array('cms/cmac/SearchUser')));
}

$this->menu=array_merge($menu_admin, array(
        array('label'=>'Dodaj klienta', 'url'=>array('cms/cmac/create')),
	array('label'=>'Wyszukaj klienta', 'url'=>array('cms/cmac/SearchUser')),

));
?>

<?php //echo $this->renderPartial('cmac/_form.php', array('model'=>$model)); ?>
<div class="form" style="width:90%;">

    <h1>Rejestracja nowego użytkownika API</h1>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'new-customer-form-newCustomer-form',
	'enableAjaxValidation'=>true,
)); ?>

	<p class="note">Pola oznaczone <span class="required">*</span> są wymagane.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->textField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>
<?php $this->endWidget(); ?>
