<?php

/**
 * This is the model class for table "{{comments}}".
 *
 * The followings are the available columns in table '{{comments}}':
 * @property string $id
 * @property string $post_id
 * @property string $author
 * @property string $email
 * @property string $url
 * @property string $date
 * @property string $status
 * @property string $user_id
 * @property string $parent_id
 *
 * The followings are the available model relations:
 * @property Posts $post
 */
class Comment extends CActiveRecord
{
	const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_SPAM = 2;
    const STATUS_TRASH = 3;
	/**
	 * Returns the static model of the specified AR class.
	 * @return Comment the static model class
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
		return '{{comments}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('post_id, content, parent_id', 'required'),
			array('post_id, parent_id', 'numerical', 'integerOnly'=>true),
			array('post_id', 'numerical', 'min'=>1),
            array('post_id', 'exist', 'attributeName'=>'id', 'className'=>'Post'),
            array('parent_id', 'exist', 'attributeName'=>'id'),
			array('content', 'safe'),
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
			'post'=>array(self::BELONGS_TO, 'Post', 'post_id'),
			'user'=>array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'post_id' => 'Post',
			'author' => 'Author',
			'email' => 'Email',
			'url' => 'Url',
			'date' => 'Date',
			'status' => 'Status',
			'user_id' => 'User',
			'parent_id' => 'Parent',
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