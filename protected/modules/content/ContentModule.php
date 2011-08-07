<?php

class ContentModule extends CWebModule
{
    const USER_PENDING_COMMENT_COUNT = 3;
    const USER_MAX_ACTIVE_IDEA = 1;

	public $cssAssetUrl;

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'content.models.*',
			'content.components.*',
		));

		$cs = Yii::app()->getClientScript();

		$this->cssAssetUrl = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.content.assets.css'), false, 0, true);
		$cs->registerCssFile($this->cssAssetUrl . '/main.css');

		$js = Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.modules.content.assets.js'), false, 0, true);
		$cs->registerScriptFile($js . '/main.js');
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}

	public static function sanitize_title_with_dashes($title) {
		$title = strip_tags($title);
		// Preserve escaped octets.
		$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
		// Remove percent signs that are not part of an octet.
		$title = str_replace('%', '', $title);
		// Restore octets.
		$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
		// replace cyrillic letter with latin
		// aperaoavaeerakaa-url
		$title = preg_replace(array(
			'/[Аа]/u',
			'/[Бб]/u',
			'/[Вв]/u',
			'/[Гг]/u',
			'/[Дд]/u',
			'/[ЕеЁёЭэ]/u',
			'/[Жж]/u',
			'/[Зз]/u',
			'/[ИиЙи]/u',
			'/[Кк]/u',
			'/[Лл]/u',
			'/[Мм]/u',
			'/[Нн]/u',
			'/[Оо]/u',
			'/[Пп]/u',
			'/[Рр]/u',
			'/[Сс]/u',
			'/[Тт]/u',
			'/[УуЮю]/u',
			'/[Фф]/u',
			'/[Хх]/u',
			'/[Цц]/u',
			'/[Чч]/u',
			'/[Шш]/u',
			'/[Щщ]/u',
			'/[ЪъЬь]/u',
			'/[Ыы]/u',
			'/[Яя]/u'
			), array(
			'/a/',
			'/b/',
			'/v/',
			'/g/',
			'/d/',
			'/e/',
			'/zh/',
			'/z/',
			'/i/',
			'/k/',
			'/l/',
			'/m/',
			'/n/',
			'/o/',
			'/p/',
			'/r/',
			'/s/',
			'/t/',
			'/u/',
			'/f/',
			'/h/',
			'/c/',
			'/ch/',
			'/sh/',
			'/sch/',
			'',
			'/y/',
			'/ya/'
			), $title);
		/*if ($this->seems_utf8($title)) {
			if (function_exists('mb_strtolower')) {
				$title = mb_strtolower($title, 'UTF-8');
			}
			//$title = $this->utf8_uri_encode($title, 200);
			$title = mb_substr(CHtml::encode($title), 0, 200);
		}*/

		$title = substr($title, 0, 200);
		$title = strtolower($title);
		$title = preg_replace('/&.+?;/', '', $title); // kill entities
		$title = str_replace('.', '-', $title);
		$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
		$title = preg_replace('/\s+/', '-', $title);
		$title = preg_replace('|-+|', '-', $title);
		$title = trim($title, '-');

		return $title;
	}
    public static function getCurrentDateTime()
    {
    	return date('Y-m-d H:i:s');
    }
    public static function getFormattedFullDate($date)
	{
		return Yii::app()->dateFormatter->format('d MMMM yyyy, h:mm a', $date);
	}
    public static function getFormattedDate($date)
	{
		return Yii::app()->dateFormatter->format('d MMMM yyyy', $date);
	}
	public static function getFormattedRelativeDate($date)
	{
        $diff = time() - $date;

        if ($diff < 60){
            return Yii::t('ContentModule.blog', 'one second ago|{n} seconds ago', $diff);
        }

        $diff /= 60;

        if ($diff < 60){
            return Yii::t('ContentModule.blog', 'one minute ago|{n} minutes ago', floor($diff));
        }

        $diff /= 60;

        if ($diff < 24){
            return Yii::t('ContentModule.blog', 'an hour ago|{n} hours ago', floor($diff));
        }

        $diff /= 24;

        if ($diff < 7){
            return Yii::t('ContentModule.blog', 'yesterday|{n} days ago', floor($diff));
        }

        if ($diff < 30){
            return Yii::t('ContentModule.blog', 'last week|{n} weeks ago', floor($diff / 7));
        }

        $diff /= 30;

        if ($diff < 12){
            return Yii::t('ContentModule.blog', 'last month|{n} months ago', floor($diff));
        }

        $diff /= 12;

        return Yii::t('ContentModule.blog', 'last year|{n} years ago', floor($diff));
	}
}
