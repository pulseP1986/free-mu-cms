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
					<script type="text/javascript">	
					var nganLuong = new nganLuong();
					nganLuong.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
					$(document).ready(function () {
						$('button[id^="buy_nganluong_"]').on('click', function () {
							nganLuong.checkout($(this).attr('id').split('_')[2]);
						});
					});
					</script>
					<?php
						if(isset($error)){
							echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
						} 
						else{
							if(!empty($packages_nganluong)){
								foreach($packages_nganluong as $packages){
									echo '<ul id="paypal-options">
											<li>
												<h4 class="float-left">' . $packages['package'] . '</h4>
												<h3 class="float-left"><span id="reward_' . $packages['id'] . '">' . $packages['reward'] . '</span> ' . $this->website->translate_credits($plugin_config['reward_type'], $this->session->userdata(array('user' => 'server'))) . ' (<span id="price_' . $packages['id'] . '" data-price="' . number_format($packages['price'], 2, '.', ',') . '">' . number_format($packages['price'], 2, '.', ',') . '</span> <span id="currency_' . $packages['id'] . '">' . $packages['currency'] . '</span>)</h3>
												<button class="float-right btn btn-primary" id="buy_nganluong_' . $packages['id'] . '">' . __('Buy Now') . '</button>
											</li>
									</ul>';
								}
							} 
							else{
								echo '<div class="alert alert-primary" role="alert">' . __('No Packages Found.') . '</div>';
							}
						}
					?>
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