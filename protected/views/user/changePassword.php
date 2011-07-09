<?php
$this->breadcrumbs=array(
	'Мой аккаунт'=>array('index'),
	'Сменить пароль',
);
?>

<h1>Сменить мой пароль</h1>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-password-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Поля отмеченные <span class="required">*</span> обязательны для заполнения.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="left width50">
		<div class="row">
			<?php echo $form->labelEx($model,'password_repeat'); ?>
			<?php echo $form->passwordField($model,'password_repeat',array('size'=>60,'maxlength'=>100)); ?>
			<?php echo $form->error($model,'password_repeat'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model,'passwordNew'); ?>
			<?php echo $form->passwordField($model,'passwordNew',array('size'=>60,'maxlength'=>100)); ?>
			<?php echo $form->error($model,'passwordNew'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model,'passwordNew_repeat'); ?>
			<?php echo $form->passwordField($model,'passwordNew_repeat',array('size'=>60,'maxlength'=>100)); ?>
			<?php echo $form->error($model,'passwordNew_repeat'); ?>
		</div>

		<div class="row buttons">
			<?php echo CHtml::submitButton('Сменить пароль'); ?>
			<?php echo CHtml::linkButton('Отмена', array('href'=>'/user')); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->