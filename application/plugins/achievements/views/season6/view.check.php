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
			<h1><?php echo $achData['title']; ?></h1>
		</div>
		<div class="box-style1" style="margin-bottom: 20px;">
			<!--<h2 class="title"><?php echo $achData['desc']; ?></h2>-->
			<div class="entry" >
				<div style="float:right;">
					<a class="custom_button" href="<?php echo $this->config->base_url;?>achievements/view/<?php echo $id;?>"><?php echo __('Achievement List');?></a>
					<a class="custom_button" href="<?php echo $this->config->base_url;?>achievements/rankings/<?php echo $this->session->userdata(['user' => 'server']);?>"><?php echo __('Rankings');?></a>
				</div>
				<div style="padding-top:40px;"></div>
				<div style="clear:left;"></div>
				<link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url; ?>assets/plugins/css/achievements.css?v2">
				<?php
				if(isset($success)):
					echo '<div class="s_note">'.$success.'</div>';
				endif;
				if(isset($error)):
					echo '<div class="e_note">'.$error.'</div>';
				endif;
				?>
				<table class="ranking-table">
					<thead>
						<tr class="main-tr">
							<th colspan="2"><?php echo __('Requirements'); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td <?php if(!in_array($achData['achievement_type'], [7, 9, 24])){ echo 'colspan="2"'; } ?>>
								<?php echo $this->Machievements->achivementTypeToReadableLong($achData['achievement_type']);?> <?php if($achDataDB['is_completed'] == 1){ ?>(<?php echo __('Completed');?>)<?php } ?>
							</td>
							<?php if(in_array($achData['achievement_type'], [7, 9, 24])){ echo '<td>'; } ?>
								<?php 
									if($achData['achievement_type'] == 7){
										if(!empty($monster_list)){
											echo '<ul class="style1">';
											foreach($monster_list AS $monster){
												echo '<li>'.$monster.'</li>';
											}
											echo '</ul>';
										}
										else{
											echo __('Any');
										}
									}
									if($achData['achievement_type'] == 24){
										
										$requirements = explode('|', json_decode($achDataDB['items'], true));
										echo '<ul class="style1">';
										echo '<li>Req Level: '.$char_info['cLevel'].' / '.$requirements[0].'</li>';
										echo '<li>Req MasterLevel: '.$char_info['mlevel'].' / '.$requirements[1].'</li>';
										echo '<li>Req Resets: '.$char_info['resets'].' / '.$requirements[2].'</li>';
										echo '<li>Req GrandResets: '.$char_info['grand_resets'].' / '.$requirements[3].'</li>';
										echo '</ul>';
									}
									if($achData['achievement_type'] == 9){
								?>
								<script type="text/javascript">
								$(document).ready(function () {
									$('span[id^="ach_item_"]').each(function () {
										App.initializeTooltip($(this), true, 'warehouse/item_info_image');
									});	
								});
								</script>
								<?php	
										if(!empty($item_list)){
											echo '<div>';
											foreach($item_list AS $key => $item){
												echo '<span id="ach_item_'.$key.'" data-info="'.$item['hex'].'"><div style="float:left;">'.$item['amount'].'x&nbsp;</div><div style="float:left;">'.$item['name'].'</div></span><div style="clear:both;"></div>';
											}
											echo '</div>';
										}
										else{
											echo __('All Items Collected.');
										}
									}
								?>
							<?php if(in_array($achData['achievement_type'], [7, 9, 24])){ echo '</td>'; } ?>
						</tr>
						<?php if(!in_array($achData['achievement_type'], [0, 9, 24])){ ?>
						<tr>
							<td><?php echo __('Required');?></td>
							<td><?php echo $achDataDB['amount'];?></td>
						</tr>
						<tr>
							<td><?php echo __('Completed');?></td>
							<td><?php echo $achDataDB['amount_completed'];?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				<?php if($achDataDB['is_completed'] == 0){ ?>
				<table style="text-align: center;width:100%;padding:10px;">
					<tr>
						<td style="text-align: center;"><form method="post" action=""><button type="submit" class="button-style2" value="check_status" name="check_status"><?php echo __('Check Status'); ?></button></form></td>
					</tr>
				</table>	
				<?php } ?>
				<?php if(isset($archievement_rewards[$achid])){ ?>
				<div style="padding-top:40px;"></div>
				<table class="ranking-table">
					<thead>
						<tr class="main-tr">
							<th colspan="2"><?php echo __('Rewards'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if(isset($archievement_rewards[$achid]['credits1']) && $archievement_rewards[$achid]['credits1'] > 0){ ?>
						<tr>
							<td style="text-align:left;"><?php echo $this->website->translate_credits(1, $this->session->userdata(['user' => 'server']));?></td>
							<td style="text-align:left;"><?php echo $archievement_rewards[$achid]['credits1'];?></td>
						</tr>
						<?php } ?>
						<?php if(isset($archievement_rewards[$achid]['credits2']) && $archievement_rewards[$achid]['credits2'] > 0){ ?>
						<tr>
							<td style="text-align:left;"><?php echo $this->website->translate_credits(2, $this->session->userdata(['user' => 'server']));?></td>
							<td style="text-align:left;"><?php echo $archievement_rewards[$achid]['credits2'];?></td>
						</tr>
						<?php } ?>
						<?php if(isset($archievement_rewards[$achid]['wcoin']) && $archievement_rewards[$achid]['wcoin'] > 0){ ?>
						<tr>
							<td style="text-align:left;"><?php echo __('WCoins'); ?></td>
							<td style="text-align:left;"><?php echo $archievement_rewards[$achid]['wcoin'];?></td>
						</tr>
						<?php } ?>
						<?php if(isset($archievement_rewards[$achid]['goblin']) && $archievement_rewards[$achid]['goblin'] > 0){ ?>
						<tr>
							<td style="text-align:left;"><?php echo __('GoblinPoint'); ?></td>
							<td style="text-align:left;"><?php echo $archievement_rewards[$achid]['goblin'];?></td>
						</tr>
						<?php } ?>
						<?php if(isset($archievement_rewards[$achid]['zen']) && $archievement_rewards[$achid]['zen'] > 0){ ?>
						<tr>
							<td style="text-align:left;"><?php echo __('Zen');?></td>
							<td style="text-align:left;"><?php echo $this->website->zen_format($archievement_rewards[$achid]['zen']);?></td>
						</tr>
						<?php } ?>
						<?php if(isset($archievement_rewards[$achid]['credits3']) && $archievement_rewards[$achid]['credits3'] > 0){ ?>
						<tr>
							<td style="text-align:left;"><?php echo $this->website->translate_credits(3, $this->session->userdata(['user' => 'server']));?></td>
							<td style="text-align:left;"><?php echo $this->website->zen_format($archievement_rewards[$achid]['credits3']);?></td>
						</tr>
						<?php } ?>
						<?php if(isset($archievement_rewards[$achid]['ruud']) && $archievement_rewards[$achid]['ruud'] > 0){ ?>
						<tr>
							<td style="text-align:left;"><?php echo __('Ruud');?></td>
							<td style="text-align:left;"><?php echo $this->website->zen_format($archievement_rewards[$achid]['ruud']);?></td>
						</tr>
						<?php } ?>
						<?php if(isset($archievement_rewards[$achid]['vip_type']) && $archievement_rewards[$achid]['vip_type'] != ''){ ?>
							<?php $vipData = $this->Machievements->get_vip_package_title($archievement_rewards[$achid]['vip_type']); ?>
							<tr>
								<td style="text-align:left;"><?php echo __('Vip');?></td>
								<td style="text-align:left;"><?php echo $vipData['package_title'];?> [<?php echo $this->website->seconds2days($vipData['vip_time']);?>]</td>
							</tr>
						<?php } ?>
						<?php if(!empty($archievement_rewards[$achid]['items'])){ ?>
						<tr>
							<td style="text-align:left;"><?php echo __('Items');?></td>
							<td style="text-align:left;">
								<script type="text/javascript">
								$(document).ready(function () {
									$('span[id^="ach_ritem_"]').each(function () {
										App.initializeTooltip($(this), true, 'warehouse/item_info_image');
									});	
								});
								</script>
								<?php	
								if(!empty($reward_items)){
									echo '<div>';
									foreach($reward_items AS $key => $item){
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
				<?php if($achDataDB['is_completed'] == 1){ ?>
				<table style="text-align: center;width:100%;padding:10px;">
					<tr>
						<td style="text-align: center;"><form method="post" action=""><button type="submit" class="button-style2" value="get_reward" name="get_reward"><?php echo __('Get Reward'); ?></button></form></td>
					</tr>
				</table>
				<?php } ?>
				<?php } else { ?>
				<div class="w_note"><?php echo __('No rewards found.');?></div>
				<?php } ?>
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
	
	