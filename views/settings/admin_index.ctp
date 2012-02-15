<?php
	$this->set('title_for_layout', 'List Settings');

	$data = array();
	foreach ($SystemSettings as $SystemSetting) {
		$SystemSettingId = $SystemSetting['SystemSetting']['id'];
		$group = $SystemSetting['SystemSetting']['group'];

		$actions = array(
			'Update value' => array('action'=>'edit', $SystemSettingId),
			'Edit setting' => array('action'=>'edit_setting', $SystemSettingId),
			'Delete' => array('action'=>'delete', $SystemSettingId),
		);
		$actionLinks = $dataRenderer->actions($actions);

		$full_key = $SystemSetting['SystemSetting']['group'] . "." . $SystemSetting['SystemSetting']['key'];
		
		$data[$group][] = array(
			'id' => $SystemSettingId,
			//'Id' => $SystemSetting['SystemSetting']['id'],
			'Current Key'=> $html->link($full_key, array('action'=>'edit', $SystemSetting['SystemSetting']['id'])),
			'Key' => $SystemSetting['SystemSetting']['key'],
			'Title' => $SystemSetting['SystemSetting']['title'],
			'Value' => $SystemSetting['SystemSetting']['value'],
			'Description' => $SystemSetting['SystemSetting']['description'],
			//'Input Type' => $SystemSetting['SystemSetting']['input_type'],
			//'Editable' => $SystemSetting['SystemSetting']['editable'],
			//'Weight' => $SystemSetting['SystemSetting']['weight'],
			//'Params' => $SystemSetting['SystemSetting']['params'],
			'Actions' => $actionLinks,
		);
	}

	$options = array(
		'pagination' => array(
			'Id' => 'SystemSetting.id',
			'Key' => 'SystemSetting.key',
			'Value' => 'SystemSetting.value',
			'Title' => 'SystemSetting.title',
			'Description' => 'SystemSetting.description',
			//'Input Type' => 'SystemSetting.input_type',
			//'Editable' => 'SystemSetting.editable',
			//'Weight' => 'SystemSetting.weight',
			//'Params' => 'SystemSetting.params',
		)
	);

?>
<div class='layout-2-column'>
<div class='layout-2-column-1'>
<div class="index index-SystemSettings">
	<?php
		echo $form->create('SystemSetting',
			array(
				'url'=>$this->here,
				'type'=>'file'
			)
		);
	?>

	<h2><?= $this->getVar('title_for_layout') ?></h2>

	<?php
		foreach($data as $group=>$settings){
			echo "<div class='group-title'><h3>" . $group . "</h3></div>";
			echo $dataRenderer->asTable($settings, array());
			echo "<br/>";
		}
	?>
	
	<?php
		echo $form->end();
	?>
	
</div> <!-- .index -->
</div> <!-- .layout-2-column-1 -->

<div class="layout-2-column-2">
<div class="fieldset fieldset-actions">
	<div class='legend'>Actions</div>
	<ul class="actions">
		<li><?= $html->link('New Setting', array('action'=>'add'), array('class'=>'action-icon action-add action-add-system-setting')); ?></li>
		<li><?= $html->link('Rebuild Configuration', array('action'=>'admin_rebuild', 'admin'=>true, 'prefix'=>'admin'), array('class'=>'action-icon action-add action-add-system-setting')); ?></li>
	</ul>
</div> <!-- .fieldset-actions -->
</div> <!-- .layout-2-column-2 -->
</div> <!-- .layout-2-column -->