<?php
	$this->pageTitle = "Cache files";
	
?>
	<div class='layout-2-column'>
		<div class='layout-2-column-1'>
			<h2><?= $this->pageTitle ?></h2>
						
			<p>
				<?php
				echo $html->link('Click here to clear all', array('action'=>$this->action, 'true'));
				echo "<br/><br/>"; 
				foreach ($file_paths as $file_path) {
					echo $file_path . "<br/>";
				}
				?>
			</p>
		</div> <!-- .layout-2-column-1 -->
		
	</div> <!-- .layout-2-column -->
	