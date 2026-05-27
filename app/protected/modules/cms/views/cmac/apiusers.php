<?php
/* elementy ze slownika */
$dict_strony 				= 	CmsDictionary::model()->dictionaryGetText('adm_strony');
$dict_zarzadzaj_stronami 	= 	Yii::t(Yii::app()->language.'_YiiTranslation', 'API users');
$dict_wyszukiwanie_zaaw  	= 	Yii::t(Yii::app()->language.'_YiiTranslation', 'API users search');


echo '<a href="http://mtp.ie/app/index.php?r=cms/cmac/create" class="api_users">Add API Users</a>';

$this->breadcrumbs			= 	array(
									$dict_strony=>array('apiusers'),
									$dict_zarzadzaj_stronami,
								);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('cms-cmac-apiusers-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<?php echo '<h1><img class="navImageBig" src="'.$this->module->assetsUrl.'/images/admin/dictionary.png" />'.$dict_zarzadzaj_stronami.'</h1>'; ?>
<?php //echo CHtml::link($dict_wyszukiwanie_zaaw,'#',array('class'=>'search-button')); ?>
<!-- search-form -->
<?php //NOWE ZARZADZANIE STRONAMI 
$provider = $model->search();
$provider->setPagination(array('pageSize'=>25));
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'cms-cmac-apiusers-grid',
	'dataProvider'=>$provider,
	'filter'=>$model,
	'template'=>'{items}{summary}{pager}',
	'columns'=>array(
		array('name'=>'username', 'filter'=>CHtml::activeTextField($model, 'username',array("placeholder"=>"Username","autocomplete"=>"off"))),
		array(
        	'header'=>'password',
			// 'name'=>'password',
            'type'=>'html',
            'value'=> function($model){
				return $model->password;
			}
        ),
		array(
        	'header'=>'status',
			// 'name'=>'password',
            'type'=>'html',
            'value'=> function($model){
				return $model->status && $model->status==1?'Active':'Blocked';
			}
        ),
		// array(
		// 	'class'=>'CButtonColumn',
		// 	'template'=>'{view}',
		// 	'buttons' =>array('view' => array(
		// 			'label'=>'View',
		// 			'imageUrl'=>Yii::app()->request->baseUrl.'/assets/6fceed0/gridview/view.png',
		// 			'url'=>'Yii::app()->createUrl("cmac/view", array("id"=>$data->id))'
		// 		),
		// 	)
		// ),
		array(
			'class'=>'CButtonColumn',
			'template'=>'{changepassword}{blockuser}{activateuser}',
			'buttons' =>array('changepassword' => array(
												'label'=>'Change Password',
												'imageUrl'=>Yii::app()->request->baseUrl.'/assets/6fceed0/gridview/lock.svg',
												'url'=>'Yii::app()->createUrl("cms/cmac/changepassword", array("id"=>base64_encode($data->id)))'
							),
							'blockuser' => array(
								'label'=>'Block User',
								'visible'=>'$data->status == 1',
								'options'=>array('class'=>'blockusers'),
								'imageUrl'=>Yii::app()->request->baseUrl.'/assets/6fceed0/gridview/pwd-change.svg',
								'url'=>'Yii::app()->createUrl("cms/cmac/blockuser", array("id"=>base64_encode($data->id)))',					
							),
							'activateuser' => array(
								'label'=>'Unblock User',
								'visible'=>'$data->status == 0',
								'options'=>array('class'=>'activateuser'),
								'imageUrl'=>Yii::app()->request->baseUrl.'/assets/6fceed0/gridview/pwd-change.svg',
								'url'=>'Yii::app()->createUrl("cms/cmac/activateuser", array("id"=>base64_encode($data->id)))'					
							)
						)
		)
	)
));

?>
<script>
	$(function(){
		// $(document).on('click','.blockusers',function(){
		// 	alert($(this).attr('href'));
		// });
		// $(document).on('click','.activateuser',function(){
		// 	alert($(this).attr('href'));
		// })
	});
</script>

<style>
.api_users {
	width: 100%;
    float: right;
    position: relative;
    text-align: right;
    font-size: 22px;
}
.button-column img{
	height: 20px;
	margin: 5px;
}
</style>