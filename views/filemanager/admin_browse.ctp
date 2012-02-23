<?php 
	$options = Configure::read('FileManager.options');
?>

<div class='layout-1-column file-manager'>
	<div class="index">
		<h2><?php echo $title_for_layout; ?></h2>
	
	    <div class="actions">
	        <ul>
	        	<?php if ($options['allowed_operations']['upload']):?>
	            <li><?php echo $this->Filemanager->link(__('Upload here', true), array('controller' => 'filemanager', 'action' => 'upload'), $path); ?></li>
	            <?php endif;?>
	            <li><?php echo $this->Filemanager->link(__('Create directory', true), array('controller' => 'filemanager', 'action' => 'create_directory'), $path); ?></li>
	            <!-- 
	            	<li><?php //echo $this->Filemanager->link(__('Create file', true), array('controller' => 'filemanager', 'action' => 'create_file'), $path); ?></li>
	             -->
	        </ul>
	    </div>
	
		<div class="file-manager breadcrumb">
		<?php
	        echo __('You are here:', true) . ' ';
	        $breadcrumb = $this->Filemanager->breadcrumb($path);
	        foreach ($breadcrumb AS $pathname => $p) {
	            echo $this->Filemanager->linkDirectory($pathname, $p);
	            echo DS;
	        }
		?>
		</div>
		<br/>
		
		
		<div class="directory-content">
	        <table cellpadding="0" cellspacing="0" class="ui-widget-content ui-corner-all ui-data">
	        <?php
	            $tableHeaders =  $this->Html->tableHeaders(array(
	                '',
	                __('Directory content', true),
	                __('Actions', true),
	            ));
	            echo $tableHeaders;
	
	            // directories
	            $rows = array();
	            foreach ($content['0'] AS $directory) {
	                $actions = $this->Filemanager->linkDirectory(__('Open', true), $path.$directory.DS);
	               
	                if ($this->Filemanager->inPath($deletablePaths, $path.$directory)) {
	                	/*if ($options['allowed_operations']['delete']){
		                	$actions .= ' ' . $this->Filemanager->link(__('Delete', true), array(
		                        'controller' => 'filemanager',
		                        'action' => 'delete_directory',
		                        'token' => @$this->params['_Token']['key'],
		                    ), $path.$directory);
	                	}*/
	                }
	                
	                $rows[] = array(
	                    $this->Html->image('/img/icons/folder.png'),
	                    $this->Filemanager->linkDirectory($directory, $path.$directory.DS),
	                    $actions,
	                );
	            }
	            echo $this->Html->tableCells($rows, array('class' => 'directory ui-data-row'), array('class' => 'directory ui-data-row-alt'));
	
	            // files
	            $rows = array();
	            foreach ($content['1'] AS $file) {
	            	if ($options['allowed_operations']['edit']){
	            		$actions = $this->Filemanager->link(__('Edit', true), array('controller' => 'filemanager', 'action' => 'editfile'), $path.$file);
	            	}else{
	            		$actions = '';	
	            	}
	            	
	                
	                if ($this->Filemanager->inPath($deletablePaths, $path.$file)) {
	                	if ($options['allowed_operations']['delete']){
		                    $actions .= $this->Filemanager->link(__('Delete', true), array(
		                        'controller' => 'filemanager',
		                        'action' => 'delete_file',
		                        'token' => @$this->params['_Token']['key'],
		                    ), $path.$file);
	                	}
	                }
	                
	                //$actions .= $this->Html->link('View', $path.$file, array('target'=>'_blank'));
	                	                
	                $rows[] = array(
	                    $this->Html->image('/img/icons/'.$this->Filemanager->filename2icon($file)),
	                    $this->Filemanager->linkFile($file, $path.$file),
	                    $actions,
	                );
	            }
	            echo $this->Html->tableCells($rows, array('class' => 'file ui-data-row'), array('class' => 'file ui-data-row-alt'));
	
	            echo $tableHeaders;
	        ?>
	        </table>
		</div>
	</div>
</div>