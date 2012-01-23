<?php

class ManageController extends Controller
{
    const PERMISSION_NEW_SUCCESS = 1;
    const PERMISSION_NEW_FAIL = -1;
    const PERMISSION_NEW_NO_NAME = -2;
    const PERMISSION_NEW_NAME_EXISTS = -3;

    const OPERATION = 0;
    const TASK = 1;
    const ROLE = 2;

    const ROLE_INFO_SUCCESS = 1;
    const ROLE_INFO_FAIL = -1;
    const REQUEST_SUCCESS = 1;
    const REQUEST_FAIL = -1;

    const SETTINGS_PER_PAGE = 10;

    public function init()
    {
        Yii::app()->getClientScript()->registerScriptFile($this->module->assets . '/js/admin.js');
    }
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
				'actions'=>array('index', 'permissions', 'getRoleData',
                                'getAuthItemUsers', 'assignUser', 'revokeUser',
                                'getDescendants', 'addChild', 'removeChild'
                ),
				'roles'=>array('superAdmin'),
			),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions'=>array('settings', 'getSiteSettingInfo', 'deleteSiteSetting'
                ),
                'roles'=>array('admin'),
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
        $auth = Yii::app()->authManager;

//        $auth->createOperation('translate', 'translate words');
//        $auth->createOperation('addTranslation', 'add translation');

//        $bizRule='return !Yii::app()->user->isGuest;';
//        $auth->createRole('regularUser', 'regular authenticated user', $bizRule);
//
//        $bizRule='return Yii::app()->user->isGuest;';
//        $auth->createRole('guest', 'guest user', $bizRule);

//        foreach($auth->roles as $role)
//        {
//            if ($role->name == 'guest')
//            {
//                $guestUserRole = $role;
//            }
//            elseif($role->name == 'regularUser')
//            {
//                $regularUser = $role;
//            }
//        }
//
//        $guestUserRole->addChild('translate');
//        $regularUser->addChild('guest');

        $superAdmin = $auth->getAuthItem('superAdmin');

        if ($superAdmin)
        {
//            $auth->assign($superAdmin->name, Yii::app()->user->id);
//            $au
        }

		$this->render('index');
	}

    public function actionPermissions()
    {
        $auth = Yii::app()->authManager;

        // if it is ajax validation request
        if(isset($_POST['ajax']) && $_POST['ajax']==='permission-form')
        {
            $results = array();
            $results['status'] = self::PERMISSION_NEW_FAIL;
            $results['message'] = Yii::t('UserModule.permission', 'We could not process your request. Please try again later.');

            if(isset($_POST['User']))
            {
                $type = array_keys($_POST['User']);
                $type = $type[0];
                $name = array_keys($_POST['User'][$type]);
                $field = array_keys($_POST['User'][$type][$name[0]]);
                $field = $field[0];
                $value = $_POST['User'][$type][$name[0]][$field];

                $authItem = $auth->getAuthItem($name[0]);

                if ($authItem)
                {
                    if ($field == 'name' && $auth->getAuthItem($value))
                    {
                        $results['status'] = self::PERMISSION_NEW_NAME_EXISTS;
                        $results['message'] = Yii::t('UserModule.permission', 'The ' . $type . ' with this name already exists.');
                    }
                    else{
                        try{
                            $authItem->$field = $value;
                            $results['status'] = self::PERMISSION_NEW_SUCCESS;
                        }
                        catch (Exception $e)
                        {

                        }
                    }

                }
                else{
                    if ($field != 'name')
                    {
                        $results['status'] = self::PERMISSION_NEW_NO_NAME;
                        $results['message'] = Yii::t('UserModule.permission', 'The ' . $type . ' name should be defined first.');
                    }
                    elseif (in_array($type, array(self::OPERATION, self::TASK, self::ROLE)))
                    {
                        $auth->createAuthItem($value, $type);
                        $results['status'] = self::PERMISSION_NEW_SUCCESS;
                    }
                }
            }

            echo CJSON::encode($results);
        }
        else{
            $this->render('permissions', array(
                'operations'=>$auth->operations,
                'tasks'=>$auth->tasks,
                'roles'=>$auth->roles
            ));
        }
    }

    public function actionGetRoleData()
    {
        $results = array();
        $results['status'] = self::ROLE_INFO_FAIL;
        $results['message'] = Yii::t('UserModule.permission', 'We could not process your request. Please try again later.');
        $results['ok'] = Yii::t('UserModule.permission', 'Close');

        if(isset($_POST['Role']))
        {
            $auth = Yii::app()->authManager;
            $role = $auth->getAuthItem($_POST['Role']['name']);

            if ($role)
            {
                $results['descendantCount'] = count($auth->getItemChildren($role->name));

                $results['status'] = self::ROLE_INFO_SUCCESS;
                $results['messages'] = array(
                    'descendants'=>Yii::t('UserModule.permission', 'Descendants'),
                    'ancestors'=>Yii::t('UserModule.permission', 'Ancestors'),
                    'users'=>Yii::t('UserModule.permission', 'Users'),
                    'filterByEmail'=>Yii::t('UserModule.permission', 'Filter by email'),
                    'filterByName'=>Yii::t('UserModule.permission', 'Filter by permission name'),
                );
            }
        }

        echo CJSON::encode($results);
    }

    public function actionGetAuthItemUsers()
    {
        $results = array();
        $results['status'] = self::REQUEST_FAIL;
        $results['message'] = Yii::t('UserModule.permission', 'We could not process your request. Please try again later.');

        if(isset($_POST['Auth']))
        {
            $auth = Yii::app()->authManager;
            $authItemName = $_POST['Auth']['name'];
            $authItem = $auth->getAuthItem($authItemName);

            if ($authItem)
            {
                $condition = 'name = :name';
                $params = array(':name'=>$authItemName);
                $email = $_POST['Auth']['email'];

                if ($email)
                {
                    $condition .= ' AND users.email LIKE :email';
                    $params[':email'] = '%' . $email . '%';
                }

                $permissionItems = PermissionItem::model()->with('users')->find($condition, $params);

                $results['userCount'] = 0;
                $results['users'] = array();

                if ($permissionItems)
                {
                    $results['userCount'] = count($permissionItems->users);

                    foreach($permissionItems->users as $user)
                    {
                        $results['users'][] = $user->email;
                    }

                    $results['messages'] = array(
                        'revoke'=>Yii::t('UserModule.permission', 'Revoke')
                    );
                }
                // such user is not part of this AuthItem
                else if ($email){
                    // check if this user exists at all
                    $results['userExists'] = User::model()->exists('email = :email', array(':email'=>$email));

                    if ($results['userExists'])
                    {
                        $results['messages'] = array(
                            'noPermission'=>Yii::t('UserModule.permission', 'This user is not assigned to this permission item. Would you like to assign <b>_permission_</b> to <b>_email_</b> user?', array('_permission_'=>$authItemName, '_email_'=>$email)),
                            'assign'=>Yii::t('UserModule.permission', 'Assign')
                        );
                    }
                }

                $results['status'] = self::REQUEST_SUCCESS;
            }
            else{
                $results['message'] = Yii::t('UserModule.permission', 'Auth item <b>item_name</b> does not exist.', array('item_name'=>$authItemName));
            }
        }

        echo CJSON::encode($results);
    }

    public function actionAssignUser()
    {
        $results = array();
        $results['status'] = self::REQUEST_FAIL;
        $results['message'] = Yii::t('UserModule.permission', 'We could not process your request. Please try again later.');

        if(isset($_POST['Auth']))
        {
            $auth = Yii::app()->authManager;
            $authItemName = $_POST['Auth']['name'];
            $authItem = $auth->getAuthItem($authItemName);

            if ($authItem)
            {
                $email = $_POST['Auth']['email'];

                if ($email)
                {
                    $condition = 'name = :name AND users.email LIKE :email';
                    $params = array(
                        ':name'=>$authItemName,
                        ':email'=>'%' . $email . '%'
                    );

                    $permissionItems = PermissionItem::model()->with('users')->find($condition, $params);
                    // if user already has this auth item, return success
                    if ($permissionItems)
                    {
                        $results['status'] = self::REQUEST_SUCCESS;
                    }
                    else{
                        // otherwise, check if user exists
                        $user = User::model()->find('email = :email', array(':email'=>$email));

                        if ($user)
                        {
                            if ($auth->assign($authItemName, $user->id))
                            {
                                $results['status'] = self::REQUEST_SUCCESS;
                            }
                        }
                    }
                }
            }
            else{
                $results['message'] = Yii::t('UserModule.permission', 'Auth item <b>item_name</b> does not exist.', array('item_name'=>$authItemName));
            }
        }

        echo CJSON::encode($results);
    }

    public function actionRevokeUser()
    {
        $results = array();
        $results['status'] = self::REQUEST_FAIL;
        $results['message'] = Yii::t('UserModule.permission', 'We could not process your request. Please try again later.');

        if(isset($_POST['Auth']))
        {
            $auth = Yii::app()->authManager;
            $authItemName = $_POST['Auth']['name'];
            $authItem = $auth->getAuthItem($authItemName);

            if ($authItem)
            {
                $email = $_POST['Auth']['email'];

                if ($email)
                {
                    $condition = 'name = :name AND users.email LIKE :email';
                    $params = array(
                        ':name'=>$authItemName,
                        ':email'=>'%' . $email . '%'
                    );

                    $permissionItems = PermissionItem::model()->with('users')->find($condition, $params);

                    if ($permissionItems)
                    {
                        $user = User::model()->find('email = :email', array(':email'=>$email));

                        if ($user)
                        {
                            if ($auth->revoke($authItemName, $user->id))
                            {
                                $results['status'] = self::REQUEST_SUCCESS;
                            }
                        }
                    }
                    // if this user doesn't belong to this auth item, return success
                    else{
                        $results['status'] = self::REQUEST_SUCCESS;
                    }
                }
            }
            else{
                $results['message'] = Yii::t('UserModule.permission', 'Auth item <b>item_name</b> does not exist.', array('item_name'=>$authItemName));
            }
        }

        echo CJSON::encode($results);
    }

    public function actionGetDescendants()
    {
        $results = array();
        $results['status'] = self::REQUEST_FAIL;
        $results['message'] = Yii::t('UserModule.permission', 'We could not process your request. Please try again later.');

        if(isset($_POST['Auth']))
        {
            $auth = Yii::app()->authManager;
            $authItemName = $_POST['Auth']['name'];
            $authItem = $auth->getAuthItem($authItemName);

            if ($authItem)
            {
                $condition = 't.name = :name';
                $params = array(':name'=>$authItemName);
                $desName = $_POST['Auth']['desName'];

                if ($desName)
                {
                    $condition .= ' AND children.name LIKE :desName';
                    $params[':desName'] = '%' . $desName . '%';
                }

                $permissionItems = PermissionItem::model()->with('children')->find($condition, $params);

                $results['descendantCount'] = 0;
                $results['descendants'] = array();

                if ($permissionItems)
                {
                    $results['descendantCount'] = count($permissionItems->children);

                    foreach($permissionItems->children as $child)
                    {
                        $results['descendants'][] = $child->name;
                    }

                    $results['messages'] = array(
                        'remove'=>Yii::t('UserModule.permission', 'Remove')
                    );
                }
                // such user is not part of this AuthItem
                else if ($desName){
                    // check if this user exists at all
                    $results['desExists'] = PermissionItem::model()->exists('name = :name', array(
                        ':name'=>$desName
                    ));

                    if ($results['desExists'])
                    {
                        $results['messages'] = array(
                            'noPermission'=>Yii::t('UserModule.permission', 'The <b>_child_</a> permission item is not child of <b>_parent_</b> permission. Would you like to add <b>_child_</b> as a child to <b>_parent_</b> permission?', array('_parent_'=>$authItemName, '_child_'=>$desName)),
                            'addChild'=>Yii::t('UserModule.permission', 'Add Child')
                        );
                    }
                }

                $results['status'] = self::REQUEST_SUCCESS;
            }
            else{
                $results['message'] = Yii::t('UserModule.permission', 'Auth item <b>item_name</b> does not exist.', array('item_name'=>$authItemName));
            }
        }

        echo CJSON::encode($results);
    }


    public function actionAddChild()
    {
        $results = array();
        $results['status'] = self::REQUEST_FAIL;
        $results['message'] = Yii::t('UserModule.permission', 'We could not process your request. Please try again later.');

        if(isset($_POST['Auth']))
        {
            $auth = Yii::app()->authManager;
            $authItemName = $_POST['Auth']['name'];
            $authItem = $auth->getAuthItem($authItemName);

            if ($authItem)
            {
                $desName = $_POST['Auth']['desName'];

                if ($desName)
                {
                    // if permission already has this auth item, return success
                    if ($authItem->hasChild($desName))
                    {
                        $results['status'] = self::REQUEST_SUCCESS;
                    }
                    else{
                        // otherwise, check if user exists
                        $descendant = PermissionItem::model()->exists('name = :name', array(
                            ':name'=>$desName
                        ));

                        if ($descendant)
                        {
                            if ($authItem->addChild($desName))
                            {
                                $results['status'] = self::REQUEST_SUCCESS;
                            }
                        }
                    }
                }
            }
            else{
                $results['message'] = Yii::t('UserModule.permission', 'Auth item <b>item_name</b> does not exist.', array('item_name'=>$authItemName));
            }
        }

        echo CJSON::encode($results);
    }

    public function actionRemoveChild()
    {
        $results = array();
        $results['status'] = self::REQUEST_FAIL;
        $results['message'] = Yii::t('UserModule.permission', 'We could not process your request. Please try again later.');

        if(isset($_POST['Auth']))
        {
            $auth = Yii::app()->authManager;
            $authItemName = $_POST['Auth']['name'];
            $authItem = $auth->getAuthItem($authItemName);

            if ($authItem)
            {
                $desName = $_POST['Auth']['desName'];

                if ($desName)
                {
                    if ($authItem->hasChild($desName))
                    {
                        if ($authItem->removeChild($desName))
                        {
                            $results['status'] = self::REQUEST_SUCCESS;
                        }
                    }
                    else{
                        $results['status'] = self::REQUEST_SUCCESS;
                    }
                }
            }
            else{
                $results['message'] = Yii::t('UserModule.permission', 'Auth item <b>item_name</b> does not exist.', array('item_name'=>$authItemName));
            }
        }

        echo CJSON::encode($results);
    }

    public function actionSettings()
    {
        $model = new SiteSettings;

        if(isset($_POST['ajax']) && $_POST['ajax'] === 'site-settings-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        if (isset($_POST['SiteSettings']))
        {
            $model->attributes = $_POST['SiteSettings'];

            if ($model->id && $model->validate(array('id')))
            {
                $model->isNewRecord = false;
            }

            if ($model->save())
            {
                $model->unsetAttributes();
            }
        }

        if (isset($_GET['ajax']) && $_GET['ajax'] === 'site-settings' && isset($_GET['SiteSettings']))
        {
            $model->attributes = $_GET['SiteSettings'];
        }

        $this->render('settings', array(
            'model'=>$model,
        ));
    }

    public function actionGetSiteSettingInfo()
    {
        $results = array();
        $results['status'] = self::REQUEST_FAIL;
        $results['message'] = Yii::t('UserModule.permission', 'We could not process your request. Please try again later.');

        if (isset($_POST['SiteSettings']))
        {
            $model = SiteSettings::model()->findByAttributes(array(
                'name'=>$_POST['SiteSettings']['name'],
                'module'=>$_POST['SiteSettings']['module'],
            ));

            if ($model)
            {
                $results['settingInfo']['id'] = $model->id;
                $results['settingInfo']['name'] = $model->name;
                $results['settingInfo']['module'] = $model->module;
                $results['settingInfo']['data_type'] = $model->data_type;
                $results['settingInfo']['default_value'] = $model->default_value;
                $results['settingInfo']['auth_item'] = $model->auth_item;
                $results['settingInfo']['en_label'] = $model->en_label;
                $results['settingInfo']['en_hint'] = $model->en_hint;
                $results['settingInfo']['on_login'] = $model->on_login;
                $results['status'] = self::REQUEST_SUCCESS;
            }
        }

        echo CJSON::encode($results);
    }

    public function actionDeleteSiteSetting()
    {
        $results = array();
        $results['status'] = self::REQUEST_FAIL;
        $results['message'] = Yii::t('UserModule.permission', 'We could not process your request. Please try again later.');

        if (isset($_POST['SiteSettings']))
        {
            $model = SiteSettings::model()->findByAttributes(array(
                'name'=>$_POST['SiteSettings']['name'],
                'module'=>$_POST['SiteSettings']['module'],
            ));

            if ($model)
            {
                if ($model->delete())
                {
                    $results['status'] = self::REQUEST_SUCCESS;
                }
                else{
                    $results['message'] = print_r($model->getErrors(), true);
                }
            }
        }

        echo CJSON::encode($results);
    }
}