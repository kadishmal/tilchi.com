<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-password-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Поля отмеченные <span class="required">*</span> обязательны для заполнения.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="left width50">
		<div class="row">
			<?php echo $form->labelEx($model,'password'); ?>
			<?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>100)); ?>
			<?php echo $form->error($model,'password'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model,'password'); ?>
			<?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>100)); ?>
			<?php echo $form->error($model,'password'); ?>
		</div>

		<div class="row buttons">
			<?php echo CHtml::submitButton('Сменить пароль'); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->