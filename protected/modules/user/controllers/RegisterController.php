<?php

class RegisterController extends Controller
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
			array('deny',  // deny all users all other actions
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays the registration page and register a new user.
     * It registers only email and password, then redirects to /user/info
     * to ask user to add personal information.
	 */
	public function actionIndex()
	{
        if (Yii::app()->user->isGuest)
        {
            $model = new User('register-email');

            // Enable ajax-based validation
            $this->performAjaxValidation($model, 'registration-form');

            if(isset($_POST['User']))
            {
                $model->attributes = $_POST['User'];

                if($model->validate())
                {
                    $tempPass = $model->password;
                    $model->join_date = time();
                    $model->cryptPassword();

                    if ($model->save(false))
                    {
                        $loginModel = new LoginForm;
                        $loginModel->email = $model->email;
                        $loginModel->password = $tempPass;
                        // validate user credentials and try to login.
                        // If success, redirect to the /user/info page to
                        // continue the registration process.
                        if($loginModel->validate() && $loginModel->login())
                        {
                            $this->render('addInfo', array('model'=>$model));
                        }
                    }
                }
            }

            $this->render('register', array('model'=>$model));
        }
        else{
            $model = $this->loadModel(Yii::app()->user->id);
            $model->scenario = 'register-info';

            if(isset($_POST['User']))
            {
                $model->attributes = $_POST['User'];

                if($model->save())
                {
                    // /index refers to /index of this model, i.e. /user/index
                    $this->redirect('index');
                }
            }

            if ( ! $model->validate())
            {
                $this->render('addInfo', array('model'=>$model));
            }
            else{
                // /index refers to /index of this model, i.e. /user/index
                $this->redirect('index');
            }
        }
	}
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model, $form = 'registration-form')
	{
		if(isset($_POST['ajax']) && $_POST['ajax'] === $form)
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}