<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div class="dmn-content">
	<div class="dmn-page-box">
		<div class="dmn-page-title">
			<h1><?php echo __($about['name']); ?></h1>
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
					<h2 class="title"><?php echo __($about['user_description']); ?> <span class="float-right"><a class="btn btn-primary" href="<?php echo $this->config->base_url;?>level-rewards"><?php echo __($about['name']); ?></a></span></h2>
					<div class="mb-5">
						<?php
						if(isset($error)){
							echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
						}
						if(isset($success)){
							echo '<div class="alert alert-success" role="alert">'.$success.'</div>';
						}
						?>
						<form method="post" action="" id="claim_reward">
							<div class="form-group">
								<label class="control-label"><?php echo __('Character');?></label>
								<select class="form-control" name="character" id="character">
								<?php foreach($characters as $char): ?>
									<option value="<?php echo $char['id'];?>"><?php echo $char['name'];?></option>
								<?php endforeach; ?>
								</select>
							</div>
							<div class="form-group">
								<div class="d-flex justify-content-center align-items-center"><button type="submit" id="claim" name="claim" class="btn btn-primary"><?php echo __('Redeem'); ?></button></div>
							</div>	
						</form>
					</div>
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