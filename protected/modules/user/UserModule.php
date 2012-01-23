<?php

class UserModule extends CWebModule
{
    public $defaultController = 'Profile';
    public $assets;

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'user.models.*',
			'user.components.*',
		));

		$this->assets = Yii::app()->assetManager
            ->publish(Yii::getPathOfAlias('application.modules.user.assets'), false, -1, true);

        Yii::app()->getClientScript()
            ->registerCssFile($this->assets . '/css/main.css')
            ->registerScriptFile($this->assets . '/js/main.js');
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
    public static function getFormattedRelativeDate($date)
    {
        $diff = time() - $date;

        if ($diff < 60){
            return Yii::t('UserModule.user', 'one second ago|{n} seconds ago', $diff);
        }

        $diff /= 60;

        if ($diff < 60){
            return Yii::t('UserModule.user', 'one minute ago|{n} minutes ago', floor($diff));
        }

        $diff /= 60;

        if ($diff < 24){
            return Yii::t('UserModule.user', 'an hour ago|{n} hours ago', floor($diff));
        }

        $diff /= 24;

        if ($diff < 7){
            return Yii::t('UserModule.user', 'yesterday|{n} days ago', floor($diff));
        }

        if ($diff < 30){
            return Yii::t('UserModule.user', 'last week|{n} weeks ago', floor($diff / 7));
        }

        $diff /= 30;

        if ($diff < 12){
            return Yii::t('UserModule.user', 'last month|{n} months ago', floor($diff));
        }

        $diff /= 12;

        return Yii::t('UserModule.user', 'last year|{n} years ago', floor($diff));
    }
}
