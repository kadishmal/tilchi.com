<?php
/**
 * This is the model class for table "tbl_users".
 *
 * The followings are the available columns in table 'tbl_users':
 * @property integer $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 *
 * The followings are the available model relations:
 * @property
 */
class SiteSettings extends CActiveRecord
{
    const TYPE_CHECKBOX = 0;
    const TYPE_NUMBER = 1;
    const TYPE_TEXT = 2;

    const YES = 1;
    const NO = 0;

	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{site_settings}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('name, module, default_value, en_label, en_hint, auth_item', 'required'),
            array('data_type', 'in', 'range'=>array(self::TYPE_CHECKBOX, self::TYPE_NUMBER, self::TYPE_TEXT)),
            array('name', 'checkUnique', 'allowEmpty'=>false),
            array('auth_item', 'exist', 'attributeName'=>'name', 'className'=>'PermissionItem', 'message'=>Yii::t('UserModule.settings', 'Such Permission does not exist.')),
            array('id', 'numerical'),
            array('id', 'exist'),
            array('on_login', 'boolean')
        );
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
//            'authItem'=>array(self::BELONGS_TO, 'CAuthItem', 'auth_item')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'name'=>Yii::t('UserModule.settings', 'Name'),
			'module'=>Yii::t('UserModule.settings', 'Module'),
			'data_type'=>Yii::t('UserModule.settings', 'Type'),
			'default_value'=>Yii::t('UserModule.settings', 'Default'),
			'en_label'=>Yii::t('UserModule.settings', 'Label'),
			'en_hint'=>Yii::t('UserModule.settings', 'Hint'),
			'auth_item'=>Yii::t('UserModule.settings', 'Permission'),
		);
	}

    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('name', $this->name, true);
        $criteria->compare('module', $this->module, true);
        $criteria->compare('auth_item', $this->auth_item, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function checkUnique($attributes, $params)
    {
        $existingSetting = SiteSettings::model()->find('name = :name AND module = :module', array(
            ':name'=>$this->name,
            ':module'=>$this->module
        ));

        if ($existingSetting && $existingSetting->id != $this->id)
        {
            $this->addError('name', Yii::t('UserModule.settings',
                'The <b>_setting</b> already exists in the <b>_module</b> module.',
                array('_setting'=>$this->name, '_module'=>$this->module)
            ));
        }
    }

    public static function getCSSClass($type)
    {
        switch($type)
        {
            case self::TYPE_NUMBER: case self::TYPE_TEXT: return 'input';
            case self::TYPE_CHECKBOX: return 'checkbox';
        }
    }
}