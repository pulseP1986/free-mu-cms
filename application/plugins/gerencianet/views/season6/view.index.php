<?php
$this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
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
		<div id="content_center">
			<div class="box-style1" style="margin-bottom:55px;">
				<h2 class="title"><?php echo __($about['user_description']); ?></h2>
				<div class="entry">
					<?php if(isset($js)): ?>
					<script src="<?php echo $js;?>"></script>
					<?php endif;?>
					<?php
					if(isset($error)){
						echo '<div class="e_note">' . $error . '</div>';
					} 
					else{
						if(!empty($packages)){
							foreach($packages as $packages){
								echo '<ul id="paypal-options">
										<li>
											<h4 class="left">' . $packages['package'] . '</h4>
											<h3 class="left"><span id="reward_' . $packages['id'] . '">' . $packages['reward'] . '</span> ' . $this->website->translate_credits($plugin_config['reward_type'], $this->session->userdata(array('user' => 'server'))) . ' (<span id="price_' . $packages['id'] . '" data-price="' . number_format($packages['price'], 2, '.', ',') . '">' . number_format($packages['price'], 2, '.', ',') . '</span> <span id="currency_' . $packages['id'] . '">' . $packages['currency'] . '</span>)</h3>
											<a href="'.$this->config->base_url . $this->request->get_controller().'/checkout/' . $packages['id'] . '" class="right custom_button" value="buy_' . $packages['id'] . '">' . __('Buy Now') . '</a>
										</li>
								</ul>';
							}
						} 
						else{
							echo '<div class="i_note">' . __('No Packages Found.') . '</div>';
						}
					}
					?>
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

