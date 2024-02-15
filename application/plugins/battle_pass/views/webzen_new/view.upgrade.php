<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div class="dmn-content">
	<div class="dmn-page-box">
		<div class="dmn-page-title">
			<h1><?php echo __('Upgrade Battle Pass'); ?></h1>
		</div>
		<div class="dmn-page-content">
			<div class="row">
				<div class="col-12">   
					<?php 
					if(isset($config_not_found)){
						echo '<div class="alert alert-danger" role="alert">'.$config_not_found.'</div>';
					} 
					else{
						if(isset($module_disabled)){
							echo '<div class="alert alert-danger" role="alert">'.$module_disabled.'</div>';
						} 
						else{
					?>	
					<h2 class="title"><?php echo __('Upgrade Battle Pass'); ?></h2>
					<?php
					if(isset($success)){
						echo '<div class="alert alert-success" role="alert">'.$success.'</div>';
					}
					if(isset($error)){
						echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
					}
					?>
					<form method="post" action="">
						<div class="form-group">
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
						<div class="form-group mb-5">
							<div class="d-flex justify-content-center align-items-center"><button type="submit" class="btn btn-primary"><?php echo __('Submit'); ?></button></div>
						</div>
					</form>
					<?php
						}
					}
					?>
				</div>	
			</div>	
		</div>	
	</div>	
</div>		
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>
	