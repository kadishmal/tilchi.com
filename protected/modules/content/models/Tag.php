<?php

/**
 * This is the model class for table "tbl_tags".
 *
 * The followings are the available columns in table 'tbl_tags':
 * @property string $id
 * @property string $name
 * @property string $slug
 *
 * The followings are the available model relations:
 * @property TblPostTags[] $tblPostTags
 */
class Tag extends CActiveRecord
{
    const FONT_SMALLEST = 8;
    const FONT_LARGEST = 22;
    const FONT_UNIT = 'pt';
    // How many tags should be displayed in the tag cloud
    const MAX_DISPLAY_COUNT = 45;
	/**
	 * Returns the static model of the specified AR class.
	 * @return Tag the static model class
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
		return '{{tags}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, slug', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			//array('id, name, slug', 'safe', 'on'=>'search'),
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
			'postTags' => array(self::HAS_MANY, 'PostTag', 'tag_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'slug' => 'Slug',
		);
	}
    /**
    * Default topic count scaling for tag links
    *
    * @param integer $count number of posts with that tag
    * @return integer scaled count
    */
    public static function default_topic_count_scale($count){
        return round(log10($count + 1) * 100);
    }
}