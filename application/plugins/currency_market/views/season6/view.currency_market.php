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
			<h2 class="title"><?php echo __($about['user_description']); ?></h2>
			<div class="entry" >
				<div style="float:right;">
					<a class="custom_button" href="<?php echo $this->config->base_url;?>currency-market/sell-currency"><?php echo __('Sell currency');?></a>
					<a class="custom_button" href="<?php echo $this->config->base_url;?>currency-market/sale-history"><?php echo __('Sale History');?></a>
				</div>
				<div style="padding-top:40px;"></div>
				<div style="clear:left;"></div>
				<div style="padding-top:20px;"></div>
				<h2>Zen Market</h2>
				<?php 
				if(isset($items1) && !empty($items1)): 
					$order = 1;
					$type = 'DESC';
					if(isset($_SESSION['zen_filder']) && $_SESSION['zen_filder'] == 1){
						$order = 0;
						$type = 'ASC';
					}
					if(isset($_SESSION['zen_filder']) && $_SESSION['zen_filder'] == 0){
						$order = 1;
						$type = 'DESC';
					}
				?>
				<table class="ranking-table">
					<thead>
					<tr class="main-tr">
						<td>#</td>
						<td><?php echo __('Merchant');?></td>
						<td><?php echo __('Reward');?> (<a href="<?php echo $this->config->base_url;?>currency-market?filter=zen&order=<?php echo $order;?>"><?php echo __('Sort');?> <?php echo $type;?></a>)</td>
						<td><?php echo __('Price + Tax');?> (<?php echo $plugin_config['sale_tax'];?>%)</td>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($items1 as $ch):
					?>
					<tr>
						<td><?php echo $ch['icon'];?></td>
						<td><?php echo htmlspecialchars($ch['seller']);?></td>
						<td><a href="<?php echo $this->config->base_url;?>currency-market/buy/<?php echo $ch['id'];?>"><?php echo $ch['reward'];?></a></span></td>					
						<td><?php echo $ch['price'];?></td>
					</tr>
					<?php
					endforeach;
					?>
					</tbody>
				</table>
				<?php
				endif;
				?>
				<div style="padding-top:20px;"></div>
				<h2>Credits Market</h2>
				<?php 
				if(isset($items2) && !empty($items2)):
					$order = 1;
					$type = 'DESC';
					if(isset($_SESSION['credits_filder']) && $_SESSION['credits_filder'] == 1){
						$order = 0;
						$type = 'ASC';
					}
					if(isset($_SESSION['credits_filder']) && $_SESSION['credits_filder'] == 0){
						$order = 1;
						$type = 'DESC';
					}
				?>
				<script>
				$(document).ready(function(){	
					$('#curr1').on('change', function() { 	
						$('#filter_form').submit();
					});
					$('#curr2').on('change', function() { 	
						$('#filter_form').submit();
					});
				});	
				</script>
				<form method="post" action="" id="filter_form" />
					<input type="hidden" name="runfilter" value="1" />
					<span style="float:left;"><input type="checkbox" <?php if(isset($_SESSION['filters']) && strpos($_SESSION['filters'], '1') !== false){ ?>checked<?php } ?> id="curr1" name="filters[]" value="1"> <?php echo $this->website->translate_credits(1, $this->session->userdata(['user' => 'server']));?>&nbsp;&nbsp;</span>
					<span style="float:left;"><input type="checkbox" <?php if(isset($_SESSION['filters']) && strpos($_SESSION['filters'], '2') !== false){ ?>checked<?php } ?> id="curr2" name="filters[]" value="2"> <?php echo $this->website->translate_credits(2, $this->session->userdata(['user' => 'server']));?></span>
				</form>
				<table class="ranking-table">
					<thead>
					<tr class="main-tr">
						<td>#</td>
						<td><?php echo __('Merchant');?></td>
						<td><?php echo __('Reward');?> (<a href="<?php echo $this->config->base_url;?>currency-market?filter=credits&order=<?php echo $order;?>"><?php echo __('Sort');?> <?php echo $type;?></a>)</td>
						<td><?php echo __('Price + Tax');?> (<?php echo $plugin_config['sale_tax'];?>%)</td>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($items2 as $ch):
					?>
					<tr>
						<td><?php echo $ch['icon'];?></td>
						<td><?php echo htmlspecialchars($ch['seller']);?></td>
						<td><a href="<?php echo $this->config->base_url;?>currency-market/buy/<?php echo $ch['id'];?>"><?php echo $ch['reward'];?></a></span></td>					
						<td><?php echo $ch['price'];?></td>
					</tr>
					<?php
					endforeach;
					?>
					</tbody>
				</table>
				<?php
				endif;
				?>
				<?php 
				if(isset($pagination)):
				?>	
				<table style="width: 100%;"><tr><td><?php echo $pagination; ?></td></tr></table>	
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
	