<?php
/**
 * This is the model class for table "api_calls".
 *
 * The followings are the available columns in table 'api_calls':
 * @property integer $id
 * @property string $action
 * @property string $parsed_time
 * @property string $reg_number
 * @property string $username
 * @property string $user_kms
 * @property string $passwordText
 * @property string $nonce
 * @property string $created	
 * @property string $full_call
 * @property string $result
 * The followings are the available model relations:
 */
class ApiCalls extends CmsActiveRecord
{
	public $date_first;

	public $date_last;
	/**
	 * Returns the static model of the specified AR class.
	 * @return CmsPage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'api_calls';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'numerical', 'integerOnly'=>true),
			array('action, parsed_time, reg_number, username, user_kms, passwordText, nonce, created, full_call, result,', 'safe'),
			array('reg_number, username, user_kms, created,date_first,date_last', 'safe', 'on'=>'search'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
		);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		if(Yii::app()->language == 'pl'){
			return array(
					'id' => 'ID',
					'action' => 'akcja',
					'parsed_time' => 'przeanalizowany czas',
					'reg_number' => 'numer rej',
					'username' => 'Nazwa Użytkownika',
					'user_kms' => 'km użytkownika',
					'passwordText' => 'hasło Tekst',
					'nonce' => 'chwilowo',
					'created' => 'Utworzony',
					'full_call' => 'pełne wezwanie',
					'result' => 'wynik'
				);

		}else{

			return 	array(
					'id' => 'ID',
					'action' => 'Action',
					'parsed_time' => 'Parsed Time',
					'reg_number' => 'Registration Number',
					'username' => 'Username',
					'user_kms' => 'User kms',
					'passwordText' => 'Password',
					'nonce' => 'Nonce',
					'created' => 'Created',
					'full_call' => 'Full Call',
					'result' => 'Result'
				);
		}
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	*/
	public function search()
	{

		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		$criteria=new CDbCriteria;
		
		if((isset($this->date_first) && trim($this->date_first) != "") && (isset($this->date_last) && trim($this->date_last) != "")){
			$this->date_last = date('Y-m-d H:i:s',strtotime($this->date_last . ' 23:59:59') + 86400);
			$criteria->addBetweenCondition('created', ''.$this->date_first.'', ''.$this->date_last.'');
		}

		// $criteria->compare('id',$this->id);
		// $criteria->compare('action',$this->action);
		// $criteria->compare('parsed_time',$this->parsed_time,true);
		// 
		// $criteria->compare('username',$this->username,true);
		// $criteria->compare('user_kms',$this->user_kms,true);
		// $criteria->compare('created',$this->created);
		// $criteria->compare('result',$this->result,true);
		$cond = '';
		$cond .= "username='cmac01' ";
		

		if(isset($this->reg_number) && !empty($this->reg_number)){
			$criteria->addSearchCondition('reg_number', $this->reg_number);
		}

		// $this->username = 'cmac01';
		if(isset($this->username) && !empty($this->username)){
			$criteria->addSearchCondition('username', $this->username);
		}
		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'id DESC',
			),
		));
	}
        
}