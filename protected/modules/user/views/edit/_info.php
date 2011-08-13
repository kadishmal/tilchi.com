<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'info-form',
    'enableAjaxValidation'=>true,
    'enableClientValidation'=>true,
    'focus'=>($model->last_name == null ? array($model,'last_name') :
			($model->first_name == null ? array($model,'first_name') :
				array($model,'gender')
			)
	),
    'clientOptions'=>array('validateOnSubmit'=>true, 'afterValidateAttribute'=>'js:function(form, attribute, data, hasError){
        if(hasError){
            $("#" + attribute.id).siblings(".errorBox").show();
        }
        else{
            $("#" + attribute.id).siblings(".errorBox").hide();
        }
    }')
)); ?>

	<?php echo $form->errorSummary($model); ?>

    <div class="row last-name"><i></i><?php
        echo CHtml::tag('span', array('class'=>'errorBox'),
                $form->error($model,'last_name') .
                CHtml::tag('span', array('class'=>'arrow-border east'), '') .
                CHtml::tag('span', array('class'=>'arrow east'),'')
            ) .
            CHtml::label(
                    Yii::t('UserModule.user', 'Enter your <b>real last name</b>.') .
                    CHtml::tag('span', array('class'=>'arrow-border west'), '') .
                    CHtml::tag('span', array('class'=>'arrow west'),'')
            , 'User_last_name', array('class'=>'required')) .
            $form->textField($model, 'last_name');
    ?></div>

    <div class="row name"><i></i><?php
        echo CHtml::tag('span', array('class'=>'errorBox'),
                $form->error($model,'first_name') .
                CHtml::tag('span', array('class'=>'arrow-border east'), '') .
                CHtml::tag('span', array('class'=>'arrow east'),'')
            ) .
            CHtml::label(
                    Yii::t('UserModule.user', 'Enter your <b>real name</b>. Every time you login we will greet you with your lovely name!') .
                    CHtml::tag('span', array('class'=>'arrow-border west'), '') .
                    CHtml::tag('span', array('class'=>'arrow west'),'')
            , 'User_first_name', array('class'=>'required')) .
            $form->textField($model, 'first_name');
    ?></div>

    <div class="gender"><?php
//        echo CHtml::tag('span', array('class'=>'errorBox'),
//                $form->error($model,'gender') .
//                CHtml::tag('span', array('class'=>'arrow-border east'), '') .
//                CHtml::tag('span', array('class'=>'arrow east'),'')
//            ) .

			echo CHtml::tag('span', array('class'=>'male'), '<i></i>') .
				CHtml::tag('span', array('class'=>'female'), '<i></i>');

			echo CHtml::tag('div', array('class'=>'options'),
					$form->radioButtonList($model, 'gender', array(User::GENDER_MALE=>Yii::t('UserModule.user', 'Male'), User::GENDER_FEMALE=>Yii::t('UserModule.user', 'Female')), array('separator'=>' '))
			);
    ?></div>

    <div class="major-buttons text-right">
        <?php echo CHtml::submitButton(Yii::t('UserModule.user', 'Save'), array('class'=>'button primary')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->