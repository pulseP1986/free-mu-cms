<?php
	$this->load->view($this->config->config_entry('main|template').DS.'view.header');
	$this->load->view($this->config->config_entry('main|template').DS.'view.left_sidebar');
?>	
<div class="news_main">
	<?php 
	if(isset($config_not_found)):
		echo '<div class="box-style1"><div class="entry"><div class="e_note">'.$config_not_found.'</div></div></div>';
	else:
		if(isset($module_disabled)):
			echo '<div class="box-style1"><div class="entry"><div class="e_note">'.$module_disabled.'</div></div></div>';
		else:
	?>		
	<div class="heding">
		<h2><?php echo __($about['name']); ?></h2>
	</div>
	<div class="content_rght_info m5">
				<div class="entry">
					<div style="float:right;">
						<a class="custom_button" href="<?php echo $this->config->base_url; ?>stats-specialization"><?php echo __('My Specializations'); ?></a>
					</div>
					 <div style="padding-top:40px;"></div>
					<?php
						if(isset($error)):
							echo '<div class="e_note">'.$error.'</div>';
						endif;	
						if(isset($success)):
							echo '<div class="s_note">'.$success.'</div>';
						endif;	
					?>
					<div class="form other">
						<form method="post" action="" id="stats_specialization_form">
							<table>
								<tr>
									<td style="width: 150px;"><?php echo __('Specialization Title'); ?>:</td>
									<td>
										<input class="validate[required,minSize[2],maxSize[30]]" type="text" name="title" id="title" value=""/>
									</td>
								</tr>
								<?php if($plugin_config['price'] > 0):?>
								<tr>
									<td style="width: 150px;"><?php echo __('Price'); ?>:</td>
									<td>
										<?php echo $plugin_config['price'] . ' ' . $plugin_config['payment_name'];?>
									</td>
								</tr>
								<?php endif;?>
								<tr>
									<td></td>
									<td>
										<button type="submit" class="flatbtn-blu m5_top"><?php echo __('Submit'); ?></button>
									</td>
								</tr>
							</table>
						</form>
						<script type="text/javascript">
							$(document).ready(function () {
								$("#stats_specialization_form").validationEngine();
							});
						</script>
					</div>
				</div>
			</div>
		<?php
			endif;
		endif;
		?>
	</div>
<?php
	$this->load->view($this->config->config_entry('main|template').DS.'view.footer');
?>

