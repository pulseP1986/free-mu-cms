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
					<a class="custom_button" href="<?php echo $this->config->base_url;?>muun-market/sell-muun"><?php echo __('Sell Muun');?></a>
					<a class="custom_button" href="<?php echo $this->config->base_url;?>muun-market/sale-history"><?php echo __('Sale History');?></a>
				</div>
				<div style="padding-top:40px;"></div>
				<div style="clear:left;"></div>
				<div style="padding-top:20px;"></div>
				<?php 
				if(isset($items) && !empty($items)): 
				?>
				<script>
                    $(document).ready(function () {
                        $('span[id^="market_item_"]').each(function () {
                            App.initializeTooltip($(this), true, 'warehouse/item_info_image_pet');
                        });
					});
				</script>	
				<table class="ranking-table">
					<thead>
					<tr class="main-tr">
						<td>#</td>
						<td><?php echo __('Item');?></td>
						<td><?php echo __('Merchant');?></td>
						<td><?php echo __('Price + Tax');?> (<?php echo $plugin_config['sale_tax'];?>%)</td>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($items as $ch):
					?>
					<tr>
						<td><?php echo $ch['icon'];?></td>
						<td><span id="market_item_<?php echo $ch['pos']; ?>" data-info="<?php echo $ch['item']; ?>"><a href="<?php echo $this->config->base_url; ?>muun-market/buy/<?php echo $ch['id']; ?>"><?php echo $ch['name']; ?></a></td>					
						<td><?php echo htmlspecialchars($ch['seller']);?></td>
						
						<td><?php echo $ch['price'];?></td>
					</tr>
					<?php
					endforeach;
					?>
					</tbody>
				</table>
				<?php
				endif;
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
	