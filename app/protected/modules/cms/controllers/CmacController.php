<?php

class CmacController extends CmsController
{
	/**
	 * @var string the default layout for the views. Defaults to 'column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout = 'column1';
	//public $defaultAction='create';
	public $defaultAction='displayPage';
	
	//to ma byc przekazane do widoku (displayPage)
	public $title;
	public $styl;
	public $txt;
	public $success;

	// public $date_first;
	// public $date_last;
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}
	
	public function actions()
	{
		return array(
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	*/
	public function accessRules()
	{
		return array(
			/* array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','create','searchuser'),
				'users'=>array('*'),
			), */
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
                    'actions'=>array('index','create','searchuser','changepassword', 'blockuser','activateuser'),
                    'users'=>array('admin','su'),
            ),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Lists all models.
	*/
	public function actionIndex()
	{
		
		$model=new ApiCalls('search');
		$model->unsetAttributes();
		if (isset($_GET['ApiCalls']))
		$model->attributes = $_GET['ApiCalls'];                
		if(isset($_GET['lang'])){
			Yii::app()->user->setLanguage($_GET['lang']);
		}

		// Get today's date in the correct format
		$today = date('Y-m-d');

		// Modify the criteria to filter by today's date
		$criteria = new CDbCriteria();
		$criteria->addCondition("DATE(request_time) = :today"); // Use request_time instead of created
		$criteria->params = array(':today' => $today);

		Yii::import('application.modules.cms.models.RiDataLookup');
		// Fetch count of today's records from ri_data_lookup
		$count = RiDataLookup::model()->count($criteria);

		$lookupModel = new RiDataLookup('search');

		// Capture filter inputs
		if (isset($_GET['RiDataLookup'])) {
			$lookupModel->attributes = $_GET['RiDataLookup'];
		}

		// Create criteria
		$criteria = new CDbCriteria();
		$criteria->select = 'DATE(request_time) as request_date, COUNT(*) as total_requests';
		$criteria->group = 'DATE(request_time)';
		$criteria->order = 'request_date DESC';

		// Apply date filters
		if (!empty($lookupModel->date_from) && !empty($lookupModel->date_to)) {
			$criteria->addCondition("DATE(request_time) BETWEEN :date_from AND :date_to");
			$criteria->params[':date_from'] = $lookupModel->date_from;
			$criteria->params[':date_to'] = $lookupModel->date_to;
		}

		$lookupDataProvider = new CActiveDataProvider('RiDataLookup', array(
			'criteria' => $criteria,
			'pagination' => array(
				'pageSize' => 10, // Adjust as needed
			),
		));

		// Output the count (for debugging)
		Yii::log("Today's request count: " . $count, CLogger::LEVEL_ERROR, 'application');
	 
		 $this->render('index', array(
			 'model' => $model,
			 'totalCount' => $count, // Pass total count to view
			//  'lookupDataProvider' => $lookupDataProvider,
			'lookupModel' => $lookupModel, // Pass filter model to view
			'lookupDataProvider' => $lookupDataProvider,
		 ));
		// new Code:
		
		/* $this->render('index',array(
			'model'=>$model
		)); */
	}

	/**
	 * Add new api user
	*/
	public function actionCreate()
	{
		include_once './protected/modules/cms/models/ApiUsers.php';
		$model = new ApiUsers;
        if (isset($_POST['yt0']) && $_POST['yt0'] == 'Submit') {
			$model->attributes 	= array('username'=>$_POST['ApiUsers']['username'], 
										'password'=>$_POST['ApiUsers']['password'],
										'chasis'=>NULL,
										'riDataOnlyOk'=>1,
										'uk_regs'=>1,
										'valuations'=>1,
										'mtp_fields'=>'RegNumber, Make, Model, Engine, Colour, mtp_code, Body, Transmission, Fuel, FullYear, RegYear, GRP, Kms',
										'is_test_user'=>NULL,
										'verisk_credential_set'=>NULL,
										'display_mtp_code'=>1,
										'display_function_name'=>'getVeriskDisplayValues',
								);
			if($model->validate()){
				if($model->save()){
						return $this->redirect(array('searchuser'));
				}
			}
            return $this->redirect(['create', 'id' => $model->id]);   
        } else {   
            return $this->render('create', [
                'model' => $model,   
            ]);   
        }

	}

	/**
	 * Lists all users.
	*/
	public function actionSearchUser()
	{
		$model=new ApiUsers('search');
		$model->unsetAttributes();
		if (isset($_GET['ApiUsers']))
			$model->attributes = $_GET['ApiUsers'];                
			if(isset($_GET['lang'])){
				Yii::app()->user->setLanguage($_GET['lang']);
			}

		$this->render('apiusers',array(
						'model'=>$model
					));
	}

	
	/**
	 * Change password
	*/
	public function actionChangepassword($id)
	{
		
		include_once './protected/modules/cms/models/ApiUsers.php';
		$model = new ApiUsers;

		
        if (isset($_POST['yt0']) && $_POST['yt0'] == 'Submit') {
			$model = ApiUsers::model()->findByAttributes(array('id'=>base64_decode($id)));
			
			$model->setScenario('changePwd');
			$model->attributes 	= 	array(
										'old_password'=>trim($_POST['ApiUsers']['old_password']),
										'new_password'=>trim($_POST['ApiUsers']['new_password']),
										'repeat_password'=>trim($_POST['ApiUsers']['repeat_password']),
										'password'=>trim($_POST['ApiUsers']['new_password'])
									);			
			if($model->validate()){
				if($model->save()){
						return $this->redirect(array('searchuser'));
				}
			}else{
				return $this->render('changepassword', [
										'model' => $model,   
									]);   
			}
        } else {
            return $this->render('changepassword', [
                'model' => $model,   
            ]);   
        }
	}

	/**
	 * Block user
	*/
	public function actionBlockuser()
	{
		include_once './protected/modules/cms/models/ApiUsers.php';
		$model = new ApiUsers;
		
        if (isset($_REQUEST['r']) && isset($_REQUEST['id']) && $_REQUEST['r'] == 'cms/cmac/blockuser') {
			$model = ApiUsers::model()->findByAttributes(array('id'=>base64_decode($_REQUEST['id'])));
			$model->status 	= 0;
				if($model->save(false)){
					return $this->redirect(array('searchuser'));
				}
            return $this->redirect(array('searchuser'));
        } else {
            return $this->redirect(array('searchuser'));  
        }
	}

	/**
	 * Activate user
	*/
	public function actionActivateuser()
	{
		include_once './protected/modules/cms/models/ApiUsers.php';
		$model = new ApiUsers;
		
		if (isset($_REQUEST['r']) && isset($_REQUEST['id']) && $_REQUEST['r'] == 'cms/cmac/activateuser') {
			$model = ApiUsers::model()->findByAttributes(array('id'=>base64_decode($_REQUEST['id'])));
			$model->status 	= 1;
				if($model->save(false)){
					return $this->redirect(array('searchuser'));
				}
            return $this->redirect(array('searchuser'));
        } else {
            return $this->redirect(array('searchuser'));  
        }
	}
}
