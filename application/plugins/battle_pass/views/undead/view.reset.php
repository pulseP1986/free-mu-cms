<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div class="dmn-content">
	<div class="dmn-page-box">
		<div class="dmn-page-title">
			<h1><?php echo __('Reset Battle Pass'); ?></h1>
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
					<h2 class="title"><?php echo __('Reset Battle Pass Progress'); ?></h2>
					<?php
					if(isset($success)){
						echo '<div class="alert alert-success" role="alert">'.$success.'</div>';
					}
					if(isset($error)){
						echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
					}
					?>
					<div class="alert alert-info" role="alert"><?php echo __('Info'); ?>: <?php echo vsprintf(__('Price for reseting battle pass progress %d %s'), [$plugin_config['reset_pass_progress_price'], $this->website->translate_credits($plugin_config['reset_pass_progress_payment_type'], $this->session->userdata(['user' => 'server']))]);?></div>
					<form method="POST" action="" id="reset_battle_pass_form" name="reset_battle_pass_form">
						<div class="form-group mb-5">
							<div class="d-flex justify-content-center align-items-center"><button type="submit" id="reset_progress" name="reset_progress" class="btn btn-primary"><?php echo __('Submit'); ?></button></div>
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
	