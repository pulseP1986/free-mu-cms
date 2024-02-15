<?php
	$this->load->view($this->config->config_entry('main|template').DS.'view.header');
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
		<div class="box-style1" style="margin-bottom: 20px;">
			<div class="entry" >
				<div style="float:right;">
					<a class="custom_button" href="<?php echo $this->config->base_url;?>currency-market"><?php echo __($about['name']); ?></a>
					<a class="custom_button" href="<?php echo $this->config->base_url;?>currency-market/sell-currency"><?php echo __('Sell Currency');?></a>
					<a class="custom_button" href="<?php echo $this->config->base_url;?>currency-market/sale-history"><?php echo __('Sale History');?></a>
				</div>
				<div style="padding-top:50px;"></div>
				<?php
				if(isset($error)){
					echo '<div class="e_note">'.$error.'</div>';
				}
				else{
					if(isset($purchase_error)){
						echo '<div class="e_note">'.$purchase_error.'</div>';
					}
					switch($sale_info['price_type']){
						case 1:
						case 2:
							$price = round(($sale_info['price'] / 100) * $plugin_config['sale_tax'] + $sale_info['price']) . ' <b>' . $this->website->translate_credits($sale_info['price_type'], $this->session->userdata(['user' => 'server'])) . '</b>';
							$reward = ($sale_info['reward_type'] == 3) ? $this->website->zen_format($sale_info['reward']) . ' <b>Zen</b>' : $sale_info['reward'] . ' <b>' . $this->website->translate_credits($sale_info['reward_type'], $this->session->userdata(['user' => 'server'])) . '</b>';
						break;
						case 3:
							$price = $this->website->zen_format(round(($sale_info['price'] / 100) * $plugin_config['sale_tax'] + $sale_info['price'])) . ' <b>Zen</b>';
							$reward = $sale_info['reward'] . ' <b>' . $this->website->translate_credits($sale_info['reward_type'], $this->session->userdata(['user' => 'server'])) . '</b>';
						break;
					}
				?>
				<table class="ranking-table">
					<thead>
					<tr class="main-tr">
						<td colspan="2" style="padding-left: 15px;"><?php echo __('Information');?></td>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td style="width: 240px;">
							<table style="width:100%;margin: 0 auto;">
								<tr>
                                    <td style="width:30%;text-align: left;padding-left: 15px;"><?php echo __('Reward'); ?>:</td>
                                    <td style="width:30%;text-align: left;padding-left: 15px;"><?php echo $reward;?></td>
                                </tr>
								<tr>
                                    <td style="width:30%;text-align: left;padding-left: 15px;"><?php echo __('Merchant'); ?>:</td>
                                    <td style="width:30%;text-align: left;padding-left: 15px;"><a href="<?php echo $this->config->base_url; ?>info/character/<?php echo bin2hex($sale_info['seller']); ?>/<?php echo $this->session->userdata(['user' => 'server']); ?>"><?php echo htmlspecialchars($sale_info['seller']); ?></a></td>
                                </tr>
								<tr>
                                    <td style="width:30%;text-align: left;padding-left: 15px;"><?php echo __('Start Time'); ?>:</td>
                                    <td style="width:30%;text-align: left;padding-left: 15px;"><?php echo date(DATETIME_FORMAT, strtotime($sale_info['add_date'])); ?></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;text-align: left;padding-left: 15px;"><?php echo __('End Time'); ?>:</td>
                                    <td style="width:30%;text-align: left;padding-left: 15px;"><?php echo date(DATETIME_FORMAT, strtotime($sale_info['active_till'])); ?></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;text-align: left;padding-left: 15px;"><?php echo __('Price'); ?>: </td>
                                    <td style="width:30%;text-align: left;padding-left: 15px;"><?php echo $price;?></td>
                                </tr>
							</table>					
						</td>
					</tr>
					</tbody>
				</table>
				<table style="text-align: center;width:100%;padding:10px;">
					<tr>
						<td style="text-align: center;"><form method="post" action=""><button type="submit" class="button-style2" value="buy" name="buy"><?php echo __('Buy Now'); ?></button></form></td>
					</tr>
				</table>
				<?php
				}
				?>
				
			</div>
		</div>
		<?php
			endif;
		endif;
		?>
	</div>
</div>
<?php
	$this->load->view($this->config->config_entry('main|template').DS.'view.right_sidebar');
	$this->load->view($this->config->config_entry('main|template').DS.'view.footer');
?>

	