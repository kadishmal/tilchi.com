<?php

/**
 * This is the model class for table "{{cron_job_settings}}".
 *
 * The followings are the available columns in table '{{cron_job_settings}}':
 * @property string $id
 * @property string $last_comment_id
 *
 * The followings are the available model relations:
 * @property Comments $lastComment
 */
class CronJobSettings extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CronJobSettings the static model class
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
		return '{{cron_job_settings}}';
	}
}