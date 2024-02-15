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
			<h2 class="title"><?php echo __($about['user_description']); ?></h2>
			<div class="entry" >
				<div style="float:right;">
					<a class="custom_button" href="<?php echo $this->config->base_url;?>currency-market"><?php echo __('Currency Market');?></a>
					<a class="custom_button" href="<?php echo $this->config->base_url;?>currency-market/sale-history"><?php echo __('Sale History');?></a>
				</div>
				<div style="padding-top:40px;"></div>
				<div style="clear:left;"></div>
				<?php
				if(isset($error)){
					echo '<div class="e_note">'.$error.'</div>';
				}
				if(isset($success)){
					echo '<div class="s_note">'.$success.'</div>';
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
					$(document).ready(function(){					
						$('#sell_zen_but').on('click', function(){
							$('#sell_credits_form').hide();
							$('#sell_zen_form').show();
						});
						$('#sell_credits_but').on('click', function(){
							$('#sell_zen_form').hide();
							$('#sell_credits_form').show();
						});
						
						$('#sell_credits').on('click', function(e){
							e.preventDefault();
							if($(this).data('action') != ''){	
								var action = $(this).data('action');
								var that = $(this).attr('id');
								var fdata = $('#sell_credits_form form').serialize();
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
											$('#sell_credits_form form')[0].reset();
											App.notice(App.lc.translate('Success').fetch(), 'success', data.success);
										}
									}
								});
							}
						});
						$('#sell_zen').on('click', function(e){
							e.preventDefault();
							if($(this).data('action') != ''){	
								var action = $(this).data('action');
								var that = $(this).attr('id');
								var fdata = $('#sell_zen_form form').serialize();
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
											$('#sell_zen_form form')[0].reset();
											App.notice(App.lc.translate('Success').fetch(), 'success', data.success);
										}
									}
								});
							}
							
						});
					});
				</script>
				 <div id="option_buttons">
					<button type="submit" class="button-style" style="float:left;" id="sell_zen_but"><?php echo __('Sell Zen'); ?></button>
					<button type="submit" class="button-style" style="float:right;" id="sell_credits_but"><?php echo __('Sell Credits'); ?></button>
				</div>
				<div style="clear:both;"></div>
				<div class="form" id="sell_zen_form" style="display:none;">
					<form method="post" action="">
						<table>
						<tr>
							<td style="width:150px;"><?php echo __('Zen Amount'); ?></td>
							<td><input type="text" name="amount_zen" id="amount_zen" value="" placeholder="0" required /></td>
						</tr>
						<tr>
							<td style="width: 150px;"><?php echo __('Merchant Char');?></td>
							<td>
								<select class="custom-select" name="mcharacter" id="mcharacter">
								<?php foreach($char_list as $char): ?>
									<option value="<?php echo $char['name'];?>"><?php echo $char['name'];?></option>
								<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><?php echo __('Selling Period'); ?></td>
							<td>
								<select class="custom-select" name="time" id="time">
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
								<select class="custom-select" name="payment_method" id="payment_method">
									<?php if (strpos($plugin_config['zen_sell_currencies'], '1') !== false) { ?><option value="1"><?php echo $this->website->translate_credits(1, $this->session->userdata(array('user' => 'server')));?></option><?php } ?>
									<?php if (strpos($plugin_config['zen_sell_currencies'], '2') !== false) { ?><option value="2"><?php echo $this->website->translate_credits(2, $this->session->userdata(array('user' => 'server')));?></option><?php } ?>				
								</select>
							</td>
						</tr>
						<tr>
							<td style="width:150px;"><?php echo __('Price'); ?></td>
							<td><input type="text" name="price" id="price" value="0" onblur="calculate_tax($('#price').val(), '#price', '#price_tax');" onkeyup="calculate_tax($('#price').val(), '#price', '#price_tax');" /></td>
						</tr>
						<tr id="price_with_tax">
							<td><?php echo __('Price + Tax'); ?> (<?php echo $plugin_config['sale_tax'];?>%)</td>
							<td><input type="text" name="price_tax" id="price_tax" value="0" disabled /></td>
						</tr>
						<tr>
							<td></td>
							<td><button id="sell_zen" name="sell_zen" class="button-style" data-action="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/sell-zen"><?php echo __('Sell Zen');?></button></td>
						</tr>
						</table>
					</form>
				</div>
				<div class="form" id="sell_credits_form" style="display:none;">
					<form method="post" action="">
						<table>
						<tr>
							<td style="width:150px;"><?php echo __('Amount'); ?></td>
							<td><input type="text" name="amount_credits" id="amount_credits" value="" placeholder="0" required /></td>
						</tr>
						<tr>
							<td><?php echo __('Type'); ?></td>
							<td>
								<select class="custom-select" name="credits_type" id="credits_type">
									<?php if (strpos($plugin_config['credits_sell_currencies'], '1') !== false) { ?><option value="1"><?php echo $this->website->translate_credits(1, $this->session->userdata(array('user' => 'server')));?> for <?php echo $this->website->translate_credits(2, $this->session->userdata(array('user' => 'server')));?> </option><?php } ?>
									<?php if (strpos($plugin_config['credits_sell_currencies'], '2') !== false) { ?><option value="2"><?php echo $this->website->translate_credits(1, $this->session->userdata(array('user' => 'server')));?> for <?php echo $this->website->translate_credits(3, $this->session->userdata(array('user' => 'server')));?> </option><?php } ?>					
									<?php if (strpos($plugin_config['credits_sell_currencies'], '3') !== false) { ?><option value="3"><?php echo $this->website->translate_credits(2, $this->session->userdata(array('user' => 'server')));?> for <?php echo $this->website->translate_credits(1, $this->session->userdata(array('user' => 'server')));?> </option><?php } ?>
									<?php if (strpos($plugin_config['credits_sell_currencies'], '4') !== false) { ?><option value="4"><?php echo $this->website->translate_credits(2, $this->session->userdata(array('user' => 'server')));?> for <?php echo $this->website->translate_credits(3, $this->session->userdata(array('user' => 'server')));?> </option><?php } ?>		
								</select>
							</td>
						</tr>
						<tr>
							<td style="width: 150px;"><?php echo __('Merchant Char');?></td>
							<td>
								<select class="custom-select" name="mcharacter" id="mcharacter">
								<?php foreach($char_list as $char): ?>
									<option value="<?php echo $char['name'];?>"><?php echo $char['name'];?></option>
								<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><?php echo __('Selling Period'); ?></td>
							<td>
								<select class="custom-select" name="time" id="time">
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
							<td style="width:150px;"><?php echo __('Price'); ?></td>
							<td><input type="text" name="pricec" id="pricec" value="0" onblur="calculate_tax($('#pricec').val(), '#pricec', '#price_taxc');" onkeyup="calculate_tax($('#pricec').val(), '#pricec', '#price_taxc');" /></td>
						</tr>
						<tr id="price_with_tax">
							<td><?php echo __('Price + Tax'); ?> (<?php echo $plugin_config['sale_tax'];?>%)</td>
							<td><input type="text" name="price_taxc" id="price_taxc" value="0" disabled /></td>
						</tr>
						<tr>
							<td></td>
							<td><button id="sell_credits" name="sell_credits" class="button-style" data-action="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/sell-credits"><?php echo __('Sell Credits');?></button></td>
						</tr>
						</table>
					</form>
				</div>
				<?php
				}
				else{
				?>
				<div class="e_note"><?php echo __('No characters found.');?></div>
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

	