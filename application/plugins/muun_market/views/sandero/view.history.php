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
							<?php echo  __('Character History');?>
							<a class="btn btn-primary" style="margin-left: auto;" href="<?php echo $this->config->base_url;?>muun-market"><?php echo __('Muun Market');?></a>
							<a class="btn btn-primary" href="<?php echo $this->config->base_url;?>muun-market/sell-muun"><?php echo __('Sell Muun');?></a>	
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
								<td><?php echo __('Item'); ?></td>
								<td><?php echo __('Merchant'); ?></td>
								<td><?php echo __('Price'); ?></td>
								<td><?php echo __('Status'); ?></td>
							</tr>
							</thead>
							<tbody>
								<?php
								foreach($items as $item){
									if($item['sold'] == 1){
										$status = __('Sold');
									} else if($item['removed'] == 1){
										$status = __('Removed');
									} else{
										$status = '<a href="' . $this->config->base_url . 'muun-market/remove/' . $item['id'] . '">' . __('Remove') . '</a>';
									}
								?>
                                <tr>
                                    <td><?php echo $item['pos']; ?></td>
                                    <td><span id="market_item_<?php echo $item['pos']; ?>" data-info="<?php echo $item['item']; ?>"><?php echo $item['name']; ?></span> </td>
									<td><?php echo $item['seller'];?></td>
                                    <td><?php echo $item['price']; ?></td>
                                    <td><?php echo $status; ?></td>
                                </tr>
								<?php } ?>
							</tbody>
						</table>
						<?php if(isset($pagination)){ ?>	
						<div class="text-center;"><?php echo $pagination; ?></div>	
						<?php } ?>
						<?php } else { ?>
						<div class="alert alert-primary" role="alert"><?php echo __('No Items Found.'); ?></div>
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