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
				'actions'=>array('index'),
				'users'=>array('?'),
			),
            array('allow', // allow any user to login
				'actions'=>array('logout'),
				'users'=>array('@'),
			),
            array('deny', // do now allow logged in users to login
				'actions'=>array('index'),
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
                    $this->redirect($app->user->returnUrl);
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
}