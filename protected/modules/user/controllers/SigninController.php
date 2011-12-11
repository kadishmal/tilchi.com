<?php

class SigninController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
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
            array('allow', // allow any user to login
				'actions'=>array('index', 'restorePassword', 'resetPassword'),
				'users'=>array('?'),
			),
            array('allow', // allow any user to login
				'actions'=>array('logout'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users all other actions
				'users'=>array('*'),
			),
		);
	}
	/**
	 * Displays the login page
	 */
	public function actionIndex()
	{
        $app = Yii::app();
		$model = new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			$app->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
            {
                $user = User::model()->findByPk($app->user->id);
                $hasFullInfo = $user->hasFullInfo();

                if ($hasFullInfo === true)
                {
                    if (strpos($app->user->returnUrl, 'resetPassword') === false)
                    {
                        $this->redirect($app->user->returnUrl);
                    }
                    else{
                        $this->redirect('/site');
                    }
                }
                else{
                    $this->redirect('/user/' . $hasFullInfo);
                }
            }
		}
        $app->user->returnUrl = $app->request->urlReferrer;
		// display the login form
		$this->render('signin', array('model'=>$model));
	}
	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->user->loginUrl);
	}

    public function actionRestorePassword()
    {
        $model = new User('restore-password');

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='restore-password-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

        $template = 'restorePassword';

		// collect user input data
		if(isset($_POST['User']))
		{
			$model->attributes = $_POST['User'];

			// validate user input and redirect to the previous page if valid
			if($model->validate())
            {
                $user = User::model()->findByAttributes(array(
                    'email'=>$model->email
                ));

                if ($user)
                {
                    // check if the user has already requested password reset
                    $userPasswordRestore = UserPasswordRestore::model()->findByAttributes(array(
                        'user_id'=>$user->id,
                        'reset_date'=>null
                    ));

                    // If user has already requested a password reset, check when previous request
                    // was handled. If before the activation period, then send another email.
                    // Otherwise, inform that an email has already been sent.
                    if ($userPasswordRestore)
                    {
                        // the password reset should be activated withing 24 hours
                        // which is 86400 seconds = 60*60*24
                        $timeDiff = time() - $userPasswordRestore->request_date;

                        // If more than 24 hours has passed, regenerate the activation token,
                        // otherwise, inform that a password reset email has already been sent
                        if ($timeDiff < 86400)
                        {
                            $template = 'restorePasswordConfirmation';
                        }
                    }
                    else{
                        $userPasswordRestore = new UserPasswordRestore;
                        $userPasswordRestore->user_id = $user->id;
                    }

                    if ($template != 'restorePasswordConfirmation')
                    {
                        $userPasswordRestore->request_date = time();
                        $ip = Yii::app()->getRequest()->getUserHostAddress();

                        if ($ip)
                        {
                            $userPasswordRestore->ip = ip2long($ip);
                        }

                        if ($userPasswordRestore->save())
                        {
                            $message = New YiiMailMessage;
                            $message->view = 'restoreUserPassword';

                            $message->subject = Yii::t('UserModule.login', 'Restoring Password at Tilchi.com');

                            $message->setBody(array(
                                'name'=>$user->first_name,
                                'url'=>Yii::app()->createAbsoluteUrl('user/signin/resetPassword/' . $userPasswordRestore->token),
                                'ip'=>$ip,
                            ), 'text/html');

                            $message->setFrom('noreply@tilchi.com', 'Tilchi.com');
                            $message->setTo($user->email, $user->first_name . ' ' . $user->last_name);

                            if (Yii::app()->mail->send($message))
                            {
                                $template = 'restorePasswordConfirmation';
                            }
                            // failed to send an email, ask to try again later. Also delete from the database.
                            else{
                                $userPasswordRestore->delete();
                                $model->addError('email', Yii::t('UserModule.login', 'Sorry, we could not process your request. Please try again later.'));
                            }
                        }
                        else{
                            // failed to save, ask to try again later
                            $model->addError('email', Yii::t('UserModule.login', 'Sorry, we could not process your request. Please try again later.'));
                        }
                    }
                }
                else{
                    $model->addError('email', Yii::t('UserModule.login', 'No user has been found with this email address.'));
                }
            }
		}
        
        $this->render($template, array('model'=>$model));
    }

    public function actionResetPassword($param)
    {
        $model = new User('restore-password');

        // if it is ajax validation request
        if(isset($_POST['ajax']))
        {
            if ($_POST['ajax']==='restore-password-form')
            {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }
            elseif ($_POST['ajax']==='reset-password-form')
            {
                $model->scenario = 'resetPassword';
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }
        }

        $template = 'resetPasswordEnterEmail';

        // collect user input data
        if(isset($_POST['User']))
        {
            $model->attributes = $_POST['User'];

            if($model->validate(array('email')))
            {
                $user = User::model()->with('userPasswordRestore')->find(
                    'email = :email AND userPasswordRestore.reset_date IS NULL'
                , array(
                   ':email'=>$model->email
                ));

                if ($user)
                {
                    if ($user->userPasswordRestore)
                    {
                        // since User <-> UserPasswordRestore relationship is HAS_MANY,
                        // this query will return an array. This is why we need to
                        // get the first element.
                        $user->userPasswordRestore = $user->userPasswordRestore[0];
                        // if user has already reset the password using this token,
                        // don't allow to use this token any more. Suggest to request password reset again.
                        if ($user->userPasswordRestore->reset_date)
                        {
                            $model->addError('email', Yii::t('UserModule.login', 'Your password has already been reset with this URL. If you haven\'t reset it yourself, request another <a href="/user/signin/restorePassword">password reset</a> or cancel.'));
                        }
                        else{
                            $parts	= explode( ':', $user->userPasswordRestore->hash_key);
                            $crypt	= $parts[0];

                            if (!isset($parts[1]))
                            {
                                $model->addError('email', Yii::t('UserModule.login', 'No user has been found with this email address.'));
                            }
                            else{
                                $salt	= $parts[1];
                                $testcrypt = User::getCryptedPassword($param, $salt);

                                // Verify the token
                                if (!($crypt == $testcrypt))
                                {
                                    $model->addError('email', Yii::t('UserModule.login', 'No user has been found with this email address.'));
                                }
                                else{
                                    $model->scenario = 'resetPassword';
                                    $template = 'resetPassword';

                                    if (isset($_POST['reset']))
                                    {
                                        if ($model->validate())
                                        {
                                            $userPasswordRestore = UserPasswordRestore::model()->findByAttributes(array(
                                                'user_id'=>$user->id,
                                                'hash_key'=>$user->userPasswordRestore->hash_key
                                            ));

                                            if ($userPasswordRestore)
                                            {
                                                $userPasswordRestore->reset_date = time();
                                                $ip = Yii::app()->getRequest()->getUserHostAddress();

                                                if ($ip)
                                                {
                                                    $userPasswordRestore->reset_ip = ip2long($ip);
                                                }

                                                if ($userPasswordRestore->save())
                                                {
                                                    $user->scenario = 'changeUserPassword';
                                                    $user->passwordNew = $model->passwordNew;

                                                    if ($user->save())
                                                    {
                                                        $template = 'resetPasswordConfirmation';
                                                    }
                                                    else{
                                                        $model->addError('passwordNew', Yii::t('UserModule.login', 'Sorry, we could not process your request. Please try again later.'));
                                                    }
                                                }
                                                else{
                                                    $model->addError('passwordNew', Yii::t('UserModule.login', 'Sorry, we could not process your request. Please try again later.'));
                                                }
                                            }
                                            else{
                                                $model->addError('passwordNew', Yii::t('UserModule.login', 'Sorry, we could not process your request. Please try again later.'));
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // user hasn't requested any password request before
                    else{
                        $model->addError('email', Yii::t('UserModule.login', 'No user has been found with this email address.'));
                    }
                }
                else{
                    $model->addError('email', Yii::t('UserModule.login', 'No user has been found with this email address.'));
                }
            }
        }

        $this->render($template, array('model'=>$model));
    }
}