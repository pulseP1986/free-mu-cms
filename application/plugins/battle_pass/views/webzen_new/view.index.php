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
					<link rel="stylesheet" href="<?php echo $this->config->base_url; ?>assets/plugins/css/battle_pass2.css">
					<link rel="stylesheet" href="<?php echo $this->config->base_url; ?>assets/plugins/css/jquery.alertable.css">
					<script type="text/javascript" src="<?php echo $this->config->base_url; ?>assets/plugins/js/jquery.alertable.min.js" /></script>
					<div style="padding:60px;">
						<div class="container-battlepass">
							<div class="row">
								<div class="col-md-12 col-lg-8 row" style="margin-bottom: 0px;">
									<div class="col-md-1" style="min-height: 20px; height: 20px;"></div>
									<div class="col-md-9 container-bp-info" style="margin-bottom: 0px;">
										<div class="row">
											<div class="col-md-12" style="margin-bottom: 0px; white-space: nowrap;">
											<table class="tbl-battlepass">
											<tbody><tr>
											<td style="line-height: 240%;">
											Purchase Date:
											<span>
											<?php if(in_array($pass['pass_type'], [1, 2])){ ?>
											<?php 
											$datePurchased = DateTime::createFromFormat('M d Y h:i:s:A', $pass['date']);
											$timeStamp = $datePurchased->getTimestamp();
											echo date('Y/m/d', $timeStamp);
											?>
											<?php } else { ?>
											-
											<?php } ?>
											</span>
											</td>
											</tr>
											</tbody></table>
											</div>
											<div class="col-md-8" style="margin-bottom: 0px;">
											<table class="tbl-battlepass">
											<tbody><tr>
											<td style="line-height: 240%;">
											Time Left:
											<span>
											<?php 
											if(strtotime($plugin_config['battle_pass_end_time']) < time()){
												$countDown = '';
											}
											else{
												$countDown = $this->website->date_diff(date('Y-m-d', time()), $plugin_config['battle_pass_end_time']);
											}
											if($countDown != ''){ 
											?>
											<?php echo $countDown;?>
											<?php } else { ?>
											<?php echo __('Season Ended');?>
											<?php } ?>
											</span>
											</td>
											</tr>
											<tr>
											<td class="time" style="line-height: 120%;">(Closing date: <?php echo $plugin_config['battle_pass_end_time'];?></td>
											</tr>
											</tbody></table>
											</div>
											<div class="col-md-4 hidden-xs hidden-sm hidden-md" style="padding-right: 20px;">
											<br>
											<a href="<?php echo $this->config->base_url;?>account-panel" class="login-button"><?php echo $this->session->userdata(['user' => 'username']);?></a>
											<br><br>
											<a href="<?php echo $this->config->base_url;?>support" class="info-button">Support</a>
											<br>
											</div>
										</div>
									</div>
									<div class="col-md-1" style="margin-bottom: 0px;"></div>
								</div>
								<div class="col-md-12 col-lg-4" style="padding-top: 20px; margin-bottom: 0px;">
									<a <?php if(in_array($pass['pass_type'], [1, 2]) || !$is_logged_in){ ?><?php } else { ?>href="<?php echo $this->config->base_url;?>donate?type=pass"<?php } ?> class="battlepass-button <?php if(in_array($pass['pass_type'], [1, 2]) || !$is_logged_in){ ?>disabled<?php } ?>">Purchase BattlePass</a><br>
									<a <?php if(in_array($pass['pass_type'], [0, 2]) || !$is_logged_in){ ?><?php } else { ?>href="<?php echo $this->config->base_url;?>donate?type=pass"<?php } ?> class="battlepass-button <?php if(in_array($pass['pass_type'], [0, 2]) || !$is_logged_in){ ?>disabled<?php } ?>">Upgrade BattlePass</a>
								</div>
							</div>
						</div>
						<div class="row battlepass-rewards" style="padding: 0px; width: calc(100% + 70px); margin-left: 0px;">
						<?php
							if($progress['pass_level'] == -1)
								$progress['pass_level'] = $last_completed_level;
						?>
							<div class="col-sm-1">
								<table class="hidden-xs hidden-sm hidden-md">
									<thead style="height: 190px;">
										<tr><td></td></tr>
									</thead>
									<tbody>
										<?php
										if(!empty($pass_levels)){
										$i = 1;
										foreach($pass_levels AS $key => $dayData){
										?>
										<tr>
											<td style="height: 125px;">Level <b><?php echo $i;?></b></td>
										</tr> 
										<?php	
											$i++;
										}
										}
										?>
									</tbody>
								</table>
							</div>
							<div class="col-lg-3 col-md-5" style="min-width: 272px!important;">
								<table style="width: 100%;">
									<thead class="battlepass-ticket-top">
										<tr>
											<td style="padding: 0px; width: 75px;">
												<img src="<?php echo $this->config->base_url;?>assets/plugins/images/ubelutD.png" style="margin-top: -20px;">
											</td>
										</tr>
									</thead>
									<tbody class="battlepass-ticket-mid">
									<?php
									if(!empty($pass_levels)){
									$i = 1;
									$rewardStatus = false;
									foreach($pass_levels AS $key => $dayData){
									?>
									<tr>
										<td>
										<div class="tooltip">
											<div class="battlepass-ticket-reward">
												<div class="battlepass-ticket-reward-top">
													<img src="<?php echo $dayData['free_pass_image'];?>" style="width: 60px; height: 60px; margin-top: 10px;">
												</div>
												<div class="battlepass-ticket-reward-bot">
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
																						if(isset($fRewardData['display_item_title']) && $fRewardData['display_item_title'] == 1){
																							$fr1 = $fRewardData['title'];
																						}
																						else{
																							$fr1 = '<span id="main_ritem_free_'.$i.'_0" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span>';
																						}
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
																						if(isset($fRewardData['display_item_title']) && $fRewardData['display_item_title'] == 1){
																							$fr2 = $fRewardData['title'];
																						}
																						else{
																							$fr2 = '<span id="main_ritem_free_'.$i.'_1" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span>';
																						}
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
													<div style="height: 50px; margin-top: -5px; margin-bottom: -23px; margin-left: -8px; width: calc(100% + 16px); color: #696868;">
														<?php echo $fr1;?>
														<?php if($fr2 != '') { ?><br /><span class="gold">+<?php echo $fr2;?></span><?php } ?>
													</div>
													<br>
													<?php if($rewardStatus['is_completed'] == 0){ ?>
														<span class="login-button battlepass-claim required"><?php echo __('Incomplete');?></span>
													<?php } else { ?>
														<?php if($rewardStatus['is_free_reward_taken'] == 0){ ?>
															<span class="login-button battlepass-claim required"><a class="get_reward" href="javascript:;" id="reward_free_<?php echo $i; ?>" data-action="<?php echo  $this->config->base_url.'battle-pass/claim/'.$dayData['id']; ?>/0"><?php echo __('Claim reward');?></a></span>
														<?php } else { ?>
															<span class="battlepass-claim required"><?php echo __('Completed');?></span>
														<?php } ?>
													<?php } ?>
												</div>
											</div>
											<table class="tooltiptext">
												<tbody>
													<?php if(!$is_logged_in){ ?>
													<tr>
													<td>
													<b style="color: #f5c870;"><?php echo __('Quests');?></b>
													</td>
													</tr>
													<tr>
													<td style="padding-left: 8px; padding-right: 8px; text-align: left!important;">
													<div style="line-height: 100%!important; padding-left: 5px;">
													<i style="font-size: 12px;">- <?php echo __('To be able to see the quests, log in');?>.</i><br><br>
													</div>
													</td>
													</tr>
													<?php } else {
													$b = 0;
													$afr = '';
													if(isset($pass_rewards[$dayData['id']])){
														foreach($pass_rewards[$dayData['id']] AS $fKey => $fRewardData){
															if($fRewardData['pass_type'] == 0){
																if($b >= 0){
																	if(!in_array($fRewardData['reward_type'], [8,9,10,11])){
																		if(in_array($fRewardData['reward_type'], [5,6,7])){
																			$fRewardData['amount'] = $this->website->zen_format($fRewardData['amount']);
																		}
																		$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- '.$fRewardData['amount'].' '.$this->Mbattle_pass->getRewardTypeData($fRewardData['reward_type'], $this->session->userdata(['user' => 'server'])).'</i><br /></div></td></tr>';
																	}
																	else{
																		if($fRewardData['reward_type'] == 11){
																			$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- '.$fRewardData['title'].'</i><br /></div></td></tr>';
																		}
																		else{
																			if($fRewardData['reward_type'] == 8 && $fRewardData['vip_type'] != ''){
																				$vipData = $this->Mbattle_pass->get_vip_package_title($fRewardData['vip_type']);
																				$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- '.$vipData['package_title'].' ['.$this->website->seconds2days($vipData['vip_time']).']'.'</i><br /></div></td></tr>';
																			}
																			else{
																				if(!empty($fRewardData['item_data'])){
																					if(isset($fRewardData['display_item_title']) && $fRewardData['display_item_title'] == 1){
																						$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- '.$fRewardData['title'].'</i><br /></div></td></tr>';
																					}
																					else{
																						$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- <span id="additional_ritem_free_'.$i.'_'.$b.'" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span></i><br /></div></td></tr>';
																					}
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
													<tr>
														<td style="padding-top: 7px;">
														<b style="color: #70c8f5;">Rewards</b>
														</td>
													</tr>
													<?php echo $afr;?>

													<?php } ?>
													<tr>
														<td style="padding-top: 25px;">
															<b style="color: #f5c870;">Requirements</b>
														</td>
													</tr>
													<tr>
													<td style="padding-left: 8px; padding-right: 8px;">
													<?php 
													if(!empty($pass_requirements[$dayData['id']])){
														foreach($pass_requirements[$dayData['id']] AS $rkey => $requirement){
															$amount = 0;
															$title = $requirement['title'];
															if($requirement['req_type'] == 1){
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
													<div>
														<i><?php echo $title;?></i>
														<div id="progress-bar">
															<div id="progress-perc" style="width: <?php echo $progressPerc;?>%;" class="progress-red"></div>
														</div>
														<div id="progress-count"><?php echo $completeProgress;?> / <?php echo $amount;?></div>
													</div>
													<?php
														}
													}
													?>
													</td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
										</td>
									</tr>
									<?php
										$i++;
									}
									}
									?>
									</tbody>
									<tbody class="battlepass-ticket-bot">
										<tr>
											<td style=" height: 115px!important;"></td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-lg-3 col-md-6" style="min-width: 272px!important;">
								<table style="width: 100%;">
									<thead class="battlepass-ticket-top">
									<tr>
									<td style="padding: 0px; width: 75px;">
									<img src="<?php echo $this->config->base_url;?>assets/plugins/images/TukXeRU.png" style="margin-top: -20px;">
									</td>
									</tr>
									</thead>
									<tbody class="battlepass-ticket-mid">
									<?php
									if(!empty($pass_levels)){
									$i = 1;
									foreach($pass_levels AS $key => $dayData){
									?>
									<tr>
										<td>
										<div class="tooltip">
											<div class="battlepass-ticket-reward">
												<div class="battlepass-ticket-reward-top">
													<img src="<?php echo $dayData['silver_pass_image'];?>" style="width: 60px; height: 60px; margin-top: 10px;">
												</div>
												<div class="battlepass-ticket-reward-bot">
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
																						if(isset($fRewardData['display_item_title']) && $fRewardData['display_item_title'] == 1){
																							$fr1 = $fRewardData['title'];
																						}
																						else{
																							$fr1 = '<span id="main_ritem_silver_'.$i.'_0" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span>';
																						}
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
																						if(isset($fRewardData['display_item_title']) && $fRewardData['display_item_title'] == 1){
																							$fr2 = $fRewardData['title'];
																						}
																						else{
																							$fr2 = '<span id="main_ritem_silver_'.$i.'_1" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span>';
																						}
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
													<div style="height: 50px; margin-top: -5px; margin-bottom: -23px; margin-left: -8px; width: calc(100% + 16px); color: #696868;">
														<?php echo $fr1;?>
														<?php if($fr2 != '') { ?><br /><span class="gold">+<?php echo $fr2;?></span><?php } ?>
													</div>
													<br>
													<?php if($pass['pass_type'] > 0){ ?>
													<?php if($rewardStatus['is_completed'] == 0){ ?>
														<span class="login-button battlepass-claim required"><?php echo __('Incomplete');?></span>
													<?php } else { ?>
														<?php if($rewardStatus['is_silver_reward_taken'] == 0){ ?>
															<span class="login-button battlepass-claim required"><a class="get_reward" href="javascript:;" id="reward_silver_<?php echo $i; ?>" data-action="<?php echo  $this->config->base_url.'battle-pass/claim/'.$dayData['id']; ?>/1"><?php echo __('Claim reward');?></a></span>
														<?php } else { ?>
															<span class="battlepass-claim required"><?php echo __('Completed');?></span>
														<?php } ?>
													<?php } ?>
													<?php } else { ?>
														<span class="login-button battlepass-claim required"><?php echo __('Upgrade pass');?></span>
													<?php } ?>
												</div>
											</div>
											<table class="tooltiptext">
												<tbody>
													<?php if(!$is_logged_in){ ?>
													<tr>
													<td>
													<b style="color: #f5c870;"><?php echo __('Quests');?></b>
													</td>
													</tr>
													<tr>
													<td style="padding-left: 8px; padding-right: 8px; text-align: left!important;">
													<div style="line-height: 100%!important; padding-left: 5px;">
													<i style="font-size: 12px;">- <?php echo __('To be able to see the quests, log in');?>.</i><br><br>
													</div>
													</td>
													</tr>
													<?php } else {
													$b = 0;
													$afr = '';
													if(isset($pass_rewards[$dayData['id']])){
														foreach($pass_rewards[$dayData['id']] AS $fKey => $fRewardData){
															if($fRewardData['pass_type'] == 1){
																if($b >= 0){
																	if(!in_array($fRewardData['reward_type'], [8,9,10,11])){
																		if(in_array($fRewardData['reward_type'], [5,6,7])){
																			$fRewardData['amount'] = $this->website->zen_format($fRewardData['amount']);
																		}
																		$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- '.$fRewardData['amount'].' '.$this->Mbattle_pass->getRewardTypeData($fRewardData['reward_type'], $this->session->userdata(['user' => 'server'])).'</i><br /></div></td></tr>';
																	}
																	else{
																		if($fRewardData['reward_type'] == 11){
																			$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- '.$fRewardData['title'].'</i><br /></div></td></tr>';
																		}
																		else{
																			if($fRewardData['reward_type'] == 8 && $fRewardData['vip_type'] != ''){
																				$vipData = $this->Mbattle_pass->get_vip_package_title($fRewardData['vip_type']);
																				$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- '.$vipData['package_title'].' ['.$this->website->seconds2days($vipData['vip_time']).']'.'</i><br /></div></td></tr>';
																			}
																			else{
																				if(!empty($fRewardData['item_data'])){
																					if(isset($fRewardData['display_item_title']) && $fRewardData['display_item_title'] == 1){
																						$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- '.$fRewardData['title'].'</i><br /></div></td></tr>';
																					}
																					else{
																						$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- <span id="additional_ritem_silver_'.$i.'_'.$b.'" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span></i><br /></div></td></tr>';
																					}
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
													<tr>
														<td style="padding-top: 7px;">
														<b style="color: #70c8f5;">Rewards</b>
														</td>
													</tr>
													<?php echo $afr;?>

													<?php } ?>
													<tr>
														<td style="padding-top: 25px;">
															<b style="color: #f5c870;">Requirements</b>
														</td>
													</tr>
													<tr>
													<td style="padding-left: 8px; padding-right: 8px;">
													<div>
														<i><?php echo __('Complete').' '.__('day');?> <?php echo $i;?> <?php echo __('free pass');?></i>
														<div id="progress-bar">
															<div id="progress-perc" style="width: <?php if($rewardStatus['is_completed'] == 1){ echo 100; } else { echo 0; } ?>%;" class="progress-red"></div>
														</div>
														<div id="progress-count"><?php if($rewardStatus['is_completed'] == 1){ echo 1; } else { echo 0; } ?> / 1</div>
													</div>
													</td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
										</td>
									</tr>
									<?php
										$i++;
									}
									}
									?>
									</tbody>
									<tbody class="battlepass-ticket-bot">
									<tr>
									<td style=" height: 115px!important;"></td>
									</tr>
									</tbody>
								</table>
								<br><br><br><br>
							</div>
							<div class="col-lg-3 col-md-12" style="min-width: 272px!important;">
								<table  style="width: 100%;">
									<thead class="battlepass-ticket-top">
									<tr>
									<td style="padding: 0px; width: 75px;">
									<img src="<?php echo $this->config->base_url;?>assets/plugins/images/RhTHWfg.png" style="margin-top: -20px;">
									</td>
									</tr>
									</thead>
									<tbody class="battlepass-ticket-mid">
									<?php
									if(!empty($pass_levels)){
									$i = 1;
									foreach($pass_levels AS $key => $dayData){
									?>
									<tr>
										<td>
										<div class="tooltip">
											<div class="battlepass-ticket-reward">
												<div class="battlepass-ticket-reward-top">
													<img src="<?php echo $dayData['platinum_pass_image'];?>" style="width: 60px; height: 60px; margin-top: 10px;">
												</div>
												<div class="battlepass-ticket-reward-bot">
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
																						if(isset($fRewardData['display_item_title']) && $fRewardData['display_item_title'] == 1){
																							$fr1 = $fRewardData['title'];
																						}
																						else{
																							$fr1 = '<span id="main_ritem_platinum_'.$i.'_0" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span>';
																						}
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
																						if(isset($fRewardData['display_item_title']) && $fRewardData['display_item_title'] == 1){
																							$fr2 = $fRewardData['title'];
																						}
																						else{
																							$fr2 = '<span id="main_ritem_platinum_'.$i.'_1" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span>';
																						}
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
													<div style="height: 50px; margin-top: -5px; margin-bottom: -23px; margin-left: -8px; width: calc(100% + 16px); color: #696868;">
														<?php echo $fr1;?>
														<?php if($fr2 != '') { ?><br /><span class="gold">+<?php echo $fr2;?></span><?php } ?>
													</div>
													<br>
													<?php if($pass['pass_type'] > 1){ ?>
													<?php if($rewardStatus['is_completed'] == 0){ ?>
														<span class="login-button battlepass-claim required"><?php echo __('Incomplete');?></span>
													<?php } else { ?>
														<?php if($rewardStatus['is_platinum_reward_taken'] == 0){ ?>
															<span class="login-button battlepass-claim required"><a class="get_reward" href="javascript:;" id="reward_platinum_<?php echo $i; ?>" data-action="<?php echo  $this->config->base_url.'battle-pass/claim/'.$dayData['id']; ?>/2"><?php echo __('Claim reward');?></a></span>
														<?php } else { ?>
															<span class="battlepass-claim required"><?php echo __('Completed');?></span>
														<?php } ?>
													<?php } ?>
													<?php } else { ?>
														<span class="login-button battlepass-claim required"><?php echo __('Upgrade pass');?></span>
													<?php } ?>
												</div>
											</div>
											<table class="tooltiptext">
												<tbody>
													<?php if(!$is_logged_in){ ?>
													<tr>
													<td>
													<b style="color: #f5c870;"><?php echo __('Quests');?></b>
													</td>
													</tr>
													<tr>
													<td style="padding-left: 8px; padding-right: 8px; text-align: left!important;">
													<div style="line-height: 100%!important; padding-left: 5px;">
													<i style="font-size: 12px;">- <?php echo __('To be able to see the quests, log in');?>.</i><br><br>
													</div>
													</td>
													</tr>
													<?php } else {
													$b = 0;
													$afr = '';
													if(isset($pass_rewards[$dayData['id']])){
														foreach($pass_rewards[$dayData['id']] AS $fKey => $fRewardData){
															if($fRewardData['pass_type'] == 2){
																if($b >= 0){
																	if(!in_array($fRewardData['reward_type'], [8,9,10,11])){
																		if(in_array($fRewardData['reward_type'], [5,6,7])){
																			$fRewardData['amount'] = $this->website->zen_format($fRewardData['amount']);
																		}
																		$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- '.$fRewardData['amount'].' '.$this->Mbattle_pass->getRewardTypeData($fRewardData['reward_type'], $this->session->userdata(['user' => 'server'])).'</i><br /></div></td></tr>';
																	}
																	else{
																		if($fRewardData['reward_type'] == 11){
																			$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- '.$fRewardData['title'].'</i><br /></div></td></tr>';
																		}
																		else{
																			if($fRewardData['reward_type'] == 8 && $fRewardData['vip_type'] != ''){
																				$vipData = $this->Mbattle_pass->get_vip_package_title($fRewardData['vip_type']);
																				$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- '.$vipData['package_title'].' ['.$this->website->seconds2days($vipData['vip_time']).']'.'</i><br /></div></td></tr>';
																			}
																			else{
																				if(!empty($fRewardData['item_data'])){
																					if(isset($fRewardData['display_item_title']) && $fRewardData['display_item_title'] == 1){
																						$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- '.$fRewardData['title'].'</i><br /></div></td></tr>';
																					}
																					else{
																						$afr .= '<tr><td style="padding-left: 8px; padding-right: 8px; text-align: left!important;"><div style="line-height: 100%!important; padding-left: 5px;"><i style="font-size: 9px;">- <span id="additional_ritem_platinum_'.$i.'_'.$b.'" data-info="'.$fRewardData['item_data'][0]['hex'].'">'.strip_tags($fRewardData['item_data'][0]['name']).'</span></i><br /></div></td></tr>';
																					}
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
													<tr>
														<td style="padding-top: 7px;">
														<b style="color: #70c8f5;">Rewards</b>
														</td>
													</tr>
													<?php echo $afr;?>

													<?php } ?>
													<tr>
														<td style="padding-top: 25px;">
															<b style="color: #f5c870;">Requirements</b>
														</td>
													</tr>
													<tr>
													<td style="padding-left: 8px; padding-right: 8px;">
													<div>
														<i><?php echo __('Complete').' '.__('day');?> <?php echo $i;?> <?php echo __('free pass');?></i>
														<div id="progress-bar">
															<div id="progress-perc" style="width: <?php if($rewardStatus['is_completed'] == 1){ echo 100; } else { echo 0; } ?>%;" class="progress-red"></div>
														</div>
														<div id="progress-count"><?php if($rewardStatus['is_completed'] == 1){ echo 1; } else { echo 0; } ?> / 1</div>
													</div>
													</td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
										</td>
									</tr>
									<?php
										$i++;
									}
									}
									?>
									</tbody>
									<tbody class="battlepass-ticket-bot">
									<tr>
									<td style=" height: 115px!important;">
									<div style="margin-top: -45px; font-size: 10px; font-style: italic; color: #fff;display:none;">
									</div>
									</td>
									</tr>
									</tbody>
								</table>
								<br><br><br><br>
							</div>
							<div class="col-sm-1">
								<table class="hidden-xs hidden-sm hidden-md">
									<thead style="height: 190px;">
										<tr><td></td></tr>
									</thead>
									<tbody>
										<?php
										if(!empty($pass_levels)){
										$i = 1;
										foreach($pass_levels AS $key => $dayData){
										?>
										<tr>
											<td style="height: 125px;">Level <b><?php echo $i;?></b></td>
										</tr> 
										<?php
											$i++;
										}
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
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
									 '<div class="form-group" style="width: 270px;">' +
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
	