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
					<link rel="stylesheet" href="<?php echo $this->config->base_url; ?>assets/plugins/css/battle_pass.css">
					<link rel="stylesheet" href="<?php echo $this->config->base_url; ?>assets/plugins/css/jquery.alertable.css">
					<script type="text/javascript" src="<?php echo $this->config->base_url; ?>assets/plugins/js/jquery.alertable.min.js" /></script>
					<div class="bBox">
						<div class="bUserData">
							<div class="bPassType">
								<?php echo $pass_title;?> <?php echo __('Pass');?>
							</div>
							<div class="bUpgradeButton">
								<?php if(in_array($pass['pass_type'], [0, 1])){ ?>
								<a href="<?php echo $this->config->base_url;?>battle-pass/upgrade"><?php echo __('Upgrade Now');?></a>
								<?php } else { ?>
								<span><?php echo __('Maxed');?></span>
								<?php } ?>
							</div>
						</div>	
						<div class="bUserData">
							<div class="bPassType">
								<?php echo __('Season ends in');?>
							</div>
							<div class="bPassType">
								<?php 
								if(strtotime($plugin_config['battle_pass_end_time']) < time()){
									$countDown = '';
								}
								else{
									$countDown = $this->website->date_diff($plugin_config['battle_pass_start_time'], $plugin_config['battle_pass_end_time']);
								}
								if($countDown != ''){ 
								?>
								<span class="bCountdown"><?php echo $countDown;?></span>
								<?php } else { ?>
								<span class="bCountdown"><?php echo __('Season Ended');?></span>
								<?php } ?>
							</div>
						</div>	
					</div>
					<div class="bTitleBox">
						<div class="bTitleDataMain bUpgradeButton">
							<img src="<?php echo $this->config->base_url;?>assets/plugins/images/freePass.png" title="Free Pass" style="width: 100px;" />
							<div><?php echo __('Total Free Wcoins');?>: <b><?php echo $free_wcoins;?></b></div>
						</div>
						<div class="bTitleDataMain bUpgradeButton">
							<img src="<?php echo $this->config->base_url;?>assets/plugins/images/silverPass.png" title="Silver Pass" style="width: 100px;" />
							<div><?php echo __('Total Free Wcoins');?>: <b><?php echo $silver_wcoins;?></b></div>
						</div>
						<div class="bTitleDataMain bUpgradeButton">
							<img src="<?php echo $this->config->base_url;?>assets/plugins/images/platinumPass.png" title="Platinum Pass"  style="width: 100px;" />
							<div><?php echo __('Total Free Wcoins');?>: <b><?php echo $platinum_wcoins;?></b></div>
						</div>
					</div>
					<?php 
					if($progress['pass_level'] == -1)
						$progress['pass_level'] = $last_completed_level;
					if(!empty($pass_levels)){
						$i = 1;
						foreach($pass_levels AS $key => $dayData){
					?>
						<div class="dayName" style="text-align:center;font-weight:bold;display:block;"><?php echo __('Day');?> <?php echo $i;?> <?php if($progress['pass_level'] == $dayData['id']){ ?>(<?php echo __('Current');?>)<?php } ?></div>
						<div class="bRTitleBox">
							<div class="bTitleData">
								<div class="bRewardData">
									<?php if(isset($dayData['free_pass_image']) && $dayData['free_pass_image'] != ''){ ?>
									<div class="bRewardImage">
										<img src="<?php echo $dayData['free_pass_image'];?>" />
									</div>
									<?php } ?>
									<div style="margin-left: 5px;">
										<?php
											$fr1 = '';
											$fr2 = '';
											$rewardStatus = $this->Mbattle_pass->checkLevelStatus($this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']), $plugin_config['battle_pass_start_time'], $dayData['id']);
											if($rewardStatus == false){
												$rewardStatus['is_completed'] = 0;
												$rewardStatus['is_free_reward_taken'] = 0;
												$rewardStatus['is_silver_reward_taken'] = 0;
												$rewardStatus['is_platinum_reward_taken'] = 0;
											}
											if(!empty($pass_rewards[$dayData['id']])){
												$a = 0;
												foreach($pass_rewards[$dayData['id']] AS $fKey => $fRewardData){
													if($fRewardData['pass_type'] == 0){
														if($a == 0){
															if(!in_array($fRewardData['reward_type'], [8,9,10,11])){
																if(in_array($fRewardData['reward_type'], [5,6,7])){
																	$fRewardData['amount'] = $this->website->zen_format($fRewardData['amount']);
																}
																$fr1 = $fRewardData['amount'].' '.$this->Mbattle_pass->getRewardTypeData($fRewardData['reward_type'], $this->session->userdata(['user' => 'server']));
															}
															else{
																if($fRewardData['reward_type'] == 11){
																	$fr1 = $fRewardData['title'];
																}
																else{
																	if($fRewardData['reward_type'] == 8 && $fRewardData['vip_type'] != ''){
																		$vipData = $this->Mbattle_pass->get_vip_package_title($fRewardData['vip_type']);
																		$fr1 = $vipData['package_title'].' ['.$this->website->seconds2days($vipData['vip_time']).']';
																	}
																	else{
																		if(!empty($fRewardData['item_data'])){
																			$fr1 = '<span id="main_ritem_free_'.$i.'_0" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span>';
																		}
																	}
																}
															}
														}

														if($a == 1){
															if(!in_array($fRewardData['reward_type'], [8,9,10,11])){
																if(in_array($fRewardData['reward_type'], [5,6,7])){
																	$fRewardData['amount'] = $this->website->zen_format($fRewardData['amount']);
																}
																$fr2 = $fRewardData['amount'].' '.$this->Mbattle_pass->getRewardTypeData($fRewardData['reward_type'], $this->session->userdata(['user' => 'server']));
															}
															else{
																if($fRewardData['reward_type'] == 11){
																	$fr2 = $fRewardData['title'];
																}
																else{
																	if($fRewardData['reward_type'] == 8 && $fRewardData['vip_type'] != ''){
																		$vipData = $this->Mbattle_pass->get_vip_package_title($fRewardData['vip_type']);
																		$fr2 = $vipData['package_title'].' ['.$this->website->seconds2days($vipData['vip_time']).']';
																	}
																	else{
																		if(!empty($fRewardData['item_data'])){
																			$fr2 = '<span id="main_ritem_free_'.$i.'_1" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span>';
																		}
																	}
																}
															}
														}
														$a++;
													}
												}
											}
										?>	
										<div style="text-align:left;">
											<div class="bRewardMain"><?php echo $fr1;?></div>
											<?php if($fr2 != '') { ?><div class="bRewardsSecond">+<?php echo $fr2;?></div><?php } ?>
											<div class="bStatusButton <?php if($rewardStatus['is_completed'] == 1){ ?>bComplete<?php } ?>">
											<?php if($rewardStatus['is_completed'] == 0){ ?>
												<?php echo __('Incomplete');?>
											<?php } else { ?>
												<?php if($rewardStatus['is_free_reward_taken'] == 0){ ?>
													<a class="get_reward" href="javascript:;" id="reward_free_<?php echo $i; ?>" data-action="<?php echo  $this->config->base_url.'battle-pass/claim/'.$dayData['id']; ?>/0"><?php echo __('Claim reward');?></a>
												<?php } else { ?>
													<?php echo __('Completed');?>
												<?php } ?>
											<?php } ?>
											</div>
										</div>
									</div>
								</div>
								<div class="bAttached">
									<div class="bOpenClose">
										<?php if($progress['pass_level'] == $dayData['id']){ ?>
										<a class="show-hide-open" href="#" data-open="true" data-id=".bDetails-<?php echo $i+1;?>"><?php echo __('Show Details');?></a>
										<?php } else { ?>
										<a class="show-hide-open" href="#" data-open="false" data-id=".bDetails-<?php echo $i+1;?>"><?php echo __('Show Details');?></a>
										<?php } ?>
									</div>
									<div class="bDetails-<?php echo $i+1;?>" style="<?php if($progress['pass_level'] == $dayData['id']){ ?>display:block;<?php } else { ?>display: none;<?php } ?>">
										<h4><?php echo __('Requirements');?></h4>
										<ul>
											<?php 
											if(!empty($pass_requirements[$dayData['id']])){
												foreach($pass_requirements[$dayData['id']] AS $rkey => $requirement){
													$amount = 0;
													$title = $requirement['title'];
													if($requirement['req_type'] == 1 || $requirement['req_type'] == 11){
														$amount = $requirement['total_votes'];
													}
													if($requirement['req_type'] == 2){
														$amount = $requirement['total_donate'];
													}
													if($requirement['req_type'] == 3){
														$amount = $requirement['mamount'];
														$title = __('Kill').' '.$this->npc->name_by_id($requirement['monsters']);
													}
													if($requirement['req_type'] == 4){
														$amount = $requirement['total_kills'];
													}
													if($requirement['req_type'] == 5 || $requirement['req_type'] == 6 || $requirement['req_type'] == 9){
														$amount = $requirement['total_stats'];
													}
													if($requirement['req_type'] == 7){
														$amount = $requirement['total_items_buy'];
													}
													if($requirement['req_type'] == 8){
														$amount = $requirement['total_items_sell'];
													}
													if($requirement['req_type'] == 12){
														$amount = $requirement['enter_count'];
													}
													if($requirement['req_type'] == 10){
														$amount = $requirement['item']['amount'];
														$title = __('Collect').' <span class="collectItem" data-info="'.$requirement['item']['hex'].'">'.strip_tags($requirement['item']['name']).'</span>';
													}
													$completeProgress = 0;
													$progressPerc = 0;
													if($progress['pass_level'] == $dayData['id']){
														if(isset($user_requirement_data[$progress['pass_level']][$rkey]['completed']) && $user_requirement_data[$progress['pass_level']][$rkey]['completed'] == 1){
															$completeProgress = $amount;
															$progressPerc = 100;
														}
														else{
															if(isset($user_requirement_data[$progress['pass_level']][$rkey]['completeProgress'])){
																$completeProgress = $user_requirement_data[$progress['pass_level']][$rkey]['completeProgress'];
																$progressPerc = $user_requirement_data[$progress['pass_level']][$rkey]['progressPerc'];
															}
															else{
																$completeProgress = 0;
																$progressPerc = 0;

															}
														}
													}
													if($rewardStatus['is_completed'] == 1){
														$completeProgress = $amount;
														$progressPerc = 100;
													}
											?>
											<li>
												<div><?php echo $title;?></div>
												<div class="bProgressBar">
													<div class="bProgressPerc bProgressRed" style="width: <?php echo $progressPerc;?>%;"></div>
												</div>
												<div class="bProgressCount" <?php if($progressPerc == 100){?>style="color: #fff;"<?php } ?>><?php echo $completeProgress;?> / <?php echo $amount;?></div>
											</li>
											<?php
												}
											}
											?>
										</ul> 
										<?php
										$b = 0;
										$afr = '';
										if(isset($pass_rewards[$dayData['id']])){
											foreach($pass_rewards[$dayData['id']] AS $fKey => $fRewardData){
												if($fRewardData['pass_type'] == 0){
													if($b >= 2){
														if(!in_array($fRewardData['reward_type'], [8,9,10,11])){
															if(in_array($fRewardData['reward_type'], [5,6,7])){
																$fRewardData['amount'] = $this->website->zen_format($fRewardData['amount']);
															}
															$afr .= '<li>'.$fRewardData['amount'].' '.$this->Mbattle_pass->getRewardTypeData($fRewardData['reward_type'], $this->session->userdata(['user' => 'server'])).'</li>';
														}
														else{
															if($fRewardData['reward_type'] == 11){
																$afr .= '<li>'.$fRewardData['title'].'</li>';
															}
															else{
																if($fRewardData['reward_type'] == 8 && $fRewardData['vip_type'] != ''){
																	$vipData = $this->Mbattle_pass->get_vip_package_title($fRewardData['vip_type']);
																	$afr .= '<li>'.$vipData['package_title'].' ['.$this->website->seconds2days($vipData['vip_time']).']'.'</li>';
																}
																else{
																	if(!empty($fRewardData['item_data'])){
																		$afr .= '<li><span id="additional_ritem_free_'.$i.'_'.$b.'" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span></li>';
																	}
																}
															}
														}
													}
													$b++;
												}	
											}
										}
										if($afr != ''){
										?>
										<h4><?php echo __('Additional reward');?></h4>	
										<ul>
											<?php echo $afr;?>
										</ul>
										<?php } ?>
									</div>
								</div>
							</div>
							<div class="bTitleData">
								<div class="bRewardData">
									<?php if(isset($dayData['silver_pass_image']) && $dayData['silver_pass_image'] != ''){ ?>
									<div class="bRewardImage">
										<img src="<?php echo $dayData['silver_pass_image'];?>" />
									</div>
									<?php } ?>
									<div style="margin-left: 5px;">
										<?php
											$fr1 = '';
											$fr2 = '';
											if(!empty($pass_rewards[$dayData['id']])){
												$a = 0;
												foreach($pass_rewards[$dayData['id']] AS $fKey => $fRewardData){
													if($fRewardData['pass_type'] == 1){
														if($a == 0){
															if(!in_array($fRewardData['reward_type'], [8,9,10,11])){
																if(in_array($fRewardData['reward_type'], [5,6,7])){
																	$fRewardData['amount'] = $this->website->zen_format($fRewardData['amount']);
																}
																$fr1 = $fRewardData['amount'].' '.$this->Mbattle_pass->getRewardTypeData($fRewardData['reward_type'], $this->session->userdata(['user' => 'server']));
															}
															else{
																if($fRewardData['reward_type'] == 11){
																	$fr1 = $fRewardData['title'];
																}
																else{
																	if($fRewardData['reward_type'] == 8 && $fRewardData['vip_type'] != ''){
																		$vipData = $this->Mbattle_pass->get_vip_package_title($fRewardData['vip_type']);
																		$fr1 = $vipData['package_title'].' ['.$this->website->seconds2days($vipData['vip_time']).']';
																	}
																	else{
																		if(!empty($fRewardData['item_data'])){
																			$fr1 = '<span id="main_ritem_silver_'.$i.'_0" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span>';
																		}
																	}
																}
															}
														}

														if($a == 1){
															if(!in_array($fRewardData['reward_type'], [8,9,10,11])){
																if(in_array($fRewardData['reward_type'], [5,6,7])){
																	$fRewardData['amount'] = $this->website->zen_format($fRewardData['amount']);
																}
																$fr2 = $fRewardData['amount'].' '.$this->Mbattle_pass->getRewardTypeData($fRewardData['reward_type'], $this->session->userdata(['user' => 'server']));
															}
															else{
																if($fRewardData['reward_type'] == 11){
																	$fr2 = $fRewardData['title'];
																}
																else{
																	if($fRewardData['reward_type'] == 8 && $fRewardData['vip_type'] != ''){
																		$vipData = $this->Mbattle_pass->get_vip_package_title($fRewardData['vip_type']);
																		$fr2 = $vipData['package_title'].' ['.$this->website->seconds2days($vipData['vip_time']).']';
																	}
																	else{
																		if(!empty($fRewardData['item_data'])){
																			$fr2 = '<span id="main_ritem_silver_'.$i.'_1" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span>';
																		}
																	}
																}
															}
														}
														$a++;
													}
												}
											}
										?>
										<div style="text-align:left;">
											<div class="bRewardMain"><?php echo $fr1;?></div>
											<?php if($fr2 != '') { ?><div class="bRewardsSecond">+<?php echo $fr2;?></div><?php } ?>
											<div class="bStatusButton <?php if($rewardStatus['is_completed'] == 1 && in_array($pass['pass_type'], [1,2])){ ?>bComplete<?php } ?>">
											<?php if(in_array($pass['pass_type'], [1,2])){ ?>
											<?php if($rewardStatus['is_completed'] == 0){ ?>
												<?php echo __('Incomplete');?>
											<?php } else { ?>
												<?php if($rewardStatus['is_silver_reward_taken'] == 0){ ?>
													<a class="get_reward" href="javascript:;" id="reward_silver_<?php echo $i; ?>" data-action="<?php echo  $this->config->base_url.'battle-pass/claim/'.$dayData['id']; ?>/1"><?php echo __('Claim reward');?></a>
												<?php } else { ?>
													<?php echo __('Completed');?>
												<?php } ?>
											<?php } ?>
											<?php } else { ?>
												<?php echo __('Upgrade pass');?>
											<?php } ?>
											</div>
										</div>
									</div>
								</div>
								<div class="bAttached">
									<div class="bOpenClose">
										<?php if($progress['pass_level'] == $dayData['id']){ ?>
										<a class="show-hide-open" href="#" data-open="true" data-id=".bDetails-<?php echo $i+1;?>"><?php echo __('Show Details');?></a>
										<?php } else { ?>
										<a class="show-hide-open" href="#" data-open="false" data-id=".bDetails-<?php echo $i+1;?>"><?php echo __('Show Details');?></a>
										<?php } ?>
									</div>
									<div class="bDetails-<?php echo $i+1;?>" style="<?php if($progress['pass_level'] == $dayData['id']){ ?>display:block;<?php } else { ?>display: none;<?php } ?>">
										<h4><?php echo __('Requirements');?></h4>
										<ul>
											<li>
												<div><?php echo __('Complete').' '.__('day');?> <?php echo $i;?> <?php echo __('free pass');?></div>
												<div class="bProgressBar">
													<div class="bProgressPerc bProgressRed" style="width: <?php if($rewardStatus['is_completed'] == 1){ echo 100; } else { echo 0; } ?>%;"></div>
												</div>
												<div class="bProgressCount" <?php if($rewardStatus['is_completed'] == 1){?>style="color: #fff;"<?php } ?>> <?php if($rewardStatus['is_completed'] == 1){ echo 1; } else { echo 0; } ?> / 1</div>
											</li>
										</ul>  
										<?php
										$b = 0;
										$afr = '';
										if(isset($pass_rewards[$dayData['id']])){
											foreach($pass_rewards[$dayData['id']] AS $fKey => $fRewardData){
												if($fRewardData['pass_type'] == 1){
													if($b >= 2){
														if(!in_array($fRewardData['reward_type'], [8,9,10,11])){
															if(in_array($fRewardData['reward_type'], [5,6,7])){
																$fRewardData['amount'] = $this->website->zen_format($fRewardData['amount']);
															}
															$afr .= '<li>'.$fRewardData['amount'].' '.$this->Mbattle_pass->getRewardTypeData($fRewardData['reward_type'], $this->session->userdata(['user' => 'server'])).'</li>';
														}
														else{
															if($fRewardData['reward_type'] == 11){
																$afr .= '<li>'.$fRewardData['title'].'</li>';
															}
															else{
																if($fRewardData['reward_type'] == 8 && $fRewardData['vip_type'] != ''){
																	$vipData = $this->Mbattle_pass->get_vip_package_title($fRewardData['vip_type']);
																	$afr .= '<li>'.$vipData['package_title'].' ['.$this->website->seconds2days($vipData['vip_time']).']'.'</li>';
																}
																else{
																	if(!empty($fRewardData['item_data'])){
																		$afr .= '<li><span id="additional_ritem_silver_'.$i.'_'.$b.'" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span></li>';
																	}
																}
															}
														}
													}
													$b++;
												}	
											}
										}
										if($afr != ''){
										?>
										<h4><?php echo __('Additional reward');?></h4>	
										<ul>
											<?php echo $afr;?>
										</ul>
										<?php } ?>	
									</div>
								</div>
							</div>
							<div class="bTitleData">
								<div class="bRewardData">
									<?php if(isset($dayData['platinum_pass_image']) && $dayData['platinum_pass_image'] != ''){ ?>
									<div class="bRewardImage">
										<img src="<?php echo $dayData['platinum_pass_image'];?>" />
									</div>
									<?php } ?>
									<div style="margin-left: 5px;">
										<?php
											$fr1 = '';
											$fr2 = '';
											if(!empty($pass_rewards[$dayData['id']])){
												$a = 0;
												foreach($pass_rewards[$dayData['id']] AS $fKey => $fRewardData){
													if($fRewardData['pass_type'] == 2){
														if($a == 0){
															if(!in_array($fRewardData['reward_type'], [8,9,10,11])){
																if(in_array($fRewardData['reward_type'], [5,6,7])){
																	$fRewardData['amount'] = $this->website->zen_format($fRewardData['amount']);
																}
																$fr1 = $fRewardData['amount'].' '.$this->Mbattle_pass->getRewardTypeData($fRewardData['reward_type'], $this->session->userdata(['user' => 'server']));
															}
															else{
																if($fRewardData['reward_type'] == 11){
																	$fr1 = $fRewardData['title'];
																}
																else{
																	if($fRewardData['reward_type'] == 8 && $fRewardData['vip_type'] != ''){
																		$vipData = $this->Mbattle_pass->get_vip_package_title($fRewardData['vip_type']);
																		$fr1 = $vipData['package_title'].' ['.$this->website->seconds2days($vipData['vip_time']).']';
																	}
																	else{
																		if(!empty($fRewardData['item_data'])){
																			$fr1 = '<span id="main_ritem_platinum_'.$i.'_0" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span>';
																		}
																	}
																}
															}
														}

														if($a == 1){
															if(!in_array($fRewardData['reward_type'], [8,9,10,11])){
																if(in_array($fRewardData['reward_type'], [5,6,7])){
																	$fRewardData['amount'] = $this->website->zen_format($fRewardData['amount']);
																}
																$fr2 = $fRewardData['amount'].' '.$this->Mbattle_pass->getRewardTypeData($fRewardData['reward_type'], $this->session->userdata(['user' => 'server']));
															}
															else{
																if($fRewardData['reward_type'] == 11){
																	$fr2 = $fRewardData['title'];
																}
																else{
																	if($fRewardData['reward_type'] == 8 && $fRewardData['vip_type'] != ''){
																		$vipData = $this->Mbattle_pass->get_vip_package_title($fRewardData['vip_type']);
																		$fr2 = $vipData['package_title'].' ['.$this->website->seconds2days($vipData['vip_time']).']';
																	}
																	else{
																		if(!empty($fRewardData['item_data'])){
																			$fr2 = '<span id="main_ritem_platinum_'.$i.'_1" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span>';
																		}
																	}
																}
															}
														}
														$a++;
													}
												}
											}
										?>
										<div style="text-align:left;">
											<div class="bRewardMain"><?php echo $fr1;?></div>
											<?php if($fr2 != '') { ?><div class="bRewardsSecond">+<?php echo $fr2;?></div><?php } ?>
											<div class="bStatusButton <?php if($rewardStatus['is_completed'] == 1 && in_array($pass['pass_type'], [2])){ ?>bComplete<?php } ?>">
											<?php if(in_array($pass['pass_type'], [2])){ ?>
											<?php if($rewardStatus['is_completed'] == 0){ ?>
												<?php echo __('Incomplete');?>
											<?php } else { ?>
												<?php if($rewardStatus['is_platinum_reward_taken'] == 0){ ?>
													<a class="get_reward" href="javascript:;" id="reward_platinum_<?php echo $i; ?>" data-action="<?php echo  $this->config->base_url.'battle-pass/claim/'.$dayData['id']; ?>/2"><?php echo __('Claim reward');?></a>
												<?php } else { ?>
													<?php echo __('Completed');?>
												<?php } ?>
											<?php } ?>
											<?php } else { ?>
												<?php echo __('Upgrade pass');?>
											<?php } ?>
											</div>
										</div>
									</div>
								</div>
								<div class="bAttached">
									<div class="bOpenClose">
										<?php if($progress['pass_level'] == $dayData['id']){ ?>
										<a class="show-hide-open" href="#" data-open="true" data-id=".bDetails-<?php echo $i+1;?>"><?php echo __('Show Details');?></a>
										<?php } else { ?>
										<a class="show-hide-open" href="#" data-open="false" data-id=".bDetails-<?php echo $i+1;?>"><?php echo __('Show Details');?></a>
										<?php } ?>
									</div>
									<div class="bDetails-<?php echo $i+1;?>" style="<?php if($progress['pass_level'] == $dayData['id']){ ?>display:block;<?php } else { ?>display: none;<?php } ?>">
										<h4><?php echo __('Requirements');?></h4>
										<ul>
											<li>
												<div><?php echo __('Complete').' '.__('day');?> <?php echo $i;?> <?php echo __('free pass');?></div>
												<div class="bProgressBar">
													<div class="bProgressPerc bProgressRed" style="width: <?php if($rewardStatus['is_completed'] == 1){ echo 100; } else { echo 0; } ?>%;"></div>
												</div>
												<div class="bProgressCount" <?php if($rewardStatus['is_completed'] == 1){?>style="color: #fff;"<?php } ?>> <?php if($rewardStatus['is_completed'] == 1){ echo 1; } else { echo 0; } ?> / 1</div>
											</li>
										</ul>
										<?php
										$b = 0;
										$afr = '';
										if(isset($pass_rewards[$dayData['id']])){
											foreach($pass_rewards[$dayData['id']] AS $fKey => $fRewardData){
												if($fRewardData['pass_type'] == 2){
													if($b >= 2){
														if(!in_array($fRewardData['reward_type'], [8,9,10,11])){
															if(in_array($fRewardData['reward_type'], [5,6,7])){
																$fRewardData['amount'] = $this->website->zen_format($fRewardData['amount']);
															}
															$afr .= '<li>'.$fRewardData['amount'].' '.$this->Mbattle_pass->getRewardTypeData($fRewardData['reward_type'], $this->session->userdata(['user' => 'server'])).'</li>';
														}
														else{
															if($fRewardData['reward_type'] == 11){
																$afr .= '<li>'.$fRewardData['title'].'</li>';
															}
															else{
																if($fRewardData['reward_type'] == 8 && $fRewardData['vip_type'] != ''){
																	$vipData = $this->Mbattle_pass->get_vip_package_title($fRewardData['vip_type']);
																	$afr .= '<li>'.$vipData['package_title'].' ['.$this->website->seconds2days($vipData['vip_time']).']'.'</li>';
																}
																else{
																	if(!empty($fRewardData['item_data'])){
																		$afr .= '<li><span id="additional_ritem_platinum_'.$i.'_'.$b.'" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span></li>';
																	}
																}
															}
														}
													}
													$b++;
												}	
											}
										}
										if($afr != ''){
										?>
										<h4><?php echo __('Additional reward');?></h4>	
										<ul>
											<?php echo $afr;?>
										</ul>
										<?php } ?>			
									</div>
								</div>
							</div>
						</div>
					<?php
							$i++;
						}
					}
					?>
					<script>
					$(document).ready(function() {
						$('.show-hide-open').on('click', function(e){
							let isOpen = $(this).data('open');
							let divId = $(this).data('id');
							if(isOpen == false){
								$(this).data('open', true);
								$(divId).slideDown();
							}
							else{
								$(this).data('open', false);
								$(divId).slideUp();
							}
							e.preventDefault();
						});
						$('span[id^="main_ritem_"], span[id^="additional_ritem_"], .collectItem').each(function () {
							App.initializeTooltip($(this), true, 'warehouse/item_info_image');
						});	
						$('.get_reward').on('click', function() {
							if($(this).data('action') != ''){
								var action = $(this).data('action');
								var that = $(this).attr('id');
								$(this).data('action', '');	
								$.alertable.prompt('Select Character', {
									prompt:
									 '<div class="form-group" style="width: 280px;">' +
									  '<select class="alertable-input" id="sel1" name="character">' +
									  <?php 
									  if($characters != false){ 
										foreach($characters AS $char){
									  ?>
										'<option value="<?php echo $char["id"];?>"><?php echo $char["name"];?></option>' +
									  <?php
										}
									  }
									  ?>
									  '</select>' +
									'</div> ' 
								}).then(function(cdata) {
									if(typeof cdata.character != 'undefined'){
										$.ajax({
											dataType: 'json',
											method: 'post',
											url: action,
											data: {'claim': 1, 'character': cdata.character},
											success: function (data) {
												if (data.error) {
													$('#'+that).data('action', action);	
													$.alertable.alert(data.error, {
														html: true
													});	
												}
												else {
													$('#'+that).parent().html('Claimed');	
													$.alertable.alert(data.success, {
														html: true
													});
												}
											},
											error: function (xhr, ajaxOptions, thrownError){
												$('#'+that).data('action', action);		
												alert(thrownError);
											}
										});
									}
									else{
										$('#'+that).data('action', action);	
										alert('<?php echo __("Invalid character.");?>');
									}
								}, function() {
									$('#'+that).data('action', action);										
								});
							}
						});
					});
					</script>
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
	