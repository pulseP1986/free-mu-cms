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
			<h1><?php echo __($about['name']); ?></h1>
		</div>
		<div class="box-style1" style="margin-bottom: 20px;">
			<h2 class="title">
			<?php
			if(!isset($error)){
				echo sprintf(__('Character %s'), $this->Mcharacter_market->get_char_name_by_id($character_info['mu_id'], $this->session->userdata(['user' => 'server'])));
			}
			?>
			</h2>
			<div class="entry" >
				<div style="float:right;">
					<a class="custom_button" href="<?php echo $this->config->base_url;?>character-market"><?php echo __($about['name']); ?></a>
					<a class="custom_button" href="<?php echo $this->config->base_url;?>character-market/sell-character"><?php echo __('Sell Character');?></a>
					<a class="custom_button" href="<?php echo $this->config->base_url;?>character-market/sale-history"><?php echo __('Sale History');?></a>
				</div>
				<div style="padding-top:50px;"></div>
				<?php
				if(isset($error)){
					echo '<div class="e_note">'.$error.'</div>';
				}
				else{
					if(isset($purchase_error)){
						echo '<div class="e_note">'.$purchase_error.'</div>';
					}
				?>
				<script>
				$(document).ready(function(){
					$('#inventory div').each(function(){
						App.initializeTooltip($(this), true, 'warehouse/item_info');
					});
					$('div[id^="item-slot-occupied-"]').each(function(){
						App.initializeTooltip($(this), true, 'warehouse/item_info');
					});
				})
				</script>
				<table class="ranking-table">
					<thead>
					<tr class="main-tr">
						<td colspan="2" style="padding-left: 15px;"><?php echo __('Information');?></td>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td style="width: 70px;">
							<img src="<?php echo $this->config->base_url;?>assets/default_assets/images/c_class/<?php echo strtolower($this->website->get_char_class($this->Mcharacter_market->char_info['Class'], true));?>.png" alt="<?php echo $this->website->get_char_class($this->Mcharacter_market->char_info['Class']);?>" />
						</td>
						<td style="width: 240px;">
							<table style="width:100%;margin: 0 auto;">
								<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Class');?></td>
									<td style="width:70%;text-align: left;"><?php echo $this->website->get_char_class($this->Mcharacter_market->char_info['Class']);?></td>
								</tr>
								<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Level');?></td>
									<td style="width:70%;text-align: left;"><?php echo $this->Mcharacter_market->char_info['cLevel'];?></td>
								</tr>
								<?php
									if(in_array($this->Mcharacter_market->char_info['Class'], array(2, 3, 7, 18, 19, 23, 34, 35, 39, 49, 50, 54, 65, 66, 70, 82, 83, 87, 97, 98, 102, 114, 115, 118, 131))){
								?>
								<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Master Level');?></td>
									<td style="width:70%;text-align: left;"><?php echo $this->Mcharacter_market->char_info['mlevel'];?></td>
								</tr>
								<?php
									}
								?>
								<?php if ($this->config->values('rankings_config', array($this->session->userdata(['user' => 'server']), 'player', 'display_resets')) == 1): ?>
								<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Resets');?></td>
									<td style="width:70%;text-align: left;"><?php echo $this->Mcharacter_market->char_info['resets']; ?></td>
								</tr>
								<?php endif;?>
								<?php if ($this->config->values('rankings_config', array($this->session->userdata(['user' => 'server']), 'player', 'display_gresets')) == 1): ?>
								<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Grand Resets');?></td>
									<td style="width:70%;text-align: left;"><?php echo $this->Mcharacter_market->char_info['grand_resets']; ?></td>
								</tr>
								<?php endif;?>
								<tr>
									<td style="width:30%;text-align: left;"><?php echo __('PK Level');?></td>
									<td style="width:70%;text-align: left;"><?php echo $this->website->pk_level($this->Mcharacter_market->char_info['PkLevel']);?> (<?php echo $this->Mcharacter_market->char_info['PkCount'];?>)</td>
								</tr>
								<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Location');?></td>
									<td style="width:70%;text-align: left;">
										<?php echo $this->website->get_map_name($this->Mcharacter_market->char_info['MapNumber']);?> (<?php echo $this->Mcharacter_market->char_info['MapPosX'];?>x<?php echo $this->Mcharacter_market->char_info['MapPosY'];?>)
									</td>
								</tr>
								<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Strength');?></td>
									<td style="width:70%;text-align: left;">
										<?php echo $this->Mcharacter_market->char_info['Strength'];?>
									</td>
								</tr>
								<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Agility');?></td>
									<td style="width:70%;text-align: left;">
										<?php echo $this->Mcharacter_market->char_info['Dexterity'];?>
									</td>
								</tr>
								<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Vitality');?></td>
									<td style="width:70%;text-align: left;">
										<?php echo $this->Mcharacter_market->char_info['Vitality'];?>
									</td>
								</tr>
								<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Energy');?></td>
									<td style="width:70%;text-align: left;">
										<?php echo $this->Mcharacter_market->char_info['Energy'];?>
									</td>
								</tr>
								<?php if(in_array($this->Mcharacter_market->char_info['Class'], array(64, 65, 66))){ ?>
								<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Commands');?></td>
									<td style="width:70%;text-align: left;">
										<?php echo $this->Mcharacter_market->char_info['Leadership'];?>
									</td>
								</tr>
								<?php } ?>
								<?php if($guild_info != false){	?>
									<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Guild');?></td>
									<td style="width:70%;text-align: left;">
										<?php echo '['.$this->website->get_guild_status($guild_info['G_Status']).'] '.$guild_info['G_Name'];?>
									</td>
								</tr>	
								<?php } ?>
								<?php if($gens_info != false){	?>
									<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Gens Family');?></td>
									<td style="width:70%;text-align: left;">
										<?php echo $this->website->get_gens_family($this->Mcharacter_market->gens_family);?>
									</td>
								</tr>	
								<?php } ?>
								<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Price');?></td>
									<td style="width:70%;text-align: left;color:red;">
										<?php echo round(($character_info['price'] / 100) * $plugin_config['sale_tax'] + $character_info['price']).' '.$this->website->translate_credits($character_info['price_type'], $character_info['server']); ?>
									</td>
								</tr>
								<tr>
									<td style="width:30%;text-align: left;"><?php echo __('Expires On');?></td>
									<td style="width:70%;text-align: left;">
										<?php echo date(DATETIME_FORMAT, $character_info['end_date']); ?>
									</td>
								</tr>
							</table>					
						</td>
					</tr>
					</tbody>
				</table>
				<table style="text-align: center;width:100%;padding:10px;">
					<tr>
						<td style="text-align: center;"><form method="post" action=""><button type="submit" class="button-style2" value="buy_character" name="buy_character"><?php echo __('Buy Now'); ?></button></form></td>
					</tr>
				</table>
				<div style="width: 100%;text-align: center;margin-top:10px;">
				<div id="inventory" style="display: inline-block;position:relative; height:407px;width:400px; background:url(<?php echo $this->config->base_url;?>assets/default_assets/images/inventories/inv_<?php echo strtolower($this->website->get_char_class($this->Mcharacter_market->char_info['Class'], true));?>.png) no-repeat left top;">
					<?php if($equipment[7] != 0){ ?>
					<div data-info="<?php echo $equipment[7]['hex'];?>" id="wings" style="background: url(<?php echo $this->itemimage->load($equipment[7]['item_id'],  $equipment[7]['item_cat'],  $equipment[7]['level'], 0);?>) no-repeat center center;">&nbsp;</div>
					<?php } ?>
					<?php if($equipment[2] != 0){ ?>
					<div data-info="<?php echo $equipment[2]['hex'];?>" id="helm" style="background: url(<?php echo $this->itemimage->load($equipment[2]['item_id'],  $equipment[2]['item_cat'],  $equipment[2]['level'], 0);?>) no-repeat center center;">&nbsp;</div> 
					<?php } ?>
					<?php if($equipment[9] != 0){ ?>
					<div data-info="<?php echo $equipment[9]['hex'];?>" id="pendant" style="background: url(<?php echo $this->itemimage->load($equipment[9]['item_id'],  $equipment[9]['item_cat'],  $equipment[9]['level'], 0);?>) no-repeat center center;">&nbsp;</div>
					<?php } ?>
					<?php if($equipment[0] != 0){ ?>
					<div data-info="<?php echo $equipment[0]['hex'];?>" id="sword" style="background: url(<?php echo $this->itemimage->load($equipment[0]['item_id'],  $equipment[0]['item_cat'],  $equipment[0]['level'], 0);?>) no-repeat center center;">&nbsp;</div>
					<?php } ?>
					<?php if($equipment[1] != 0){ ?>
					<div data-info="<?php echo $equipment[1]['hex'];?>" id="shield" style="background: url(<?php echo $this->itemimage->load($equipment[1]['item_id'],  $equipment[1]['item_cat'],  $equipment[1]['level'], 0);?>) no-repeat center center;">&nbsp;</div>
					<?php } ?>
					<?php if($equipment[3] != 0){ ?>
					<div data-info="<?php echo $equipment[3]['hex'];?>" id="armor" style="background: url(<?php echo $this->itemimage->load($equipment[3]['item_id'],  $equipment[3]['item_cat'],  $equipment[3]['level'], 0);?>) no-repeat center center;">&nbsp;</div>
					<?php } ?>
					<?php if($equipment[4] != 0){ ?>
					<div data-info="<?php echo $equipment[4]['hex'];?>" id="pants" style="background: url(<?php echo $this->itemimage->load($equipment[4]['item_id'],  $equipment[4]['item_cat'],  $equipment[4]['level'], 0);?>) no-repeat center center;">&nbsp;</div>
					<?php } ?>
					<?php if($equipment[5] != 0){ ?>
					<div data-info="<?php echo $equipment[5]['hex'];?>" id="gloves" style="background: url(<?php echo $this->itemimage->load($equipment[5]['item_id'],  $equipment[5]['item_cat'],  $equipment[5]['level'], 0);?>) no-repeat center center;">&nbsp;</div>
					<?php } ?>
					<?php if($equipment[6] != 0){ ?>
					<div data-info="<?php echo $equipment[6]['hex'];?>" id="boots" style="background: url(<?php echo $this->itemimage->load($equipment[6]['item_id'],  $equipment[6]['item_cat'],  $equipment[6]['level'], 0);?>) no-repeat center center;">&nbsp;</div>
					<?php } ?>
					<?php if($equipment[10] != 0){ ?>
					<div data-info="<?php echo $equipment[10]['hex'];?>" id="ring_left" style="background: url(<?php echo $this->itemimage->load($equipment[10]['item_id'],  $equipment[10]['item_cat'],  $equipment[10]['level'], 0);?>) no-repeat center center;">&nbsp;</div>
					<?php } ?>
					<?php if($equipment[11] != 0){ ?>
					<div data-info="<?php echo $equipment[11]['hex'];?>" id="ring_right" style="background: url(<?php echo $this->itemimage->load($equipment[11]['item_id'],  $equipment[11]['item_cat'],  $equipment[11]['level'], 0);?>) no-repeat center center;">&nbsp;</div>
					<?php } ?>
				</div>
				</div>
				<div style="float:left;width:320px;margin-top:5px;">	
					<div class="waretitle" style="width:255px;text-align:center;"><?php echo __('Inventory');?></div>
					<div>
						<?php
						$inv_content = '';
						foreach($inventory AS $key => $item){
							if(isset($item['hex'])){ 
								$data = 'data-info="'.$item['hex'].'"';
								$inv_content .= '<div id="item-slot-occupied-'.$key.'" style="margin-top:'.($item['yy']*32).'px; margin-left:'.($item['xx']*32).'px; position:absolute; z-index:999;width:'.($item['x']*32).'px; background-image: url('.$this->config->base_url.'assets/'.$this->config->config_entry('main|template').'/images/v.gif); height:'.($item['y']*32).'px;" '.$data.'><img width="100%" height="100%" alt="'.$item['name'].'" src="'.$this->itemimage->load($item['item_id'], $item['item_cat'], $item['level'], 0).'" /></div>'."\n";
							}						
							else{
								$inv_content .= '<div id="item-slot-'.$key.'" style="margin-top:'.($item['yy']*32).'px; margin-left:'.($item['xx']*32).'px; position:absolute; z-index:1;width:32px; background-image: url('.$this->config->base_url.'assets/'.$this->config->config_entry('main|template').'/images/v.gif); height:32px;"></div>'."\n";
							}
						}
						echo $inv_content;
						?>
					</div>
					<?php if($this->website->get_value_from_server($this->session->userdata(array('user'=>'server')), 'wh_size') > 1920){ ?>
					<div style="margin-top: 280px;">
						<div class="waretitle" style="width:255px;text-align:center;"><?php echo sprintf(__('Expanded Inventory %d'), 1);?></div>	
						<?php
						$inv_content = '';
						foreach($inventory2 AS $key => $item){
							if(isset($item['hex'])){ 
								$data = 'data-info="'.$item['hex'].'"';
								$inv_content .= '<div id="item-slot-occupied-'.$key.'" style="margin-top:'.($item['yy']*32).'px; margin-left:'.($item['xx']*32).'px; position:absolute; z-index:999;width:'.($item['x']*32).'px; background-image: url('.$this->config->base_url.'assets/'.$this->config->config_entry('main|template').'/images/v.gif); height:'.($item['y']*32).'px;" '.$data.'><img width="100%" height="100%" alt="'.$item['name'].'" src="'.$this->itemimage->load($item['item_id'], $item['item_cat'], $item['level'], 0).'" /></div>'."\n";
							}
							else{
								$inv_content .= '<div id="item-slot-'.$key.'" style="margin-top:'.($item['yy']*32).'px; margin-left:'.($item['xx']*32).'px; position:absolute; z-index:1;width:32px; background-image: url('.$this->config->base_url.'assets/'.$this->config->config_entry('main|template').'/images/v.gif); height:32px;"></div>'."\n";
							}
						}
						echo $inv_content;
						?>
					</div>
					<?php } ?>
					
				</div>
				
				<div style="float:left;margin-top:5px;padding-bottom:50px;">	
					<div>
						<div class="waretitle" style="width:255px;text-align:center;"><?php echo __('Personal Store');?></div>
						<?php
						$inv_content = '';
						foreach($store AS $key => $item){
							if(isset($item['hex'])){ 
								$data = 'data-info="'.$item['hex'].'"';
								$inv_content .= '<div id="item-slot-occupied-'.$key.'" style="margin-top:'.($item['yy']*32).'px; margin-left:'.($item['xx']*32).'px; position:absolute; z-index:999;width:'.($item['x']*32).'px; background-image: url('.$this->config->base_url.'assets/'.$this->config->config_entry('main|template').'/images/v.gif); height:'.($item['y']*32).'px;" '.$data.'><img width="100%" height="100%" alt="'.$item['name'].'" src="'.$this->itemimage->load($item['item_id'], $item['item_cat'], $item['level'], 0).'" /></div>'."\n";
							}
							else{
								$inv_content .= '<div id="item-slot-'.$key.'" style="margin-top:'.($item['yy']*32).'px; margin-left:'.($item['xx']*32).'px; position:absolute; z-index:1;width:32px; background-image: url('.$this->config->base_url.'assets/'.$this->config->config_entry('main|template').'/images/v.gif); height:32px;"></div>'."\n";
							}
						}
						echo $inv_content;
						?>
					</div>
					<?php if($this->website->get_value_from_server($this->session->userdata(array('user'=>'server')), 'wh_size') > 1920){ ?>
					<div style="margin-top: 280px;">
						<div class="waretitle" style="width:255px;text-align:center;"><?php echo sprintf(__('Expanded Inventory %d'), 2);?></div>	
						<?php
						$inv_content = '';
						foreach($inventory3 AS $key => $item){
							if(isset($item['hex'])){ 
								$data = 'data-info="'.$item['hex'].'"';
								$inv_content .= '<div id="item-slot-occupied-'.$key.'" style="margin-top:'.($item['yy']*32).'px; margin-left:'.($item['xx']*32).'px; position:absolute; z-index:999;width:'.($item['x']*32).'px; background-image: url('.$this->config->base_url.'assets/'.$this->config->config_entry('main|template').'/images/v.gif); height:'.($item['y']*32).'px;" '.$data.'><img width="100%" height="100%" alt="'.$item['name'].'" src="'.$this->itemimage->load($item['item_id'], $item['item_cat'], $item['level'], 0).'" /></div>'."\n";
							}						
							else{
								$inv_content .= '<div id="item-slot-'.$key.'" style="margin-top:'.($item['yy']*32).'px; margin-left:'.($item['xx']*32).'px; position:absolute; z-index:1;width:32px; background-image: url('.$this->config->base_url.'assets/'.$this->config->config_entry('main|template').'/images/v.gif); height:32px;"></div>'."\n";
							}
						}
						echo $inv_content;
						?>
					</div>
					<?php } ?>
					
				</div>
				<?php
				}
				?>
				
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

	