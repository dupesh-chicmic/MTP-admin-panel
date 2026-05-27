<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
    public $login;
    public $password;

    //private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
    public function __construct()
    {
        //TODO: to chyba nie tutaj ma być
//        if(Yii::app()->user->hasOutdatedSession())
//        {
//            echo 'Your session has timed out. Please log again.';die;
//            $this->addError('Sesja', 'Twoja sesja wygasła po '.Yii::app()->user->getIdleTime().' sekundach nieaktywności.<br/>Pamiętaj, że pozostawianie stanowiska komputerowego z zalogowaną sesją stanowi zagrożenie bezpieczeństwa systemu.<br/>Zaloguj się ponownie.');
//            Yii::app()->user->logout();
//        }

    }

    public function rules()
    {
        return array
        (
			// username and password are required
            array('login, password', 'required'), //password
            //array('login', 'isUser'),
			// rememberMe needs to be a boolean
			//array('rememberMe', 'boolean'),
			// password needs to be authenticated
            array('password', 'authenticate'),
	);
    }

	/**
	 * Declares attribute labels.
	 */
    public function attributeLabels()
    {
        return array
        (
            'login'=>'Login (email)',
            'password'=>'Password'
	);
    }
    
    

    
    
    //function called from validation rules above for the field password
    public function authenticate($attribute,$params)
    {
        // we only want to authenticate when no input errors np poprzednie rules'y
        if(!$this->hasErrors())
        {
            $identity=new UserIdentity($this->login,$this->password);
           
            $identity->authenticate();
            switch($identity->errorCode)
            {
                case        UserIdentity::ERROR_NONE:
                            Yii::app()->user->login($identity);
                            return true;
                            break;
                        
                case        100:   //default value                         
                            $this->addError('user','There is no such user!');
                            return false;
                            break;

                case        UserIdentity::ERROR_USERNAME_INVALID:
                            $this->addError('user','There is no such user!');
                            return false;
                            break;

                case        UserIdentity::ERROR_SESSION_TIMEOUT:
                            $this->addError('sesja', 'Your session has expired. Please log in again');
                            return false;
                            break;

                case        UserIdentity::ERROR_SESSION_UNKNOWN:
                            $this->addError('sesja', 'Session error. Please log in again');
                            return false;
                            break;

                case       UserIdentity::ERROR_PASSWORD_INVALID:
                           $this->addError('password','Password is incorrect.');
                           return false;
                           break;
                       
                default:
                           $this->addError('user','There is no such user!');
                            return false;
                            break;        
                
            }
        }
    }
}