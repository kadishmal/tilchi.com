<?php

class ProfileController extends Controller
{
	const SETTINGS_SET_STATUS_SUCCESS = 1;
	const SETTINGS_SET_STATUS_FAIL = -1;
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
				'actions'=>array('index', 'setSettings'),
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
		$model = User::model()->with('settings')->findByPk(Yii::app()->user->id);

        if (!$model->settings)
        {
            $userSettings = new UserSettings;
            $userSettings->user_id = $model->id;

            if (!$userSettings->save())
            {
                throw new CHttpException(404, Yii::t('UserModule.profile',
                    'Could not process your request. Please try again later. If this happens regularly, please <a href="/forum/new/issue">report this issue</a>.'));
            }

            $model->settings = $userSettings;
        }

        $this->render('index', array('model'=>$model));
	}

	public function actionSetSettings()
	{
		$results = array();
        $results['status'] = self::SETTINGS_SET_STATUS_FAIL;

		if(isset($_POST['User']))
		{
            $target = $_POST['User']['t'];
			$value = $_POST['User']['v'];

            $model = UserSettings::model()->findByAttributes(array(
                'user_id'=>Yii::app()->user->id
            ));

            if ($model && $model->hasAttribute($target))
            {
                $model->$target = ($value == 'true' ? true : false);

                if ($model->save())
                {
                    $results['status'] = self::SETTINGS_SET_STATUS_SUCCESS;
                    Yii::app()->user->setState($target, $model->$target);
                }
            }
		}

		echo CJSON::encode($results);
	}
}