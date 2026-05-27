<div class="wide form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl('cms/cmac/index'),
	'enableAjaxValidation'=>true,
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'reg_number'); ?>
		<?php echo $form->textField($model,'reg_number'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'created'); ?>
		<?php echo $form->textField($model,'created'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton('Szukaj'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- search-form -->