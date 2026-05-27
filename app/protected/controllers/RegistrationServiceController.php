<?php
/**
 *
 *******************************************************************************
 * <hr>
 * Plik: <b>RegistrationServiceController.php</b><br>
 * Autor: <b>Mariusz Winiarz</b><br>
 * Firma: <b>Qbix-Soft</b><br>
 * Data utworzenia: <b>12-01-2014</b><br>
 * <hr>
 *******************************************************************************
 * Klasa sluzy do obslugi danych zwroconych z Risk Inteligence
 *******************************************************************************
 * <hr>
 * @author mariusz
 *******************************************************************************
 */
class RegistrationServiceController extends Controller
{
    public static $PARAM_USED_CARS_MODEL = "UsedCarsModel"; 
    public static $PARAM_USED_COM_CARS_MODEL = "UsedComCarsModel";
    public static $PARAM_RGE_OR_NULL_EMPTY = "Rge or Mdl is empty.";
            
    /**
     * @return array action filters
     */
    public function filters()
    {
            return array(
                    'accessControl', // perform access control for CRUD operations
            );
    }
        
    public function accessRules()
    {
            return array(
                    array('allow',  // allow all users to perform 'index' and 'view' actions
                            'actions'=>array('apiRegLookup'),
                            'users'=>array('*'),
                    ),
                    array('allow', // allow authenticated user to perform 'create' and 'update' actions
                            'actions'=>array('checkPlateNumber', 'checkPlateNumberByRegLookUpArchive','usedComCarsArchiveRegLookup','usedCarsArchiveRegLookup','historyCheck', 'historyCheckMobile', 'generatePdfHistoryCheck', 'generatePdf','ajaxCalculateByRegLookUpMobile', 'ajaxCalculateByRegLookUpAlt', 'ajaxCalculateByRegLookUp', 'ajaxCalculate','usedCarsLookupIFrame','usedCarsLookupIFrameTest','usedCommercialLookupIFrame','checkPlateNumberByRegLookUp', 'checkPlateNumberByRegLookUpTest','pdfByRegLookUp','calculateByRegLookUp','checkPlateNumberByRegLookUpNoAjax', 'checkPlateNumberByRegLookUpMobileSelectsFilter', 'checkPlateNumberByRegLookUpMobile', 'ajaxCalculateByRegLookUpArch'),
                            'users'=>array('@'),
                    ),
                    array('allow', // allow admin user to perform 'admin' and 'delete' actions
                            'actions'=>array(''),
                            'users'=>array('admin','su'),
                    ),
                    array('deny',  // deny all users
                            'users'=>array('*'),
                    ),
            );
    }

    public function beforeAction($action)
    {
        if (!(Yii::app()->mobileDetect->isMobile() || Yii::app()->mobileDetect->isTablet())) {
            Uzytkownik::updateNetworkUserSession();
        }
        
        return parent::beforeAction($action);
    }

    /**
     * Metoda pobiera dane z RI i renderuje z nimi widok
     * 'checksOnly' 'usedCarComModel' 'importId' to dane potrzebne
     * do obliczenia wartosci samochodu {odometer calculation}
     */
    public function actionCheckPlateNumber()
    {
        $lvVehicleRegNumber = $_REQUEST['VehicleRegNumber'];

        //$lvVehicleRegNumber = "131D1234";//"06D51321"; // TEST ONLY
        $lvRIVehicleData = new RIVehicleData();
        $checksOnly = true;// Yii::app()->user->getIsCheckOnly(); //@todo: płatny/nie płatny zależy od wyboru użytkownika (czy chce pełne dane pojazdu czy podstawowe), nie jest to cecha samego użytkownika.
        $lvSoapResult = ($checksOnly) ? $lvRIVehicleData->vehicleFreeReport($lvVehicleRegNumber) : $lvRIVehicleData->queryVehicle($lvVehicleRegNumber);

        //var_dump($lvSoapResult);die;
        Yii::app()->controller->renderPartial('checkPlateNumberResults',
                array('RIdata'=>$lvSoapResult,
                      'checksOnly'=>$checksOnly,
                      'usedCarComModel'=>$_REQUEST['usedCarComModel'],
                      'importId'=>$_REQUEST['importId'],
                ));
    }
    
    public function actionHistoryCheck()
    {   
        $this->layout = '//layouts/iframe';
        $lvSoapResult=null;
        if(!empty($_POST['VehicleRegNumber'])){
            $lvVehicleRegNumber = $_REQUEST['VehicleRegNumber'];

            //$lvVehicleRegNumber = "131D1234";//"06D51321"; // TEST ONLY
            $lvRIVehicleData = new RIVehicleData();
            $checksOnly = true;// Yii::app()->user->getIsCheckOnly(); //@todo: płatny/nie płatny zależy od wyboru użytkownika (czy chce pełne dane pojazdu czy podstawowe), nie jest to cecha samego użytkownika.
            //$lvSoapResult = ($checksOnly) ? $lvRIVehicleData->vehicleFreeReport($lvVehicleRegNumber) : $lvRIVehicleData->queryVehicle($lvVehicleRegNumber);
            $lvSoapResult = $lvRIVehicleData->vehicleHistoryCheck($lvVehicleRegNumber);
            //CVarDumper::dump($lvSoapResult);
        }
        //
        Yii::app()->controller->render('checkPlateNumberResultsHistory',
            array('RIdata'=>$lvSoapResult,
                  'checksOnly'=>true,
                  //'usedCarComModel'=>$_REQUEST['usedCarComModel'],
                  //'importId'=>$_REQUEST['importId'],
            ));
    }
    
    
    public function actionHistoryCheckMobile()
    {   
        //this->layout = '//layouts/iframe';
        
        $this->layout = '//layouts/mainMobile';
        $lvSoapResult=null;
            if(!empty($_POST['VehicleRegNumber'])){
                $lvVehicleRegNumber = $_REQUEST['VehicleRegNumber'];

                //$lvVehicleRegNumber = "131D1234";//"06D51321"; // TEST ONLY
                $lvRIVehicleData = new RIVehicleData();
                $checksOnly = true;// Yii::app()->user->getIsCheckOnly(); //@todo: płatny/nie płatny zależy od wyboru użytkownika (czy chce pełne dane pojazdu czy podstawowe), nie jest to cecha samego użytkownika.
                //$lvSoapResult = ($checksOnly) ? $lvRIVehicleData->vehicleFreeReport($lvVehicleRegNumber) : $lvRIVehicleData->queryVehicle($lvVehicleRegNumber);
                $lvSoapResult = $lvRIVehicleData->vehicleHistoryCheck($lvVehicleRegNumber);
                //CVarDumper::dump($lvSoapResult);
            }
        //    echo 'ASDA';
        //
        $this->render('checkPlateNumberResultsHistoryMobile',
                array('RIdata'=>$lvSoapResult,
                      'checksOnly'=>true,
                      //'usedCarComModel'=>$_REQUEST['usedCarComModel'],
                      //'importId'=>$_REQUEST['importId'],
                ));
    }

    /**
     * Metoda generuje PDF z otzymanymi danych z RI
     */
    public function actionGeneratePdf()
    {
        Yii::import('application.extensions.*');
        require_once('tcpdf/config/lang/eng.php');
        require_once('tcpdf/tcpdf.php');
        require_once('./protected/views/registrationService/generatePdf.php'); // inaczej nie da sie otworzyc pliku
        $this->render('generatePdf');
    }

    /**
     * Odometer calculation
     */
    public function actionAjaxCalculate()
    {
        echo "Not implemented yet.";
    }

    public function actionUsedCarsLookup()
    {
        $this->layout = Controller::getLayoutDevice();
        $this->render('usedCarsByRegLookup',array('hideHeader' => false));
    }
    
    public function actionUsedCarsLookupIFrameTest()
    {
        //$this->layout = Controller::getLayoutDevice();
        $this->layout = '//layouts/iframe';
        if (Uzytkownik::model()->checkExpirationDate(Uzytkownik::PARAM_USED_CARS)) {
                $this->render('usedCarsByRegLookupTest',array('hideHeader' => false));
            } else {
                echo '<script>window.top.location.href = "http://mtp.ie/license/";</script>';
                exit;
            }
        
    }
    public function actionUsedCarsLookupIFrame()
    {
        //$this->layout = Controller::getLayoutDevice();
        $this->layout = '//layouts/iframe';
        if (Uzytkownik::model()->checkExpirationDate(Uzytkownik::PARAM_USED_CARS)) {
                $this->render('usedCarsByRegLookup',array('hideHeader' => false));
            } else {
                echo '<script>window.top.location.href = "http://mtp.ie/license/";</script>';
                exit;
            }
        
    }
    
    public function actionUsedCarsArchiveRegLookup()
    {
        //$this->layout = Controller::getLayoutDevice();
        $this->layout = '//layouts/iframe';
        $this->render('usedCarsByRegLookup',array('hideHeader' => false));
    }
    
    public function actionUsedCommercialLookup()
    {
        $this->layout = Controller::getLayoutDevice();
        
        $this->render('usedCommercialByRegLookup',array('hideHeader' => false));
    }
    
    public function actionUsedCommercialLookupIFrame()
    {
        //$this->layout = Controller::getLayoutDevice();
        $this->layout = '//layouts/iframe';
        if (Uzytkownik::model()->checkExpirationDate(Uzytkownik::PARAM_USED_CARS)) {
                $this->render('usedCommercialByRegLookup',array('hideHeader' => false));
            } else {
                echo '<script>window.top.location.href = "http://mtp.ie/license/";</script>';
                exit;
            }
        
    }
    
    public function actionUsedComCarsArchiveRegLookup()
    {
        //$this->layout = Controller::getLayoutDevice();
        $this->layout = '//layouts/iframe';
        $this->render('usedCommercialByRegLookup',array('hideHeader' => false));
    }
    
    // TODO: refactor
    public function actionCheckPlateNumberByRegLookUp()
    {
        //$this->layout = Controller::getLayoutDevice();
        $this->layout = 'iframe';
        
        $vehicleData = array();
        $vehicleData = RegistrationService::getRiDataResults($_POST['VehicleRegNumber']);
//       var_dump($vehicleData);die;
        $selectedModel = $_POST['usedCarComModel'];

        if(!empty($vehicleData['errors']))
        {
            Yii::app()->user->setFlash('errorMsg', $vehicleData['errors']);
            if($_POST['useAjax'] == '')
            {
                if($selectedModel == self::$PARAM_USED_CARS_MODEL)
                {
                    CController::forward('member/usedCars');
                }
                else
                {
                    CController::forward('member/usedCommercial');
                }
                exit;
            }
            echo $this->renderPartial('_basicDetailsInformation',
                array('vehicle' => "",
                      'model' => "",
                      'coreWithAssociatedCarsModel' => "",
                      'type'=> $selectedModel
                ));
            exit;
        }
		if(strtolower($_POST['VehicleRegNumber'])=="151d18418"){
			$vehicleData['code'] = "6802400350";
		}
		if(strtolower(trim($_POST['VehicleRegNumber']))=="161wh6"){
			$vehicleData['code'] = "8002400474";
		}
        if(strtolower(trim($_POST['VehicleRegNumber']))=="161wh112"){
			$vehicleData['code'] = "8002400474";
		}
		
        $returnedCodeNumber = $vehicleData['code'];
       // echo 'returned code: >>'.$returnedCodeNumber.'<< ';
        //MW Link files
        // if the codenumber returned by RI is not the core model we will look yp the right code in the links files and use it right code to make teh valueation.
        $indicator24 = substr($returnedCodeNumber, 3,2);
       // echo $indicator24;
        if($indicator24 != '24'){
          //  echo '!';
            if($selectedModel == self::$PARAM_USED_CARS_MODEL)
                {
                //echo 'uc';
                    //used cars
                    $linkedCode = XmlUcarsLinks::getLinkedCarCode($returnedCodeNumber);
                    if(!empty($linkedCode)){
                        $returnedCodeNumber = $linkedCode;
                       
                    }
                }
                else
                {
                //    echo 'ucm';
                    //used comms
                    $linkedCode = XmlUcommsLinks::getLinkedCarCode($returnedCodeNumber);
                    if(!empty($linkedCode)){
                        $returnedCodeNumber = $linkedCode;
                    }
                    
                }          
        }
     //   echo 'changed if found in linked >>'.$returnedCodeNumber.'<<<';
        if(!empty($returnedCodeNumber) && RegistrationService::isValidYear($vehicleData) )
        {
            // najpierw szukamy core jesli core jest pusty to commercial
            $model = RegistrationService::getCarCommModel("UsedCarsModel", $returnedCodeNumber);
        //    echo 'a';
            if(empty($model))
            {
              //  echo 'b';
                // search commercial
                $model = RegistrationService::getCarCommModel("UsedComCarsModel", $returnedCodeNumber);
                if(empty($model)){
                    $model = null;
                    $main_coreWithAssociatedCarsModel = array();
                    $rest_coreWithAssociatedCarsModel = array();
                    $vehicleKms = array("kmsForYear" => "");
                }else {
                    $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);
                    $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);
                }
                
            } else {
            //    echo 'c';
                // search cars
                $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
                $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
            }
            $vehicleKms = array("kmsForYear" => RegistrationService::getFieldValueForYear("kms", $vehicleData['year'], $model));
            // jesli $vehicleKms="" to znaczy ze w bazie nie jest uzupelniony rok dla tego samochodu (rok ktory zwraca RI)
        }
        else 
        {
            $model = null;
            $main_coreWithAssociatedCarsModel = array();
            $rest_coreWithAssociatedCarsModel = array();
            $vehicleKms = array("kmsForYear" => "");
        }
       /* echo '$model:';
        var_dump($model);
        echo "<br>";
        echo "<br>";
        echo '$main_coreWithAssociatedCarsModel:';
        var_dump($main_coreWithAssociatedCarsModel);
        echo "<br>";
        echo "<br>";
        echo '$rest_coreWithAssociatedCarsModel:';
        var_dump($rest_coreWithAssociatedCarsModel);
        echo "<br>";
        echo "<br>";
        /**/
        if(empty($coreWithAssociatedCarsModel))
            Yii::app()->user->setFlash('infoMsg', self::$PARAM_RGE_OR_NULL_EMPTY);

        if($_POST['useAjax']) 
        {
            $this->renderPartial('_basicDetailsInformation',
                    array('vehicle' => array_merge($vehicleData, $vehicleKms),
                          'model' => $model,
                          'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                          'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                          'calculatedMain_coreWithAssociatedCars' => array(),
                          'calculatedRest_coreWithAssociatedCars' => array(),
                          'type'=> $selectedModel
                    ));
        } 
        else 
        {
            // no ajax
            $view = ($selectedModel == self::$PARAM_USED_CARS_MODEL) ? 'usedCarsByRegLookup' : 'usedCommercialByRegLookup';
            $this->render($view,
                array('vehicle' => array_merge($vehicleData, $vehicleKms),
                      'model' => $model,
                      'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                      'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                      'calculatedMain_coreWithAssociatedCars' => array(),
                      'calculatedRest_coreWithAssociatedCars' => array(),
                      'type' => $selectedModel,
                      'hideHeader' => true
                ));   
        }
    }
    public function actionCheckPlateNumberByRegLookUpTest()
    {
        //$this->layout = Controller::getLayoutDevice();
        $this->layout = 'iframe';
        
        $vehicleData = array();
        $vehicleData = RegistrationService::getRiDataResults($_POST['VehicleRegNumber']);
//       var_dump($vehicleData);die;
        $selectedModel = $_POST['usedCarComModel'];

        if(!empty($vehicleData['errors']))
        {
            Yii::app()->user->setFlash('errorMsg', $vehicleData['errors']);
            if($_POST['useAjax'] == '')
            {
                if($selectedModel == self::$PARAM_USED_CARS_MODEL)
                {
                    CController::forward('member/usedCars');
                }
                else
                {
                    CController::forward('member/usedCommercial');
                }
                exit;
            }
            echo $this->renderPartial('_basicDetailsInformation',
                array('vehicle' => "",
                      'model' => "",
                      'coreWithAssociatedCarsModel' => "",
                      'type'=> $selectedModel
                ));
            exit;
        }
		if(strtolower($_POST['VehicleRegNumber'])=="151d18418"){
			$vehicleData['code'] = "6802400350";
		}
        
        $returnedCodeNumber = $vehicleData['code'];
       // echo 'returned code: >>'.$returnedCodeNumber.'<< ';
        //MW Link files
        // if the codenumber returned by RI is not the core model we will look yp the right code in the links files and use it right code to make teh valueation.
        $indicator24 = substr($returnedCodeNumber, 3,2);
       // echo $indicator24;
        if($indicator24 != '24'){
          //  echo '!';
            if($selectedModel == self::$PARAM_USED_CARS_MODEL)
                {
                //echo 'uc';
                    //used cars
                    $linkedCode = XmlUcarsLinks::getLinkedCarCode($returnedCodeNumber);
                    if(!empty($linkedCode)){
                        $returnedCodeNumber = $linkedCode;
                       
                    }
                }
                else
                {
                //    echo 'ucm';
                    //used comms
                    $linkedCode = XmlUcommsLinks::getLinkedCarCode($returnedCodeNumber);
                    if(!empty($linkedCode)){
                        $returnedCodeNumber = $linkedCode;
                    }
                    
                }          
        }
     //   echo 'changed if found in linked >>'.$returnedCodeNumber.'<<<';
        if(!empty($returnedCodeNumber) && RegistrationService::isValidYear($vehicleData) )
        {
            // najpierw szukamy core jesli core jest pusty to commercial
            $model = RegistrationService::getCarCommModel("UsedCarsModel", $returnedCodeNumber);
        //    echo 'a';
            if(empty($model))
            {
              //  echo 'b';
                // search commercial
                $model = RegistrationService::getCarCommModel("UsedComCarsModel", $returnedCodeNumber);
                if(empty($model)){
                    $model = null;
                    $main_coreWithAssociatedCarsModel = array();
                    $rest_coreWithAssociatedCarsModel = array();
                    $vehicleKms = array("kmsForYear" => "");
                }else {
                    $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);
                    $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);
                }
                
            } else {
            //    echo 'c';
                // search cars
                $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
                $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
            }
            $vehicleKms = array("kmsForYear" => RegistrationService::getFieldValueForYear("kms", $vehicleData['year'], $model));
            // jesli $vehicleKms="" to znaczy ze w bazie nie jest uzupelniony rok dla tego samochodu (rok ktory zwraca RI)
        }
        else 
        {
            $model = null;
            $main_coreWithAssociatedCarsModel = array();
            $rest_coreWithAssociatedCarsModel = array();
            $vehicleKms = array("kmsForYear" => "");
        }
       /* echo '$model:';
        var_dump($model);
        echo "<br>";
        echo "<br>";
        echo '$main_coreWithAssociatedCarsModel:';
        var_dump($main_coreWithAssociatedCarsModel);
        echo "<br>";
        echo "<br>";
        echo '$rest_coreWithAssociatedCarsModel:';
        var_dump($rest_coreWithAssociatedCarsModel);
        echo "<br>";
        echo "<br>";
        /**/
        if(empty($coreWithAssociatedCarsModel))
            Yii::app()->user->setFlash('infoMsg', self::$PARAM_RGE_OR_NULL_EMPTY);

        if($_POST['useAjax']) 
        {
            $this->renderPartial('_basicDetailsInformationTest',
                    array('vehicle' => array_merge($vehicleData, $vehicleKms),
                          'model' => $model,
                          'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                          'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                          'calculatedMain_coreWithAssociatedCars' => array(),
                          'calculatedRest_coreWithAssociatedCars' => array(),
                          'type'=> $selectedModel
                    ));
        } 
        else 
        {
            // no ajax
            $view = ($selectedModel == self::$PARAM_USED_CARS_MODEL) ? 'usedCarsByRegLookup' : 'usedCommercialByRegLookup';
            $this->render($view,
                array('vehicle' => array_merge($vehicleData, $vehicleKms),
                      'model' => $model,
                      'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                      'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                      'calculatedMain_coreWithAssociatedCars' => array(),
                      'calculatedRest_coreWithAssociatedCars' => array(),
                      'type' => $selectedModel,
                      'hideHeader' => true
                ));   
        }
    }
    
    public function actionCheckPlateNumberByRegLookUpArchive()
    {
        $arch = null;
        if(!empty($_POST['arch'])){
            $arch = $_POST['arch'];
        }
        //$this->layout = Controller::getLayoutDevice();
        $this->layout = 'iframe';
        
        $vehicleData = array();
        $vehicleData = RegistrationService::getRiDataResults($_POST['VehicleRegNumber']);
//       var_dump($vehicleData);die;
        $selectedModel = $_POST['usedCarComModel'];

        if(!empty($vehicleData['errors']))
        {
            Yii::app()->user->setFlash('errorMsg', $vehicleData['errors']);
            if($_POST['useAjax'] == '')
            {
                if($selectedModel == self::$PARAM_USED_CARS_MODEL)
                {
                    CController::forward('member/usedCars');
                }
                else
                {
                    CController::forward('member/usedCommercial');
                }
                exit;
            }
            echo $this->renderPartial('_basicDetailsInformation',
                array('vehicle' => "",
                      'model' => "",
                      'coreWithAssociatedCarsModel' => "",
                      'type'=> $selectedModel
                ));
            exit;
        }
        
        $returnedCodeNumber = $vehicleData['code'];
//echo ' returned code: -->'.$returnedCodeNumber.'<--';
        //MW Link files
        // if the codenumber returned by RI is not the core model we will look yp the right code in the links files and use it right code to make teh valueation.
        $indicator24 = substr($returnedCodeNumber, 3,2);
//        echo $indicator24;
        if($indicator24 != '24'){
          //  echo '!';
            if($selectedModel == self::$PARAM_USED_CARS_MODEL)
                {
                //echo 'uc';
                    //used cars
                    $linkedCode = XmlUcarsLinks::getLinkedCarCode($returnedCodeNumber, $arch);
                    if(!empty($linkedCode)){
                        $returnedCodeNumber = $linkedCode;
                       
                    }
                }
                else
                {
                //    echo 'ucm';
                    //used comms
                    $linkedCode = XmlUcommsLinks::getLinkedCarCode($returnedCodeNumber, $arch);
                    if(!empty($linkedCode)){
                        $returnedCodeNumber = $linkedCode;
                    }
                    
                }          
        }
//        echo ' changed if found in linked -->'.$returnedCodeNumber.'<--';
        //TODO - allow for years from arch to have lower limits
        if(!empty($returnedCodeNumber) && RegistrationService::isValidYear($vehicleData) )
        {
            // najpierw szukamy core jesli core jest pusty to commercial
            $model = RegistrationService::getCarCommModel("UsedCarsModel", $returnedCodeNumber, $arch);
            if(empty($model))
            {
                // search commercial
                $model = RegistrationService::getCarCommModel("UsedComCarsModel", $returnedCodeNumber, $arch);
                //TODO - follow up here:
                $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model, $arch);
                $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model, $arch);
            } else {
                // search cars
                $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model, $arch);
                $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model, $arch);
            }
            if(!empty($model)){
                $vehicleKms = array("kmsForYear" => RegistrationService::getFieldValueForYear("kms", $vehicleData['year'], $model));
            }else {
                $vehicleKms = array("kmsForYear" => "");
                Yii::app()->user->setFlash('infoMsg', 'We have problem finding the car of that registration');
            }
            
            // jesli $vehicleKms="" to znaczy ze w bazie nie jest uzupelniony rok dla tego samochodu (rok ktory zwraca RI)
        }
        else 
        {
            //TODO MW CLEAN ECHO
            echo 'year not empty';
            $model = null;
            $main_coreWithAssociatedCarsModel = array();
            $rest_coreWithAssociatedCarsModel = array();
            $vehicleKms = array("kmsForYear" => "");
        }
//        echo '$model:';
//        var_dump($model);
//        echo "<br>";
//        echo "<br>";
//        echo '$main_coreWithAssociatedCarsModel:';
//        var_dump($main_coreWithAssociatedCarsModel);
//        echo "<br>";
//        echo "<br>";
//        echo '$rest_coreWithAssociatedCarsModel:';
//        var_dump($rest_coreWithAssociatedCarsModel);
//        echo "<br>";
//        echo "<br>";
 
        

        if(empty($coreWithAssociatedCarsModel))
            Yii::app()->user->setFlash('infoMsg', self::$PARAM_RGE_OR_NULL_EMPTY);

        if($_POST['useAjax'])
        {
            $this->renderPartial('_basicDetailsInformation',
                    array('vehicle' => array_merge($vehicleData, $vehicleKms),
                          'model' => $model,
                          'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                          'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                          'calculatedMain_coreWithAssociatedCars' => array(),
                          'calculatedRest_coreWithAssociatedCars' => array(),
                          'type'=> $selectedModel
                    ));
        } 
        else 
        {
            // no ajax
            $view = ($selectedModel == self::$PARAM_USED_CARS_MODEL) ? 'usedCarsByRegLookup' : 'usedCommercialByRegLookup';
            $this->render($view,
                array('vehicle' => array_merge($vehicleData, $vehicleKms),
                      'model' => $model,
                      'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                      'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                      'calculatedMain_coreWithAssociatedCars' => array(),
                      'calculatedRest_coreWithAssociatedCars' => array(),
                      'type' => $selectedModel,
                      'hideHeader' => true
                ));   
        }
    }
    
    public function actionCheckPlateNumberByRegLookUpMobile()
    {
        //MW HERE 2016-11
        
       // Yii::app()->user->setFlash('showBackButton',"SHOW");
        //$this->layout="//layouts/iframeMobile.php";
        //$this->layout = Controller::getLayoutDevice();
         if(empty($_POST['useAjax']))
            {
                $this->redirect(array('mobile/selectCars'));
                exit;
            }
        $this->layout = 'iframe';
        
        $vehicleData = array();
        $vehicleData = RegistrationService::getRiDataResults($_POST['VehicleRegNumber']);
//       var_dump($vehicleData);die;
        $selectedModel = $_POST['usedCarComModel'];

        if(!empty($vehicleData['errors']))
        {
            Yii::app()->user->setFlash('errorMsg', $vehicleData['errors']);
            if($_POST['useAjax'] == '')
            {
                $this->redirect('mobile/selectCars');
                if($selectedModel == self::$PARAM_USED_CARS_MODEL)
                {
                    CController::forward('member/usedCars');
                }
                else
                {
                    CController::forward('member/usedCommercial');
                }
                exit;
            }
            echo $this->renderPartial('_basicDetailsInformation',
                array('vehicle' => "",
                      'model' => "",
                      'coreWithAssociatedCarsModel' => "",
                      'type'=> $selectedModel
                ));
            exit;
        }
        
        $returnedCodeNumber = $vehicleData['code'];

        //MW Link files
        // if the codenumber returned by RI is not the core model we will look yp the right code in the links files and use it right code to make teh valueation.
        $indicator24 = substr($returnedCodeNumber, 3,2);
       // echo $indicator24;
        if($indicator24 != '24'){
          //  echo '!';
            if($selectedModel == self::$PARAM_USED_CARS_MODEL)
                {
                //echo 'uc';
                    //used cars
                    $linkedCode = XmlUcarsLinks::getLinkedCarCode($returnedCodeNumber);
                    if(!empty($linkedCode)){
                        $returnedCodeNumber = $linkedCode;
                       
                    }
                }
                else
                {
                //    echo 'ucm';
                    //used comms
                    $linkedCode = XmlUcommsLinks::getLinkedCarCode($returnedCodeNumber);
                    if(!empty($linkedCode)){
                        $returnedCodeNumber = $linkedCode;
                    }
                    
                }          
        }
       // echo $returnedCodeNumber;
        if(!empty($returnedCodeNumber) && RegistrationService::isValidYear($vehicleData) )
        {
            // najpierw szukamy core jesli core jest pusty to commercial
            $model = RegistrationService::getCarCommModel("UsedCarsModel", $returnedCodeNumber);
            if(empty($model))
            {
                // search commercial
                $model = RegistrationService::getCarCommModel("UsedComCarsModel", $returnedCodeNumber);
                $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);
                $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);
            } else {
                // search cars
                $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
                $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
            }
            $vehicleKms = array("kmsForYear" => RegistrationService::getFieldValueForYear("kms", $vehicleData['year'], $model));
            // jesli $vehicleKms="" to znaczy ze w bazie nie jest uzupelniony rok dla tego samochodu (rok ktory zwraca RI)
        }
        else 
        {
            $model = null;
            $main_coreWithAssociatedCarsModel = array();
            $rest_coreWithAssociatedCarsModel = array();
            $vehicleKms = array("kmsForYear" => "");
        }
        //adjusted values 
        $vehicleYear = (!empty($_POST['userGuideKm'])) ? $_POST['vehicleYear'] : $vehicleKms;
        if(!empty($_POST['userGuideKm'])){
            $userKm = (empty($_POST['userGuideKm'])) ? $vehicleKms : $_POST['userGuideKm'];
            $userGuideKm = RegistrationService::multiplyUserKm($userKm);
            

            $skipCalc = ($_POST['defaultKmsForYear'] == $_POST['userGuideKm'] || empty($_POST['userGuideKm'])) ? true : false;

            $calculatedMain_coreWithAssociatedCars = RegistrationService::odometerCalculationByRegLookUp($main_coreWithAssociatedCarsModel, $userGuideKm, $vehicleYear, $skipCalc);
            $calculatedRest_coreWithAssociatedCars = RegistrationService::odometerCalculationByRegLookUp($rest_coreWithAssociatedCarsModel, $userGuideKm, $vehicleYear, $skipCalc);

            $selectedModel = (empty($model)) ? "UsedComCarsModel" : "UsedCarsModel";
            $calculatedCustomValue = RegistrationService::odometerCalculationByRegLookUpCustomValue($_POST['customValueGrp'], $userGuideKm, $vehicleYear, $selectedModel, $returnedCodeNumber);//$_POST['coreCodenumberForCustomValue']);
            $checkedAllCheckboxes = (!empty($_POST['checkedAllCheckboxes'])) ? $_POST['checkedAllCheckboxes'] : null;
            $grpCustomValeResult = (!empty($_POST['customValueGrp'])) ? $_POST['customValueGrp'] : null;
            //andjusted values end
        }else {
            $calculatedMain_coreWithAssociatedCars = array();
            $calculatedRest_coreWithAssociatedCars = array();
            $checkedAllCheckboxes = (!empty($_POST['checkedAllCheckboxes'])) ? $_POST['checkedAllCheckboxes'] : null;
            $grpCustomValeResult = (!empty($_POST['customValueGrp'])) ? $_POST['customValueGrp'] : null;
            $calculatedCustomValue  = null;
        }
        if(empty($coreWithAssociatedCarsModel))
            Yii::app()->user->setFlash('infoMsg', self::$PARAM_RGE_OR_NULL_EMPTY);

        if($_POST['useAjax']) {
        
            if(isset($_GET['last_select'])){
                $this->renderPartial('_basicDetailsInformationMobile',
                    array('vehicle' => array_merge($vehicleData, $vehicleKms),
                          'model' => $model,
                          'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                          'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                        //adjust
                        'calculatedMain_coreWithAssociatedCars' => $calculatedMain_coreWithAssociatedCars,
                        'calculatedRest_coreWithAssociatedCars' => $calculatedRest_coreWithAssociatedCars,
                        'vehicleYear' => $vehicleYear,
                        'checkedAllCheckboxes'=>$checkedAllCheckboxes,
                        'grpCustomValeResult'=>$grpCustomValeResult,
                        'calculatedCustomValue'=>$calculatedCustomValue,
                        //adjust end
                          'type'=> $selectedModel
                    ));
            }else {
                    $this->render('_basicDetailsInformationMobile',
                    array('vehicle' => array_merge($vehicleData, $vehicleKms),
                          'model' => $model,
                          'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                          'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                          'calculatedMain_coreWithAssociatedCars' => $calculatedMain_coreWithAssociatedCars,
                            'calculatedRest_coreWithAssociatedCars' => $calculatedRest_coreWithAssociatedCars,
                        'vehicleYear' => $vehicleYear,
                        'checkedAllCheckboxes'=>$checkedAllCheckboxes,
                        'grpCustomValeResult'=>$grpCustomValeResult,
                        'calculatedCustomValue'=>$calculatedCustomValue,

                          'type'=> $selectedModel
                    ));
            }
            
        } 
        else 
        {
            // no ajax
            $view = ($selectedModel == self::$PARAM_USED_CARS_MODEL) ? 'usedCarsByRegLookup' : 'usedCommercialByRegLookup';
            $this->render($view,
                array('vehicle' => array_merge($vehicleData, $vehicleKms),
                      'model' => $model,
                      'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                      'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                      'calculatedMain_coreWithAssociatedCars' => array(),
                      'calculatedRest_coreWithAssociatedCars' => array(),
                      'type' => $selectedModel,
                      'hideHeader' => true
                ));   
        }
    }
    public function actionCheckPlateNumberByRegLookUpMobileSelectsFilter()
    {
        //echo 'bbbbbbbbbbbbbbb';
       // exit;
        //$this->layout="//layouts/iframeMobile.php";
        //$this->layout = Controller::getLayoutDevice();
         if(empty($_POST['useAjax']))
            {
                $this->redirect(array('mobile/selectCars'));
                exit;
            }
        $this->layout = 'iframe';
        
        $vehicleData = array();
        $vehicleData = RegistrationService::getRiDataResults($_POST['VehicleRegNumber']);
//       var_dump($vehicleData);die;
        $selectedModel = $_POST['usedCarComModel'];

//        if(!empty($vehicleData['errors']))
//        {
//            Yii::app()->user->setFlash('errorMsg', $vehicleData['errors']);
//            if($_POST['useAjax'] == '')
//            {
//                $this->redirect('mobile/selectCars');
//                if($selectedModel == self::$PARAM_USED_CARS_MODEL)
//                {
//                    CController::forward('member/usedCars');
//                }
//                else
//                {
//                    CController::forward('member/usedCommercial');
//                }
//                exit;
//            }
//            echo $this->renderPartial('_basicDetailsInformation',
//                array('vehicle' => "",
//                      'model' => "",
//                      'coreWithAssociatedCarsModel' => "",
//                      'type'=> $selectedModel
//                ));
//            exit;
//        }
        
        $returnedCodeNumber = $vehicleData['code'];

        //MW Link files
        // if the codenumber returned by RI is not the core model we will look yp the right code in the links files and use it right code to make teh valueation.
        $indicator24 = substr($returnedCodeNumber, 3,2);
       // echo $indicator24;
        if($indicator24 != '24'){
          //  echo '!';
            if($selectedModel == self::$PARAM_USED_CARS_MODEL)
                {
                //echo 'uc';
                    //used cars
                    $linkedCode = XmlUcarsLinks::getLinkedCarCode($returnedCodeNumber);
                    if(!empty($linkedCode)){
                        $returnedCodeNumber = $linkedCode;
                       
                    }
                }
                else
                {
                //    echo 'ucm';
                    //used comms
                    $linkedCode = XmlUcommsLinks::getLinkedCarCode($returnedCodeNumber);
                    if(!empty($linkedCode)){
                        $returnedCodeNumber = $linkedCode;
                    }
                    
                }          
        }
       // echo $returnedCodeNumber;
        if(!empty($returnedCodeNumber) && RegistrationService::isValidYear($vehicleData) )
        {
            // najpierw szukamy core jesli core jest pusty to commercial
            $model = RegistrationService::getCarCommModel("UsedCarsModel", $returnedCodeNumber);
            if(empty($model))
            {
                // search commercial
                $model = RegistrationService::getCarCommModel("UsedComCarsModel", $returnedCodeNumber);
                $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);
                $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);
            } else {
                // search cars
                $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
                $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
            }
            $vehicleKms = array("kmsForYear" => RegistrationService::getFieldValueForYear("kms", $vehicleData['year'], $model));
            // jesli $vehicleKms="" to znaczy ze w bazie nie jest uzupelniony rok dla tego samochodu (rok ktory zwraca RI)
        }
        else 
        {
            $model = null;
            $main_coreWithAssociatedCarsModel = array();
            $rest_coreWithAssociatedCarsModel = array();
            $vehicleKms = array("kmsForYear" => "");
        }

        if(empty($coreWithAssociatedCarsModel))
            Yii::app()->user->setFlash('infoMsg', self::$PARAM_RGE_OR_NULL_EMPTY);

        if($_POST['useAjax']) {
        
            if(isset($_GET['last_select'])){
                $this->render('_basicDetailsInformationMobile',
                    array('vehicle' => array_merge($vehicleData, $vehicleKms),
                          'model' => $model,
                          'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                          'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                          'calculatedMain_coreWithAssociatedCars' => array(),
                          'calculatedRest_coreWithAssociatedCars' => array(),
                          'type'=> $selectedModel
                    ));
            }else {
                    $this->render('_basicDetailsInformationMobile',
                    array('vehicle' => array_merge($vehicleData, $vehicleKms),
                          'model' => $model,
                          'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                          'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                          'calculatedMain_coreWithAssociatedCars' => array(),
                          'calculatedRest_coreWithAssociatedCars' => array(),
                          'type'=> $selectedModel
                    ));
            }
            
        } 
        else 
        {
            // no ajax
            $view = ($selectedModel == self::$PARAM_USED_CARS_MODEL) ? 'usedCarsByRegLookup' : 'usedCommercialByRegLookup';
            $this->render($view,
                array('vehicle' => array_merge($vehicleData, $vehicleKms),
                      'model' => $model,
                      'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                      'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                      'calculatedMain_coreWithAssociatedCars' => array(),
                      'calculatedRest_coreWithAssociatedCars' => array(),
                      'type' => $selectedModel,
                      'hideHeader' => true
                ));   
        }
    }
    
    public function actionAjaxCalculateByRegLookUpAlt()
    {
      //  echo 'AAAA';
        $userKm = (empty($_POST['userGuideKm'])) ? $_POST['defaultKmsForYear'] : $_POST['userGuideKm'];
        $userGuideKm = RegistrationService::multiplyUserKm($userKm);
        $vehicleYear = $_POST['vehicleYear'];
        $selectedModel = $_POST['usedCarComModel'];        
        $mainVehicleMtpCode = $_POST['mainVehicleMtpCode'];        
        
        $vehicleData = RegistrationService::getRiDataResults($_POST['VehicleRegNumber']);
        $returnedCodeNumber = $vehicleData['code'];
		$indicator24 = substr($returnedCodeNumber, 3,2);
       // echo $indicator24;
        if($indicator24 != '24'){
          //  echo '!';
            if($selectedModel == self::$PARAM_USED_CARS_MODEL)
                {
                //echo 'uc';
                    //used cars
                    $linkedCode = XmlUcarsLinks::getLinkedCarCode($returnedCodeNumber);
                    if(!empty($linkedCode)){
                        $returnedCodeNumber = $linkedCode;                       
                    }
                }
                else
                {
                    //echo 'ucm';
                    //used comms
                    $linkedCode = XmlUcommsLinks::getLinkedCarCode($returnedCodeNumber);
                    if(!empty($linkedCode)){
                        $returnedCodeNumber = $linkedCode;
                    }
                    
                }          
        }
        $returnedCodeNumber = $mainVehicleMtpCode;
      //  echo 'changed if found in linked >>'.$returnedCodeNumber.'<<<';
         //   var_dump($vehicleData);
        // najpierw szukamy core jesli core jest pusty to commercial
        $model = RegistrationService::getCarCommModel("UsedCarsModel", $returnedCodeNumber);
        if(empty($model))
        {
            // search commercial
         //   echo "empty UsedCARSModel";
            $model = RegistrationService::getCarCommModel("UsedComCarsModel", $returnedCodeNumber);
         //   echo '<br>MODEl:<br>';
          //  var_dump($model);
         //   echo '<br>END MODEl:<br>';
            $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);
            $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);            
        } else {
          //  echo "NOT empty UsedCARSModel";
            // search cars
            $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
            $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
        }
       // echo '--------<br>';
       // var_dump($main_coreWithAssociatedCarsModel);
       // echo '--------<br>';
       // var_dump($rest_coreWithAssociatedCarsModel);
        $skipCalc = ($_POST['defaultKmsForYear'] == $_POST['userGuideKm'] || empty($_POST['userGuideKm'])) ? true : false;
        
        $calculatedMain_coreWithAssociatedCars = RegistrationService::odometerCalculationByRegLookUp($main_coreWithAssociatedCarsModel, $userGuideKm, $vehicleYear, $skipCalc);
        $calculatedRest_coreWithAssociatedCars = RegistrationService::odometerCalculationByRegLookUp($rest_coreWithAssociatedCarsModel, $userGuideKm, $vehicleYear, $skipCalc);
        
        $selectedModel = (empty($model)) ? "UsedComCarsModel" : "UsedCarsModel";
        $calculatedCustomValue = RegistrationService::odometerCalculationByRegLookUpCustomValue($_POST['customValueGrp'], $userGuideKm, $vehicleYear, $selectedModel,$returnedCodeNumber);// $_POST['coreCodenumberForCustomValue']);
        
        // clear when $calculatedCustomValue = Valuation cannot be made. Mileage is outside calculation allowance of 
        // on the default clear GRP km Adjustment column
       // echo '-->'.$calculatedCustomValue;
        
        if(strlen($calculatedCustomValue) > 9 || $skipCalc==true){
       //     echo '>9'.$calculatedCustomValue;
            $calculatedCustomValue = "";
        }
        $this->renderPartial('_associatedValuesByRegLookUp',
            array(
                'model' => $model,
                'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                'calculatedMain_coreWithAssociatedCars' => $calculatedMain_coreWithAssociatedCars,
                'calculatedRest_coreWithAssociatedCars' => $calculatedRest_coreWithAssociatedCars,
                'vehicleYear' => $vehicleYear,
                'checkedAllCheckboxes'=>$_POST['checkedAllCheckboxes'],
                'grpCustomValeResult'=>$_POST['customValueGrp'],
                'calculatedCustomValue'=>$calculatedCustomValue,
            ));        
    }    
    
    /**
     * Odometer calculation byRegLookUp
     */
    // TODO: refactor
    public function actionAjaxCalculateByRegLookUp()
    {
      //  echo 'AAAA';
        $userKm = (empty($_POST['userGuideKm'])) ? $_POST['defaultKmsForYear'] : $_POST['userGuideKm'];
        $userGuideKm = RegistrationService::multiplyUserKm($userKm);
        $vehicleYear = $_POST['vehicleYear'];
        $selectedModel = $_POST['usedCarComModel'];        
        
        $vehicleData = RegistrationService::getRiDataResults($_POST['VehicleRegNumber']);
        $returnedCodeNumber = $vehicleData['code'];
		$indicator24 = substr($returnedCodeNumber, 3,2);
       // echo $indicator24;
        if($indicator24 != '24'){
          //  echo '!';
            if($selectedModel == self::$PARAM_USED_CARS_MODEL)
                {
                //echo 'uc';
                    //used cars
                    $linkedCode = XmlUcarsLinks::getLinkedCarCode($returnedCodeNumber);
                    if(!empty($linkedCode)){
                        $returnedCodeNumber = $linkedCode;
                       
                    }
                }
                else
                {
                //    echo 'ucm';
                    //used comms
                    $linkedCode = XmlUcommsLinks::getLinkedCarCode($returnedCodeNumber);
                    if(!empty($linkedCode)){
                        $returnedCodeNumber = $linkedCode;
                    }
                    
                }          
        }
      //  echo 'changed if found in linked >>'.$returnedCodeNumber.'<<<';
         //   var_dump($vehicleData);
        // najpierw szukamy core jesli core jest pusty to commercial
        $model = RegistrationService::getCarCommModel("UsedCarsModel", $returnedCodeNumber);
        if(empty($model))
        {
            // search commercial
         //   echo "empty UsedCARSModel";
            $model = RegistrationService::getCarCommModel("UsedComCarsModel", $returnedCodeNumber);
         //   echo '<br>MODEl:<br>';
          //  var_dump($model);
         //   echo '<br>END MODEl:<br>';
            $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);
            $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);            
        } else {
          //  echo "NOT empty UsedCARSModel";
            // search cars
            $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
            $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
        }
       // echo '--------<br>';
       // var_dump($main_coreWithAssociatedCarsModel);
       // echo '--------<br>';
       // var_dump($rest_coreWithAssociatedCarsModel);
        $skipCalc = ($_POST['defaultKmsForYear'] == $_POST['userGuideKm'] || empty($_POST['userGuideKm'])) ? true : false;
        
        $calculatedMain_coreWithAssociatedCars = RegistrationService::odometerCalculationByRegLookUp($main_coreWithAssociatedCarsModel, $userGuideKm, $vehicleYear, $skipCalc);
        $calculatedRest_coreWithAssociatedCars = RegistrationService::odometerCalculationByRegLookUp($rest_coreWithAssociatedCarsModel, $userGuideKm, $vehicleYear, $skipCalc);
        
        $selectedModel = (empty($model)) ? "UsedComCarsModel" : "UsedCarsModel";
        $calculatedCustomValue = RegistrationService::odometerCalculationByRegLookUpCustomValue($_POST['customValueGrp'], $userGuideKm, $vehicleYear, $selectedModel,$returnedCodeNumber);// $_POST['coreCodenumberForCustomValue']);
        
        // clear when $calculatedCustomValue = Valuation cannot be made. Mileage is outside calculation allowance of 
        // on the default clear GRP km Adjustment column
       // echo '-->'.$calculatedCustomValue;
        
        if(strlen($calculatedCustomValue) > 9 || $skipCalc==true){
       //     echo '>9'.$calculatedCustomValue;
            $calculatedCustomValue = "";
        }
        $this->renderPartial('_associatedValuesByRegLookUp',
            array(
                'model' => $model,
                'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                'calculatedMain_coreWithAssociatedCars' => $calculatedMain_coreWithAssociatedCars,
                'calculatedRest_coreWithAssociatedCars' => $calculatedRest_coreWithAssociatedCars,
                'vehicleYear' => $vehicleYear,
                'checkedAllCheckboxes'=>$_POST['checkedAllCheckboxes'],
                'grpCustomValeResult'=>$_POST['customValueGrp'],
                'calculatedCustomValue'=>$calculatedCustomValue,
            ));        
    }    
    
    public function actionAjaxCalculateByRegLookUpArch()
    {
        $arch = null;
        if(!empty($_POST['arch'])){
            $arch = $_POST['arch'];
        }
        
        $userKm = (empty($_POST['userGuideKm'])) ? $_POST['defaultKmsForYear'] : $_POST['userGuideKm'];
        $userGuideKm = RegistrationService::multiplyUserKm($userKm);
        $vehicleYear = $_POST['vehicleYear'];
        $selectedModel = $_POST['usedCarComModel'];        
        
        $vehicleData = RegistrationService::getRiDataResults($_POST['VehicleRegNumber']);
        $returnedCodeNumber = $vehicleData['code'];

        // najpierw szukamy core jesli core jest pusty to commercial
        $model = RegistrationService::getCarCommModel("UsedCarsModel", $returnedCodeNumber, $arch);
        if(empty($model))
        {
            // search commercial
            $model = RegistrationService::getCarCommModel("UsedComCarsModel", $returnedCodeNumber, $arch);
            $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model, $arch);
            $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model, $arch);
        } else {
            // search cars
            $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model, $arch);
            $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model, $arch);
        }
        
        $skipCalc = ($_POST['defaultKmsForYear'] == $_POST['userGuideKm'] || empty($_POST['userGuideKm'])) ? true : false;
        
        $calculatedMain_coreWithAssociatedCars = RegistrationService::odometerCalculationByRegLookUp($main_coreWithAssociatedCarsModel, $userGuideKm, $vehicleYear, $skipCalc, $arch);
        $calculatedRest_coreWithAssociatedCars = RegistrationService::odometerCalculationByRegLookUp($rest_coreWithAssociatedCarsModel, $userGuideKm, $vehicleYear, $skipCalc, $arch);
        
        $selectedModel = (empty($model)) ? "UsedComCarsModel" : "UsedCarsModel";
        $calculatedCustomValue = RegistrationService::odometerCalculationByRegLookUpCustomValue($_POST['customValueGrp'], $userGuideKm, $vehicleYear, $selectedModel,$returnedCodeNumber, $arch);// $_POST['coreCodenumberForCustomValue']);
        
        // clear when $calculatedCustomValue = Valuation cannot be made. Mileage is outside calculation allowance of 
        // on the default clear GRP km Adjustment column
       // echo '-->'.$calculatedCustomValue;
        
        if(strlen($calculatedCustomValue) > 9 || $skipCalc==true){
           // echo '>9'.$calculatedCustomValue;
            $calculatedCustomValue = "";
        }
        $this->renderPartial('_associatedValuesByRegLookUp',
            array(
                'model' => $model,
                'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                'calculatedMain_coreWithAssociatedCars' => $calculatedMain_coreWithAssociatedCars,
                'calculatedRest_coreWithAssociatedCars' => $calculatedRest_coreWithAssociatedCars,
                'vehicleYear' => $vehicleYear,
                'checkedAllCheckboxes'=>$_POST['checkedAllCheckboxes'],
                'grpCustomValeResult'=>$_POST['customValueGrp'],
                'calculatedCustomValue'=>$calculatedCustomValue,
            ));        
    }  
    
    public function actionAjaxCalculateByRegLookUpMobile()
    {
        $userKm = (empty($_POST['userGuideKm'])) ? $_POST['defaultKmsForYear'] : $_POST['userGuideKm'];
        $userGuideKm = RegistrationService::multiplyUserKm($userKm);
        $vehicleYear = $_POST['vehicleYear'];
        $selectedModel = $_POST['usedCarComModel'];        
        
        $vehicleData = RegistrationService::getRiDataResults($_POST['VehicleRegNumber']);
        $returnedCodeNumber = $vehicleData['code'];

        // najpierw szukamy core jesli core jest pusty to commercial
        $model = RegistrationService::getCarCommModel("UsedCarsModel", $returnedCodeNumber);
        if(empty($model))
        {
            // search commercial
            $model = RegistrationService::getCarCommModel("UsedComCarsModel", $returnedCodeNumber);
            $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);
            $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);
        } else {
            // search cars
            $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
            $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
        }

        $skipCalc = ($_POST['defaultKmsForYear'] == $_POST['userGuideKm'] || empty($_POST['userGuideKm'])) ? true : false;
        
        $calculatedMain_coreWithAssociatedCars = RegistrationService::odometerCalculationByRegLookUp($main_coreWithAssociatedCarsModel, $userGuideKm, $vehicleYear, $skipCalc);
        $calculatedRest_coreWithAssociatedCars = RegistrationService::odometerCalculationByRegLookUp($rest_coreWithAssociatedCarsModel, $userGuideKm, $vehicleYear, $skipCalc);

        $selectedModel = (empty($model)) ? "UsedComCarsModel" : "UsedCarsModel";
        $calculatedCustomValue = RegistrationService::odometerCalculationByRegLookUpCustomValue($_POST['customValueGrp'], $userGuideKm, $vehicleYear, $selectedModel, $returnedCodeNumber);//$_POST['coreCodenumberForCustomValue']);
        
        // clear when $calculatedCustomValue = Valuation cannot be made. Mileage is outside calculation allowance of 
        // on the default clear GRP km Adjustment column
        if(strlen($calculatedCustomValue) > 9 || $skipCalc==true)
            $calculatedCustomValue = "";
        
        $this->renderPartial('_associatedValuesByRegLookUpMobile',
            array(
                'model' => $model,
                'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
                'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
                'calculatedMain_coreWithAssociatedCars' => $calculatedMain_coreWithAssociatedCars,
                'calculatedRest_coreWithAssociatedCars' => $calculatedRest_coreWithAssociatedCars,
                'vehicleYear' => $vehicleYear,
                'checkedAllCheckboxes'=>$_POST['checkedAllCheckboxes'],
                'grpCustomValeResult'=>$_POST['customValueGrp'],
                'calculatedCustomValue'=>$calculatedCustomValue,
            ));        
    }   
    
    public function actionPdfByRegLookUp()
    {
        Yii::import('application.extensions.*');
        require_once('tcpdf/config/lang/eng.php');
        require_once('tcpdf/tcpdf.php');
        require_once('./protected/views/registrationService/pdfByRegLookUp.php'); // inaczej nie da sie otworzyc pliku
        //$this->render('generatePdf');
        //$this->render('pdfByRegLookUp');
    }
    
    
    
    public function actionGeneratePdfHistoryCheck() {
        $RIdata = null;
        
        if (isset($_GET['reg']) && $_GET['reg'] != '') {
            $lvVehicleRegNumber = $_REQUEST['reg'];
            $lvRIVehicleData = new RIVehicleData();
            $RIdata = $lvRIVehicleData->vehicleHistoryCheck($lvVehicleRegNumber);
        }

        Yii::import('application.extensions.*');
        require_once('tcpdf/config/lang/eng.php');
        require_once('tcpdf/tcpdf.php');
        require_once('./protected/views/registrationService/generatePdfHistoryCheck.php'); // inaczej nie da sie otworzyc pliku

        $this->render('generatePdfHistoryCheck', array(
            'RIdata' => $RIdata,
        ));
    }
    
    //API call
    public function actionApiRegLookup($reg_number)
    {
        exit();
       

            //reg_number  no spaces not case sensitive
        //structire to return:
        //status: OK, ERROR :required
        //error_msg: present on erro state not to be displayed to users shoudl be logged for sorting out problems will contein information to debug on server side
        //msg: string(255):optional if present has text to display always present on error status messegaes to be approved so they can be displayed to user.
        //vehicles: array of vehicles: required can be empty or can have only one car
        //vehicle{
        //  ri_code \\ car code or linked code
        //  mtp_code \\ car code
        //  
        //  make
        //  model
        //  type
        //  drs
        //  body
        //  transmission
        //  value   
        //          }
//151 D 31575
//131 KY 940
//11MO1436
        

        
        $validRegNumber = false;
        $out = array();
        if(!empty($_GET['reg_number'])){
            
            $regNumber = $_GET['reg_number'];
//            if($regNumber=='151D31575'){$validRegNumber=true;}
//            if($regNumber=='131KY940'){$validRegNumber=true;}
//            if($regNumber=='11MO1436'){$validRegNumber=true;}
            $validRegNumber=true;
        }else {
            $out = $this->getApiMessage('ERROR', 'Empty registration number',  'Empty registration number', array());
            echo $out;
            exit;
        }
        if(!$validRegNumber){
            $out = $this->getApiMessage('ERROR', 'Registration number outside of test scope reg:'.$regNumber,  'Registration number outside of test scope', array());
            echo $out;
            exit;
        }
        $vehicleData = array();
        $vehicleData = RegistrationService::getRiDataResults($regNumber);
       var_dump($vehicleData);die;
        //$selectedModel = $_POST['usedCarComModel'];

//        if(!empty($vehicleData['errors']))
//        {
//            Yii::app()->user->setFlash('errorMsg', $vehicleData['errors']);
//            if($_POST['useAjax'] == '')
//            {
//                if($selectedModel == self::$PARAM_USED_CARS_MODEL)
//                {
//                    CController::forward('member/usedCars');
//                }
//                else
//                {
//                    CController::forward('member/usedCommercial');
//                }
//                exit;
//            }
//            echo $this->renderPartial('_basicDetailsInformation',
//                array('vehicle' => "",
//                      'model' => "",
//                      'coreWithAssociatedCarsModel' => "",
//                      'type'=> $selectedModel
//                ));
//            exit;
//        }
        
        $returnedCodeNumber = $vehicleData['code'];
        $trace['returnedCodeNumber'] = $returnedCodeNumber;
//echo ' returned code: -->'.$returnedCodeNumber.'<--';
        //MW Link files
        // if the codenumber returned by RI is not the core model we will look yp the right code in the links files and use it right code to make teh valueation.
        $indicator24 = substr($returnedCodeNumber, 3,2);
       // echo $indicator24;
        if($indicator24 != '24'){
          //  echo '!';
//            if($selectedModel == self::$PARAM_USED_CARS_MODEL)
//                {
                //echo 'uc';
                    //used cars
                    $linkedCode = XmlUcarsLinks::getLinkedCarCode($returnedCodeNumber);
                    if(!empty($linkedCode)){
                       
                        $returnedCodeNumber = $linkedCode;
                        
                       
                    }else {
                        $linkedCode = XmlUcommsLinks::getLinkedCarCode($returnedCodeNumber);
                        if(!empty($linkedCode)){
                            $returnedCodeNumber = $linkedCode;
                        }
                    }
//                }
//                else
//                {
                //    echo 'ucm';
                    //used comms
//                    $linkedCode = XmlUcommsLinks::getLinkedCarCode($returnedCodeNumber);
//                    if(!empty($linkedCode)){
//                        $returnedCodeNumber = $linkedCode;
//                    }
                    
//                }          
        }
         $trace['mtpCode'] = $returnedCodeNumber;
         if(!RegistrationService::isValidYear($vehicleData)){
             $out = $this->getApiMessage('ERROR', 'The car is too old or too yourg to be quoted. :'.$regNumber,  'The car is too old or too yourg to be quoted.', array());
             echo $out;
             exit;
         }
        //echo ' changed if found in linked -->'.$returnedCodeNumber.'<--';
        if(!empty($returnedCodeNumber) && RegistrationService::isValidYear($vehicleData) )
        {
            // najpierw szukamy core jesli core jest pusty to commercial
            $model = RegistrationService::getCarCommModel("UsedCarsModel", $returnedCodeNumber);
            if(empty($model))
            {
                // search commercial
                $model = RegistrationService::getCarCommModel("UsedComCarsModel", $returnedCodeNumber);
                $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);
                $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedComCarsModel", $model);
            } else {
                // search cars
                $main_coreWithAssociatedCarsModel = RegistrationService::getMain_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
                $rest_coreWithAssociatedCarsModel = RegistrationService::getRest_AllCoreWithAssociatedCarsModel("UsedCarsModel", $model);
            }
            $vehicleKms = array("kmsForYear" => RegistrationService::getFieldValueForYear("kms", $vehicleData['year'], $model));
            // jesli $vehicleKms="" to znaczy ze w bazie nie jest uzupelniony rok dla tego samochodu (rok ktory zwraca RI)
        }
        else 
        {
            $model = null;
            $main_coreWithAssociatedCarsModel = array();
            $rest_coreWithAssociatedCarsModel = array();
            $vehicleKms = array("kmsForYear" => "");
        }

        $vehicle = array_merge($vehicleData, $vehicleKms);
        foreach($main_coreWithAssociatedCarsModel as $item){
           if(RegistrationService::getFieldValueForYear("yr", $vehicle['year'], $item) != ''){
                    $outVehicle['ri_code'] = $trace['returnedCodeNumber'];
                    $outVehicle['mtp_code'] = $item['codenumber'];//$trace['mtpCode'];    
                    $outVehicle['make'] = $vehicle['make'];
                    $outVehicle['model'] = $vehicle['model'];
                    $outVehicle['type'] = $item['badgetype'];
                    $outVehicle['drs'] = $item['drs'];
                    $outVehicle['body'] = $item['bod'];
                    $outVehicle['transmission'] = $item['transmission'];
            $outVehicle['value'] = round(self::strToNumber(RegistrationService::getFieldValueForYear("GRP", $vehicle['year'], $item)),2);
            
            $outVehicles[] = $outVehicle;
           
           
           
           }   
        }
        
        
        //MTP Options
        foreach($rest_coreWithAssociatedCarsModel as $item){                
                    if($main_coreWithAssociatedCarsModel[0]['codenumber'] == $item['codenumber'] ||
                       $main_coreWithAssociatedCarsModel[0]['codenumber'] == $item['corecode']) {
                        continue;
                    }
                 if(RegistrationService::getFieldValueForYear("yr", $vehicle['year'], $item) != ''){
                    $outVehicle['ri_code'] = $trace['returnedCodeNumber'];
                    $outVehicle['mtp_code'] = $item['codenumber'];//$trace['mtpCode'];    
                    $outVehicle['make'] = $vehicle['make'];
                    $outVehicle['model'] = $vehicle['model'];
                    $outVehicle['type'] = $item['badgetype'];
                    $outVehicle['drs'] = $item['drs'];
                    $outVehicle['body'] = $item['bod'];
                    $outVehicle['transmission'] = $item['transmission'];
                    $outVehicle['value'] = round(self::strToNumber(RegistrationService::getFieldValueForYear("GRP", $vehicle['year'], $item)),2);

                    $outVehicles[] = $outVehicle;
                 
                     
                 }   
        }
        
        $out = $this->getApiMessage('OK', null,  null, $outVehicles);
             echo $out;
             exit;
                    
        
        $selectedModel = self::$PARAM_USED_CARS_MODEL;
//            $view = 'usedCarsByRegLookup';
//            $this->render($view,
//                array('vehicle' => array_merge($vehicleData, $vehicleKms),
//                      'model' => $model,
//                      'main_coreWithAssociatedCarsModel' => $main_coreWithAssociatedCarsModel,
//                      'rest_coreWithAssociatedCarsModel' => $rest_coreWithAssociatedCarsModel,
//                      'calculatedMain_coreWithAssociatedCars' => array(),
//                      'calculatedRest_coreWithAssociatedCars' => array(),
//                      'type' => $selectedModel,
//                      'hideHeader' => true
//                ));   
//        }
    }
    public static function getApiMessage($status, $errorMsg, $visibleMsg, $vehicles){
        $out['status'] = $status;
        $out['error_msg'] = $errorMsg;
        $out['msg'] = $visibleMsg;
        $out['vehicles']=$vehicles;

        $response = json_encode($out);
        return $response;
        
        
        
        //structire to return:
        //status: OK, ERROR :required
        //error_msg: present on erro state not to be displayed to users shoudl be logged for sorting out problems will contein information to debug on server side
        //msg: string(255):optional if present has text to display always present on error status messegaes to be approved so they can be displayed to user.
        //vehicles: array of vehicles: required can be empty or can have only one car
        //vehicle{
        //  ri_code \\ car code or linked code
        //  mtp_code \\ car code
        //  
        //  make
        //  model
        //  type
        //  drs
        //  body
        //  transmission
        //  value   
    }
    
    public static function strToNumber($str){
                $str = str_replace('$', '', $str);
                $str = str_replace('£', '', $str);
                $str = str_replace('€', '', $str);
                $str = str_replace(',', '', $str);
                return $str;
        }
        
        public static function displayFormatCurrency($value, $currency){
            //$currency = self::getCurrencyHtmlEntity($currency);
            
            $value = round($value,2,PHP_ROUND_HALF_UP);
                $numberFmtCurrency = new \NumberFormatter('en_GB', \NumberFormatter::CURRENCY);
                $numberFmtCurrency->setAttribute(\NumberFormatter::ROUNDING_INCREMENT, 0);
               
                return $numberFmtCurrency->formatCurrency($value, $currency);
        }
}
?>