<?php
	$baseUrl = Yii::app()->baseUrl;

	Yii::app()->clientScript->registerScriptFile($baseUrl . '/js/main.js')->
		registerCoreScript('cookie')->
		registerScriptFile($baseUrl . '/js/jquery.typing-0.2.0.min.js')->
		registerScriptFile($baseUrl . '/js/tilchi.js')->
		registerScript('activate-menu', "
		activateMainMenu();
	");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="<?php echo Yii::app()->language; ?>" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<!--[if lt IE 9]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <script type="text/javascript">
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-3787071-5']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>
</head>

<body>

<div class="container" id="page">

	<div id="header">
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
	</div><!-- header -->

	<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>Yii::t('tilchi','Dictionary'), 'url'=>'/site'),
                array('label'=>Yii::t('blog', 'Blog'), 'url'=>'/blog/', 'itemOptions'=>array('class'=>'parent'), 'items'=>array(
					array('label'=>Yii::t('blog', 'New Post'), 'url'=>'/blog/new', 'visible'=>Yii::app()->user->checkAccess('blogContributor')),
					array('label'=>Yii::t('blog', 'Comments'), 'url'=>'/blog/comments', 'visible'=>Yii::app()->user->checkAccess('blogEditor')),
					array('label'=>Yii::t('blog', 'Posts'), 'url'=>'/blog/posts', 'visible'=>Yii::app()->user->checkAccess('blogEditor')),
				)),
                array('label'=>Yii::t('forum','Forum'), 'url'=>'/forum', 'itemOptions'=>array('class'=>'parent'), 'items'=>array(
					array('label'=>Yii::t('forum', 'Ask a question'), 'url'=>'/forum/new/question'),
					array('label'=>Yii::t('forum', 'Submit an idea'), 'url'=>'/forum/new/idea'),
					array('label'=>Yii::t('forum', 'Report an issue'), 'url'=>'/forum/new/issue'),
				)),
                array('label'=>Yii::t('site', 'Manage'), 'itemOptions'=>array('class'=>'parent'), 'visible'=>Yii::app()->user->checkAccess('admin'), 'items'=>array(
                    array('label'=>Yii::t('user', 'User management'), 'url'=>'/user/manage'),
                )),
                array('label'=>Yii::t('site','Register'), 'url'=>'/user/register', 'visible'=>Yii::app()->user->isGuest),
                array('label'=>Yii::t('site','Login'), 'url'=>'/user/signin', 'visible'=>Yii::app()->user->isGuest, 'itemOptions'=>array('class'=>'right')),
				array('label'=>Yii::t('site', 'Account'), 'visible'=>!Yii::app()->user->isGuest, 'itemOptions'=>array('class'=>'right parent'), 'items'=>array(
					array(
                        'label'=>Yii::app()->user->name,
                        'url'=>'/user', 'visible'=>!Yii::app()->user->isGuest,
                        'linkOptions'=>array('class'=>'user-menu-item', 'style'=>'background:url(' . Yii::app()->user->getState('gravatar') . ') no-repeat')
                    ),
					array('label'=>Yii::t('site', 'Logout'), 'url'=>'/user/logout', 'visible'=>!Yii::app()->user->isGuest)
				))
			),
		)); ?>
	</div><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div id="footer">
        <p>Создүк | Словарь | Dictionary | 사전 | Sözlük</p>
		<?php echo date('Y'); ?> &copy; Tilchi.com<br /><?php echo Yii::t('site', 'All Rights Reserved.'); ?>
	</div><!-- footer -->

	<div id="spinner"></div>
	<div id="floodPanel"></div>
	<div id="msgBox"></div>
</div><!-- page -->
</body>
</html>