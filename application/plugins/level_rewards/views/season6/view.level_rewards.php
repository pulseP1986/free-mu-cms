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
				?>
				<div class="title1">
					<h1><?php echo __($about['name']); ?></h1>
				</div>
				<div id="content_center">
					<div class="box-style1" style="margin-bottom:55px;">
						<h2 class="title"><?php echo __($about['user_description']); ?></h2>
						<div class="entry">
							<?php
							if(isset($module_disabled)){
								echo '<div class="e_note">' . $module_disabled . '</div>';
							}
							else{
							?>
							<link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url; ?>assets/plugins/css/level_rewards.css?v2">
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
							<?php if(!empty($reward_list)){ ?>
							<?php foreach($reward_list AS $rid => $reward){ ?>
							<?php if($reward['status'] == 1){ ?>
							<div class="character" id="rewards-<?php echo $rid;?>">
								<div class="title-name">
									<?php if($reward['min_level'] > 0){ ?>[ <?php echo __('Min');?> <?php echo __('Level');?>: <?php echo $reward['min_level']; ?> ]<?php } ?>
									<?php if($reward['min_mlevel'] > 0){ ?>[ <?php echo __('Min');?> <?php echo __('MLevel');?>: <?php echo $reward['min_mlevel']; ?> ]<?php } ?>
									<?php if($reward['min_resets'] > 0){ ?>[ <?php echo __('Min');?> <?php echo __('Resets');?>: <?php echo $reward['min_resets']; ?> ]<?php } ?>
									<?php if($reward['min_gresets'] > 0){ ?>[ <?php echo __('Min');?> <?php echo __('GResets');?>: <?php echo $reward['min_gresets']; ?> ]<?php } ?>
								</div>
								
							</div>
							<div id="rewards-list-<?php echo $rid;?>" style="display: none;width: 100%;text-align: center;">
								<table class="ranking-table" style="width: 612px;">
									<thead>
										<tr class="main-tr">
											<th colspan="2"><?php echo __('Rewards'); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php if(isset($reward['credits1']) && $reward['credits1'] > 0){ ?>
										<tr>
											<td style="text-align:left;"><?php echo $this->website->translate_credits(1, $this->session->userdata(['user' => 'server']));?></td>
											<td style="text-align:left;"><?php echo $reward['credits1'];?></td>
										</tr>
										<?php } ?>
										<?php if(isset($reward['credits2']) && $reward['credits2'] > 0){ ?>
										<tr>
											<td style="text-align:left;"><?php echo $this->website->translate_credits(2, $this->session->userdata(['user' => 'server']));?></td>
											<td style="text-align:left;"><?php echo $reward['credits2'];?></td>
										</tr>
										<?php } ?>
										<?php if(isset($reward['wcoin']) && $reward['wcoin'] > 0){ ?>
										<tr>
											<td style="text-align:left;"><?php echo __('WCoins'); ?></td>
											<td style="text-align:left;"><?php echo $reward['wcoin'];?></td>
										</tr>
										<?php } ?>
										<?php if(isset($reward['goblin']) && $reward['goblin'] > 0){ ?>
										<tr>
											<td style="text-align:left;"><?php echo __('GoblinPoint'); ?></td>
											<td style="text-align:left;"><?php echo $reward['goblin'];?></td>
										</tr>
										<?php } ?>
										<?php if(isset($reward['zen']) && $reward['zen'] > 0){ ?>
										<tr>
											<td style="text-align:left;"><?php echo __('Zen');?></td>
											<td style="text-align:left;"><?php echo $this->website->zen_format($reward['zen']);?></td>
										</tr>
										<?php } ?>
										<?php if(isset($reward['credits3']) && $reward['credits3'] > 0){ ?>
										<tr>
											<td style="text-align:left;"><?php echo $this->website->translate_credits(3, $this->session->userdata(['user' => 'server']));?></td>
											<td style="text-align:left;"><?php echo $this->website->zen_format($reward['credits3']);?></td>
										</tr>
										<?php } ?>
										<?php if(isset($reward['ruud']) && $reward['ruud'] > 0){ ?>
										<tr>
											<td style="text-align:left;"><?php echo __('Ruud');?></td>
											<td style="text-align:left;"><?php echo $this->website->zen_format($reward['ruud']);?></td>
										</tr>
										<?php } ?>
										<?php if(isset($reward['vip_type']) && $reward['vip_type'] != ''){ ?>
										<?php $vipData = $this->Mlevel_rewards->get_vip_package_title($reward['vip_type']); ?>
										<tr>
											<td style="text-align:left;"><?php echo __('Vip');?></td>
											<td style="text-align:left;"><?php echo $vipData['package_title'];?> [<?php echo $this->website->seconds2days($vipData['vip_time']);?>]</td>
										</tr>
										<?php } ?>
										<?php if(!empty($reward['items'])){ ?>
										<tr>
											<td style="text-align:left;"><?php echo __('Items');?></td>
											<td style="text-align:left;">
												<?php	
												if(!empty($reward_items[$rid])){
													echo '<div>';
													foreach($reward_items[$rid] AS $key => $item){
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
								<table style="text-align: center;width:100%;padding:10px;">
									<tr>
										<td style="text-align: center;">
											<a href="<?php echo $this->config->base_url;?>level-rewards/claim/<?php echo $rid;?>"><?php echo __('Get Reward');?></a>
										</td>
									</tr>
								</table>
							</div>
							<?php } ?>	
							<?php } ?>
							<?php } ?>
							<?php
							}
							?>
						</div>
					</div>
				</div>
			<?php
			}
        ?>
    </div>
</div>
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>

