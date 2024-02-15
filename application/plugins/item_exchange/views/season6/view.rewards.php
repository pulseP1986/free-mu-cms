<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div id="content">
    <div id="box1">
        <?php
            if(isset($config_not_found)){
                echo '<div class="box-style1"><div class="entry"><div class="e_note">' . $config_not_found . '</div></div></div>';
			}
            else{
				 if(isset($module_disabled)){
					echo '<div class="box-style1"><div class="entry"><div class="e_note">' . $module_disabled . '</div></div></div>';
				}
				else{
				?>
				<div class="title1">
					<h1><?php echo __($about['name']); ?></h1>
				</div>
				<div id="content_center">
					<link rel="stylesheet" href="<?php echo $this->config->base_url; ?>assets/plugins/css/jquery.alertable.css">
					<script type="text/javascript" src="<?php echo $this->config->base_url; ?>assets/plugins/js/jquery.alertable.min.js" /></script>
					<div class="box-style1" style="margin-bottom:55px;">
						<h2 class="title"><?php echo __($about['user_description']); ?> <span style="float:right;"><?php echo __('My').' '.__('Growth Points');?>: <span id="my_growth_points"><?php echo $growth_points;?></span></span></h2>
						<div class="entry">
							<table class="ranking-table">
                                <thead>
                                <tr class="main-tr">
                                    <th><?php echo __('Growth Points'); ?></th>
                                    <th><?php echo __('Rewards'); ?></th>
                                    <th><?php echo __('Action'); ?></th>
                                </tr>
                                </thead>
								<tbody>
									<?php if(!empty($reward_list)){ ?>
									<?php foreach($reward_list AS $rid => $reward){ ?>
									<?php if($reward['status'] == 1){ ?>
									<tr>
										<td><?php echo $reward['growth_points'];?></td>
										<td>
											<?php if(isset($reward['credits1']) && $reward['credits1'] > 0){ ?>
												<div><?php echo $reward['credits1'];?> <?php echo $this->website->translate_credits(1, $this->session->userdata(['user' => 'server']));?></div>
											<?php } ?>
											<?php if(isset($reward['credits1']) && $reward['credits2'] > 0){ ?>
												<div><?php echo $reward['credits2'];?> <?php echo $this->website->translate_credits(2, $this->session->userdata(['user' => 'server']));?></div>
											<?php } ?>
											<?php if(isset($reward['wcoin']) && $reward['wcoin'] > 0){ ?>
												<div><?php echo $reward['wcoin'];?> <?php echo __('WCoins');?></div>
											<?php } ?>
											<?php if(isset($reward['goblin']) && $reward['goblin'] > 0){ ?>
												<div><?php echo $reward['goblin'];?> <?php echo __('GoblinPoint');?></div>
											<?php } ?>
											<?php if(isset($reward['credits3']) && $reward['credits3'] > 0){ ?>
												<div><?php echo $this->website->zen_format($reward['credits3']);?> <?php echo $this->website->translate_credits(3, $this->session->userdata(['user' => 'server']));?></div>
											<?php } ?>
											<?php if(isset($reward['zen']) && $reward['zen'] > 0){ ?>
												<div><?php echo $this->website->zen_format($reward['zen']);?> <?php echo __('Zen');?></div>
											<?php } ?>
											<?php if(isset($reward['ruud']) && $reward['ruud'] > 0){ ?>
												<div><?php echo $reward['ruud'];?> <?php echo __('Ruud');?></div>
											<?php } ?>
											<?php if(isset($reward['vip_type']) && $reward['vip_type'] != ''){ ?>
												<?php $vipData = $this->Maccumulated_donation_rewards->get_vip_package_title($reward['vip_type']); ?>
												<div><?php echo __('Vip');?> <?php echo $vipData['package_title'];?> [<?php echo $this->website->seconds2days($vipData['vip_time']);?>]</div>
											<?php } ?>
											<?php if(!empty($reward['items']) || !empty($reward['items2'])){ ?>
												<?php	
												if(!empty($reward_items[$rid])){
													echo '<div>';
													foreach($reward_items[$rid] AS $key => $item){
														echo '<span id="ach_ritem_'.$key.'" data-info="'.$item['hex'].'">'.$item['name'].'</span>';
													}
													echo '</div>';
												}
												?>
											<?php } ?>
											
										</td>
										<td>
											<?php if($growth_points >= $reward['growth_points']){ ?>
											<?php $isClaimed = $this->Maccumulated_donation_rewards->checkClaimedReward($rid, $this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server'])); ?>
											<?php if($isClaimed != false){ ?><?php echo __('Claimed');?><?php } else { ?><a class="get_reward" href="javascript:;" id="reward_<?php echo $rid; ?>" data-action="<?php echo  $this->config->base_url.'accumulated-donation-rewards/claim/'.$rid; ?>"><?php echo __('Claim Reward');?></a><?php } ?>
											<?php } else { ?>
											<?php echo __('More growth points required');?>
											<?php } ?>
										</td>
									</tr>
									<?php } ?>
									<?php } ?>
									<?php } ?>
								</tbody>
							</table>	
							<script type="text/javascript">
								$(document).ready(function (){
									$('span[id^="ach_ritem_"]').each(function () {
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
						</div>
					</div>
				</div>
			<?php
				}
			}
        ?>
    </div>
</div>
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>

