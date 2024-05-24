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
						echo '<div class="alert alert-danger" role="alert">' . $config_not_found . '</div>';
					}
					else{
					?>
					<h2 class="title"><?php echo __($about['user_description']); ?></h2>
					<div class="mb-5">
						<?php
							if(isset($error)){
								echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
							}
							if(isset($success)){
								echo '<div class="alert alert-success" role="alert">' . $success . '</div>';
							}
						?>
						<form method="post" action="">
						<table class="table dmn-rankings-table table-striped">
							<thead>
							<tr>
								<th style="text-align: left;padding-left: 15px;" colspan="3"><?php echo __('Details'); ?></th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td style="width:30%;text-align: left;padding-left: 15px;"><?php echo __('Online Hours'); ?></td>
								<td style="width:70%;text-align: left;padding-left: 15px;"><?php echo $online_time; ?></td>
							</tr>
							<tr>
								<td style="width:30%;text-align: left;padding-left: 15px;"><?php echo __('Reward'); ?></td>
								<td style="width:70%;text-align: left;padding-left: 15px;">
									<?php
									$reward = $plugin_config['reward'];
									if($this->session->userdata('vip')){
										$reward += $this->session->userdata(['vip' => 'online_hour_exchange_bonus']);
									}
									echo (int)$reward * $online_time . ' ' . $this->website->translate_credits($plugin_config['reward_method'], $this->session->userdata(['user' => 'server']));
									?>
								</td>
							</tr>
							</tbody>
						</table>
						</form>
						<div class="text-center">
							<button type="submit" class="btn btn-primary" id="trade_hours" name="trade_hours"><?php echo __('Trade Now'); ?></button>
						</div>
					</div>
					<?php } ?>
				</div>	
			</div>	
		</div>	
	</div>	
</div>		
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>