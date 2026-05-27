<?php
/**
 * This is the model class for table "api_calls".
 *
 * The followings are the available columns in table 'api_calls':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property integer $chasis
 * @property integer $riDataOnlyOk
 * @property integer $uk_regs
 * @property integer $valuations
 * @property string $mtp_fields
 * @property integer $is_test_user	
 * @property string $verisk_credential_set
 * @property integer $display_mtp_code
 * @property string $display_function_name
 * The followings are the available model relations:
 */
class ApiUsers extends CmsActiveRecord
{

	public $old_password;
	public $new_password;
	public $repeat_password;

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
		return 'api_users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		//         * @property integer $id
		//  * @property string $username
		//  * @property string $password
		//  * @property integer $chasis
		//  * @property integer $riDataOnlyOk
		//  * @property integer $uk_regs
		//  * @property integer $valuations
		//  * @property string $mtp_fields
		//  * @property integer $is_test_user	
		//  * @property string $verisk_credential_set
		//  * @property integer $display_mtp_code
		//  * @property string $display_function_name
		return array(
			array('id', 'numerical', 'integerOnly'=>true),
			array('username, password', 'required'),
			array('chasis, riDataOnlyOk, uk_regs, valuations, mtp_fields, is_test_user, verisk_credential_set, display_mtp_code, display_function_name', 'safe'),
			array('username', 'safe', 'on'=>'search'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('old_password, new_password, repeat_password', 'required', 'on' => 'changePwd'),
			array('old_password', 'findPasswords', 'on' => 'changePwd'),
			array('repeat_password', 'compare', 'compareAttribute'=>'new_password', 'on'=>'changePwd')
		);
	}


	//matching the old password with your existing password.
	public function findPasswords($attribute, $params)
	{
		$user = ApiUsers::model()->findByPk($this->id);
		if ($user->password != $_REQUEST['ApiUsers']['old_password'])
			$this->addError($attribute, 'Old password is incorrect.');
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		if(Yii::app()->language == 'pl'){
			return array(
					'id' => 'ID',
					'username' => 'Nazwa Użytkownika',
					'password' => 'hasło Tekst'
				);

		}else{

			return 	array(
					'id' => 'ID',
					'username' => 'Username',
					'password' => 'Password'
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
		
		// $criteria->compare('username',$this->username,true);
		// $criteria->compare('user_kms',$this->user_kms,true);
		// $criteria->compare('created',$this->created);
		// $criteria->compare('result',$this->result,true);
		// $cond = '';
		// $cond .= "username='cmac01' ";
		

		// if(isset($this->reg_number) && !empty($this->reg_number)){
		// 	$criteria->addSearchCondition('reg_number', $this->reg_number);
		// }

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