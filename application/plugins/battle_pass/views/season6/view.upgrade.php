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
		<h2 class="title"><?php echo __('Upgrade Battle Pass'); ?></h2>
		<div class="entry" >
			<?php
			if(isset($success)):
				echo '<div class="s_note">'.$success.'</div>';
			endif;
			if(isset($error)):
				echo '<div class="e_note">'.$error.'</div>';
			endif;
			?>
			<form method="post" action="">
				<table>
					<tr>
						<td colspan="2">
							<div style="margin-left: 150px;">
								<?php if($pass['pass_type'] < 1){ ?>
								<input type="radio" name="pass_type" value="1"/> <?php echo __('Silver'); ?> (<?php echo $plugin_config['silver_pass_upgrade_price'];?> <?php echo $this->website->translate_credits($plugin_config['silver_pass_payment_type'], $this->session->userdata(['user' => 'server']));?>)
								<br/>
								<?php } ?>
								<?php 
								if($pass['pass_type'] == 1 && $plugin_config['silver_pass_payment_type'] == $plugin_config['platinum_pass_payment_type']){
									$plugin_config['platinum_pass_upgrade_price'] -= $plugin_config['silver_pass_upgrade_price'];
								}
								?>
								<input type="radio" name="pass_type" value="2"/> <?php echo __('Platinum'); ?>  (<?php echo $plugin_config['platinum_pass_upgrade_price'];?> <?php echo $this->website->translate_credits($plugin_config['platinum_pass_payment_type'], $this->session->userdata(['user' => 'server']));?>)
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div style="margin-left: 150px;"><button type="submit" class="button-style"><?php echo __('Upgrade'); ?></button></div>
						</td>
					</tr>
				</table>
			</form>
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
	
	