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
							<a class="btn btn-primary" style="margin-left: auto;" href="<?php echo $this->config->base_url;?>character-market/sell-character"><?php echo __('Sell Character');?></a> 
							<a class="btn btn-primary" href="<?php echo $this->config->base_url;?>character-market/sale-history"><?php echo __('Sale History');?></a>
						</h2>
						<div class="mb-4"></div>
						<?php if(isset($chars) && !empty($chars)){ ?>
						<table class="table dmn-rankings-table table-striped">
							<thead>
							<tr>
								<th>#</th>
								<th><?php echo __('Character');?></th>
								<th><?php echo __('Merchant');?></th>
								<th><?php echo __('Price + Tax');?> (<?php echo $this->config->values('character_market', array($this->session->userdata(array('user'=>'server')), 'sale_tax'));?>%)</th>
								<th><?php echo __('Class');?></th>
							</tr>
							</thead>
							<tbody>
							<?php foreach($chars as $ch){ ?>
							<tr>
								<td><?php echo $ch['icon'];?></td>
								<td><a href="<?php echo $this->config->base_url;?>character-market/buy/<?php echo $ch['id'];?>"><?php echo $ch['name'];?></a></td>
								<td><?php echo htmlspecialchars($ch['seller']);?></td>
								<td><?php echo $ch['price'];?></td>
								<td><?php echo $ch['class'];?></td>
							</tr>
							<?php } ?>
							</tbody>
						</table>
						<?php  if(isset($pagination)){ ?>	
						<div class="text-center;"><?php echo $pagination; ?></div>	
						<?php } ?>
						<?php } else { ?>
						<div class="alert alert-primary" role="alert"><?php echo __('No Characters Found.');?></div>
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