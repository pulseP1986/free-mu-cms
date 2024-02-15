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
			<h2 class="title"><?php echo  __('History');?></h2>
			<div class="entry" >
				<div style="float:right;">
					<a class="custom_button" href="<?php echo $this->config->base_url;?>currency-market"><?php echo __('Currency Market');?></a>
					<a class="custom_button" href="<?php echo $this->config->base_url;?>currency-market/sell-currency"><?php echo __('Sell Currency');?></a>	
				</div>
				<div style="padding-top:40px;"></div>
				<div style="clear:left;"></div>
				<div style="padding-top:20px;"></div>
				<?php if(isset($items) && !empty($items)): ?>
				<table class="ranking-table">
					<thead>
					<tr class="main-tr">
						<td>#</td>
						<td><?php echo __('Info');?></td>
						<td><?php echo __('Status');?></td>					
					</tr>
					</thead>
					<tbody>
					<?php
					$i = 0;
					foreach($items as $sale_info):
					$i++;
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
					<tr>
						<td><?php echo $i;?></td>
						<td>Selling <?php echo $reward;?> for <?php echo $price;?></td>
						<td>
						<?php
							if($sale_info['sold'] == 1){
								echo 'Sold';
							}
							elseif($sale_info['removed'] == 1){
								echo 'Removed';
							}
							else{
								echo '<a href="'.$this->config->base_url.'currency-market/remove/'.$sale_info['id'].'">Remove</a>';
							}
						?>
						</td>
					</tr>
					<?php
					endforeach;
					?>
					</tbody>
				</table>
				<?php 
				else:
				?>
				<div class="w_note"><?php echo __('No Sales Found.');?></div>
				<?php
				endif;
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
	