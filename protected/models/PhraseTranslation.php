<?php

/**
 * This is the model class for table "{{phrase_translations}}".
 *
 * The followings are the available columns in table '{{phrase_translations}}':
 * @property string $id
 * @property string $user_id
 * @property string $phrase_id
 * @property string $translation_phrase_id
 * @property integer $translation_language_id
 * @property integer $date
 * @property string $explanation
 * @property string $status
 *
 * The followings are the available model relations:
 * @property Phrases $phrase
 * @property Phrases $translationPhrase
 * @property Users $user
 * @property TranslationComments[] $translationComments
 * @property TranslationVotes[] $translationVotes
 */
class PhraseTranslation extends CActiveRecord
{
	const STATUS_NEW_APPROVED = 0;
	const STATUS_NEW_PENDING = 1;
	// the minimum number of translations required to become a translator
	// which will have all next translations automatically approved
	const MIN_TRANSLATOR_COUNT = 5;
	/**
	 * Returns the static model of the specified AR class.
	 * @return PhraseTranslation the static model class
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
		return '{{phrase_translations}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('phrase_id, translation_phrase_id, translation_language_id', 'required'),
			array('phrase_id, translation_language_id', 'numerical', 'integerOnly'=>true),
			array('explanation', 'safe')
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
			'phrase' => array(self::BELONGS_TO, 'Phrase', 'phrase_id'),
			'translationPhrase' => array(self::BELONGS_TO, 'Phrase', 'translation_phrase_id'),
			'translationLanguage' => array(self::BELONGS_TO, 'Language', 'translation_language_id'),
			'comments' => array(self::HAS_MANY, 'TranslationComment', 'translation_id'),
			'commentsCount' => array(self::STAT, 'TranslationComment', 'translation_id'),
			'votesCount' => array(self::STAT, 'TranslationVote', 'translation_id'),
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
			'phrase_id' => 'Phrase',
			'translation_phrase_id' => 'Translation Phrase',
			'translation_language_id' => 'Translation Language',
			'date' => 'Date',
		);
	}
}