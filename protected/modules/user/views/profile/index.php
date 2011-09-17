<?php
$this->pageTitle = Yii::t('UserModule.user', 'My profile') . ' | ' . Yii::app()->name;

Yii::app()->clientScript->registerScript('vote-post', "
	var subsсrPostComments = $('#subsсr_post_comments');

	subsсrPostComments.change(function(){
		jQuery.ajax({
            'type':'POST',
            'url': '/user/profile/subscribe',
            'cache': true,
            'dataType':'json',
            'data': 'User[target]=post_comments&User[v]=' + this.checked,
            'success': function(data, textStatus, jqXHR)
			{
				if (data.status != '1')
				{
					this.checked = !this.checked;
				}
            }
        });
	});
");

$this->menu=array(
	array('label'=>'Изменить мои данные', 'url'=>array('edit', 'c'=>'profile'), 'visible'=>!Yii::app()->user->isGuest),
	array('label'=>'Сменить мой пароль', 'url'=>array('edit', 'c'=>'pwd'), 'visible'=>!Yii::app()->user->isGuest),
);
?>
<div id="profile">
	<h1><?php echo Yii::t('UserModule.user', 'My profile'); ?></h1>
	<div class="row">
		<div class="th"><?php echo $model->getAttributeLabel('first_name') ?></div>
		<div class="td"><b><?php echo $model->first_name . ' ' . $model->last_name; ?></b></div>
		<div class="td leftm10"><?php echo CHtml::link(Yii::t('UserModule.user', 'Change'), '/user/edit/profile', array('class'=>'link-button')); ?></div>
	</div>
	<div class="row">
		<div class="th"><?php echo $model->getAttributeLabel('email') ?></div>
		<div class="td"><b><?php echo $model->email; ?></b></div>
	</div>
	<div class="row">
		<div class="th"><?php echo $model->getAttributeLabel('password') ?></div>
		<div class="td"><?php echo '******'; ?></div>
		<div class="td"><?php echo CHtml::link(Yii::t('UserModule.user', 'Change'), '/user/edit/password', array('class'=>'link-button mLeft10')); ?></div>
	</div>
	<div class="section">
		<div class="th"><?php echo Yii::t('UserModule.user', 'Notifications'); ?></div>
		<div class="td">
			<div class="settings checkbox"><input type="checkbox" id="subsсr_post_comments" <?php echo $model->subsсr_post_comments ? 'checked' : ''; ?> /><label for="subsсr_post_comments"><?php echo Yii::t('UserModule.user', 'Comments on my posts'); ?></label><span class="msg" title="<?php echo Yii::t('UserModule.user', 'Receive email notifications, if someone comments on a post I have created or I have previously commented on.'); ?>"><i></i></span></div>
		</div>
	</div>
</div>