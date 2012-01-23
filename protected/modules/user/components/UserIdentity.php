<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	const ERROR_EMAIL_INVALID = 3;

	public $email;

	private $_id;
	/**
	 * Constructor.
	 * @param string $username username
	 * @param string $password password
	 */
	public function __construct($email,$password)
	{
		$this->email = $email;
		$this->password = $password;
	}
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the email and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$user = User::model()->findByAttributes(array(
            'email'=>$this->email
        ));

        if($user === null)
        {
            $this->errorCode = self::ERROR_EMAIL_INVALID;
        }
        else{
	        $parts	= explode(':', $user->password);
			$crypt	= $parts[0];
			$salt	= @$parts[1];
			$testcrypt = User::getCryptedPassword($this->password, $salt);

			if ($crypt == $testcrypt)
            {
				$this->_id = $user->id;
                $this->errorCode = self::ERROR_NONE;
			} else {
				$this->errorCode = self::ERROR_PASSWORD_INVALID;
			}
        }

        return !$this->errorCode;
	}
	/**
	 * Returns the unique identifier for the identity.
	 * The default implementation simply returns {@link username}.
	 * This method is required by {@link IUserIdentity}.
	 * @return string the unique identifier for the identity.
	 */
	public function getId()
    {
        return $this->_id;
    }
	/**
	 * Returns the display name for the identity.
	 * The default implementation simply returns {@link username}.
	 * This method is required by {@link IUserIdentity}.
	 * @return string the display name for the identity.
	 */
	public function getName()
	{
		return $this->email;
	}
}