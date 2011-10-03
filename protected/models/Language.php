<?php

/**
 * This is the model class for table "{{languages}}".
 *
 * The followings are the available columns in table '{{languages}}':
 * @property string $id
 * @property string $abbreviation
 * @property string $en
 * @property string $ru
 * @property string $ky
 * @property string $ko
 *
 * The followings are the available model relations:
 * @property Phrases[] $phrases
 * @property Translations[] $translations
 */
class Language extends CActiveRecord
{
	const LANGUAGE_ALL = 0;
	/**
	 * Returns the static model of the specified AR class.
	 * @return Language the static model class
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
		return '{{languages}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('en', 'required'),
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
			'phrases' => array(self::HAS_MANY, 'Phrase', 'language_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'language' => 'Language',
			'ru' => 'Ru',
			'ky' => 'Ky',
		);
	}
}