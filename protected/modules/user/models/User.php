<?php
/**
 * This is the model class for table "tbl_users".
 *
 * The followings are the available columns in table 'tbl_users':
 * @property integer $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 *
 * The followings are the available model relations:
 * @property
 */
class User extends CActiveRecord
{
    const GENDER_MALE = 0;
    const GENDER_FEMALE = 1;

	public $password_repeat;
	public $passwordNew;
	public $passwordNew_repeat;
	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
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
		return '{{users}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            // Register email
            array('email', 'required', 'on'=>'register-email', 'message'=>Yii::t('UserModule.register', 'For successful registration, it is necessary to fill in an <b>email address</b>.')),
            array('password', 'required', 'on'=>'register-email', 'message'=>Yii::t('UserModule.register', 'To protect your account, enter a <b>strong password</b>.')),
			array('email', 'length', 'max'=>100),
			array('email', 'filter', 'filter'=>'strtolower'),
			array('email', 'email', 'message'=>Yii::t('UserModule.register', 'Please enter a valid email address (ex. <b>myname@example.com</b>).')),
			array('email', 'unique', 'on'=>'register-email', 'message'=>Yii::t('UserModule.register', 'A user with this email address <b>already exists</b>. If this is you, proceed to a <a href="signin_url">signin</a> page. Otherwise, enter your own email address.', array('signin_url'=>'/user/signin'))),
            // Register info
            array('first_name, last_name', 'required', 'on'=>'register-info'),
			array('gender', 'required', 'on'=>'register-info', 'message'=>Yii::t('UserModule.register','Please choose your gender.')),
			array('first_name, last_name', 'type', 'type'=>'string'),
			array('first_name, last_name', 'length', 'max'=>45),
            array('gender', 'in', 'range'=>array(self::GENDER_MALE, self::GENDER_FEMALE)),
            // Login
			array('email, password', 'required', 'on'=>'login'),
			// Change password
			array('password_repeat', 'required', 'on'=>'changePassword', 'message'=>Yii::t('UserModule.user', 'Enter your <b>old password</b>.')),
			array('passwordNew', 'required', 'on'=>'changePassword', 'message'=>Yii::t('UserModule.user', 'Enter a <b>new password</b>.')),
			array('passwordNew_repeat', 'required', 'on'=>'changePassword', 'message'=>Yii::t('UserModule.user', 'Repeat your <b>new password</b>.')),
			array('password', 'compare', 'on'=>'changePassword', 'message'=>Yii::t('UserModule.user', 'Your old password is incorrect.')),
			array('passwordNew_repeat', 'compare', 'compareAttribute'=>'passwordNew', 'on'=>'changePassword', 'message'=>'The confirmation password does not match with your new password. You need to enter your new password twice.'),
			//array('username', 'length', 'max'=>150),
			array('password, password_repeat, passwordNew, passwordNew_repeat', 'length', 'max'=>100, 'min'=>6),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'email' => Yii::t('UserModule.user', 'Email'),
			'password' => Yii::t('UserModule.user', 'Password'),
			'password_repeat' => 'Старый пароль',
			'passwordNew' => 'Новый пароль',
			'passwordNew_repeat' => 'Подтвердите пароль',
			'username' => Yii::t('UserModule.user', 'Username'),
			'first_name' => Yii::t('UserModule.user', 'First name'),
			'last_name' => Yii::t('UserModule.user', 'Last name'),
			'gender' => Yii::t('UserModule.user', 'Gender'),
			'join_date'=>Yii::t('UserModule.user', 'Join date'),
		);
	}
	/**
	 * @return actions to perform before saving ie: hash password
     */
    public function beforeSave()
    {
    	if (parent::beforeSave()){
			if ($this->scenario == 'changePassword' || $this->scenario == 'changeUserPassword'){
				$this->password = $this->passwordNew;
			}

	    	if ($this->scenario == 'changePassword' || $this->scenario == 'changeUserPassword'){
	    		$this->cryptPassword();
	    	}

			return true;
		}

		return false;
    }

    public static function getCurrentDateTime()
    {
    	return date('Y-m-d H:i:s');
    }

	public function cryptPassword()
	{
		$salt  = $this->genRandomPassword(32);
		$crypt = User::getCryptedPassword($this->password, $salt);
		$this->password = $crypt.':'.$salt;
	}
	/**
	 * Generate a random password
	 *
	 * @static
	 * @param	int		$length	Length of the password to generate
	 * @return	string			Random Password
	 * @since	1.5
	 */
	private function genRandomPassword($length = 8)
	{
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$len = strlen($salt);
		$makepass = '';

		$stat = @stat(__FILE__);
		if (empty($stat) || !is_array($stat)) $stat = array(php_uname());

		mt_srand(crc32(microtime() . implode('|', $stat)));

		for ($i = 0; $i < $length; $i ++) {
			$makepass .= $salt[mt_rand(0, $len -1)];
		}

		return $makepass;
	}
	/**
	 * Formats a password using the current encryption.
	 *
	 * @access	public
	 * @param	string	$plaintext	The plaintext password to encrypt.
	 * @param	string	$salt		The salt to use to encrypt the password. []
	 *								If not present, a new salt will be
	 *								generated.
	 * @param	string	$encryption	The kind of pasword encryption to use.
	 *								Defaults to md5-hex.
	 * @param	boolean	$show_encrypt  Some password systems prepend the kind of
	 *								encryption to the crypted password ({SHA},
	 *								etc). Defaults to false.
	 *
	 * @return string  The encrypted password.
	 */
	public static function getCryptedPassword($plaintext, $salt = '', $encryption = 'md5-hex', $show_encrypt = false)
	{
		// Get the salt to use.
		$salt = User::getSalt($encryption, $salt, $plaintext);

		// Encrypt the password.
		switch ($encryption)
		{
			case 'plain' :
				return $plaintext;
			/*
			case 'sha' :
				$encrypted = base64_encode(mhash(MHASH_SHA1, $plaintext));
				return ($show_encrypt) ? '{SHA}'.$encrypted : $encrypted;

			case 'crypt' :
			case 'crypt-des' :
			case 'crypt-md5' :
			case 'crypt-blowfish' :
				return ($show_encrypt ? '{crypt}' : '').crypt($plaintext, $salt);

			case 'md5-base64' :
				$encrypted = base64_encode(mhash(MHASH_MD5, $plaintext));
				return ($show_encrypt) ? '{MD5}'.$encrypted : $encrypted;

			case 'ssha' :
				$encrypted = base64_encode(mhash(MHASH_SHA1, $plaintext.$salt).$salt);
				return ($show_encrypt) ? '{SSHA}'.$encrypted : $encrypted;

			case 'smd5' :
				$encrypted = base64_encode(mhash(MHASH_MD5, $plaintext.$salt).$salt);
				return ($show_encrypt) ? '{SMD5}'.$encrypted : $encrypted;

			case 'aprmd5' :
				$length = strlen($plaintext);
				$context = $plaintext.'$apr1$'.$salt;
				$binary = JUserHelper::_bin(md5($plaintext.$salt.$plaintext));

				for ($i = $length; $i > 0; $i -= 16) {
					$context .= substr($binary, 0, ($i > 16 ? 16 : $i));
				}
				for ($i = $length; $i > 0; $i >>= 1) {
					$context .= ($i & 1) ? chr(0) : $plaintext[0];
				}

				$binary = JUserHelper::_bin(md5($context));

				for ($i = 0; $i < 1000; $i ++) {
					$new = ($i & 1) ? $plaintext : substr($binary, 0, 16);
					if ($i % 3) {
						$new .= $salt;
					}
					if ($i % 7) {
						$new .= $plaintext;
					}
					$new .= ($i & 1) ? substr($binary, 0, 16) : $plaintext;
					$binary = JUserHelper::_bin(md5($new));
				}

				$p = array ();
				for ($i = 0; $i < 5; $i ++) {
					$k = $i +6;
					$j = $i +12;
					if ($j == 16) {
						$j = 5;
					}
					$p[] = JUserHelper::_toAPRMD5((ord($binary[$i]) << 16) | (ord($binary[$k]) << 8) | (ord($binary[$j])), 5);
				}

				return '$apr1$'.$salt.'$'.implode('', $p).JUserHelper::_toAPRMD5(ord($binary[11]), 3);
			*/
			case 'md5-hex' :
			default :
				$encrypted = ($salt) ? md5($plaintext.$salt) : md5($plaintext);
				return ($show_encrypt) ? '{MD5}'.$encrypted : $encrypted;
		}
	}
	/**
	 * Returns a salt for the appropriate kind of password encryption.
	 * Optionally takes a seed and a plaintext password, to extract the seed
	 * of an existing password, or for encryption types that use the plaintext
	 * in the generation of the salt.
	 *
	 * @access public
	 * @param string $encryption  The kind of pasword encryption to use.
	 *							Defaults to md5-hex.
	 * @param string $seed		The seed to get the salt from (probably a
	 *							previously generated password). Defaults to
	 *							generating a new seed.
	 * @param string $plaintext	The plaintext password that we're generating
	 *							a salt for. Defaults to none.
	 *
	 * @return string  The generated or extracted salt.
	 */
	public static function getSalt($encryption = 'md5-hex', $seed = '', $plaintext = '')
	{
		// Encrypt the password.
		switch ($encryption)
		{
			case 'crypt' :
			case 'crypt-des' :
				if ($seed) {
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 2);
				} else {
					return substr(md5(mt_rand()), 0, 2);
				}
				break;

			case 'crypt-md5' :
				if ($seed) {
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 12);
				} else {
					return '$1$'.substr(md5(mt_rand()), 0, 8).'$';
				}
				break;

			case 'crypt-blowfish' :
				if ($seed) {
					return substr(preg_replace('|^{crypt}|i', '', $seed), 0, 16);
				} else {
					return '$2$'.substr(md5(mt_rand()), 0, 12).'$';
				}
				break;

			case 'ssha' :
				if ($seed) {
					return substr(preg_replace('|^{SSHA}|', '', $seed), -20);
				} else {
					return mhash_keygen_s2k(MHASH_SHA1, $plaintext, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
				}
				break;

			case 'smd5' :
				if ($seed) {
					return substr(preg_replace('|^{SMD5}|', '', $seed), -16);
				} else {
					return mhash_keygen_s2k(MHASH_MD5, $plaintext, substr(pack('h*', md5(mt_rand())), 0, 8), 4);
				}
				break;

			case 'aprmd5' :
				/* 64 characters that are valid for APRMD5 passwords. */
				$APRMD5 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

				if ($seed) {
					return substr(preg_replace('/^\$apr1\$(.{8}).*/', '\\1', $seed), 0, 8);
				} else {
					$salt = '';
					for ($i = 0; $i < 8; $i ++) {
						$salt .= $APRMD5 {
							rand(0, 63)
							};
					}
					return $salt;
				}
				break;

			default :
				$salt = '';
				if ($seed) {
					$salt = $seed;
				}
				return $salt;
				break;
		}
	}
    public function hasFullInfo()
    {
        $scenario = $this->scenario;
        $this->scenario = 'register-info';
        $hasFullInfo = false;

        if ($this->validate())
        {
            $hasFullInfo = true;
        }
        else{
            $hasFullInfo = 'edit/profile';
        }

        $this->scenario = $scenario;
        return $hasFullInfo;
    }
}