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
					<a class="custom_button" href="<?php echo $this->config->base_url;?>character-market"><?php echo __('Character Market');?></a>
					<a class="custom_button" href="<?php echo $this->config->base_url;?>character-market/sale-history"><?php echo __('Sale History');?></a>
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

					function calculate_tax(val){
						$(document).ready(function(){
							if((val.toString().search(/^-?[0-9]+$/) != 0)){
								$('#price').val('0');
								$('#price_tax').val('0');
							}
							else{
								total = (parseInt(val) / 100) * parseInt(<?php echo $plugin_config['sale_tax'];?>);
								tax = Math.round((parseInt(val) + total));	
								$('#price_tax').val(tax);	
							}
						});
					}
				</script>
				<div class="form">
				<form method="post" action="" id="sell_character">
					<table>
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
						<td><?php echo __('Character For Sale');?></td>
						<td>
							<select class="custom-select" name="scharacter" id="scharacter">
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
								<option value="1"><?php echo $this->config->config_entry('credits_'.$this->session->userdata(array('user' => 'server')).'|title_1');?></option>
								<option value="2"><?php echo $this->config->config_entry('credits_'.$this->session->userdata(array('user' => 'server')).'|title_2');?></option>						
							</select>
						</td>
					</tr>
					<tr>
						<td style="width:150px;"><?php echo __('Price'); ?></td>
						<td><input type="text" name="price" id="price" value="0" onblur="calculate_tax($('#price').val());" onkeyup="calculate_tax($('#price').val());" /></td>
					</tr>
					<tr id="price_with_tax">
						<td><?php echo __('Price + Tax'); ?> (<?php echo $this->config->values('charactermarket_config', array($this->session->userdata(array('user'=>'server')), 'sale_tax'));?>%)</td>
						<td><input type="text" name="price_tax" id="price_tax" value="0" disabled /></td>
					</tr>
					<tr>
						<td></td>
						<td><button type="submit" id="sell_character" name="sell_character" value="sell_character" class="button-style"><?php echo __('Sell Character');?></button></td>
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

	