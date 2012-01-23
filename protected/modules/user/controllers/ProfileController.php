<?php

class ProfileController extends Controller
{
	const REQUEST_SUCCESS = 1;
	const REQUEST_FAIL = -1;
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
				'actions'=>array('index', 'setSettings'),
				'roles'=>array('member'),
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
        // get a list of profile settings this user has permission to alter
        $userSettingsList = SiteSettings::model()->findAll(array(
            'condition'=>'module LIKE :module',
            'params'=>array(':module'=>'user.profile%'),
            'order'=>'module, name'
        ));

        $userAccessibleSettings = array();
        // check if this user has access to each of these settings
        foreach($userSettingsList as $setting)
        {
            if(Yii::app()->user->checkAccess($setting->auth_item))
            {
                $userAccessibleSettings[$setting->id] = $setting;
            }
        }

        $model = User::model()->with('settings')->findByPk(Yii::app()->user->id);

        // retrieve user's own preferences set perviously for some of these settings
        foreach($model->settings as $setting)
        {
            $userAccessibleSettings[$setting->setting_id]->default_value = $setting->value;
        }

        $this->render('index', array(
            'model'=>$model,
            'userAccessibleSettings'=>$userAccessibleSettings
        ));
	}

	public function actionSetSettings()
	{
		$results = array();
        $results['status'] = self::REQUEST_FAIL;

		if(isset($_POST['User']))
		{
            $target = $_POST['User']['t'];
			$value = $_POST['User']['v'];
            $user_id = Yii::app()->user->id;

            $setting = SiteSettings::model()->find(
                'module LIKE :module AND name = :name',
                array(
                    ':module'=>$this->module->id . '.' . $this->id . '%',
                    ':name'=>$target
                )
            );

            if ($setting && Yii::app()->user->checkAccess($setting->auth_item))
            {
                $userSetting = UserSettings::model()->findByAttributes(array(
                    'user_id'=>$user_id,
                    'setting_id'=>$setting->id
                ));

                if (!$userSetting)
                {
                    $userSetting = new UserSettings;
                    $userSetting->user_id = $user_id;
                    $userSetting->setting_id = $setting->id;
                }

                if ($setting->data_type == SiteSettings::TYPE_CHECKBOX)
                {
                    $value = ($value == 'true' ? 1 : 0);
                }

                $userSetting->value = $value;

                if ($userSetting->save())
                {
                    $results['status'] = self::REQUEST_SUCCESS;
                    Yii::app()->user->setState($setting->name, $value);
                }
            }
		}

		echo CJSON::encode($results);
	}
}