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
							<a class="btn btn-primary" style="margin-left: auto;" href="<?php echo $this->config->base_url;?>muun-market"><?php echo __('Muun Market');?></a>
							<a class="btn btn-primary" href="<?php echo $this->config->base_url;?>muun-market/sale-history"><?php echo __('Sale History');?></a>
						</h2>
						<div class="mb-4"></div>
						<?php
						if(isset($success)){
							echo '<div class="alert alert-success" role="alert">'.$success.'</div>';
						}
						if(isset($error)){
							echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
						}
						if(isset($char_list) && $char_list != false){
						?>
						<script>
							var total = 0;
							var tax = 0;			

							function calculate_tax(val, id1, id2){
								$(document).ready(function(){	
									if((val.toString().search(/^-?[0-9]+$/) != 0)){
										$(id1).val('0');
										$(id2).val('0');
									}
									else{
										total = (parseInt(val) / 100) * parseInt(<?php echo $plugin_config['sale_tax'];?>);
										tax = Math.round((parseInt(val) + total));	
										$(id2).val(tax);	
									}
								});	
							}
							$(document).ready(function () {
								$('.item-mapping img, .warehouse_item_block img').on('click', function(){
									var name = $(this).data('name');
									var slot = $(this).data('slot');
									var hex = $(this).data('info');
									var character = $(this).data('char');

									$('#muun_name').html(name);
									$('#sell_slot').val(slot);
									$('#sell_hex').val(hex);
									$('#sell_char').val(character);
									$('#sell_muun_form:hidden').show();
								});
								
								$('#sell_muun').on('click', function(e){
									e.preventDefault();
									if($(this).data('action') != ''){	
										var action = $(this).data('action');
										var that = $(this).attr('id');
										var fdata = $('#sell_muun_form form').serialize();
										var slot = $('#sell_slot').val();
										$(this).data('action', '');
										$.ajax({
											url: action,
											data: fdata,
											success: function (data) {
												if (data.error) {
													$('#'+that).data('action', action);		
													App.notice(App.lc.translate('Error').fetch(), 'error', data.error);
												}
												else {
													$('#'+that).data('action', action);	
													$('#sell_muun_form form')[0].reset();
													$('#sell_muun_form').hide();
													$('[data-slot="'+slot+'"]').replaceWith('');
													App.notice(App.lc.translate('Success').fetch(), 'success', data.success);
												}
											}
										});
									}
									
								});

								$('.previousP').on('click', function () {
									mu_id = $(this).data('char');
									curPage = parseInt($('#currentPage-'+mu_id+'').text());
									if(curPage <= 1)
										return false;
									else{
										$('#page-'+mu_id+'-'+curPage+':visible').hide();
										$('#page-'+mu_id+'-'+(curPage-1)+':hidden').show();
										$('#currentPage-'+mu_id+'').html(curPage-1);
									}								
								});
								$('.nextP').on('click', function () {
									mu_id = $(this).data('char');
									curPage = parseInt($('#currentPage-'+mu_id+'').text());
									if(curPage >= 5)
										return false;
									else{
										$('#page-'+mu_id+'-'+curPage+':visible').hide();
										$('#page-'+mu_id+'-'+(curPage+1)+':hidden').show();
										$('#currentPage-'+mu_id+'').html(curPage+1);
									}								
								});

								$('.item-mapping img, .warehouse_item_block img').each(function () {
									App.initializeTooltip($(this), true, 'warehouse/item_info_pet');
								});
								$('div[id^="char-"]').on('click', function () {
									mu_id = $(this).attr("id").split("-")[1];
									$('div[id^="muun-inv"]:visible').slideToggle();
									$('#muun-inv-' + mu_id + ':hidden').slideToggle();
								});
							});
						</script>
						<div class="form" id="sell_muun_form" style="display:none;width: 100%;">
							<form method="post" action="">
								<input type="hidden" name="slot" id="sell_slot" value="" />
								<input type="hidden" name="hex"  id="sell_hex" value="" />
								<input type="hidden" name="char"  id="sell_char" value="" />
								<table>
								<tr>
									<td style="width:150px;"><?php echo __('Sell'); ?></td>
									<td id="muun_name"></td>
								</tr>
								<tr>
									<td><?php echo __('Selling Period'); ?></td>
									<td>
										<select class="form-control" name="time" id="time">
											<option value="1">1 <?php echo __('Day'); ?></option>
											<option value="2">2 <?php echo __('Days'); ?></option>
											<option value="3">3 <?php echo __('Days'); ?></option>
											<option value="4">4 <?php echo __('Days'); ?></option>
											<option value="5">5 <?php echo __('Days'); ?></option>
											<option value="7">7 <?php echo __('Days'); ?></option>
											<option value="14">14 <?php echo __('Days'); ?></option>
										</select>
									</td>
								</tr>
								<tr>
								<td><?php echo __('Payment Type'); ?></td>
									<td>
										<select class="form-control" name="payment_method" id="payment_method">
											<option value="1"><?php echo $this->config->config_entry('credits_'.$this->session->userdata(array('user' => 'server')).'|title_1');?></option>
											<option value="2"><?php echo $this->config->config_entry('credits_'.$this->session->userdata(array('user' => 'server')).'|title_2');?></option>	
											<option value="3"><?php echo $this->config->config_entry('credits_'.$this->session->userdata(array('user' => 'server')).'|title_3');?></option>										
										</select>
									</td>
								</tr>
								<tr>
									<td style="width:150px;"><?php echo __('Price'); ?></td>
									<td><input type="text" class="form-control" name="price" id="price" value="0" onblur="calculate_tax($('#price').val(), '#price', '#price_tax');" onkeyup="calculate_tax($('#price').val(), '#price', '#price_tax');" /></td>
								</tr>
								<tr id="price_with_tax">
									<td><?php echo __('Price + Tax'); ?> (<?php echo $plugin_config['sale_tax'];?>%)</td>
									<td><input type="text" class="form-control" name="price_tax" id="price_tax" value="0" disabled /></td>
								</tr>
								<tr>
									<td></td>
									<td><button id="sell_muun" name="sell_muun" class="btn btn-primary" data-action="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/do-sell-muun"><?php echo __('Sell Muun');?></button></td>
								</tr>
								</table>
							</form>
						</div>
						<?php
							foreach($char_list as $ch){	
						?>
						
						<link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url; ?>assets/plugins/css/muun_market.css?v2">
						<div class="character" id="char-<?php echo $ch['id']; ?>">
							<div class="title-name"><?php echo $ch['name']; ?>
								<span><?php echo $this->website->get_char_class($ch['Class']); ?></span>
							</div>
						</div>
						<div id="muun-inv-<?php echo $ch['id']; ?>" style="display: none;width: 100%;text-align: center;">
						<div style="display: inline-block;margin-top:5px;">
						<?php if($ch['Muuns'] != false){ ?>
						<div class="muunHead">
							<div class="equipments">
								<div class="item-mapping pet_main_item">
									<?php if($ch['Muuns'][0] != 0){ ?>
										<img data-char="<?php echo $ch['id']; ?>" data-name="<?php echo $ch['Muuns'][0]['name']; ?>" data-info="<?php echo $ch['Muuns'][0]['hex']; ?>" data-slot="m-1" style="position: relative; max-height: 95%; max-width: 100%;" src="<?php echo $this->itemimage->load($ch['Muuns'][0]['item_id'], $ch['Muuns'][0]['item_cat'], $ch['Muuns'][0]['level'], 0); ?>" />
									<?php } ?>
								</div>
								<div class="item-mapping pet_help_item">
									<?php if($ch['Muuns'][1] != 0){ ?>
										<img data-char="<?php echo $ch['id']; ?>" data-name="<?php echo $ch['Muuns'][1]['name']; ?>" data-info="<?php echo $ch['Muuns'][1]['hex']; ?>" data-slot="m-2" style="position: relative; max-height: 95%; max-width: 100%;" src="<?php echo $this->itemimage->load($ch['Muuns'][1]['item_id'], $ch['Muuns'][1]['item_cat'], $ch['Muuns'][1]['level'], 0); ?>" />
									<?php } ?>
								</div>
							</div>
						</div>
						<?php
							$ch['Muuns'] = array_slice($ch['Muuns'], 3);
							$pages = array_chunk($ch['Muuns'], 20, true);
						?>
						<div class="muunContent">
							<div class="muunLeft"></div>
							<div class="muunCenter">
								<?php 
								foreach($pages AS $key => $page){ 
								?>
								<div class="warehouse_block" id="page-<?php echo $ch['id']; ?>-<?php echo $key+1;?>" <?php if($key != 0){ echo 'style="display:none;"'; }?>>
									<?php 
									$ll = 0;
									$tt = 0;
									foreach($page AS $k => $item){ 
									?>
									<div class="warehouse_item_block" style="position: absolute; height: 71px; width: 66px; left: <?php echo (66 * $ll);?>px; top: <?php echo (71 * $tt);?>px;">
									<?php
										if($item != 0){
									?>
										<img data-char="<?php echo $ch['id']; ?>" data-name="<?php echo $item['name']; ?>" data-info="<?php echo $item['hex']; ?>" data-slot="<?php echo $k; ?>" style="position: relative; max-height: 90%; max-width: 100%;" src="<?php echo $this->itemimage->load($item['item_id'], $item['item_cat'], $item['level'], 0); ?>" />
									<?php 
										}
									?>	
									</div> 
									<?php 
										$ll++;
										if($ll == 4){
											$ll = 0;
											$tt++;
										}
										if($tt == 20){
											$tt = 0;
										}
									} 
									?>
								</div>
								<?php 
									
								} 
								?>
							</div>
							<div class="muunRight"></div>
							<div style="clear:both;"></div>
						</div>
						<div class="muunFooter">
							<div style="width: 100%;height: 100%;">
								<div style="background-color: #302A21;width: 110px;height:25px;text-align:center;position:absolute;margin-left:110px;margin-top: 10px;">
									<div class="pagerButton previousP" id="previousP" data-char="<?php echo $ch['id']; ?>">&lt;</div>
									<div class="pagerNumber"><span id="currentPage-<?php echo $ch['id']; ?>">1</span> / 5</div>
									<div class="pagerButton nextP" id="nextP" data-char="<?php echo $ch['id']; ?>">&gt;</div>
								</div>
							</div>
						</div>
						<?php } else { ?>
						<div class="alert alert-primary" role="alert"><?php echo __('Muun inventory not found');?></div>
						<?php } ?>
						</div>
						</div>
						
						<?php
							}
						}
						else{
						?>
						<div class="alert alert-primary" role="alert"><?php echo __('No characters found.');?></div>
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