<?php
	$isAdd = (strpos($this->action, 'edit') === false);
	$this->set('title_for_layout', 'New Setting');
	
?>


<div class='layout-2-column'>
<div class='layout-2-column-1'>
<div class="form form-SystemSettings">
	<?php 
		echo $form->create('SystemSetting',
			array(
				'url'=>$this->here,
				'type'=>'file'
			)
		);
	?>

	<h2><?= $this->getVar('title_for_layout') ?></h2>
	<div class='fieldset'>

		<?php
			if (!$isAdd) echo $form->input('id');
			
			echo $form->input(
				'group',
				array(
					'label'=>'Group <br/><small>A prefix required for grouping i.e. Site, Cms, Blog, Eblast, etc.</small>',
					'maxLength'=>'64',
				)
			);
			
			echo $form->input(
				'key',
				array(
					'label'=>'Key <br/><small>It\'s used for referencing to this setting. <br/> Group.key  <br/> i.e. Site.keywords</small>',
					'maxLength'=>'64',
				)
			);
			//echo "<div id='key-sample'></div>";
			
			echo $form->input(
				'title',
				array(
					'label'=>'Field Title <br/><small>This title will appear as a field name.</small>',
				)
			);
			
			echo $form->input(
				'description',
				array(
					'label'=>'Field Description <br/><small>Hint</small>',
				)
			);
			
			
			echo $form->input(
				'input_type',
				array(
					'type'		=>'select',
					'options'	=> array('text'=>'Text', 'textarea'=>'Textarea', 'checkbox'=>'Checkbox (yes/no)'),
				)
			);
			
			
		?>
	</div>


	<div class='submit'>
		<?php
			$title = 'Next';
			if ($this->action = 'admin_edit_setting'){
				$title = 'Save';
			}
			echo $form->submit($title);
		?>
	</div>
	
	<?php
		echo $form->end();
	?>
</div> <!-- .form -->
</div> <!-- .layout-2-column-1 -->

<div class="layout-2-column-2">
<div class="fieldset fieldset-actions">
	<div class='legend'>Actions</div>
	<ul class="actions">
		<li><?= $html->link('Delete', array('action' => 'delete', $form->value('SystemSetting.id')), array('class'=>'action-icon action-delete action-delete-SystemSetting'), 'Are you sure you want to delete #' . $form->value('SystemSetting.id') . ' ?'); ?></li>
				<li><?= $html->link('List Settings', array('action'=>'index'), array('class'=>'action-icon action-list action-list-SystemSettings')); ?></li>
		<li><?= $html->link('New Setting', array('action'=>'add'), array('class'=>'action-icon action-add action-add-system-setting')); ?></li>
	</ul>
</div> <!-- .fieldset-actions -->
</div> <!-- .layout-2-column-2 -->
</div> <!-- .layout-2-column -->
