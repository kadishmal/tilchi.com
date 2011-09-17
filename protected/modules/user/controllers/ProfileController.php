<?php

class ProfileController extends Controller
{
	const SUBSCR_STATUS_SUCCESS = 1;
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
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index', 'subscribe'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	/**
	 * Displays a user profile
	 */
	public function actionIndex()
	{
		$model = $this->loadModel(Yii::app()->user->id);
		$this->render('index', array('model'=>$model));
	}
	public function actionSubscribe()
	{
		$results = array();

		if(isset($_POST['User']))
		{
            $target = $_POST['User']['target'];
			$value = $_POST['User']['v'];

			if ($target == 'post_comments')
			{
				$model = $this->loadModel(Yii::app()->user->id);
				$model->subsсr_post_comments = ($value == 'true' ? true : false);

				if ($model->save())
				{
					$results['status'] = self::SUBSCR_STATUS_SUCCESS;
				}
			}
		}

		echo CJSON::encode($results);
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
}