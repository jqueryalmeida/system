<?php
	$isAdd = (strpos($this->action, 'edit') === false);
	$this->set('title_for_layout', ($isAdd ? 'Add' : 'Edit') . ' Setting');
	
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
			
			echo "Current setting: " . $this->data['SystemSetting']['group'] . "." . $this->data['SystemSetting']['key'];
			 
			echo $form->input(
				'group',
				array(
					'disabled'=> true,
				)
			);
			
			echo $form->input(
				'key',
				array(
					'disabled'=> true,
				)
			);
			
			
			
			$input_type = $this->data['SystemSetting']['input_type']; 
			$field_title = $this->data['SystemSetting']['title'];
			$field_desc = $this->data['SystemSetting']['description'];
			
			if (!blank($field_desc)){
				$field_desc = "<br/><small>" . $field_desc . "</small>";				
			}
			
			$field_options = array(
				'type'	=>$input_type,
				'label' => $field_title . $field_desc,			
			);
				
			echo $form->input(
				'value',
				$field_options
			);
			
			/*
			echo $form->input(
				'title',
				array(
					'label'=>$field_title,
				)
			);
			
			echo $form->input(
				'description',
				array(
				)
			);*/
			
			/*
			echo $form->input(
				'input_type',
				array(
				)
			);
			
			echo $form->input(
				'editable',
				array(
				)
			);
			
			echo $form->input(
				'weight',
				array(
				)
			);
			
			echo $form->input(
				'params',
				array(
				)
			);
			*/
			
		?>
	</div>


	<div class='submit'>
		<?php
			echo $form->submit('Save');
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
