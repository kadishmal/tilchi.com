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
}
