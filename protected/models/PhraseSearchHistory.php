<?php

/**
 * This is the model class for table "{{phrase_search_history}}".
 *
 * The followings are the available columns in table '{{phrase_search_history}}':
 * @property string $id
 * @property string $user_id
 * @property string $language_id
 * @property string $search_date
 *
 * The followings are the available model relations:
 * @property Users $user
 * @property Phrases $phrase
 */
class PhraseSearchHistory extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PhraseSearchHistory the static model class
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
		return '{{phrase_search_history}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('phrase_id, language_id', 'required')
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
			'language' => array(self::BELONGS_TO, 'Language', 'language_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'phrase_id' => 'Phrase',
			'user_id' => 'User',
			'search_date' => 'Search Date',
			'count' => 'Count',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('phrase_id',$this->phrase_id,true);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('search_date',$this->search_date,true);
		$criteria->compare('count',$this->count,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}