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
							<?php
							if(!isset($error)){
								echo sprintf(__('Character %s'), $this->Mcharacter_market->get_char_name_by_id($character_info['mu_id'], $this->session->userdata(['user' => 'server']))['Name']);
							}
							?>
							<a class="btn btn-primary" style="margin-left: auto;" href="<?php echo $this->config->base_url;?>character-market"><?php echo __('Character Market');?></a>
							<a class="btn btn-primary" href="<?php echo $this->config->base_url;?>character-market/sell-character"><?php echo __('Sell Character');?></a>	
							<a class="btn btn-primary" href="<?php echo $this->config->base_url;?>character-market/sale-history"><?php echo __('Sale History');?></a>
						</h2>
						<div class="mb-4"></div>
						<?php
						if(isset($error)){
							echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
						}
						else{
							if(isset($purchase_error)){
								echo '<div class="alert alert-danger" role="alert">'.$purchase_error.'</div>';
							}
						?>
						<script>
						$(document).ready(function(){
							$('#inventoryc div').each(function(){
								App.initializeTooltip($(this), true, 'warehouse/item_info');
							});
							$('div[id^="item-slot-occupied-"]').each(function(){
								App.initializeTooltip($(this), true, 'warehouse/item_info');
							});
						})
						</script>
						<table class="table dmn-rankings-table table-striped">
							<thead>
							<tr>
								<th colspan="2"><?php echo __('Information');?></th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td style="width: 70px;">
									<img src="<?php echo $this->config->base_url;?>assets/default_assets/images/c_class/<?php echo strtolower($this->website->get_char_class($this->Mcharacter_market->char_info['Class'], true));?>.png" alt="<?php echo $this->website->get_char_class($this->Mcharacter_market->char_info['Class']);?>" />
								</td>
								<td style="width: 340px;">
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
						<form method="post" action="">
							<div class="d-flex justify-content-center align-items-center"><button type="submit" class="button-style2" value="buy_character" name="buy_character"><?php echo __('Buy Now'); ?></button></div>
						</form>	
						<div class="mb-4"></div>
						<div style="width: 100%;text-align: center;margin-top:10px;">
							<div id="inventoryc">
								<img src="<?php echo $this->config->base_url; ?>assets/default_assets/images/char_icons/<?php echo strtolower($this->website->get_char_class($this->Mcharacter_market->char_info['Class'], true));?>.jpg" title="<?php echo $this->website->get_char_class($this->Mcharacter_market->char_info['Class']);?>" alt="" />
								<?php if($equipment[0] != 0){ ?><div id="in_weapon" data-info="<?php echo $equipment[0]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[0]['item_id'], $equipment[0]['item_cat'], $equipment[0]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>                        
								<?php if($equipment[1] != 0){ ?><div id="in_shield" data-info="<?php echo $equipment[1]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[1]['item_id'], $equipment[1]['item_cat'], $equipment[1]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>                                
								<?php if($equipment[2] != 0){ ?><div id="in_helm" data-info="<?php echo $equipment[2]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[2]['item_id'], $equipment[2]['item_cat'], $equipment[2]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>                               
								<?php if($equipment[3] != 0){ ?><div id="in_armor" data-info="<?php echo $equipment[3]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[3]['item_id'], $equipment[3]['item_cat'], $equipment[3]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>                             
								<?php if($equipment[4] != 0){ ?><div id="in_pants" data-info="<?php echo $equipment[4]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[4]['item_id'], $equipment[4]['item_cat'], $equipment[4]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>                                
								<?php if($equipment[5] != 0){ ?><div id="in_gloves" data-info="<?php echo $equipment[5]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[5]['item_id'], $equipment[5]['item_cat'], $equipment[5]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>                             
								<?php if($equipment[6] != 0){ ?><div id="in_boots" data-info="<?php echo $equipment[6]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[6]['item_id'], $equipment[6]['item_cat'], $equipment[6]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>        
								<?php if($equipment[7] != 0){ ?><div id="in_wings" data-info="<?php echo $equipment[7]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[7]['item_id'], $equipment[7]['item_cat'], $equipment[7]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>      
								<?php if($equipment[9] != 0){ ?><div id="in_pendant" data-info="<?php echo $equipment[9]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[9]['item_id'], $equipment[9]['item_cat'], $equipment[9]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>                            
								<?php if($equipment[10] != 0){ ?><div id="in_ring1" data-info="<?php echo $equipment[10]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[10]['item_id'], $equipment[10]['item_cat'], $equipment[10]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>          
								<?php if($equipment[11] != 0){ ?><div id="in_ring2" data-info="<?php echo $equipment[11]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[11]['item_id'], $equipment[11]['item_cat'], $equipment[11]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>      
								<?php if(isset($equipment[12]) && $equipment[12] != 0){ ?><div id="in_pentagram" data-info="<?php echo $equipment[12]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[12]['item_id'], $equipment[12]['item_cat'], $equipment[12]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>    
								<?php if(isset($equipment[13]) && $equipment[13] != 0){ ?><div id="in_ear1" data-info="<?php echo $equipment[13]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[13]['item_id'], $equipment[13]['item_cat'], $equipment[13]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>       
								<?php if(isset($equipment[14]) && $equipment[14] != 0){ ?><div id="in_ear2" data-info="<?php echo $equipment[14]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[14]['item_id'], $equipment[14]['item_cat'], $equipment[14]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>        
								<?php if($equipment[8] != 0){ ?><div id="in_zoo" data-info="<?php echo $equipment[8]['hex']; ?>" style="background: url(<?php echo $this->itemimage->load($equipment[8]['item_id'], $equipment[8]['item_cat'], $equipment[8]['level'], 0); ?>) no-repeat center center;"></div><?php } ?>        			
							</div>  
						</div>
						<div class="d-flex justify-content-center">
						<div style="float:left;width:320px;margin-top:5px;">	
							<div class="waretitle" style="width:257px;text-align:center;background-color: #0C1A31;"><?php echo __('Inventory');?></div>
							<div style="height: 260px;">
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
							<div style="margin-top: 20px;">
								<div class="waretitle" style="width:257px;text-align:center;background-color: #0C1A31;"><?php echo sprintf(__('Expanded Inventory %d'), 1);?></div>	
								<div style="height: 160px;">
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
							</div>
							<?php } ?>		
						</div>
						<div style="float:left;margin-top:5px;padding-bottom:50px;">	
							<div>
								<div class="waretitle" style="width:257px;text-align:center;background-color: #0C1A31;"><?php echo __('Personal Store');?></div>
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
								<div class="waretitle" style="width:257px;text-align:center;background-color: #0C1A31;"><?php echo sprintf(__('Expanded Inventory %d'), 2);?></div>	
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
							</div>
							<?php } ?>
						</div>
						<?php
						}
						?>
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