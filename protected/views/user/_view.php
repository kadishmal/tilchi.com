<div class="view">

	<?php
		if ($data->email && $data->email !== '')
			echo CHtml::link('Показать детали', array('editUser', 'c'=>'profile', 'id'=>$data->id));
		else{
			//TODO: check who is a parent_user
		}
	?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('first_name')); ?>:</b>
	<?php echo CHtml::encode($data->first_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('last_name')); ?>:</b>
	<?php echo CHtml::encode($data->last_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('phone')); ?>:</b>
	<?php echo CHtml::encode($data->phone); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('city.city')); ?>:</b>
	<?php echo CHtml::encode($data->city->city); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('address')); ?>:</b>
	<?php echo CHtml::encode($data->address); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('building_number')); ?>:</b>
	<?php echo CHtml::encode($data->building_number); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('apartment')); ?>:</b>
	<?php echo CHtml::encode($data->apartment); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('email')); ?>:</b>
	<?php echo CHtml::encode($data->email); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('password')); ?>:</b>
	<?php echo CHtml::encode('******'); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('join_date')); ?>:</b>
	<?php echo CHtml::encode($data->join_date); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('other_user_address')); ?>:</b>
	<?php echo CHtml::encode($data->other_user_address); ?>
	<br />
	*/
	?>

</div>