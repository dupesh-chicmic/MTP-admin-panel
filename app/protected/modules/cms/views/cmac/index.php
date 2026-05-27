<?php
/* elementy ze slownika */
$dict_strony = CmsDictionary::model()->dictionaryGetText('adm_strony');
$dict_zarzadzaj_stronami = Yii::t(Yii::app()->language.'_YiiTranslation', 'Lookup couters');
$dict_wyszukiwanie_zaaw = Yii::t(Yii::app()->language.'_YiiTranslation', 'API Users');

/* */
echo '<a href="http://mtp.ie/app/index.php?r=cms/cmac/searchuser" class="api_users">API Users</a>';
//CHtml::link($dict_wyszukiwanie_zaaw,'http://mtp.ie/app/index.php?r=cms/cmac/searchuser',array('class'=>'search-button','target'=>"_SELF"));

$this->breadcrumbs=array(
	$dict_strony=>array('index'),
	$dict_zarzadzaj_stronami,
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('cms-cmac-index-grid', {
		data: $(this).serialize()
	});
	return false;
});

$( '.date-picker' ).datepicker(jQuery.extend({showMonthAfterYear:false}, jQuery.datepicker.regional['id'], {'showAnim':'fold','dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true','constrainInput':'true'}));

");
// Include Bootstrap tabs
// Yii::app()->clientScript->registerCoreScript('jquery.ui');
?>

<?php echo '<h1><img class="navImageBig" src="'.$this->module->assetsUrl.'/images/admin/dictionary.png" />'.$dict_zarzadzaj_stronami.'</h1>'; ?>


<?php 
// Define Tabs
echo "<div id='tabs'>
        <ul>
            <li><a href='#tab-1'>API Calls</a></li>
            <li><a href='#tab-2'>Verisk Lookup Counts</a></li>
        </ul>";

// this is the date picker
$dateisOn = $this->widget('zii.widgets.jui.CJuiDatePicker', array(
				    'name' => 'ApiCalls[date_first]',
				    'language' => 'id',
					'value' => $model->date_first,
				    // additional javascript options for the date picker plugin
				    'options'=>array(
					'showAnim'=>'fold',
					'dateFormat'=>'yy-mm-dd',
					'changeMonth' => 'true',
					'changeYear'=>'true',
					'constrainInput' => 'false',
				    ),
				    'htmlOptions'=>array(
					'style'=>'height:20px;width:70px;',
					'placeholder'=>'From Date',
					"autocomplete"=>"off"
				    ),
					// DONT FORGET TO ADD TRUE this will create the datepicker return as string
				),true) . ' To  ' . $this->widget('zii.widgets.jui.CJuiDatePicker', array(
					'model'=>$model,
				    'name' => 'ApiCalls[date_last]',
				    'language' => 'id',
					'value' => $model->date_last,
				    // additional javascript options for the date picker plugin
				    'options'=>array(
					'showAnim'=>'fold',
					'dateFormat'=>'yy-dd-mm',
					'changeMonth' => 'true',
					'changeYear'=>'true',
					'constrainInput' => 'false',
				    ),
				    'htmlOptions'=>array(
					'style'=>'height:20px;width:70px',
					'placeholder'=>'To Date',
					"autocomplete"=>"off"
				    ),
					// DONT FORGET TO ADD TRUE this will create the datepicker return as string
				),true);
?>
<?php //NOWE ZARZADZANIE STRONAMI 
$provider = $model->search();
$provider->setPagination(array('pageSize'=>25));
// Tab 1: Existing API Calls Table
echo "<div id='tab-1'>";
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'cms-cmac-index-grid',
	'dataProvider'=>$provider,
	'filter'=>$model,
	// DONT FORGET TO TURN ON afterAjaxUpdate Or after the first search the Datepicker won't Run
	'afterAjaxUpdate'=>"function() {
		jQuery('#ApiCalls_date_first').datepicker(jQuery.extend({showMonthAfterYear:false}, jQuery.datepicker.regional['id'], {'showAnim':'fold','dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true','constrainInput':'true'}));
	 	jQuery('#ApiCalls_date_last').datepicker(jQuery.extend({showMonthAfterYear:false}, jQuery.datepicker.regional['id'], {'showAnim':'fold','dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true','constrainInput':'true'}));
	 }",	
	'template'=>'{items}{summary}{pager}',
	'columns'=>array(
		array('name'=>'reg_number', 'filter'=>CHtml::activeTextField($model, 'reg_number',array("placeholder"=>"Registration Number","autocomplete"=>"off"))),
		array(
            // 'header'=>'Username',
			'name'=>'username',
            // 'type'=>'html',
            'value'=> function($model){
				return $model->username;
			}
        ),
		array(
			'name'=>'created',
			'filter'=>$dateisOn,
			'value'=>function($model){
				return date("Y-m-d", strtotime($model->created));
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
	),
)); 
echo "</div>"; // Close tab-1

// Tab 2: New Lookup Counts Table
echo "<div id='tab-2'>";
/* $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'ri-data-lookup-grid',
    'dataProvider' => $lookupDataProvider,
    'template' => '{items}{summary}{pager}',
    'columns' => array(
        array(
            'name' => 'request_date',
            'header' => 'Date',
            'value' => '$data->request_date',
        ),
        array(
            'name' => 'total_requests',
            'header' => 'API Lookup Count',
            'value' => '$data->total_requests',
        ),
    ),
)); */

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'ri-data-lookup-grid',
    'dataProvider' => $lookupDataProvider,
    'filter' => $lookupModel, // Add filter model
    'template' => '{items}{summary}{pager}',
	'afterAjaxUpdate'=>"function() {
		jQuery('.date-picker').datepicker(jQuery.extend({showMonthAfterYear:false}, jQuery.datepicker.regional['id'], {'showAnim':'fold','dateFormat':'yy-mm-dd','changeMonth':'true','showButtonPanel':'true','changeYear':'true','constrainInput':'true'}));
	}",
	
    'columns' => array(
        array(
            'name' => 'request_date',
            'header' => 'Date',
            'value' => '$data->request_date',
            'filter' => '<div style="display: flex; gap: 5px; align-items: center;">
							From ' . CHtml::activeTextField($lookupModel, 'date_from', array(
								'placeholder' => 'YYYY-MM-DD',
								'style' => 'width: 90px;',
								"autocomplete" => "off",
								'class' => 'date-picker'
							)) . 
							' To ' . CHtml::activeTextField($lookupModel, 'date_to', array(
								'placeholder' => 'YYYY-MM-DD',
								"autocomplete" => "off",
								'style' => 'width: 90px;',
								'class' => 'date-picker'
							)) . 
						'</div>',
        ),
        array(
            'name' => 'total_requests',
            'header' => 'API Lookup Count',
            'value' => '$data->total_requests',
        ),
    ),
));
/* $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'ri-data-lookup-grid',
    'dataProvider' => $lookupDataProvider,
    'filter' => $lookupModel, // Add filter model
    'template' => '{items}{summary}{pager}',
    'columns' => array(
        array(
            'name' => 'request_date',
            'header' => 'Date',
            'value' => '$data->request_date',
            'filter' => '<div style="display: flex; gap: 5px; align-items: center;">
			From ' . CHtml::activeTextField($lookupModel, 'date_from', array(
							'placeholder' => 'YYYY-MM-DD',
							'style' => 'width: 90px;',
							'class' => 'date-picker'
						)) . 
						' To ' . CHtml::activeTextField($lookupModel, 'date_to', array(
							'placeholder' => 'YYYY-MM-DD',
							'style' => 'width: 90px;',
							'class' => 'date-picker'
						)) . 
					'</div>',
        ),
        array(
            'name' => 'total_requests',
            'header' => 'API Lookup Count',
            'value' => '$data->total_requests',
        ),
    ),
)); */
echo "</div>"; // Close tab-2

echo "</div>"; // Close tabs

// Initialize jQuery Tabs
Yii::app()->clientScript->registerScript('tab-script', "
    $('#tabs').tabs();
");
?>

<style>
.api_users {
	width: 100%;
    float: right;
    position: relative;
    text-align: right;
    font-size: 22px;
}
.custom-api-lookup {
    float: right;
    margin-top: 35px;
	font-size: 15px;
	font-weight: bold;
}
#tatalCount {
	color: #f06666;
}
</style>