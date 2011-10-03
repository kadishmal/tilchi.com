<?php

/**
 * This is the model class for table "{{phrases}}".
 *
 * The followings are the available columns in table '{{phrases}}':
 * @property string $id
 * @property string $phrase
 * @property string $language_id
 * @property string $user_id
 *
 * The followings are the available model relations:
 * @property PhraseComments[] $phraseComments
 * @property PhraseVotes[] $phraseVotes
 * @property Languages $language
 * @property Users $user
 * @property Phrase $parentPhrase
 * @property Phrase[] $phrases
 * @property Translations[] $translations
 */
class Phrase extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Phrase the static model class
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
		return '{{phrases}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('phrase, language_id', 'required'),
			array('language_id', 'numerical', 'integerOnly'=>true),
			array('phrase', 'checkUnique', 'allowEmpty'=>false, 'caseSensitive'=>false, 'on'=>'new')
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
			'language' => array(self::BELONGS_TO, 'Language', 'language_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'searchHistory' => array(self::HAS_MANY, 'PhraseSearchHistory', 'phrase_id'),
			'translations' => array(self::HAS_MANY, 'PhraseTranslation', 'phrase_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'phrase' => 'Phrase',
			'language_id' => 'Language',
			'user_id' => 'User',
			'date' => 'Date',
			'type' => 'Type',
		);
	}

	public function checkUnique($attribute,$params)
    {
        $existringPhrases = Phrase::model()->findAllByAttributes(array(
			'phrase'=>$this->$attribute,
			'language_id'=>$this->language_id
		));

		foreach($existringPhrases as $phrase)
		{
			if ($phrase->phrase == $this->$attribute)
			{
				$this->addError('phrase', Yii::t('tilchi', 'The "<b>phrase</b>" phrase already exists.', array('phrase'=>$this->$attribute)));
			}
		}
    }
}