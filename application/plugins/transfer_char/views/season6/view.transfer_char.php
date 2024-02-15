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
				<div style="padding-top:40px;"></div>
				<div style="clear:left;"></div>
				<?php
				if(isset($error)){
					echo '<div class="e_note">'.$error.'</div>';
				}
				if(isset($success)){
					echo '<div class="s_note">'.$success.'</div>';
				}
				if(isset($char_list) && $char_list != false){		
				?>
				<div class="form">
				<form method="post" action="" id="transfer_character">
					<table>
					<tr>
						<td style="width: 150px;"><?php echo __('Char');?></td>
						<td>
							<select class="custom-select" name="character" id="character">
							<?php foreach($char_list as $char): ?>
								<option value="<?php echo $char['name'];?>"><?php echo $char['name'];?></option>
							<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style="width:150px;"><?php echo __('Username'); ?></td>
						<td><input type="text" name="username" id="username" value="" /></td>
					</tr>
					<?php if($plugin_config['price'] > 0){ ?>
					<tr>
						<td style="width:150px;"><?php echo __('Price'); ?></td>
						<td><?php echo $plugin_config['price'];?> <?php echo $this->website->translate_credits($plugin_config['price_type'], $this->session->userdata(['user' => 'server']));?></td>
					</tr>
					<?php } ?>
					<tr>
						<td></td>
						<td><button type="submit" id="transfer_character" name="transfer_character" value="transfer_character" class="button-style"><?php echo __('Transfer Character');?></button></td>
					</tr>
					</table>
				</form>
				</div>
				<?php
				}
				else{
				?>
				<div class="e_note"><?php echo __('No characters found.');?></div>
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

	