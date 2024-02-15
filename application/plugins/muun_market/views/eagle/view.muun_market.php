<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div class="dmn-content">
	<div class="dmn-page-box">
		<?php if(isset($config_not_found)){ ?>
		<div class="alert alert-danger" role="alert"><?php echo $config_not_found; ?></div>
		<?php } else { ?>
			<?php if(isset($module_disabled)){ ?>
				<div class="alert alert-primary" role="alert"><?php echo $module_disabled; ?></div>
			<?php } else { ?>	
			<div class="dmn-page-title">
				<h1><?php echo __($about['name']); ?></h1>
			</div>
			<div class="dmn-page-content">
				<div class="row">
					<div class="col-12">     
						<h2 class="title d-flex align-items-center">
							<?php echo __($about['user_description']); ?>
							<a class="btn btn-primary" style="margin-left: auto;" href="<?php echo $this->config->base_url;?>muun-market/sell-muun"><?php echo __('Sell Muun');?></a> 
							<a class="btn btn-primary" href="<?php echo $this->config->base_url;?>muun-market/sale-history"><?php echo __('Sale History');?></a>
						</h2>
						<div class="mb-4"></div>
						<?php if(isset($items) && !empty($items)){ ?>
						<script>
							$(document).ready(function () {
								$('span[id^="market_item_"]').each(function () {
									App.initializeTooltip($(this), true, 'warehouse/item_info_image_pet');
								});
							});
						</script>	
						<table class="table dmn-rankings-table table-striped">
							<thead>
							<tr>
								<td>#</td>
								<td><?php echo __('Item');?></td>
								<td><?php echo __('Merchant');?></td>
								<td><?php echo __('Price + Tax');?> (<?php echo $plugin_config['sale_tax'];?>%)</td>
							</tr>
							</thead>
							<tbody>
							<?php foreach($items as $ch){ ?>
							<tr>
								<td><?php echo $ch['icon'];?></td>
								<td><span id="market_item_<?php echo $ch['pos']; ?>" data-info="<?php echo $ch['item']; ?>"><a href="<?php echo $this->config->base_url; ?>muun-market/buy/<?php echo $ch['id']; ?>"><?php echo $ch['name']; ?></a></td>					
								<td><?php echo htmlspecialchars($ch['seller']);?></td>
								
								<td><?php echo $ch['price'];?></td>
							</tr>
							<?php } ?>
							</tbody>
						</table>
						<?php } ?>
						<?php  if(isset($pagination)){ ?>	
						<div class="text-center;"><?php echo $pagination; ?></div>	
						<?php } ?>
					</div>	
				</div>	
			</div>
			<?php } ?>	
		<?php } ?>		
	</div>	
</div>		
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>