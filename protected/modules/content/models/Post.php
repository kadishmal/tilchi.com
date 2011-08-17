<?php

/**
 * This is the model class for table "tbl_posts".
 *
 * The followings are the available columns in table 'tbl_posts':
 * @property string $id
 * @property string $user_id
 * @property integer $publish_date
 * @property string $content
 * @property string $title
 * @property string $status
 *
 * The followings are the available model relations:
 * @property TblUsers $user
 */
class Post extends CActiveRecord
{
	const STATUS_DRAFT = 0;
	const STATUS_PUBLISHED = 1;
	const STATUS_TRASHED = 2;
    const TYPE_BLOG = 0;
    const TYPE_QUESTION = 1;
    const TYPE_IDEA = 2;
    const TYPE_ISSUE = 3;
    const RESPONSE_NEW = 0;
    const RESPONSE_UNDER_REVIEW = 1;
    const RESPONSE_ACCEPTED = 2;
    const RESPONSE_ASSIGNED = 3;
    const RESPONSE_COMPLETED = 4;
    const RESPONSE_DUPLICATE = 5;

	public $jj; // day
	public $mm; // month
	public $aa; // Year
	public $hh; // hour
	public $mn; // minute

    private $oldTags;
	/**
	 * Returns the static model of the specified AR class.
	 * @return Post the static model class
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
		return '{{posts}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('status', 'required'),
			array('jj, mm, aa, hh, mn', 'required', 'on'=>'blog'),
            array('title, content', 'required', 'on'=>'forum'),
			array('status', 'in', 'range'=>array(self::STATUS_DRAFT, self::STATUS_PUBLISHED, self::STATUS_TRASHED)),
			array('jj, mm, aa, hh, mn', 'numerical', 'integerOnly'=>true),
			array('jj, mm, hh, mn', 'length', 'max'=>2),
			array('aa', 'length', 'max'=>4),
			array('slug', 'unique'),
			array('content, title, tags', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			//array('id, user_id, date, content, title, status', 'safe', 'on'=>'search'),
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
			'author' => array(self::BELONGS_TO, 'User', 'user_id'),
			'postTags' => array(self::HAS_MANY, 'PostTag', 'post_id'),
			'comments' => array(self::HAS_MANY, 'Comment', 'post_id', 'condition'=>'comments.status = ' . Comment::STATUS_APPROVED . ' OR comments.user_id = ' . (Yii::app()->user->isGuest ? 0 : Yii::app()->user->id), 'order' => 'comments.order ASC'),
			'commentsCount' => array(self::STAT, 'Comment', 'post_id', 'condition'=>'status = ' . Comment::STATUS_APPROVED),
            'votesCount' => array(self::STAT, 'Vote', 'post_id'),
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
			'publish_date' => 'Publish Date',
			'title' => Yii::t('ContentModule.forum', 'Title'),
			'content' => Yii::t('ContentModule.forum', 'Content'),
			'title' => Yii::t('blog', 'Title'),
			'status' => 'Status',
		);
	}
	public function afterConstruct()
	{
		$this->jj = date('j');
		$this->mm = date('m');
		$this->aa = date('Y');
		$this->hh = date('H');
		$this->mn = date('i');
	}
	public function afterFind()
	{
		$this->jj = date('j', $this->publish_date);
		$this->mm = date('m', $this->publish_date);
		$this->aa = date('Y', $this->publish_date);
		$this->hh = date('H', $this->publish_date);
		$this->mn = date('i', $this->publish_date);

        $this->oldTags = $this->tagsAsArray();
	}
	public function beforeSave()
	{
        $this->publish_date = strtotime(
                $this->aa . '-' . $this->mm . '-' . $this->jj . ' ' .
                $this->hh . ':' . $this->mn
        );

		$this->tags = implode(', ', array_unique($this->tagsAsArray()));

		return true;
	}
	public function afterSave()
	{
		$tags = $this->tagsAsArray();

        foreach($tags as &$tag)
        {
            $tagRecord = Tag::model()->find(
                'name = :tag',
                array(':tag'=>$tag)
            );

            if (!$tagRecord){
                $tagRecord = new Tag;

                $tagRecord->name = $tag;
                $tagRecord->slug = ContentModule::sanitize_title_with_dashes($tag);

                $tagRecord->save();

                $postTag = new PostTag;

                $postTag->post_id = $this->id;
                $postTag->tag_id = $tagRecord->id;

                $postTag->save();
            }
            else{
                if (!PostTag::model()->exists(
                    'post_id = ' . $this->id . ' AND tag_id = ' . $tagRecord->id
                )){
                    $postTag = new PostTag;

                    $postTag->post_id = $this->id;
                    $postTag->tag_id = $tagRecord->id;

                    $postTag->save();

                    $tagRecord->saveAttributes(array('frequency'=>$tagRecord->frequency + 1));
                }
            }
        }

        if ($this->oldTags && !empty($this->oldTags))
        {
            $removeTags = array_diff($this->oldTags, $tags);

            foreach ($removeTags as $tag)
            {
                $tagModel = Tag::model();
                $transaction = $tagModel->dbConnection->beginTransaction();

                try
                {
                    $tagRecord = $tagModel->find(
                        'name = :tag',
                        array(':tag'=>$tag)
                    );

                    if ($tagRecord->frequency == 1){
                        if ($tagRecord->delete()){
                            $transaction->commit();
                        }
                    }
                    else
                    {
                        PostTag::model()->deleteAll(
                            'post_id = ' . $this->id . ' AND tag_id = ' . $tagRecord->id
                        );

                        if ($tagRecord->saveAttributes(array('frequency'=>$tagRecord->frequency - 1))){
                            $transaction->commit();
                        }
                    }
                }
                catch(Exception $e)
                {
                    $transaction->rollBack();
                }
            }
        }
	}
	public function getStatusList()
	{
		return array(
			self::STATUS_DRAFT => Yii::t('ContentModule.blog', 'Draft'),
			self::STATUS_PUBLISHED => Yii::t('ContentModule.blog', 'Published'),
			self::STATUS_TRASHED => Yii::t('ContentModule.blog', 'Trashed')
		);
	}
	public function getStatusTitle()
	{
		switch($this->status)
		{
			case self::STATUS_PUBLISHED:
                return Yii::t('ContentModule.blog', 'Published'); break;
			case self::STATUS_TRASHED:
                return Yii::t('ContentModule.blog', 'Trashed'); break;
            default:
                return Yii::t('ContentModule.blog', 'Draft');
        }
	}
    public function getTypeCode($type)
	{
		switch($type)
		{
			case 'question': return self::TYPE_QUESTION;
                break;
			case 'idea': return self::TYPE_IDEA;
                break;
            case 'issue': return self::TYPE_ISSUE;
                break;
            case 'blog': return self::TYPE_BLOG;
                break;
        }
	}
    public static function getGlobalTypeTitle($type)
    {
        if ($type == self::TYPE_BLOG)
            return 'blog';
        else
            return 'forum';
    }
	public static function getTypeTitle($type)
    {
		switch($type)
		{
            case self::TYPE_BLOG: return 'blog';
			case self::TYPE_QUESTION: return 'question';
			case self::TYPE_IDEA: return 'idea';
            // this will assume TYPE_ISSUE
			default: return 'issue';
        }
	}
	public static function getResponseTitle($response_type)
	{
		switch($response_type)
		{
			case self::RESPONSE_NEW: return 'new';
            case self::RESPONSE_UNDER_REVIEW: return 'under-review';
			case self::RESPONSE_ACCEPTED: return 'accepted';
            case self::RESPONSE_ASSIGNED: return 'assigned';
            case self::RESPONSE_COMPLETED: return 'completed';
            case self::RESPONSE_DUPLICATE: return 'duplicate';
        }
	}
	public static function getResponseText($response_type, $type)
	{
		switch($response_type)
		{
			case self::RESPONSE_NEW: return Yii::t('ContentModule.forum', 'n==1#New|n==2||n==3#New', $type);
            case self::RESPONSE_UNDER_REVIEW: return Yii::t('ContentModule.forum', 'Under Review');
			case self::RESPONSE_ACCEPTED: return Yii::t('ContentModule.forum', 'n==1#Accepted|n==2||n==3#Accepted', $type);
            case self::RESPONSE_ASSIGNED: return Yii::t('ContentModule.forum', 'Assigned');
            case self::RESPONSE_COMPLETED: return Yii::t('ContentModule.forum', 'n==1#Answered|n==2#Completed|n==3#Fixed', $type);
            case self::RESPONSE_DUPLICATE: return Yii::t('ContentModule.forum', 'Duplicate');
        }
	}
	public function tagsAsArray()
	{
		if (strlen(trim($this->tags)) > 0)
		{
			return explode(',', preg_replace('/\s*,\s+/', ',', $this->tags));
		}

		return array();
	}
    public function isVoted()
    {
        if (Yii::app()->user->isGuest) return false;

        return Vote::model()->countByAttributes(array(
            'user_id'=>Yii::app()->user->id,
            'post_id'=>$this->id
        )) > 0;
    }
}