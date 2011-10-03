<?php

/**
 * This is the model class for table "{{translation_comments}}".
 *
 * The followings are the available columns in table '{{translation_comments}}':
 * @property string $id
 * @property string $translation_id
 * @property string $content
 * @property string $user_id
 * @property string $comment_date
 *
 * The followings are the available model relations:
 * @property PhraseTranslations $translation
 * @property Users $user
 */
class TranslationComment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return TranslationComment the static model class
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
		return '{{translation_comments}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('translation_id, content', 'required'),
			array('translation_id', 'numerical', 'integerOnly'=>true),
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
			'translation' => array(self::BELONGS_TO, 'PhraseTranslation', 'translation_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'translation_id' => 'Translation',
			'content' => 'Content',
			'user_id' => 'User',
			'comment_date' => 'Comment Date',
		);
	}
}