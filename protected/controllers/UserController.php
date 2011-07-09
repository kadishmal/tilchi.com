<?php

class UserController extends Controller
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
            array('allow', // allow guest users to register
				'actions'=>array('register'),
				'users'=>array('?'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('info', 'index', 'edit'),
				'users'=>array('@'),
			),
            array('deny', // do now allow logged in users to register
				'actions'=>array('register'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete', 'view', 'create', 'editUser'),
				'users'=>array(Yii::app()->params['adminEmail']),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	/**
	 * Displays the registration page and register a new user.
     * It registers only email and password, then redirects to /user/info
     * to ask user to add personal information.
	 */
	public function actionRegister()
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

                if ($model->save(false)){
                    $loginModel = new LoginForm;
                    $loginModel->email = $model->email;
                    $loginModel->password = $tempPass;
                    // validate user credentials and try to login.
                    // If success, redirect to the /user/info page to
                    // continue the registration process.
                    if($loginModel->validate() && $loginModel->login())
                        $this->redirect('/site');
                }
            }
        }

        $this->render('register', array('model'=>$model));
	}
    /**
	 * Displays the second phase of the registration process which prompts
     * users to enter their personal information.
	 */
	public function actionInfo()
	{
        $model = $this->loadModel(Yii::app()->user->id);
        $model->scenario = 'register-name';

        // Enable ajax-based validation
        $this->performAjaxValidation($model, 'info-form');

        if(isset($_POST['User']))
        {
            $model->attributes = $_POST['User'];

            if($model->validate())
            {
                echo 'ok';
                Yii::app()->end();
//                if($model->save()){
//                    $modelUserAddress = new UserAddress;
//                    $modelUserAddress->parent_user_id = Yii::app()->user->id;
//                    $modelUserAddress->child_user_id = $model->id;
//
//                    if ($modelUserAddress->save())
//                        $this->redirect(array('index'));
//                }
            }
                print_r($model->getErrors());
                Yii::app()->end();
        }

        $this->render('addInfo', array('model'=>$model));
	}
	/**
	 * Displays a user profile
	 */
	public function actionIndex()
	{
		$this->render('index', array(
			'model'=>$this->loadModel(Yii::app()->user->id),
		));
	}
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionEdit($c)
	{
		$model=$this->loadModel(Yii::app()->user->id);

		if ($c == 'pwd'){
			$model->scenario = 'changePassword';
		}
		else{
			$model->scenario = 'edit';
		}

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save())
				$this->redirect(array('index'));
		}

		$this->render($model->scenario,array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionEditUser($c)
	{
		$id = (isset($_GET['id']) ? $_GET['id'] : 'null');

		if (is_numeric($id)){
			if ($id == Yii::app()->user->id)
				$this->redirect(array('edit', 'c'=>'profile'));

			$model=$this->loadModel($id);
		}
		else
			$this->redirect(array('view'));

		if ($c == 'pwd'){
			$model->scenario = 'changeUserPassword';
		}
		else{
			$model->scenario = 'editUser';
		}

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save())
				$this->redirect(array('view'));
		}

		$this->render($model->scenario,array(
			'model'=>$model,
		));
	}
	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionView()
	{
		$dataProvider=new CActiveDataProvider('User');
		$this->render('view',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];

		$this->render('admin',array(
			'model'=>$model,
		));
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
	protected function performAjaxValidation($model, $form = 'user-form')
	{
		if(isset($_POST['ajax']) && $_POST['ajax'] === $form)
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
