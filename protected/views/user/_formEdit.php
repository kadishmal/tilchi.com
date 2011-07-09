<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Поля отмеченные <span class="required">*</span> обязательны для заполнения.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="left width50">
		<div class="row">
			<?php echo $form->labelEx($model,'city_id'); ?>
			<?php echo $form->dropDownList($model,'city_id', 
				CHtml::listData(City::model()->findAll(), 'id', 'city'),
				array(
					'options'=>array(
						'city_id'=>array('selected'=>true)
					)
				)
			); ?>
			<?php echo $form->error($model,'city_id'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model,'address'); ?>
			<?php echo $form->textField($model,'address',array('size'=>60,'maxlength'=>255)); ?>
			<?php echo $form->error($model,'address'); ?>
		</div>

		<div class="row">
			<div class="width25">
				<?php echo $form->labelEx($model,'building_number'); ?>
				<?php echo $form->textField($model,'building_number',array('size'=>5,'maxlength'=>5)); ?>
				<?php echo $form->error($model,'building_number'); ?>
			</div>

			<div class="width25">
				<?php echo $form->labelEx($model,'apartment'); ?>
				<?php echo $form->textField($model,'apartment',array('size'=>5,'maxlength'=>5)); ?>
				<?php echo $form->error($model,'apartment'); ?>
			</div>
		</div>
	</div>

	<div class="width50">
		<div class="row">
			<?php echo $form->labelEx($model,'first_name'); ?>
			<?php echo $form->textField($model,'first_name',array('size'=>45,'maxlength'=>45)); ?>
			<?php echo $form->error($model,'first_name'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model,'last_name'); ?>
			<?php echo $form->textField($model,'last_name',array('size'=>45,'maxlength'=>45)); ?>
			<?php echo $form->error($model,'last_name'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($model,'phone'); ?>
			<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'phone'); ?>
		</div>
	</div>

	<div class="center width50">
		<div class="row">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>100)); ?>
			<?php echo $form->error($model,'email'); ?>
		</div>

		<div class="row buttons">
			<?php echo CHtml::submitButton('Сохранить'); ?>
			<?php echo CHtml::linkButton('Отмена', array('href'=>'/user')); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->