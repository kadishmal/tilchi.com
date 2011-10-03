<?php

/**
 * This is the model class for table "{{translation_votes}}".
 *
 * The followings are the available columns in table '{{translation_votes}}':
 * @property string $id
 * @property string $user_id
 * @property string $translation_id
 * @property integer $vote_status
 *
 * The followings are the available model relations:
 * @property Users $user
 * @property PhraseTranslations $translation
 */
class TranslationVote extends CActiveRecord
{
	const VOTE_UP = 1;
	const VOTE_DOWN = -1;
	/**
	 * Returns the static model of the specified AR class.
	 * @return TranslationVote the static model class
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
		return '{{translation_votes}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('translation_id, vote_status', 'required'),
			array('translation_id', 'numerical', 'integerOnly'=>true),
			array('vote_status', 'in', 'range'=>array(self::VOTE_UP, self::VOTE_DOWN)),
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
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
			'translation' => array(self::BELONGS_TO, 'PhraseTranslation', 'translation_id'),
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
			'translation_id' => 'Translation',
			'vote_status' => 'Vote Status',
		);
	}
}