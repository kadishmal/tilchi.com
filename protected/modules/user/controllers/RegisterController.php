<?php

class RegisterController extends Controller
{
    const EXCHANGE_REGISTERED_USERS = 'tilchi.exchange.registered.users';
    const QUEUE_REGISTERED_USERS = 'tilchi.queue.registered.users';

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
            array('allow',
				// Allow any user to execute register action even the
				// registered user as the registration process consists of
				// several steps.
				'actions'=>array('index'),
				'users'=>array('*'),
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

			if (Yii::app()->user->isGuest){
				$this->render('register', array('model'=>$model));
			}
        }
        else{
            $model = $this->loadModel(Yii::app()->user->id);
            $model->scenario = 'register-info';

            // Enable ajax-based validation
            $this->performAjaxValidation($model, 'info-form');

            if(isset($_POST['User']))
            {
                $model->attributes = $_POST['User'];

                if($model->save())
                {
                    $amqp = Yii::app()->amqp;
                    $amqp->declareExchange(RegisterController::EXCHANGE_REGISTERED_USERS, AMQP_EX_TYPE_DIRECT, AMQP_DURABLE);

                    $ex = $amqp->exchange(RegisterController::EXCHANGE_REGISTERED_USERS);
                    $amqp->declareQueue(RegisterController::QUEUE_REGISTERED_USERS, AMQP_DURABLE);
                    $queue = $amqp->queue(RegisterController::QUEUE_REGISTERED_USERS);
                    $queue->bind(RegisterController::EXCHANGE_REGISTERED_USERS, RegisterController::QUEUE_REGISTERED_USERS);

                    $ex->publish($model->id, RegisterController::QUEUE_REGISTERED_USERS, AMQP_MANDATORY);

                    $this->redirect('/user');
                }
            }

            if ( ! $model->validate())
            {
                $this->render('addInfo', array('model'=>$model));
            }
            else{
                $this->redirect('/user');
            }
        }
	}
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model = User::model()->findByPk((int)$id);

        if($model === null)
			throw new CHttpException(404, 'The requested page does not exist.');

		return $model;
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