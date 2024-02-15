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
			<li><a href="<?php echo $this->config->base_url; ?>muun-market/logs" role="tab">Logs</a></li>
			</ul>
            <div class="clearfix"></div>
        </div>
    </div>
	<?php if(isset($js)): ?>
	<script src="<?php echo $js;?>"></script>
	<script type="text/javascript">	
	var muunMarket = new muunMarket();
	muunMarket.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
	$(document).ready(function(){
		$('form[id^="muun_market_settings_form_"]').on("submit", function (e){
			e.preventDefault();
			muunMarket.saveSettings($(this));
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
                        <form class="form-horizontal" method="POST" action="" id="muun_market_settings_form_<?php echo $key;?>">
							<input type="hidden" id="server"  name="server" value="<?php echo $key; ?>"/>
                            <div class="control-group">
                                <label class="control-label" for="active">Status </label>
                                <div class="controls">
                                    <select id="active" name="active" required>
                                        <option value="0" <?php if($val['active'] == 0){echo 'selected="selected"';}?>>Inactive</option>
                                        <option value="1" <?php if($val['active'] == 1){echo 'selected="selected"';}?>>Active</option>
                                    </select>
                                    <p class="help-block">Use muun market module.</p>
                                </div>
                            </div>
							<div class="control-group">
								<label class="control-label" for="muuns_per_page">Muuns Per Page</label>
								<div class="controls">
									<select id="muuns_per_page" name="muuns_per_page" required>
										<?php for ($a = 0; $a <= 100; $a++): ?>
											<option
												value="<?php echo $a; ?>" <?php if ($val['muuns_per_page'] == $a) {
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
							<div class="control-group">
								<label class="control-label" for="blacklist_items">BlackList</label>
								<div class="controls">
									<input type="text" data-role="tagsinput" class="span3 typeahead" id="blacklist_items"
										   name="blacklist_items"
										   value="<?php echo $val['blacklist_items']; ?>" />

									<p class="help-block">
									 Dissalowed muuns example, 16#1,16#2
									</p>
								</div>
							</div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" name="edit_muun_market_settings" id="edit_muun_market_settings">Save changes</button>
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
