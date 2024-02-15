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
						echo '<div class="alert alert-danger" role="alert">'.$config_not_found.'</div>';
					} 
					else{
						if(isset($module_disabled)){
							echo '<div class="alert alert-danger" role="alert">'.$module_disabled.'</div>';
						} 
						else{
					?>	
					<h2 class="title"><?php echo __($about['user_description']); ?></h2>
					<link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url; ?>assets/plugins/css/vip_rewards.css?v2">
					<script>
						$(document).ready(function () {
							$('div[id^="rewards-"]').on('click', function () {
								var id = $(this).attr("id").split("-")[1];
								$('div[id^="rewards-list"]:visible').slideToggle();
								$('#rewards-list-' + id + ':hidden').slideToggle();
							});
							
							$('span[id^="ach_ritem_"]').each(function () {
								App.initializeTooltip($(this), true, 'warehouse/item_info_image');
							});	
							
							
						});
					</script>
					<?php if(!empty($reward_list[$vip_package['viptype']])){ ?>
					<?php if(!empty($reward_list[$vip_package['viptype']][1]) && $reward_list[$vip_package['viptype']][1]['status'] == 1){ ?>
					<div class="character" id="rewards-1">
						<div class="title-name"><?php echo $this->Mvip_rewards->get_vip_package_title($vip_package['viptype'], $plugin_config['vip_type']); ?>
							<span><?php echo __('Daily');?></span>
						</div>
					</div>
					<div id="rewards-list-1" style="display: none;width: 100%;text-align: center;">
						<table class="table dmn-rankings-table table-striped" style="width: 612px;margin: 0 auto;">
							<thead>
								<tr>
									<th colspan="2"><?php echo __('Rewards'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php if(isset($reward_list[$vip_package['viptype']][1]['credits1']) && $reward_list[$vip_package['viptype']][1]['credits1'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo $this->website->translate_credits(1, $this->session->userdata(['user' => 'server']));?></td>
									<td style="text-align:left;"><?php echo $reward_list[$vip_package['viptype']][1]['credits1'];?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][1]['credits2']) && $reward_list[$vip_package['viptype']][1]['credits2'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo $this->website->translate_credits(2, $this->session->userdata(['user' => 'server']));?></td>
									<td style="text-align:left;"><?php echo $reward_list[$vip_package['viptype']][1]['credits2'];?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][1]['wcoin']) && $reward_list[$vip_package['viptype']][1]['wcoin'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('WCoins'); ?></td>
									<td style="text-align:left;"><?php echo $reward_list[$vip_package['viptype']][1]['wcoin'];?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][1]['goblin']) && $reward_list[$vip_package['viptype']][1]['goblin'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('GoblinPoint'); ?></td>
									<td style="text-align:left;"><?php echo $reward_list[$vip_package['viptype']][1]['goblin'];?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][1]['zen']) && $reward_list[$vip_package['viptype']][1]['zen'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('Zen');?></td>
									<td style="text-align:left;"><?php echo $this->website->zen_format($reward_list[$vip_package['viptype']][1]['zen']);?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][1]['credits3']) && $reward_list[$vip_package['viptype']][1]['credits3'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo $this->website->translate_credits(3, $this->session->userdata(['user' => 'server']));?></td>
									<td style="text-align:left;"><?php echo $this->website->zen_format($reward_list[$vip_package['viptype']][1]['credits3']);?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][1]['ruud']) && $reward_list[$vip_package['viptype']][1]['ruud'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('Ruud');?></td>
									<td style="text-align:left;"><?php echo $this->website->zen_format($reward_list[$vip_package['viptype']][1]['ruud']);?></td>
								</tr>
								<?php } ?>
								<?php if(!empty($reward_list[$vip_package['viptype']][1]['items'])){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('Items');?></td>
									<td style="text-align:left;">
										<?php	
										if(!empty($reward_items[1])){
											echo '<div>';
											foreach($reward_items[1] AS $key => $item){
												echo '<span id="ach_ritem_'.$key.'" data-info="'.$item['hex'].'">'.$item['name'].'</span>';
											}
											echo '</div>';
										}
										?>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<div class="d-flex justify-content-center align-items-center mb-1 mt-1">
							<?php if($this->Mvip_rewards->checkClaimedReward(1, $vip_package['viptype'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server'])) == false){ ?>
							<a class="btn btn-primary" href="<?php echo $this->config->base_url;?>vip-rewards/claim/1/<?php echo $vip_package['viptype'];?>"><?php echo __('Get Reward');?></a>
							<?php } else { ?>
							<div class="alert alert-warning" role="alert"><?php echo __('Vip reward already claimed');?></div>
							<?php } ?>
						</div>
					</div>
					<?php } ?>
					<?php if(!empty($reward_list[$vip_package['viptype']][2]) && $reward_list[$vip_package['viptype']][2]['status'] == 1){ ?>
					<div class="character" id="rewards-2">
						<div class="title-name"><?php echo $this->Mvip_rewards->get_vip_package_title($vip_package['viptype'], $plugin_config['vip_type']); ?>
							<span><?php echo __('Weekly');?></span>
						</div>
					</div>
					<div id="rewards-list-2" style="display: none;width: 100%;text-align: center;">
						<table class="table dmn-rankings-table table-striped" style="width: 612px;margin: 0 auto;">
							<thead>
								<tr class="main-tr">
									<th colspan="2"><?php echo __('Rewards'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php if(isset($reward_list[$vip_package['viptype']][2]['credits1']) && $reward_list[$vip_package['viptype']][2]['credits1'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo $this->website->translate_credits(1, $this->session->userdata(['user' => 'server']));?></td>
									<td style="text-align:left;"><?php echo $reward_list[$vip_package['viptype']][2]['credits1'];?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][2]['credits2']) && $reward_list[$vip_package['viptype']][2]['credits2'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo $this->website->translate_credits(2, $this->session->userdata(['user' => 'server']));?></td>
									<td style="text-align:left;"><?php echo $reward_list[$vip_package['viptype']][2]['credits2'];?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][2]['wcoin']) && $reward_list[$vip_package['viptype']][2]['wcoin'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('WCoins'); ?></td>
									<td style="text-align:left;"><?php echo $reward_list[$vip_package['viptype']][2]['wcoin'];?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][2]['goblin']) && $reward_list[$vip_package['viptype']][2]['goblin'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('GoblinPoint'); ?></td>
									<td style="text-align:left;"><?php echo $reward_list[$vip_package['viptype']][2]['goblin'];?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][2]['zen']) && $reward_list[$vip_package['viptype']][2]['zen'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('Zen');?></td>
									<td style="text-align:left;"><?php echo $this->website->zen_format($reward_list[$vip_package['viptype']][2]['zen']);?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][2]['credits3']) && $reward_list[$vip_package['viptype']][2]['credits3'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo $this->website->translate_credits(3, $this->session->userdata(['user' => 'server']));?></td>
									<td style="text-align:left;"><?php echo $this->website->zen_format($reward_list[$vip_package['viptype']][2]['credits3']);?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][2]['ruud']) && $reward_list[$vip_package['viptype']][2]['ruud'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('Ruud');?></td>
									<td style="text-align:left;"><?php echo $this->website->zen_format($reward_list[$vip_package['viptype']][2]['ruud']);?></td>
								</tr>
								<?php } ?>
								<?php if(!empty($reward_list[$vip_package['viptype']][2]['items'])){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('Items');?></td>
									<td style="text-align:left;">
										<?php	
										if(!empty($reward_items[2])){
											echo '<div>';
											foreach($reward_items[2] AS $key => $item){
												echo '<span id="ach_ritem_'.$key.'" data-info="'.$item['hex'].'">'.$item['name'].'</span>';
											}
											echo '</div>';
										}
										?>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<div class="d-flex justify-content-center align-items-center mb-1 mt-1">
							<?php if($this->Mvip_rewards->checkClaimedReward(2, $vip_package['viptype'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server'])) == false){ ?>
							<a class="btn btn-primary" href="<?php echo $this->config->base_url;?>vip-rewards/claim/2/<?php echo $vip_package['viptype'];?>"><?php echo __('Get Reward');?></a>
							<?php } else { ?>
							<div class="alert alert-warning" role="alert"><?php echo __('Vip reward already claimed');?></div>
							<?php } ?>
						</div>	
					</div>
					<?php } ?>
					<?php if(!empty($reward_list[$vip_package['viptype']][3]) && $reward_list[$vip_package['viptype']][3]['status'] == 1){ ?>
					<div class="character" id="rewards-3">
						<div class="title-name"><?php echo $this->Mvip_rewards->get_vip_package_title($vip_package['viptype'], $plugin_config['vip_type']); ?>
							<span><?php echo __('Monthly');?></span>
						</div>
					</div>
					<div id="rewards-list-3" style="display: none;width: 100%;text-align: center;">
						<table class="table dmn-rankings-table table-striped" style="width: 612px;margin: 0 auto;">
							<thead>
								<tr class="main-tr">
									<th colspan="2"><?php echo __('Rewards'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php if(isset($reward_list[$vip_package['viptype']][3]['credits1']) && $reward_list[$vip_package['viptype']][3]['credits1'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo $this->website->translate_credits(1, $this->session->userdata(['user' => 'server']));?></td>
									<td style="text-align:left;"><?php echo $reward_list[$vip_package['viptype']][3]['credits1'];?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][3]['credits2']) && $reward_list[$vip_package['viptype']][3]['credits2'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo $this->website->translate_credits(2, $this->session->userdata(['user' => 'server']));?></td>
									<td style="text-align:left;"><?php echo $reward_list[$vip_package['viptype']][3]['credits2'];?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][3]['wcoin']) && $reward_list[$vip_package['viptype']][3]['wcoin'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('WCoins'); ?></td>
									<td style="text-align:left;"><?php echo $reward_list[$vip_package['viptype']][3]['wcoin'];?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][3]['goblin']) && $reward_list[$vip_package['viptype']][3]['goblin'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('GoblinPoint'); ?></td>
									<td style="text-align:left;"><?php echo $reward_list[$vip_package['viptype']][3]['goblin'];?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][3]['zen']) && $reward_list[$vip_package['viptype']][3]['zen'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('Zen');?></td>
									<td style="text-align:left;"><?php echo $this->website->zen_format($reward_list[$vip_package['viptype']][3]['zen']);?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][3]['credits3']) && $reward_list[$vip_package['viptype']][3]['credits3'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo $this->website->translate_credits(3, $this->session->userdata(['user' => 'server']));?></td>
									<td style="text-align:left;"><?php echo $this->website->zen_format($reward_list[$vip_package['viptype']][3]['credits3']);?></td>
								</tr>
								<?php } ?>
								<?php if(isset($reward_list[$vip_package['viptype']][3]['ruud']) && $reward_list[$vip_package['viptype']][3]['ruud'] > 0){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('Ruud');?></td>
									<td style="text-align:left;"><?php echo $this->website->zen_format($reward_list[$vip_package['viptype']][3]['ruud']);?></td>
								</tr>
								<?php } ?>
								<?php if(!empty($reward_list[$vip_package['viptype']][3]['items'])){ ?>
								<tr>
									<td style="text-align:left;"><?php echo __('Items');?></td>
									<td style="text-align:left;">
										<?php	
										if(!empty($reward_items[3])){
											echo '<div>';
											foreach($reward_items[3] AS $key => $item){
												echo '<span id="ach_ritem_'.$key.'" data-info="'.$item['hex'].'">'.$item['name'].'</span>';
											}
											echo '</div>';
										}
										?>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<div class="d-flex justify-content-center align-items-center mb-1 mt-1">
							<?php if($this->Mvip_rewards->checkClaimedReward(3, $vip_package['viptype'], $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server'])) == false){ ?>
							<a class="btn btn-primary" href="<?php echo $this->config->base_url;?>vip-rewards/claim/3/<?php echo $vip_package['viptype'];?>"><?php echo __('Get Reward');?></a>
							<?php } else { ?>
							<div class="alert alert-warning" role="alert"><?php echo __('Vip reward already claimed');?></div>
							<?php } ?>
						</div>	
					</div>
					<?php } ?>
					<?php } ?>
					</div>
					<?php
						}
					}
					?>
				</div>	
			</div>	
		</div>	
	</div>	
</div>		
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>