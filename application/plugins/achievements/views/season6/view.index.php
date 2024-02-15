<?php
	$this->load->view($this->config->config_entry('main|template').DS.'view.header');
?>	
<div id="content">
	<div id="box1">
		<?php 
		if(isset($config_not_found)):
			echo '<div class="box-style1"><div class="entry"><div class="e_note">'.$config_not_found.'</div></div></div>';
		else:
			if(isset($module_disabled)):
				echo '<div class="box-style1"><div class="entry"><div class="e_note">'.$module_disabled.'</div></div></div>';
			else:
		?>	
		<div class="title1">
			<h1><?php echo __($about['name']); ?></h1>
		</div>
		<div class="box-style1" style="margin-bottom: 20px;">
			<h2 class="title"><?php echo __($about['user_description']); ?></h2>
			<div class="entry" >
				<div style="float:right;">
					<a class="custom_button" href="<?php echo $this->config->base_url;?>achievements/rankings/<?php echo $this->session->userdata(['user' => 'server']);?>"><?php echo __('Rankings');?></a>
				</div>
				<div style="padding-top:40px;"></div>
				<div style="clear:left;"></div>
				<?php if(isset($characters) && $characters != false){ ?>
				<table class="ranking-table">
					<thead>
					<tr class="main-tr">
						<td><?php echo __('Character');?></td>
						<td><?php echo __('Level');?></td>
						<td><?php echo __('Res');?></td>
						<td><?php echo __('Class');?></td>
						<td>Action</td>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($characters as $ch):
					?>
					<tr>
						<td><?php echo $ch['name'];?></td>
						<td><?php echo $ch['level'];?></td>
						<td><?php echo $ch['resets'];?></td>
						<td><?php echo $this->website->get_char_class($ch['class']);?></td>
						<td>
						<?php if($this->Machievements->checkUnlocked($ch['id'], $this->session->userdata(['user' => 'server'])) != false){ ?>
						<a href="<?php echo $this->config->base_url; ?>achievements/view/<?php echo $ch['id'];?>"><?php echo __('View');?></a>
						<?php } else { ?>
						<a href="<?php echo $this->config->base_url; ?>achievements/unlock/<?php echo $ch['id'];?>"><?php echo __('Unlock');?></a>
						<?php } ?>
						</td>
					</tr>
					<?php
					endforeach;
					?>
					</tbody>
				</table>
				
				<?php 
				}
				else{
				?>
				<div class="w_note"><?php echo __('No Characters Found.');?></div>
				<?php
				}
				?>
			</div>
		</div>
		<?php
			endif;
		endif;
		?>
	</div>
</div>
<?php
	$this->load->view($this->config->config_entry('main|template').DS.'view.right_sidebar');
	$this->load->view($this->config->config_entry('main|template').DS.'view.footer');
?>
	