<?php
	$cs = Yii::app()->getClientScript();
	//$cs->registerScriptFile($js . '/main.js');

	/*$cs->registerScript('edit-post', "
		activateEditPost();
	");*/
	/*<script type="text/javascript">
		document.write(tinyMCEPopup.editor.getContent());
	</script>*/
?>

<div id="blog">
	<div class="post">
	<?php
	echo Yii::getPathOfAlias('application.modules.content.extension.js');
	?>
	</div>
</div>