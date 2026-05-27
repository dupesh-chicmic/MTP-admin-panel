<?php
/**
 * 
 *******************************************************************************
 * <hr>
 * Plik: <b>MobileController.php</b><br> 
 * Autor: <b>Mariusz Winiarz</b><br>
 * Firma: <b>Qbix-Soft</b><br>
 * Data utworzenia: <b>10-07-2013</b><br>
 * <hr>
 *******************************************************************************
 * Klasa posiada akcje do zarzadzania wersja mobilna aplikacji
 *******************************************************************************
 * <hr> 
 * @author mariusz 
 *******************************************************************************
 */
class MobileController extends Controller
{
    public static $PARAM_ACCES_DENIED = "Access denied."; 
    public static $PARAM_INCORRECT_LOGIN_PASS = "Login or password is incorrect.";
    public $layout="//layouts/mainMobile";

    /**
     * Metoda ustawia przycisk Back button dla wszystkich akcji z tego kontrolera
     * @param type $action
     * @return boolean
     */
    public function beforeAction($action) {
        Yii::app()->user->setFlash('showBackButton',"SHOW");
        parent::beforeAction($action);
        return true;
    }

    public function accessRules()
    {
            return array(
                    
                    array('allow', // allow authenticated user to perform 'create' and 'update' actions
                            'users'=>array('@'),
                    ),
//                    array('allow', // allow admin user to perform 'admin' and 'delete' actions
//                            'actions'=>array('importXmlFiles','importXml_dieselBands','importXml_petrolBands','importXml_kmsBands','importXmlFilesMain','importXml_used_carsCars','importXml_used_carsCom','deleteArchive','deleteImport'),
//                            'users'=>array('admin','su'),
//                    ),
                    array('deny',  // deny all users
                            'users'=>array('*'),
                    ),
            );
    }
    
    /**
     * Captcha action renders the CAPTCHA image displayed on the contact page
     * @return type
     */
	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			)
		);
	}
    
    /**
     * Metoda renderuje widok menu glownego
     * Akcja jest wywolywana po metodzie beforeAction dlatego dla tej akcji 
     * back button musi byc ukryty
     */
	public function actionMainMenu()
	{
        Yii::app()->user->setFlash('showBackButton',null);
        $this->render('//mobile/mainMenu',array(
            'mobileMenu'=>Mobile::getMobileSites(),
        ));
	}
    
    /**
     * Metoda renderuje strone pobrana po urlu z BD
     */
	public function actionView()
	{
        $lvUrl = (empty($_GET['url'])) ? '' : $_GET['url'];
        if(!empty($lvUrl))
        {
            $lvMobilePage = Mobile::getSiteContent($lvUrl);
            switch($lvMobilePage->layout)
            {
                case 39: // Editorial
                    $lvRenderPage = '//mobile/mobileEditorialsPage';
                    $lvConditions = array(
                        'site'=>$lvMobilePage,
                        'lvNews'=>Mobile::getAllNews()
                    );
                    break;
                case 17: // Contact
                    $lvRenderPage = '//mobile/mobileContactPage';
                    $lvConditions = array('site'=>$lvMobilePage);
                    break;
                default: // Text Page
                    $lvRenderPage = '//mobile/mobileTextPage';
                    $lvConditions = array('site'=>$lvMobilePage);
                    break;
            }
            $this->render($lvRenderPage,$lvConditions);
        }
        else
        {
            $this->forward('mainMenu');
        }               
	}

    /**
     * Render panelu logowania
     */
    public function actionLoginPanel()
    {
        $this->render('//mobile/loginPanel',array('model'=>new LoginForm));
    }
    
    /**
     * Logowanie do aplikacji
     * Dodatkowo jest przeprowadzana walidacja czy user ma dostep do mobile.
     */
    public function actionLogin()
	{
		$model=new LoginForm;
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			if($model->validate()) 
            {
                if(Yii::app()->user->isMobileOn)
                {
                    //$this->forward('chooseType');
                    $this->forward('mainMenu');
                }
                else
                {
                    Yii::app()->user->setFlash('mobileError', self::$PARAM_ACCES_DENIED);
                    $this->forward('mainMenu');
                }
            }
            else
            {
                Yii::app()->user->setFlash('mobileError', self::$PARAM_INCORRECT_LOGIN_PASS);
            }
		}
        $this->forward('loginPanel');
    }   

    /**
     * W akcji sprawdzane jest ciastko. Jeśli jest poprawne renderowany jest 
     * widok w ktorym uzytkownik wybiera typ Cars/Commercial
     */
    public function actionChooseType()
    {
        if(Yii::app()->user->isGuest)
        {
            $this->forward('loginPanel');
        }
        else
        {
            $lvUser = Uzytkownik::model()->find(array(
                'condition'=>'id=:id',
                'params'=>array(':id'=>Yii::app()->user->getId())
            ));
            if(Mobile::checkCookie($lvUser))
            {
                $this->render('//mobile/chooseType',array('userModel'=>$lvUser));
            }
            else
            {
                Yii::app()->user->setFlash('mobileError', self::$PARAM_ACCES_DENIED);
                $this->forward('mainMenu');
            }
        }
    }
    public function actionChooseTypeIFrame()
    {
            $this->layout = '//layouts/iframeMobile';
        if(Yii::app()->user->isGuest)
        {
            $this->forward('loginPanel');
        }
        else
        {
            $lvUser = Uzytkownik::model()->find(array(
                'condition'=>'id=:id',
                'params'=>array(':id'=>Yii::app()->user->getId())
            ));
            if(Mobile::checkCookie($lvUser))
            {
                $this->render('//mobile/chooseType',array('userModel'=>$lvUser));
            }
            else
            {
                Yii::app()->user->setFlash('mobileError', self::$PARAM_ACCES_DENIED);
                $this->forward('mainMenu');
            }
        }
    }
    
    /**
     * Wylogowanie uzytkownika z aplikacji
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->forward('mainMenu');
    }   
    public function actionLogoutwp()
    {   Uzytkownik::destroyNetworkSession();
        Yii::app()->user->logout();
        //$this->redirect(array('/site/loginIframe'));
        
        //header("Location: index.php?r=site/loginIframe");
        //echo '<script>window.top.location.href = "http://mtp.ie/";</script>';
        echo '<script>window.top.location.href = "http://mtp.ie/login/";</script>';
    }   
    
    /**
     * Metoda obslugujaca strone kontaktowa wersji mobilnej
     */
    public function actionContact()
    {
		$model=new ContactForm;
		if(isset($_POST['ContactForm'])) 
        {
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
            {
                $header="From {$model->email}\r\nReply-To: {$model->email}";
                mail(Yii::app()->params['adminEmail'], $model->subject, $model->body, $header);
                Yii::app()->user->setFlash('contact','Message sent.');
                $this->refresh();
            }
            $this->render('//mobile/mobileContactPage',array('site'=>Mobile::getSiteContent('mob_contact')));
            Yii::app()->end();
		}
        $this->actionView('mob_contact');
	}
    
    /**
     * Render widoku pozwalajacego wybrac New Prices
     * Cars / Commercial
     */
    public function actionChooseNewPrices()
    {
        $this->render('chooseNewPrices');
    }
    
     public function actionTempMessage()
    {
        $this->render('tempMessage');
    }
    
    /**
     * CARS
     * Metoda renderuje specjalnie dla mobile przygotowany widok new prices
     */
    public function actionShowNewPricesCars()
    {
        $this->render('mobileNewPricesCars',array('xmlManFileData'=>Mobile::getNewPricesCarsManData()));
    }
    
    public function actionShowNewPricesCarsRanges($manufacturer, $car)
    {
        $this->render('mobileNewPricesCarsRanges',array('car'=>$car, 'manufacturer'=>$manufacturer));
    }

    /**
     * Render widoku New Prices CARS
     */
    public function actionCarModels()
    {
        $this->render('mobileNewPricesCarsModelsList',array(
            'carModels'=>Mobile::getCarData($_GET['car']),
            'rangecode'=>$_GET['rangecode'],
            'file'=>$_GET['car']
            ));
    }

    /**
     * NEW PRICES - CARS
     * Metoda renderuje widok z uzupelniona tablica danych
     */    
    public function actionViewAjaxNewCars()
    {
        $vehicle = array();
        $vehicle['model'] = $_GET['model'];
        $vehicle['doors'] = $_GET['doors'];
        $vehicle['body'] = $_GET['body'];
        $vehicle['retail'] = $_GET['retail'];
        $vehicle['engine'] = $_GET['engine'];
        $vehicle['bhp'] = $_GET['bhp'];
        $vehicle['vrt'] = $_GET['vrt'];
        $vehicle['band'] = $_GET['band'];
        $vehicle['co2'] = $_GET['co2'];
        $vehicle['fuel'] = $_GET['fuel'];
        $vehicle['tax'] = $_GET['tax'];

        //var_dump($vehicle);
        $this->renderPartial('_mobileNewPricesCarsDetails',
                array('vehicle'=>$vehicle), false, true);
    }

    /**
     * COMMERCIAL
     * Metoda renderuje specjalnie dla mobile przygotowany widok new prices
     */    
    public function actionShowNewPricesComm()
    {
        $this->render('mobileNewPricesCommercial',array('xmlManFileData'=>Mobile::getNewPricesCommManData()));
    }
    
    public function actionShowNewPricesCommRanges($manufacturer, $car)
    {
        $this->render('mobileNewPricesCommRanges',array('car'=>$car, 'manufacturer'=>$manufacturer));
    }
    
    /**
     * Render widoku New Prices COMMERCIAL
     */    
    public function actionCommModels()
    {
        $this->render('mobileNewPricesCommModelsList',array(
            'carModels'=>Mobile::getCommData($_GET['car']),
            'rangecode'=>$_GET['rangecode'],
            'file'=>$_GET['car']
            ));
        
      
        
    }
    
    /**
     * NEW PRICES - COMMERCIAL
     * Metoda renderuje widok z uzupelniona tablica danych
     */
    public function actionViewAjaxNewComm()
    {
        $vehicle = array();
        $vehicle['model'] = $_GET['model'];
        $vehicle['body'] = $_GET['body'];
        $vehicle['retail'] = $_GET['retail'];
        $vehicle['gvw'] = $_GET['gvw'];
        $vehicle['cc'] = $_GET['cc'];
        $vehicle['cat'] = $_GET['cat'];
        $vehicle['vrt'] = $_GET['vrt'];
        $vehicle['band'] = $_GET['band'];
        $vehicle['co2'] = $_GET['co2'];
        $vehicle['fuel'] = $_GET['fuel'];
        $vehicle['tax'] = $_GET['tax'];

        //var_dump($vehicle);
        $this->renderPartial('_mobileNewPricesCommDetails',
                array('vehicle'=>$vehicle));
    }    
    
    /**
     * Metoda renderuje specjalnie dla mobile przygotowany widok po wyslaniu formularza valuations
     */
    public function actionValuationThanks()
    {
        unset($_POST);
        $this->render('valuationThanks');
    }    
    
    /* ---------------------------------------------------------------------- */
    /* CARS actions ----------------------------------------------------------*/
    /* ---------------------------------------------------------------------- */
    
    /**
     * Metoda renderuje widok, dodatkowo przekazuje liste marek
     */
    public function actionSelectCars()
    {
        $this->render('//mobile/selectCars',array(
            'usedCarsMark'=>Mobile::getUsedCarsMark(),
        ));
    }
    
    public function actionSelectCarsTest()
    {
        $this->render('//mobile/selectCarsTest',array(
            'usedCarsMark'=>Mobile::getUsedCarsMark(),
        ));
    }
    
    /**
     * Metoda ajaxowo uzupelnia select modelami dla wybranej marki
     */
    public function actionLoadCarsModel()
    {
        //$data = Mobile::getModelsForTheBandForCars((int) $_POST['mark_id']);
        
        $data = Mobile::getModelsByRangeForTheBandForCars((int) $_POST['mark_id'], $_POST['range_id']);
        //vehiclaBadgeAndYear zdefiniowane specjalnie dla tej listy w modelu UsedCarsModel
        $listData = CHtml::listData($data,'id','vehiclaBadgeAndYear');
        echo "<option value=''>Select Model</option>";
        foreach($listData as $value=>$cars_model)
            echo CHtml::tag('option', array('value'=>$value),CHtml::encode($cars_model),true);    
    }
    
    public function actionLoadCarsRanges()
    {
        
        $ranges = UsedCars::getManufacturersRanges((int) $_POST['mark_id']);
        //$data = Mobile::getModelsForTheBandForCars((int) $_POST['mark_id']);
       $ranges = array_flip($ranges);
        //vehiclaBadgeAndYear zdefiniowane specjalnie dla tej listy w modelu UsedCarsModel
        //$listData = CHtml::listData($ranges,'id','vehiclaBadgeAndYear');
        echo "<option value=''>Select Model</option>";
        foreach($ranges as $value=>$cars_model)
            echo CHtml::tag('option', array('value'=>$value),CHtml::encode($cars_model),true);    
    }
    
    /**
     * Metoda renderuje widok z danymi wybranego modelu
     */
    public function actionLoadCarsModelDetails()
    {
        if(!empty($_POST['car_id']))
        {
            $lvDataImportu = (empty($_POST['import_nazwa'])) ? '' : $_POST['import_nazwa'];
            $lvIdImportu = (empty($_POST['import_id'])) ? '' : $_POST['import_id'];
            $this->renderPartial('_detailsData', 
                    array(
                        'data'=>Mobile::getDetailsDataForUsedCars((int) $_POST['car_id']),
                        'nazwa_importu'=>$lvDataImportu,
                        'import_id'=>$lvIdImportu,
                        'carOrCom'=>'UsedCarsModel'
                    ),false,true);
            Yii::app()->end();
        }
    }
    
    /* ---------------------------------------------------------------------- */
    /* COMMERCIAL actions ----------------------------------------------------*/
    /* ---------------------------------------------------------------------- */
    
    /**
     * Metoda ajaxowo renderuje widok z danymi wybranego modelu
     */    
    public function actionSelectCommercials()
    {
        $this->render('//mobile/selectCommercials',array(
            'usedCommercialMark'=>Mobile::getUsedCommercialMark(),
        ));
    }
public function actionLoadCommercialRanges()
    {
        
        $ranges = UsedComCars::getManufacturersRanges((int) $_POST['mark_id']);
        //$data = Mobile::getModelsForTheBandForCars((int) $_POST['mark_id']);
       $ranges = array_flip($ranges);
        //vehiclaBadgeAndYear zdefiniowane specjalnie dla tej listy w modelu UsedCarsModel
        //$listData = CHtml::listData($ranges,'id','vehiclaBadgeAndYear');
        echo "<option value=''>Select Model</option>";
        foreach($ranges as $value=>$cars_model)
            echo CHtml::tag('option', array('value'=>$value),CHtml::encode($cars_model),true);    
    }
    /**
     * Metoda ajaxowo uzupelnia select modelami dla wybranej marki
     */    
    public function actionLoadCommercialModel()
    {
        //$data = Mobile::getModelsForTheBandForCommercial((int) $_POST['mark_id']);
        $data = Mobile::getModelsByRangeForTheBandForComms((int) $_POST['mark_id'], $_POST['range_id']);
        //vehiclaBadgeAndYear zdefiniowane specjalnie dla tej listy w modelu UsedComCarsModel
        $listData = CHtml::listData($data,'id','vehiclaBadgeAndYear');
        echo "<option value=''>Select Model</option>";
        foreach($listData as $value=>$cars_model)
            echo CHtml::tag('option', array('value'=>$value),CHtml::encode($cars_model),true);    
    }

    /**
     * Metoda ajaxowo renderuje widok z danymi wybranego modelu
     */    
    public function actionLoadCommercialModelDetails()
    {
        if(!empty($_POST['car_id']))
        {
            $lvDataImportu = (empty($_POST['import_nazwa'])) ? '' : $_POST['import_nazwa'];
            $lvIdImportu = (empty($_POST['import_id'])) ? '' : $_POST['import_id'];
            $this->renderPartial('_detailsData', 
                    array(
                        'data'=>Mobile::getDetailsDataForUsedCommercial((int) $_POST['car_id']),
                        'nazwa_importu'=>$lvDataImportu,
                        'import_id'=>$lvIdImportu,
                        'carOrCom'=>'UsedComCarsModel'
                    ),false,true);
            Yii::app()->end();
        }
    }    
    
    /**
     * Render widoku po obliczeniach - wyniki sa przekazywane
     */
    public function actionAjaxOdometerCalc()
    {
        $this->renderPartial('//member/_ajaxAdjustedValue',UsedCars::odometerCalculation($_POST));
    }

}
?>
