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
					} else{
						if(isset($module_disabled)){
							echo '<div class="alert alert-danger" role="alert">'.$module_disabled.'</div>';
						} else{
					?>	
					<h2 class="title"><?php echo __($about['user_description']); ?></h2>
					<div class="mb-5">
					<?php if(isset($js)){ ?>
					<script src="<?php echo $js;?>"></script>
					<?php } ?>
					<script>
						var creditsExchange = new creditsExchange();
						creditsExchange.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
						$(document).ready(function () {
							$('#credits_exchange_form').on("submit", function (e) {
								e.preventDefault();
								creditsExchange.submit($(this));
							});
						});
					</script>
					<?php
						list($cred, $cred2) = explode('/', $plugin_config['ratio']);
						$cred2_trans = ($plugin_config['exchange_type'] == 1) ? $this->website->translate_credits(2, $this->session->userdata(['user' => 'server'])) : $this->website->translate_credits(1, $this->session->userdata(['user' => 'server']));
					?>
						<div class="alert alert-info" role="alert"><?php echo __('Exchange info'); ?>: <?php echo __('You can get') . ' ' . $cred2 . ' ' . $cred2_trans . ' for ' . $cred . ' ' . $this->website->translate_credits($plugin_config['exchange_type'], $this->session->userdata(['user' => 'server'])); ?></div>
						<form method="post" action="" id="credits_exchange_form">
							<div class="form-group">
								<label class="control-label"><?php echo __('Amount of') . ' ' . $this->website->translate_credits($plugin_config['exchange_type'], $this->session->userdata(['user' => 'server'])); ?></label>
								<input type="text" id="game_currency" name="game_currency" value="" class="form-control" onblur="creditsExchange.calculateCurrency($('#game_currency').val(), '<?php echo $plugin_config['ratio']; ?>');" onkeyup="creditsExchange.calculateCurrency($('#game_currency').val(), '<?php echo $plugin_config['ratio']; ?>');" />
							</div>
							<div class="form-group">
								<label class="control-label"><?php echo __('Will Receive'); ?> <?php echo $cred2_trans; ?></label>
								<input type="text" id="cred2" name="cred2" value="" class="form-control" disabled />
							</div>
							<div class="form-group">
								<div class="d-flex justify-content-center align-items-center"><button type="submit" id="exchange_credits" name="exchange_credits" disabled="disabled" class="btn btn-primary"><?php echo __('Submit'); ?></button></div>
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