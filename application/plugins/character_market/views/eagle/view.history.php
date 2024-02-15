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
							<a class="btn btn-primary" style="margin-left: auto;" href="<?php echo $this->config->base_url;?>character-market"><?php echo __('Character Market');?></a>
							<a class="btn btn-primary" href="<?php echo $this->config->base_url;?>character-market/sell-character"><?php echo __('Sell Character');?></a>	
						</h2>
						<div class="mb-4"></div>
						<?php if(isset($chars) && !empty($chars)){ ?>
						<table class="table dmn-rankings-table table-striped">
							<thead>
							<tr>
								<th>#</th>
								<th><?php echo __('Character');?></th>
								<th><?php echo __('Status');?></th>
							</tr>
							</thead>
							<tbody>
							<?php
							$i = 0;
							foreach($chars as $ch){
							$i++;
							?>
							<tr>
								<td><?php echo $i;?></td>
								<td><?php echo $this->Mcharacter_market->get_char_name_by_id($ch['mu_id'], $this->session->userdata(['user' => 'server']))['Name'];?></td>
								<td>
									<?php
										if($ch['is_sold'] == 1){
											echo 'Sold';
										}
										elseif($ch['removed'] == 1){
											echo 'Removed';
										}
										else{
											echo '<a href="'.$this->config->base_url.'character-market/remove/'.$ch['id'].'">Remove</a>';
										}
									?>
								</td>
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