<?php

class SiteController extends Controller {

    const MAIL_DAY_BEFORE_EXPIRED = 1;
    const MAIL_MONTH_BEFORE_EXPIRED = 30;
    const MSG_RESET_USER_TOKEN = 'Token has been removed.';
    const MSG_RESET_ALL_MOBILE_TOKENS = 'Mobile tokens were cleared successfully.';
    const MSG_NO_USERS_WITH_MOBILE_TOKEN = 'No users with mobile token.';
    const MSG_GUIDE_TOKEN_SELECTION_SAVED = 'Selected users were saved for future daily resets.';
    const MSG_AFTER_SENT_CONTACT_MSG = 'Thank you for contacting us. We will respond to you as soon as possible.';
    const MSG_USER_ADDED = 'User has been added.';
    const MSG_USER_DELETED = 'User was deleted.';
    const PARAM_LOGIN_WELCOME_PAGE_ID = 75;
    const GUIDE_TOKEN_SELECTION_FILE = 'reset_guide_tokens_selected_users.json';

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function actionLoggedWelcome() {
        //$this->layout = Controller::getLayoutDevice();
        $this->layout = "//layouts/iframe";
        $model = CmsPage::model()->findByPk(self::PARAM_LOGIN_WELCOME_PAGE_ID);
        $this->render('loggedWelcome', array('model' => $model));
    }

    public function actionValuation() {
        $this->layout = Controller::getLayoutDevice();
        $this->render('vehicleValuation', array('model' => new Valuation));
    }

    public function actionForgotPassword() {
        $this->layout = '//layouts/iframeLogin';
        if (isset($_POST['username'])) {
            $loginValue = $_POST['username'];
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($loginValue == "") {
                $this->render('forgotPassword', array("msg" => "You must enter a username."));
            }
        }
        if (!empty($loginValue)) {
            $userModel = Uzytkownik::model()->find('login=:login', array('login' => $_POST['username']));
            if (empty($userModel)) {
                $userModel = Uzytkownik::model()->find('email=:login', array('login' => $_POST['username']));
            }
            if (!empty($userModel)) {
//                $userModel->email = null;
                $userEmail = $userModel->email;
//                CVarDumper::dump($userModel);
                if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                    $this->render('forgotPassword', array("msg" => "There is no valid email address associated with this account."));
                }
                if (filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                    $newPass = $this->randomPassword();
                    $userModel->haslo = $newPass;
                    if ($userModel->save()) {
                        if (Email::sendEmail($userEmail, $newPass)) {
                            $this->redirect(array('site/emailSubmitted'));
                        }
                    }
                }
            }
            if (empty($userModel)) {
                $this->render('forgotPassword', array("msg" => "That username could not be found."));             
            }
        }

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->render('forgotPassword');
        }
    }

    public function actionEmailSubmitted() {
        //$this->layout = Controller::getLayoutDevice();
        $this->layout = '//layouts/iframeLogin';
        $this->render('confirmEmailSubmitted');
    }

    public function actionSaveValuation() {
        $this->layout = Controller::getLayoutDevice();
        if (!empty($_POST)) {
            if (empty($_POST['make'])) {
                Yii::app()->user->setFlash('errorValidation', 'Please fill in all the required fields. You\'re missing: Make.');
                $this->render('vehicleValuation', array('model' => new Valuation));
                die;
            }
            if (empty($_POST['model'])) {
                Yii::app()->user->setFlash('errorValidation', 'Please fill in all the required fields. You\'re missing: Model.');
                $this->render('vehicleValuation', array('model' => new Valuation));
                die;
            }
            if (empty($_POST['type'])) {
                Yii::app()->user->setFlash('errorValidation', 'Please fill in all the required fields. You\'re missing: Type.');
                $this->render('vehicleValuation', array('model' => new Valuation));
                die;
            }
            if (empty($_POST['year'])) {
                Yii::app()->user->setFlash('errorValidation', 'Please fill in all the required fields. You\'re missing: Year.');
                $this->render('vehicleValuation', array('model' => new Valuation));
                die;
            }
            if (empty($_POST['transmission'])) {
                Yii::app()->user->setFlash('errorValidation', 'Please fill in all the required fields. You\'re missing: Transmission.');
                $this->render('vehicleValuation', array('model' => new Valuation));
                die;
            }
            if (empty($_POST['body'])) {
                Yii::app()->user->setFlash('errorValidation', 'Please fill in all the required fields. You\'re missing: Body.');
                $this->render('vehicleValuation', array('model' => new Valuation));
                die;
            }
            if (empty($_POST['engine'])) {
                Yii::app()->user->setFlash('errorValidation', 'Please fill in all the required fields. You\'re missing: Engine.');
                $this->render('vehicleValuation', array('model' => new Valuation));
                die;
            }
            if (empty($_POST['fuel'])) {
                Yii::app()->user->setFlash('errorValidation', 'Please fill in all the required fields. You\'re missing: Fuel.');
                $this->render('vehicleValuation', array('model' => new Valuation));
                die;
            }
            if (empty($_POST['odometer'])) {
                Yii::app()->user->setFlash('errorValidation', 'Please fill in all the required fields. You\'re missing: Odometer.');
                $this->render('vehicleValuation', array('model' => new Valuation));
                die;
            }
//                if(empty($_POST['year_registered'])){
//                    Yii::app()->user->setFlash('errorValidation','Please fill in all the required fields. You\'re missing: Year 1st Registered.');
//                    $this->render('vehicleValuation',array('model'=>new Valuation));die;
//                }
            if (empty($_POST['country_registered'])) {
                Yii::app()->user->setFlash('errorValidation', 'Please fill in all the required fields. You\'re missing: Country 1st Registered.');
                $this->render('vehicleValuation', array('model' => new Valuation));
                die;
            }
            if (empty($_POST['valuation_date'])) {
                Yii::app()->user->setFlash('errorValidation', 'Please fill in all the required fields. You\'re missing: Valuation Date.');
                $this->render('vehicleValuation', array('model' => new Valuation));
                die;
            }
            if (empty($_POST['contact_email'])) {
                Yii::app()->user->setFlash('errorValidation', 'Please fill in all the required fields. You\'re missing: Contact Email.');
                $this->render('vehicleValuation', array('model' => new Valuation));
                die;
            }

            // CAPTCHA VALIDATE
            $model = new Valuation;
            $model->verifyCode = $_POST['Valuation']['verifyCode'];
            if (!$model->validate()) {
                Yii::app()->user->setFlash('errorValidation', 'The verification code is incorrect.');
                $this->render('vehicleValuation', array('model' => new Valuation));
                die;
            } else {
                $this->actionSendMailFromVehicleValuationSite($_POST['contact_email'], $_POST);
                $this->actionSendMailFromVehicleValuationSite("carguide@mtp.ie", $_POST);
                ($this->layout == '//layouts/mainMobile') ? CController::forward('mobile/valuationThanks') : $this->render('valuationThanks');
                die;
            }
        } else {
            throw new CHttpException(400, 'Invalid request. Form data is empty.');
        }
    }

    public function actionthanks() {
        $this->layout = Controller::getLayoutDevice();
        $this->render('valuationThanks');
    }

    public function actionResetValuation() {
        $this->layout = Controller::getLayoutDevice();
        unset($_POST);
        $this->render('vehicleValuation', array('model' => new Valuation));
    }

    public function actionSendMailFromVehicleValuationSite($recipienEmail, $post) {
        ob_start();

        $criteria = new CDbCriteria;
        $criteria->compare('login', $recipienEmail);
        $user = Uzytkownik::model()->find($criteria);

        Yii::import('application.extensions.phpmailer.JPhpMailer');
        $mail = new JPhpMailer;
        $SMTPDebug = true;
        //$mail->IsSMTP();
        $mail->CharSet = "UTF-8";
        $mail->Host = Yii::app()->params['mail_host'];
        $mail->SMTPAuth = true;
        $mail->Port = '587';
        $mail->Username = Yii::app()->params['mail_username'];
        $mail->Password = Yii::app()->params['mail_password'];
        $mail->SetFrom("carguide@mtp.ie", 'mtp system');
        $mail->Subject = 'MTP - Vehicle Valuation';
        $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
        $mail->MsgHTML('
Thank you for submitting the valuation form on mtp.ie. You can expect to receive a reply shortly.Below is a summary of the details you entered:<br /><br />
Make: ' . $post['make'] . '<br />
Model: ' . $post['model'] . '<br />
Type: ' . $post['type'] . '<br />
Year: ' . $post['year'] . '<br />
Transmission: ' . $post['transmission'] . '<br />
Doors: ' . $post['doors'] . '<br />
Body: ' . $post['body'] . '<br />
Engine: ' . $post['engine'] . '<br />
Fuel: ' . $post['fuel'] . '<br />
Odometer: ' . $post['odometer_radio'] . '<br />
Reading: ' . $post['odometer'] . '<br />
Country: ' . $post['country_registered'] . '<br />
Extras: ' . $post['extras'] . '<br />
ValuationDate: ' . $post['valuation_date'] . '<br />
ContactName: ' . $post['contact_name'] . '<br />
ContactEmail: ' . $post['contact_email'] . '<br />

<p>Regards<br />
MTP Team
              ');

        $mail->AddAddress($recipienEmail);
        $mail->Send();
        $mail->ClearAddresses();
        $mail->ClearAttachments();
        ob_end_clean();
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        /**
         * Pierwsze wlaczenie strony
         */
        if (Yii::app()->mobileDetect->isMobile() || Yii::app()->mobileDetect->isTablet()) {
//            $this->layout = Controller::getLayoutDevice();
//            Yii::app()->user->setFlash('showBackButton',null);
//                $this->render('//mobile/mainMenu',array(
//                    'mobileMenu'=>Mobile::getMobileSites(),
//                ));
//            Yii::app()->end();
        }

        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        //$this->render('index');
        /* Przekieruj na home */
//            $urlHome = CmsPage::model()->getElement('id', 4, 'CmsPage', 'url');
//            if(empty($urlHome)){
//                    $criteria = new CDbCriteria;
//                    $criteria->compare('display',1);
//                    $criteria->order='`order`';
//                    $criteria->limit=1;
//
//                    $defaultPage = CmsPage::model()->findAll($criteria);
//                    $urlHome = $defaultPage[0]->url;
//            }
//            $this->redirect(array('/cms/cmsPage/displayPage','url'=>$urlHome));
        $this->redirect(array('/member/usedCarsIFrame'));
        exit;
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            $this->layout = Controller::getLayoutDevice();
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the contact page
     */
    public function actionContact() {
        var_dump('test');
        die;
        $model = new ContactForm;
        if (isset($_POST['ContactForm'])) {
            $model->attributes = $_POST['ContactForm'];
            if ($model->validate()) {
                //$headers="From: {$model->email}\r\nReply-To: {$model->email}";
                //mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
                ob_start();
                Yii::import('application.extensions.phpmailer.JPhpMailer');
                $mail = new JPhpMailer;
                $SMTPDebug = true;
                //$mail->IsSMTP();
                $mail->CharSet = "UTF-8";
                $mail->Host = Yii::app()->params['mail_host'];
                $mail->SMTPAuth = true;
                $mail->Port = '587';
                $mail->Username = Yii::app()->params['mail_username'];
                $mail->Password = Yii::app()->params['mail_password'];
                $mail->SetFrom("carguide@mtp.ie", 'mtp system');
                $mail->Subject = $model->subject;
                $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
                $mail->MsgHTML($model->body);

                $mail->AddAddress(Yii::app()->params['adminEmail']);
                $mail->Send();
                $mail->ClearAddresses();
                $mail->ClearAttachments();
                ob_end_clean();

                Yii::app()->user->setFlash('contact', self::MSG_AFTER_SENT_CONTACT_MSG);
                $this->refresh();
            }
        }
        $this->render('contact', array('model' => $model));
    }

    public function actionNoDesktopToken() {
        //display no token information with STORE TOKEN link
        $this->layout = 'iframe';
        $this->render('noDesktopToken');        
    }
    
    public function actionSessionExpired() {
        //display no token information with STORE TOKEN link
        $this->layout = 'iframe';
        $this->render('sessionExpired');        
    }
    
    public function actionTooManyActiveNetworkUsers() {
        //display no token information with STORE TOKEN link
        $this->layout = 'iframe';
        $this->render('tooManyActiveNetworkUsers');        
    }
    
    public function actionStoreDesktopToken() {
        //store token and redirect to welcome page
        $lvUser = Uzytkownik::model()->find(array(
                'condition'=>'id=:id',
                'params'=>array(':id'=>Yii::app()->user->getId())
            ));
        $changesMade = UserTokenChanges::model()->findAll('uzytkownik_id=:user_id', array('user_id'=>$lvUser->id));
        if(sizeof($changesMade)>3){
            $this->redirect(array('/site/tooManyTokenChanges'));
        }
        $oldToken = null;
        if(!empty($lvUser->desktop_token)){
            $oldToken = $lvUser->desktop_token;
        }
        
        if(Uzytkownik::storeDesktopCookie()){
            $newChange = new UserTokenChanges;
            $newChange->uzytkownik_id = $lvUser->id;
            $newChange->old_token = $oldToken;
            $newChange->ip_address = Yii::app()->request->userHostAddress;
            $newChange->save();
            echo '<script>window.top.location.href = "http://mtp.ie/welcome/";</script>';
        }
    }
    
    public function actionWrongDesktopToken() {
        //display no token information with STORE TOKEN link        
        $this->layout = 'iframe';
        $this->render('wrongDesktopToken'); 
    }
    
    public function actionTooManyTokenChanges() {
        //display no token information with STORE TOKEN link        
        $this->layout = 'iframe';
        $this->render('tooManyTokenChanges'); 
    }
    
    /**
     * Displays the login page
     */
    public function actionLogin() {
        $model = new LoginForm;

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate()) {

                // $this->forward('account');
            } else {
                //print_r($model->getErrors());die;
            }
        }
        $this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionLoginIFrame() {
        $msg = null;
        if (!Yii::app()->user->isGuest) {
            //   echo 'isNOTGuest';

            if (Yii::app()->mobileDetect->isMobile() || Yii::app()->mobileDetect->isTablet()) {
                
                echo '<script>window.top.location.href = "http://mtp.ie/mobile/";</script>';
            } else {
                
                Uzytkownik::cleanUnusedNetworkSessions();
                Uzytkownik::checkDesktopCookie();
                //exit;
                echo '<script>window.top.location.href = "http://mtp.ie/welcome/";</script>';
            }
            //uncoment for live
            exit;
        }
        $this->layout = '//layouts/iframeLogin';
        $model = new LoginForm;
        
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            if ($model->validate()) {
                //
                if (Yii::app()->user->getIsAdmin()) {
                    //echo "<script>window.location.assign('http://mtp.ie/app/index.php?r=cms/cmsPage/create');</script>";
                    echo '<script>window.top.location.href = "http://mtp.ie/app/index.php?r=cms/cmsPage/create";</script>';

                    // echo '<script>window.top.location.href = "http://mtp.ie/welcome2/";</script>'; 
                    exit;
                    //    echo '<script>window.top.location.href = "http://mtp.ie/app/index.php?r=cms/cmsPage/create";</script>'; 
                    // window.location.replace("http://stackoverflow.com");
                }
                //CVardumper::dump();
                //$layout = Controller::getLayoutDevice();
                if (Yii::app()->mobileDetect->isMobile() || Yii::app()->mobileDetect->isTablet()) {
                    // $this->forward('mobile/mainMenu');
                    echo '<script>window.top.location.href = "http://mtp.ie/mobile/";</script>';
                    exit;
                } else {

                    //$this->forward('account');
                    // echo '<script>window.top.location.href = "http://mtp.ie/used-cars/";</script>'; 
                    Uzytkownik::checkDesktopCookie();
                    //exit;
                    echo '<script>window.top.location.href = "http://mtp.ie/welcome/";</script>';
                    exit;
                    //  exit;
                }
            } else {
                // echo 'error';
                // print_r($model->getErrors());die;
                $msg = '<span style="color: red; padding-bottom:10px;">Wrong login or password.  Please try again!</span>';
            }
        }
        $this->render('login', array('model' => $model, 'msg' => $msg));
        //$this->redirect(Yii::app()->request->urlReferrer);
    }

    public function actionAccount() {
        // order is important
        if (Yii::app()->user->isAdmin) {
            $this->redirect(array('/cms/site/index'));
            exit;
        }

        if (Yii::app()->user->isGuest) {
            $this->redirect(array('/cms/cmsPage/displayPage', 'url' => 'home'));
            exit;
        } else {
            $this->layout = 'main';
            // check licence
            $licence = Uzytkownik::model()->checkExpirationDate(Uzytkownik::PARAM_USED_CARS);
            if ($licence) {
                $this->redirect(array('/cms/cmsPage/displayPage', 'url' => 'login_welcome'));
                //echo '<script>window.top.location.href = "http://mtp.ie/welcome/";</script>';
                exit;
            } else {
                //$this->redirect(array('/cms/cmsPage/displayPage','url'=>'licence_expired'));
                echo '<script>window.top.location.href = "http://mtp.ie/license/";</script>';
                exit;
            }
        }
        //$this->redirect(array('site/index'));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        //$this->redirect(Yii::app()->homeUrl);
        $this->redirect('site/loginIframe');
    }

    public function actionLogoutwp() {
        Uzytkownik::destroyNetworkSession();
        Yii::app()->user->logout();
        
        //$this->redirect(Yii::app()->homeUrl);
        $this->redirect('http://mtp.ie');
    }

    public function actionViewNewsAjax() {
        if (isset($_GET['id']) && $_GET['id'] != '') {
            $news = CmsNews::model()->find(array(
                'condition' => 'id=:newsid',
                'params' => array(':newsid' => $_GET['id'])
            ));
            $this->renderPartial('cms.views.layouts.CmsNews._ajaxShowNewsLong', array('item' => $news));
        }
    }

    public function actionHideNewsAjax() {
        if (isset($_GET['id']) && $_GET['id'] != '') {
            $news = CmsNews::model()->find(array(
                'condition' => 'id=:newsid',
                'params' => array(':newsid' => $_GET['id'])
            ));
            $this->renderPartial('cms.views.layouts.CmsNews._ajaxHideNewsLong', array('item' => $news));
        }
    }

    public function actionSendMailToUser($emailRecipient, $password) {
        $subject = 'MTP - Access data';
        $msg = '';

        ob_start();

        $criteria = new CDbCriteria;
        $criteria->compare('login', $emailRecipient);
        $user = Uzytkownik::model()->find($criteria);

        Yii::import('application.extensions.phpmailer.JPhpMailer');
        $mail = new JPhpMailer;
        $SMTPDebug = true;
        //$mail->IsSMTP();
        $mail->CharSet = "UTF-8";
        $mail->Host = Yii::app()->params['mail_host'];
        $mail->SMTPAuth = true;
        $mail->Port = '587';
        $mail->Username = Yii::app()->params['mail_username'];
        $mail->Password = Yii::app()->params['mail_password'];
        $mail->SetFrom("carguide@mtp.ie", 'mtp system');
        $mail->Subject = $subject;
        $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
        $mail->MsgHTML('
              <p>Welcome,<br />' . $user['imie'] . ' ' . $user['nazwisko'] . '<p>
                  Your login is<br/>' . $user['login'] . '<p>
                  Your password is<br/>' . $password . '<p>
              <a target="_blank" href="http://mtp.ie/login/">Click here to log in</a>
              <br />
              or paste the link in the browser address bar<br />
              http://mtp.ie/login/
              
              <p>Regards<br />
              MTP Team
              ');
        //'.$_SERVER['HTTP_HOST'].Yii::app()->request->baseUrl.'

        $mail->AddAddress($user['email'], $user['imie'] . ' ' . $user['nazwisko']);
        $mail->Send();
        $mail->ClearAddresses();
        $mail->ClearAttachments();
        ob_end_clean();
    }

    public function actionSendEmails() {
        // wywolanie
        // {N} - liczba dni 1 or 30 - liczba z selecta w cms elements/email (dbField => cmsEmailData.status)
        // echo '<a href="index.php?r=site/sendEmails&days={N}">Send Emails to users</a><br />';

        if (isset($_GET['days']) && $_GET['days'] != '') {
            $days = $_GET['days'];
            self::sendMail($days);
        }
    }

    public static function sendMail($type) {

        $criteria = new CDbCriteria;
        $criteria->compare('typ_uzytkownika', 1101);

        //type = 1 or n
        $cmsDataEmail = CmsEmailData::model()->find(array(
            'condition' => 'status=:stat',
            'params' => array(':stat' => $type)
        ));
        if (empty($cmsDataEmail)) {
            $cmsDataEmail = new CmsEmailData();
            $cmsDataEmail->title = '';
            $cmsDataEmail->text = '';
            $cmsDataEmail->status = 00;
        }


        $mailStatus = 0;
        $newMailStatus = 1;
        $subject = $cmsDataEmail->title;
        $msgHtml = $cmsDataEmail->text;
        switch ($type) {
            case self::MAIL_DAY_BEFORE_EXPIRED: // 1
                $status = 33001;
                // uzytkownicy ktorym licencja konczy sie jutro
                $criteria->condition = "`lic_exp_cars` > CURDATE() AND (DATEDIFF(`lic_exp_cars`,CURDATE()) = 1)";
                break;
//			case self::MAIL_MONTH_BEFORE_EXPIRED: // 30
//                                $status=33002;
//                                // uzytkownicy ktorym licencja konczy sie w ciagu 30 dni ( ale aby nie powtarzac maila z "licencja konczy sie jutro" jest warunek AND (DATEDIFF(`lic_exp_date`,CURDATE())+1 = 2)
//                                $criteria->condition = "`lic_exp_date` > CURDATE() AND (DATEDIFF(`lic_exp_date`,CURDATE())+1 <= 30) AND (DATEDIFF(`lic_exp_date`,CURDATE())+1 > 2)";
//				break;
            default: // n
                //throw new Exception('Not implemented');
                $status = 330; //plus $cmsDataEmail->status
                if ($cmsDataEmail->status < 10 && length($cmsDataEmail->status < 2)) {
                    $status = $status . '0' . $cmsDataEmail->status;
                } else {
                    $status = $status . $cmsDataEmail->status;
                }
                // uzytkownicy ktorym licencja konczy sie w ciagu $cmsDataEmail->status dni ( ale aby nie powtarzac maila z "licencja konczy sie jutro" jest warunek AND (DATEDIFF(`lic_exp_date`,CURDATE())+1 = 2)
                $criteria->condition = '`lic_exp_cars` > CURDATE() AND (DATEDIFF(`lic_exp_cars`,CURDATE())+1 <= ' . $cmsDataEmail->status . ') AND (DATEDIFF(`lic_exp_cars`,CURDATE())+1 > 2)';
                break;
        }

        $models = Uzytkownik::model()->findAll($criteria);

        if (count($models) > 0) {
            Yii::import('application.extensions.phpmailer.JPhpMailer');
            $mail = new JPhpMailer;
            $SMTPDebug = true;
            //$mail->IsSMTP();
            $mail->CharSet = "UTF-8";
            $mail->Host = Yii::app()->params['mail_host'];
            $mail->SMTPAuth = true;
            $mail->Port = '587';
            $mail->Username = Yii::app()->params['mail_username'];
            $mail->Password = Yii::app()->params['mail_password'];
            $mail->SetFrom("carguide@mtp.ie", 'mtp system');
            $mail->Subject = $subject;
            $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';

            foreach ($models as $user) {
                $mail->MsgHTML($msgHtml);
//				$mail->MsgHTML(str_replace(
//                                        array('{FIRST_NAME}', '{LINK}'),
//                                        array($user->imie, CHtml::link('linkDoOdnowieniaLicencji')),
//                                        $msgHtml
//                                ));
                $mail->AddAddress($user->login, $user->imie . ' ');

                // sprawdz czy do tego user juz email byl wyslany o statusie (1/30 dni) oraz email_status =1 (wyslany)
                $emailExist = Email::model()->find(array(
                    'condition' => 'recipient=:login AND status=:stat AND email_status=:emstat',
                    'params' => array(':login' => $user->login, ':stat' => $status, ':emstat' => 1)
                ));
                //echo $emailExist->recipient.' '.$emailExist->lic_exp.' <p>';
                if (!empty($emailExist) && $emailExist->lic_exp >= date('Y-m-d')) {

                    // email by wyslany
                    // jeszcze ma licencje
                    // nie wysylaj do niego maila !
                } else if (!empty($emailExist) && $emailExist->lic_exp <= date('Y-m-d')) {
                    // licencja sie skonczyla np. dzis lub wczoraj
                    // nie wysylaj do niego maila !
                    //sprawdz czy przedluzyl licencje
                    if ($user->lic_exp_cars !== $emailExist->lic_exp) {
                        //sprawdz czy z nowa  * email.$USER->lic_exp_date *  nie ma juz wpisu !!
                        $exists = Email::model()->find(array(
                            'condition' => 'recipient=:login AND status=:stat AND email_status=:emstat AND lic_exp=:licExp',
                            'params' => array(':login' => $user->login, ':stat' => $status, ':emstat' => 1, ':licExp' => $user->lic_exp_cars)
                        ));

                        if (empty($exists)) {
                            //tak - dodaj nowy rekord
                            $email = new Email();
                            $email->sender = Yii::app()->params['mailUsername'];
                            $email->recipient = $user->login;
                            $email->title = $subject;
                            $email->text = $msgHtml;
                            $email->status = $status;
                            $email->email_status = $mailStatus; //0
                            $email->lic_exp = $user->lic_exp_cars;
                            $email->save();

                            if ($mail->Send()) {
                                $updateEmail = Email::model()->find(array(
                                    'condition' => 'id=:eid',
                                    'params' => array(':eid' => $email->id)
                                ));
                                $updateEmail->email_status = $newMailStatus; //1
                                $updateEmail->update(array('email_status'));
                            } else {
                                echo '<br>error sending email ' . $user->login . ' name ' . $user->imie . ' ';
                            }
                        }
                    }
                } else {

                    $email = new Email();
                    $email->sender = Yii::app()->params['mailUsername'];
                    $email->recipient = $user->login;
                    $email->title = $subject;
                    $email->text = $msgHtml;
                    $email->status = $status;
                    $email->email_status = $mailStatus; //0
                    $email->lic_exp = $user->lic_exp_cars;
                    $email->save();

                    if ($mail->Send()) {
                        $updateEmail = Email::model()->find(array(
                            'condition' => 'id=:eid',
                            'params' => array(':eid' => $email->id)
                        ));
                        $updateEmail->email_status = $newMailStatus; //1
                        $updateEmail->update(array('email_status'));
                    } else {
                        echo '<br>error sending email ' . $user->login . ' name ' . $user->imie . ' ';
                    }
                }
            }
        }
    }

    public function actionWelcome() {
        
        $this->layout = 'mainSubPage';
        $this->render('welcome');
    }

    /**
     * Licence Expired view
     */
    public function actionExpired() {
        $this->layout = Controller::getLayoutDevice();
        $this->render('licenceOver');
    }

    public function actionResetUserToken() {
        $this->layout = 'mainSubPage';
        if (isset($_GET['reset']) && $_GET['reset'] != '') {
            $model = Uzytkownik::model()->find('id=?', array($_GET['reset']));
            $model->mobile_token = "";
            if ($model->update(array('mobile_token')))
                Yii::app()->user->setFlash('successMsg', self::MSG_RESET_USER_TOKEN);
        }
        $this->actionUsers();
    }
    public function actionResetGuideHomeScreenIosToken() {
        $this->layout = 'mainSubPage';
        if (isset($_GET['reset']) && $_GET['reset'] != '') {
            $model = Uzytkownik::model()->find('id=?', array($_GET['reset']));
            $model->guide_mobile_token_ios = "";
            if ($model->update(array('guide_mobile_token_ios')))
                Yii::app()->user->setFlash('successMsg', self::MSG_RESET_USER_TOKEN);
        }
        $this->actionUsers();
    }
    public function actionResetGuideToken() {
        $this->layout = 'mainSubPage';
        if (isset($_GET['reset']) && $_GET['reset'] != '') {
            $model = Uzytkownik::model()->find('id=?', array($_GET['reset']));
            $model->guide_mobile_token = "";
            if ($model->update(array('guide_mobile_token')))
                Yii::app()->user->setFlash('successMsg', self::MSG_RESET_USER_TOKEN);
        }
        $this->actionUsers();
    }
    
    public function actionResetGuideTokensUI() {

        $this->layout = '//layouts/admin/mainSubPageForManagingUsers';
        
        $criteria = new CDbCriteria;
        $criteria->order = 'imie ASC, nazwisko ASC, login ASC';
        $allUsers = Uzytkownik::model()->findAll($criteria);
        $savedSelectedUsers = $this->getSavedGuideTokenUserIds();
        
        $this->render('resetGuideTokens', array(
            'users' => $allUsers,
            'savedSelectedUsers' => $savedSelectedUsers,
        ));
    }

    public function actionResetGuideTokensForSelectedUsers() {
        $this->layout = '//layouts/admin/mainSubPageForManagingUsers';
        $criteria = new CDbCriteria;
        $criteria->order = 'imie ASC, nazwisko ASC, login ASC';
        $allUsers = Uzytkownik::model()->findAll($criteria);
        
        if (isset($_POST['saveSelection'])) {
            $selectedUserIds = isset($_POST['selectedUsers']) && is_array($_POST['selectedUsers']) ? $_POST['selectedUsers'] : array();
            $normalizedUserIds = $this->normalizeGuideTokenUserIds($selectedUserIds);
            $this->saveGuideTokenUserIds($normalizedUserIds);
            Yii::app()->user->setFlash('successMsg', self::MSG_GUIDE_TOKEN_SELECTION_SAVED);
            $this->render('resetGuideTokens', array(
                'users' => $allUsers,
                'savedSelectedUsers' => $normalizedUserIds,
            ));
            return;
        }

        $selectedUserIds = array();
        if (isset($_POST['selectedUsers']) && is_array($_POST['selectedUsers']) && count($_POST['selectedUsers']) > 0) {
            $selectedUserIds = $this->normalizeGuideTokenUserIds($_POST['selectedUsers']);
        } elseif (isset($_GET['runSaved']) && $_GET['runSaved'] == '1') {
            $selectedUserIds = $this->getSavedGuideTokenUserIds();
        } else {
            if (!empty($_POST)) {
                Yii::app()->user->setFlash('errorMsg', 'Please select at least one user or save a selection first.');
            } else {
                Yii::app()->user->setFlash('errorMsg', 'No users were selected.');
            }
            $this->render('resetGuideTokens', array(
                'users' => $allUsers,
                'savedSelectedUsers' => $this->getSavedGuideTokenUserIds(),
            ));
            return;
        }

        if (empty($selectedUserIds)) {
            Yii::app()->user->setFlash('errorMsg', 'Please select at least one user.');
            $this->render('resetGuideTokens', array(
                'users' => $allUsers,
                'savedSelectedUsers' => $this->getSavedGuideTokenUserIds(),
            ));
            return;
        }

        $logFile = Yii::app()->runtimePath . '/reset_guide_tokens.log';
        $results = array();
        $results[] = 'Guide token refresh started: ' . date('Y-m-d H:i:s');

        foreach ($selectedUserIds as $userId) {
            $model = Uzytkownik::model()->find('id=?', array($userId));

            if ($model === null) {
                $results[] = "NOT FOUND: ID {$userId}";
                continue;
            }

            $model->guide_mobile_token = "";
            if ($model->update(array('guide_mobile_token'))) {
                $results[] = "SUCCESS: {$model->login} ({$model->imie} {$model->nazwisko}) - id={$model->id}";
            } else {
                $results[] = "FAILED: {$model->login} ({$model->imie} {$model->nazwisko}) - id={$model->id}";
            }
        }

        $results[] = 'Guide token refresh finished: ' . date('Y-m-d H:i:s');
        file_put_contents($logFile, implode("\n", $results) . "\n", FILE_APPEND);

        Yii::app()->user->setFlash('successMsg', 'Token reset process completed. Check the log for details.');
        $this->render('resetGuideTokensResults', array('results' => $results));
    }

    protected function getGuideTokenSelectionFilePath() {
        return Yii::app()->runtimePath . DIRECTORY_SEPARATOR . self::GUIDE_TOKEN_SELECTION_FILE;
    }

    protected function normalizeGuideTokenUserIds($userIds) {
        $normalized = array();
        foreach ((array) $userIds as $userId) {
            $userId = (int) $userId;
            if ($userId > 0) {
                $normalized[$userId] = $userId;
            }
        }
        return array_values($normalized);
    }

    protected function saveGuideTokenUserIds(array $userIds) {
        $payload = array(
            'updatedAt' => date('Y-m-d H:i:s'),
            'userIds' => $userIds,
        );
        file_put_contents($this->getGuideTokenSelectionFilePath(), json_encode($payload));
    }

    protected function getSavedGuideTokenUserIds() {
        $filePath = $this->getGuideTokenSelectionFilePath();
        if (!is_file($filePath)) {
            return array();
        }

        $data = json_decode(@file_get_contents($filePath), true);
        if (!is_array($data) || empty($data['userIds']) || !is_array($data['userIds'])) {
            return array();
        }

        return $this->normalizeGuideTokenUserIds($data['userIds']);
    }

    public function actionResetUserDesktopToken() {
        $this->layout = 'mainSubPage';
        if (isset($_GET['reset']) && $_GET['reset'] != '') {
            $model = Uzytkownik::model()->find('id=?', array($_GET['reset']));
            $model->desktop_token = "";
            if ($model->update(array('desktop_token')))
                Yii::app()->user->setFlash('successMsg', self::MSG_RESET_USER_TOKEN);
            $tokenChanges = UserTokenChanges::model()->deleteAll('uzytkownik_id=:user_id', array('user_id'=>$_GET['reset']));
        }
        $this->actionUsers();
    }

    public function actionClearAllMobileTokens() {
        $this->layout = 'mainSubPage';

        $criteria = new CDbCriteria;
        $criteria->addCondition("mobile_token IS NOT NULL AND mobile_token <> ''");
        $models = Uzytkownik::model()->findAll($criteria);

        if (empty($models)) {
            Yii::app()->user->setFlash('successMsg', self::MSG_NO_USERS_WITH_MOBILE_TOKEN);
        } else {
            foreach ($models as $user) {
                $user->mobile_token = "";
                if ($user->update(array('mobile_token'))) {
                    Yii::app()->user->setFlash('successMsg', self::MSG_RESET_ALL_MOBILE_TOKENS);
                }
            }
        }
        $this->actionUsers();
    }

    /**
     * Show list of users
     */
    public function actionUsers() {
        $this->layout = "admin/mainSubPageForManagingUsers";
        $model = new Uzytkownik('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Uzytkownik']))
            $model->attributes = $_GET['Uzytkownik'];
        $this->render('/uzytkownik/admin', array('model' => $model));
    }

    public function actionAddNewUser() {
        $this->layout = "admin/mainSubPageForManagingUsers";
        $model = new Uzytkownik;

        if (isset($_POST['Uzytkownik'])) {
            $model->attributes = $_POST['Uzytkownik'];
            $model->typ_uzytkownika = 1101;
            $model->status_uzytkownika = 2002;
            $notSha1Password_temp = Uzytkownik::model()->generateNewPassword();
            $model->haslo = $notSha1Password_temp;
            $model->mobile_on = $_POST['Uzytkownik']['mobile_on'];

            $model->lic_start_cars = ($model->lic_start_cars == '') ? date('Y-m-d') : $model->lic_start_cars;
            $model->lic_start_comm = ($model->lic_start_comm == '') ? date('Y-m-d') : $model->lic_start_comm;
            $model->lic_start_cars_mob = ($model->lic_start_cars_mob == '') ? date('Y-m-d') : $model->lic_start_cars_mob;
            $model->lic_start_comm_mob = ($model->lic_start_comm_mob == '') ? date('Y-m-d') : $model->lic_start_comm_mob;
            if ($_POST['Uzytkownik']['checks'] == '0') {
                if (Uzytkownik::checkDifferenceBetweenExpirationDates($model) == false) {
                    $this->render('/uzytkownik/create', array('model' => $model));
                    Yii::app()->end();
                }
                $model->checks = 0;
            } else {
                $model->lic_exp_cars = null;
                $model->lic_exp_comm = null;
                $model->lic_exp_cars_mob = null;
                $model->lic_exp_comm_mob = null;
                $model->trial = 0;
                $model->dinkey = 0;
                $model->checks = 1;
            }
            // validate user input and redirect to the previous page if valid
            if ($model->validate()) {
                //dodaj usera
                if ($model->save()) {
                    //wyslij maila do usera z nie zakodowanym haslem (aby mogl zmienic)
                    //$this->actionSendMailToUser($model->login, $notSha1Password_temp);
                }
                $model = new Uzytkownik;
                Yii::app()->user->setFlash('successMsg', self::MSG_USER_ADDED);
                $this->render('/uzytkownik/admin', array('model' => $model));
                exit;
            }
        }
        $this->render('/uzytkownik/create', array('model' => $model));
    }

    public function actionUpdateUser() {
        $this->layout = 'admin/mainSubPageForManagingUsers';
        if (isset($_GET['update']) && $_GET['update'] != '') {
            $model = Uzytkownik::model()->find('id=?', array($_GET['update']));
            // collect user input data
            if (isset($_POST['Uzytkownik'])) {
                $old_lic_start_cars = $model->lic_start_cars;
                $old_lic_exp_cars = $model->lic_exp_cars;
                $old_lic_start_comm = $model->lic_start_comm;
                $old_lic_exp_comm = $model->lic_exp_comm;
                $old_lic_start_cars_mob = $model->lic_start_cars_mob;
                $old_lic_exp_cars_mob = $model->lic_exp_cars_mob;
                $old_lic_start_comm_mob = $model->lic_start_comm_mob;
                $old_lic_exp_comm_mob = $model->lic_exp_comm_mob;

                $model->attributes = $_POST['Uzytkownik'];
                //

                $model->typ_uzytkownika = 007;
                $model->status_uzytkownika = 008;
                $model->mobile_on = $_POST['Uzytkownik']['mobile_on'];
                $model->network_licences_number = $_POST['Uzytkownik']['network_licences_number'];

                $model->lic_start_cars = ($model->lic_start_cars == '') ? $old_lic_start_cars : $model->lic_start_cars;
                $model->lic_exp_cars = ($model->lic_exp_cars == '') ? $old_lic_exp_cars : $model->lic_exp_cars;
                $model->lic_start_comm = ($model->lic_start_comm == '') ? $old_lic_start_comm : $model->lic_start_comm;
                $model->lic_exp_comm = ($model->lic_exp_comm == '') ? $old_lic_exp_comm : $model->lic_exp_comm;
                $model->lic_start_cars_mob = ($model->lic_start_cars_mob == '') ? $old_lic_start_cars_mob : $model->lic_start_cars_mob;
                $model->lic_exp_cars_mob = ($model->lic_exp_cars_mob == '') ? $old_lic_exp_cars_mob : $model->lic_exp_cars_mob;
                $model->lic_start_comm_mob = ($model->lic_start_comm_mob == '') ? $old_lic_start_comm_mob : $model->lic_start_comm_mob;
                $model->lic_exp_comm_mob = ($model->lic_exp_comm_mob == '') ? $old_lic_exp_comm_mob : $model->lic_exp_comm_mob;

                if ($_POST['Uzytkownik']['checks'] == '0') { // checks = false
                    if (Uzytkownik::checkDifferenceBetweenExpirationDates($model)) {
                        $model->checks = 0;
                        $model->update(array('checks'));
                        $model->free_tokens = 0;
                        $model->update(array('free_tokens'));
                    }
                } else {
                    $model->checks = 1;
                    $model->update(array('checks'));
                    $model->update(array('free_tokens'));
                }
                if ($model->validate()) {
                    $basicFields = array('login', 'imie', 'nazwisko', 'used_cars', 'used_com_cars', 'network_licences_number', 'trial', 'pages', 'dinkey', 'mobile_on', 'email');
                    $licenceFields = array('lic_start_cars', 'lic_exp_cars', 'lic_start_comm', 'lic_exp_comm', 'lic_start_cars_mob', 'lic_exp_cars_mob', 'lic_start_comm_mob', 'lic_exp_comm_mob');
                    $updateFields = array_merge($basicFields, $licenceFields);
                    // add user
                    if ($_POST['Uzytkownik']['haslo'] != '') {
                        $passField = array('haslo');
                        $model->haslo = UserIdentity::encryptPassword($_POST['Uzytkownik']['haslo']);
                        $updateFields = array_merge($updateFields, $passField);
                    }
                    if ($model->saveAttributes($updateFields)) {
                        Yii::app()->user->setFlash('successMsg', 'User ' . $model->imie . ' ' . $model->nazwisko . ' (' . $model->login . ') has been changed. ');
                    } else {
                        //print_r($model->getErrors());die;
                    }
                    Yii::app()->user->setFlash('successMsg', 'User ' . $model->imie . ' ' . $model->nazwisko . ' (' . $model->login . ') has been changed. ');
                    $this->render('/uzytkownik/update', array('model' => $model));
                    exit;
                }
            }
            $this->render('/uzytkownik/update', array('model' => $model));
            exit;
        }
        $this->render('/uzytkownik/admin', array('model' => new Uzytkownik));
    }

    public function actionDeleteUser() {
        if (isset($_GET['delete']) && $_GET['delete'] != '') {
            $model = Uzytkownik::model()->find('id=?', array($_GET['delete']));
            $model->delete();
            Yii::app()->user->setFlash('successMsg', self::MSG_USER_DELETED);
        }
        $this->actionUsers();
    }

    public function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

}
