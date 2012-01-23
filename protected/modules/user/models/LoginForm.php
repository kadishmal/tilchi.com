<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $email;
	public $password;
	public $rememberMe;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that email and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// email and password are required
			array('email, password', 'required'),
			array('email', 'length', 'max'=>100),
			array('email', 'email'),
			// rememberMe needs to be a boolean
			array('rememberMe', 'boolean'),
			// password needs to be authenticated
			array('password', 'authenticate'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'email'=>'Email',
			'password'=>Yii::t('UserModule.user', 'Password'),
			'rememberMe'=>Yii::t('UserModule.login', 'Remember me'),
		);
	}
	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity = new UserIdentity($this->email,$this->password);

			if(!$this->_identity->authenticate())
            {
				$this->addError('password', Yii::t('UserModule.login', 'Make sure you have correctly entered your email address and password.'));
            }
		}
	}

	/**
	 * Logs in the user using the given email and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($this->email, $this->password);
			$this->_identity->authenticate();
		}

		if($this->_identity->errorCode === UserIdentity::ERROR_NONE)
		{
            $webUser = Yii::app()->user;

			$duration = $this->rememberMe ? 3600*24*30 : 0; // 30 days
            $webUser->login($this->_identity, $duration);

            // get a list of settings to load at login time this user has permission to alter
            $userSettingsList = SiteSettings::model()->findAll(
                'on_login = :on_login',
                array(':on_login'=>SiteSettings::YES)
            );

            if (count($userSettingsList))
            {
                $userAccessibleSettings = array();
                // check if this user has access to each of these settings
                foreach($userSettingsList as $setting)
                {
                    if(Yii::app()->user->checkAccess($setting->auth_item))
                    {
                        $webUser->setState($setting->name, $setting->default_value);
                        $userAccessibleSettings[$setting->id] = $setting;
                    }
                }

                $criteria = new CDbCriteria;
                $criteria->compare('user_id', $webUser->id);
                $criteria->addInCondition('setting_id', array_keys($userAccessibleSettings));

                $userExistingSettings = UserSettings::model()->findAll($criteria);

                foreach ($userExistingSettings as $userSetting)
                {
                    $webUser->setState($userAccessibleSettings[$userSetting->setting_id]->name, $userSetting->value);
                }
            }

			return true;
		}
		else
			return false;
	}
}