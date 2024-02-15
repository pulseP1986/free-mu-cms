<?php
$this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div id="content">
	<div id="box1">
		<?php 
		if(isset($config_not_found)):
			echo '<div class="box-style1"><div class="entry"><div class="e_note">'.$config_not_found.'</div></div></div>';
		else:
			if(isset($char_error)):
				echo '<div class="box-style1"><div class="entry"><div class="e_note">'.$char_error.'</div></div></div>';
			else:
		?>	
		<div class="title1">
			<h1><?php echo __($about['name']); ?></h1>
		</div>
		<div id="content_center">
			<div class="box-style1" style="margin-bottom:55px;">
				<h2 class="title"><?php echo __($about['user_description']); ?></h2>
				<div class="entry">
					<div style="float:right;">
						<a class="custom_button" href="<?php echo $this->config->base_url; ?>skill-tree-specialization"><?php echo __('My Specializations'); ?></a>
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
					<div class="form">
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
										<button type="submit" class="button-style"><?php echo __('Submit'); ?></button>
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
		</div>
		<?php
			endif;
		endif;
		?>
	</div>
</div>
<?php
$this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
$this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>

