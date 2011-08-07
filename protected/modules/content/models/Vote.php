<?php

/**
 * This is the model class for table "{{votes}}".
 *
 * The followings are the available columns in table '{{votes}}':
 * @property string $id
 * @property string $user_id
 * @property string $post_id
 *
 * The followings are the available model relations:
 * @property Users $user
 * @property Posts $post
 */
class Vote extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Votes the static model class
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
		return '{{votes}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, post_id', 'required'),
            array('post_id', 'numerical', 'integerOnly'=>true),
            array('post_id', 'numerical', 'min'=>1),
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
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'post' => array(self::BELONGS_TO, 'Post', 'post_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'post_id' => 'Post',
		);
	}
    // Scope
    public function my()
	{
		$criteria = $this->getDbCriteria();

        $criteria->mergeWith(array(
            'condition'=>'user_id = :user_id',
            'params'=>array(':user_id'=>Yii::app()->user->id)
        ));

		return $this;
	}
}