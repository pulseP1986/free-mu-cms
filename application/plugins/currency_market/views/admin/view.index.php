<?php
$this->load->view('admincp' . DS . 'view.header');
$this->load->view('admincp' . DS . 'view.sidebar');
?>
<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url; ?>admincp">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url; ?>admincp/manage-plugins">Manage Plugins</a></li>
        </ul>
    </div>
	<?php $server_list = ($is_multi_server == 0) ? ['all' => ['title' => 'All']] : $this->website->server_list(); ?>
	<div class="row-fluid">
        <div class="span12">
            <ul class="nav nav-pills">
			<?php 
			$i = 0;
			foreach($server_list AS $key => $val):
			$i++;
			?>
			<li role="presentation" <?php if($i == 1){?> class="active"<?php }?>><a href="#<?php echo $key;?>" aria-controls="<?php echo $key;?>" role="tab" data-toggle="tab"><?php echo $val['title'];?> Server Settings</a></li>
			<?php endforeach;?>
			<li><a href="<?php echo $this->config->base_url; ?>currency-market/logs" role="tab">Logs</a></li>
			</ul>
            <div class="clearfix"></div>
        </div>
    </div>
	<?php if(isset($js)): ?>
	<script src="<?php echo $js;?>"></script>
	<script type="text/javascript">	
	var currencyMarket = new currencyMarket();
	currencyMarket.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
	$(document).ready(function(){
		$('form[id^="currency_market_settings_form_"]').on("submit", function (e){
			e.preventDefault();
			currencyMarket.saveSettings($(this));
		});
	});
	</script>
	<?php endif;?>
	<div class="row-fluid">
        <div class="box span12">
            <div class="tab-content">
				<?php 
				$i = 0;	
				foreach($server_list AS $key => $data):				
					$val = ($is_multi_server == 0) ? $plugin_config : (isset($plugin_config[$key]) ? $plugin_config[$key] : false);
					$i++;
				?>
                <div role="tabpanel" class="tab-pane fade in <?php if($i == 1){?>active<?php }?>" id="<?php echo $key;?>">
                    <div class="box-header well">
                        <h2><i class="icon-edit"></i> <?php echo $data['title'];?> Server Settings</h2>
                    </div>
                    <div class="box-content">
                        <form class="form-horizontal" method="POST" action="" id="currency_market_settings_form_<?php echo $key;?>">
							<input type="hidden" id="server"  name="server" value="<?php echo $key; ?>"/>
                            <div class="control-group">
                                <label class="control-label" for="active">Status </label>
                                <div class="controls">
                                    <select id="active" name="active" required>
                                        <option value="0" <?php if($val['active'] == 0){echo 'selected="selected"';}?>>Inactive</option>
                                        <option value="1" <?php if($val['active'] == 1){echo 'selected="selected"';}?>>Active</option>
                                    </select>
                                    <p class="help-block">Use currency market module.</p>
                                </div>
                            </div>
							<div class="control-group">
								<label class="control-label" for="currency_per_page">Currency Per Page</label>
								<div class="controls">
									<select id="currency_per_page" name="currency_per_page" required>
										<?php for ($a = 0; $a <= 100; $a++): ?>
											<option
												value="<?php echo $a; ?>" <?php if ($val['currency_per_page'] == $a) {
												echo 'selected="selected"';
											} ?>><?php echo $a; ?></option>
										<?php endfor; ?>
									</select>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="sale_tax">Sale Tax</label>
								<div class="controls">
									<select id="sale_tax" name="sale_tax" required>
										<?php for ($a = 0; $a <= 100; $a++): ?>
											<option
												value="<?php echo $a; ?>" <?php if ($val['sale_tax'] == $a) {
												echo 'selected="selected"';
											} ?>><?php echo $a; ?>%</option>
										<?php endfor; ?>
									</select>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="sale_price_minimum_zen">Min Price Zen</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="sale_price_minimum_zen"
										   name="sale_price_minimum_zen"
										   value="<?php echo $val['sale_price_minimum_zen']; ?>" required />

									<p class="help-block">
									 Sale price minimum in zen currency
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="sale_price_minimum_credits">Min Price Credits</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="sale_price_minimum_credits"
										   name="sale_price_minimum_credits"
										   value="<?php echo $val['sale_price_minimum_credits']; ?>" required />

									<p class="help-block">
									 Sale price minimum in credits currency
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="sale_price_maximum_zen">Max Price Zen</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="sale_price_maximum_zen"
										   name="sale_price_maximum_zen"
										   value="<?php echo $val['sale_price_maximum_zen']; ?>" required />

									<p class="help-block">
									 Sale price maximum in zen currency
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="sale_price_maximum_credits">Max Price Credits</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="sale_price_maximum_credits"
										   name="sale_price_maximum_credits"
										   value="<?php echo $val['sale_price_maximum_credits']; ?>" required />

									<p class="help-block">
									 Sale price maximum in credits currency
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="zen_sell_currencies">Zen Price Limit</label>

								<div class="controls">
									<select id="zen_sell_currencies" name="zen_sell_currencies[]" required multiple>
										<option value="1" <?php if (strpos($val['zen_sell_currencies'], '1') !== false) {
											echo 'selected="selected"';
										} ?>>Credits 1
										</option>
										<option value="2" <?php if (strpos($val['zen_sell_currencies'], '2') !== false) {
											echo 'selected="selected"';
										} ?>>Credits 2
										</option>
									</select>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="credits_sell_currencies">Credits Price Limit</label>

								<div class="controls">
									<select id="credits_sell_currencies" name="credits_sell_currencies[]" required multiple>
										<option value="1" <?php if (strpos($val['credits_sell_currencies'], '1') !== false) {
											echo 'selected="selected"';
										} ?>>Credits 1 for Credits 2
										</option>
										<option value="2" <?php if (strpos($val['credits_sell_currencies'], '2') !== false) {
											echo 'selected="selected"';
										} ?>>Credits 1 for Zen
										</option>
										<option value="3" <?php if (strpos($val['credits_sell_currencies'], '3') !== false) {
											echo 'selected="selected"';
										} ?>>Credits 2 for Credits 1
										</option>
										<option value="4" <?php if (strpos($val['credits_sell_currencies'], '4') !== false) {
											echo 'selected="selected"';
										} ?>>Credits 2 for Zen
										</option>
									</select>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="allow_remove_before_expires">Remove Limit</label>
								<div class="controls">
									<select id="allow_remove_before_expires" name="allow_remove_before_expires" required>
										<option
											value="0" <?php if ($val['allow_remove_before_expires'] == 0) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
										<option
											value="1" <?php if ($val['allow_remove_before_expires'] == 1) {
											echo 'selected="selected"';
										} ?>>No
										</option>
									</select>
									<p class="help-block">Allow remove sale from market only when it expires.</p>
								</div>
							</div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" name="edit_currency_market_settings" id="edit_currency_market_settings">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>
</div>
<?php
$this->load->view('admincp' . DS . 'view.footer');
?>
