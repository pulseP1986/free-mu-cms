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
							<a class="btn btn-primary" style="margin-left: auto;" href="<?php echo $this->config->base_url;?>character-market"><?php echo __('Character Market');?></a>
							<a class="btn btn-primary" href="<?php echo $this->config->base_url;?>character-market/sale-history"><?php echo __('Sale History');?></a>
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
						<form method="post" action="" id="sell_character">
							<div class="form-group">
								<label class="control-label"><?php echo __('Merchant Char');?></label>
								<div>
									<select name="mcharacter" id="mcharacter" class="form-control">
										<?php foreach($char_list as $char){ ?>
											<option value="<?php echo $char['name'];?>"><?php echo $char['name'];?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label"><?php echo __('Character For Sale');?></label>
								<div>
									<select name="scharacter" id="scharacter" class="form-control">
										<?php foreach($char_list as $char){ ?>
											<option value="<?php echo $char['name'];?>"><?php echo $char['name'];?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label"><?php echo __('Selling Period'); ?></label>
								<div>
									<select name="time" id="time" class="form-control">
										<option value="1">1 <?php echo __('Day'); ?></option>
										<option value="2">2 <?php echo __('Days'); ?></option>
										<option value="3">3 <?php echo __('Days'); ?></option>
										<option value="4">4 <?php echo __('Days'); ?></option>
										<option value="5">5 <?php echo __('Days'); ?></option>
										<option value="7">7 <?php echo __('Days'); ?></option>
										<option value="14">14 <?php echo __('Days'); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label"><?php echo __('Payment Type'); ?></label>
								<div>
									<select name="payment_method" id="payment_method" class="form-control">
										<option value="1"><?php echo $this->config->config_entry('credits_'.$this->session->userdata(array('user' => 'server')).'|title_1');?></option>
										<option value="2"><?php echo $this->config->config_entry('credits_'.$this->session->userdata(array('user' => 'server')).'|title_2');?></option>		
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label"><?php echo __('Price'); ?></label>
								<input type="text" class="form-control" name="price" id="price" value="0" onblur="calculate_tax($('#price').val());" onkeyup="calculate_tax($('#price').val());" />
							</div>
							<div class="form-group">
								<label class="control-label"><?php echo __('Price + Tax'); ?> (<?php echo $this->config->values('character_market', array($this->session->userdata(array('user'=>'server')), 'sale_tax'));?>%)</label>
								<input type="text" class="form-control" name="price_tax" id="price_tax" value="0" disabled />
							</div>
							<div class="form-group mb-5">
								<div class="d-flex justify-content-center align-items-center"><button type="submit" class="btn btn-primary" id="sell_character" name="sell_character" value="sell_character"><?php echo __('Sell Character'); ?></button></div>
							</div>
						</form>
						<?php } else { ?>
						<div class="alert alert-primary" role="alert"><?php echo __('No characters found.');?></div>
						<?php } ?>
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