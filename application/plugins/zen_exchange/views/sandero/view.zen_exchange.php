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
							if(isset($js)){ ?>
							<script src="<?php echo $js; ?>"></script>
							<?php } ?>
							<script>
								var zenExchange = new zenExchange();
								zenExchange.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
								$(document).ready(function () {
									$('#zen_exchange_form').on("submit", function (e) {
										e.preventDefault();
										zenExchange.submit($(this));
									});
								});
							</script>
							<?php
							if(!empty($char_list)){
								list($zen, $cred) = explode('/', $plugin_config['ratio']);
							?>
								<div class="alert alert-info" role="alert"><?php echo __('Exchange info'); ?>:
									<?php echo __('You can get') . ' ' . $cred . ' ' . $this->website->translate_credits($plugin_config['reward_type'], $this->session->userdata(['user' => 'server'])) . ' for ' . $this->website->zen_format($zen) . ' ' . __('Zen'); ?>
								</div>
								<form method="POST" action="" id="zen_exchange_form">
									<div class="form-group">
										<label class="control-label"><?php echo __('Location'); ?></label>
										<div>
											<select name="character" id="character" class="form-control">
												<?php foreach($char_list as $chars){ ?>
												<option value="<?php echo $chars['id']; ?>"><?php echo $chars['Name']; ?></option>
												<?php } ?>
												<option value="-1"><?php echo __('Warehouse'); ?></option>
												<option value="-2"><?php echo __('Zen Wallet'); ?></option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label"><?php echo __('Amount of Zen'); ?></label>
										<input type="text" class="form-control" type="text"  id="zen" name="zen" value="" onblur="zenExchange.calculateCurrency($('#zen').val(), '<?php echo $plugin_config['ratio']; ?>');" onkeyup="zenExchange.calculateCurrency($('#zen').val(), '<?php echo $plugin_config['ratio']; ?>');">
									</div>
									<div class="form-group">
										<label class="control-label"><?php echo __('Will Receive'); ?> <?php echo $this->website->translate_credits($plugin_config['reward_type'], $this->session->userdata(['user' => 'server'])); ?></label>
										<input type="text" class="form-control" id="game_currency" name="game_currency" value="" disabled>
									</div>
									<div class="form-group mb-5">
										<div class="d-flex justify-content-center align-items-center"><button type="submit" id="exchange_zen" name="exchange_zen" class="btn btn-primary" disabled><?php echo __('Submit'); ?></button></div>
									</div>								
								</form>
							<?php
							}
							else{
								echo '<div class="alert alert-info" role="alert">' . __('No characters found.') . '</div>';
							}
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