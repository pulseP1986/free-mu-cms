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
				</ul>
            <div class="clearfix"></div>
        </div>
    </div>
	<?php if(isset($js)): ?>
	<script src="<?php echo $js;?>"></script>
	<script type="text/javascript">	
	var characterMarket = new characterMarket();
	characterMarket.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
	$(document).ready(function(){
		$('form[id^="character_market_settings_form_"]').on("submit", function (e){
			e.preventDefault();
			characterMarket.saveSettings($(this));
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
                        <form class="form-horizontal" method="POST" action="" id="character_market_settings_form_<?php echo $key;?>">
							<input type="hidden" id="server"  name="server" value="<?php echo $key; ?>"/>
                            <div class="control-group">
                                <label class="control-label" for="active">Status </label>
                                <div class="controls">
                                    <select id="active" name="active" required>
                                        <option value="0" <?php if($val['active'] == 0){echo 'selected="selected"';}?>>Inactive</option>
                                        <option value="1" <?php if($val['active'] == 1){echo 'selected="selected"';}?>>Active</option>
                                    </select>
                                    <p class="help-block">Use character market module.</p>
                                </div>
                            </div>
							<div class="control-group">
								<label class="control-label" for="characters_per_page">Character Per Page</label>
								<div class="controls">
									<select id="characters_per_page" name="characters_per_page" required>
										<?php for ($a = 0; $a <= 100; $a++): ?>
											<option
												value="<?php echo $a; ?>" <?php if ($val['characters_per_page'] == $a) {
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
								<label class="control-label" for="sale_price_minimum">Min Price </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="sale_price_minimum"
										   name="sale_price_minimum"
										   value="<?php echo $val['sale_price_minimum']; ?>" required />

									<p class="help-block">
									 Character selling minimum price
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="sale_price_maximum">Max Price </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="sale_price_maximum"
										   name="sale_price_maximum"
										   value="<?php echo $val['sale_price_maximum']; ?>" required />

									<p class="help-block">
									 Character selling maximum price
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="allow_sell_with_guild">Allow Guild</label>
								<div class="controls">
									<select id="allow_sell_with_guild" name="allow_sell_with_guild" required>
										<option
											value="0" <?php if ($val['allow_sell_with_guild'] == 0) {
											echo 'selected="selected"';
										} ?>>No
										</option>
										<option
											value="1" <?php if ($val['allow_sell_with_guild'] == 1) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
									</select>
									<p class="help-block">Allow selling characters which have guild</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="allow_sell_with_gens">Allow Gens</label>
								<div class="controls">
									<select id="allow_sell_with_gens" name="allow_sell_with_gens" required>
										<option
											value="0" <?php if ($val['allow_sell_with_gens'] == 0) {
											echo 'selected="selected"';
										} ?>>No
										</option>
										<option
											value="1" <?php if ($val['allow_sell_with_gens'] == 1) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
									</select>
									<p class="help-block">Allow selling characters which have gens</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="max_character_level">Max Level </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="max_character_level"
										   name="max_character_level"
										   value="<?php if(isset($val['max_character_level'])){ echo $val['max_character_level']; } ?>" required />

									<p class="help-block">
									 Character max level allowed for selling
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="min_character_level">Min Level </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="min_character_level"
										   name="min_character_level"
										   value="<?php if(isset($val['min_character_level'])){ echo $val['min_character_level']; } ?>" required />

									<p class="help-block">
									 Character min level allowed for selling
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="reg_empty_inventory">Req Empty Inventory</label>
								<div class="controls">
									<select id="reg_empty_inventory" name="reg_empty_inventory" required>
										<option
											value="1" <?php if ($val['reg_empty_inventory'] == 1) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
										<option
											value="0" <?php if ($val['reg_empty_inventory'] == 0) {
											echo 'selected="selected"';
										} ?>>No
										</option>
									</select>
									<p class="help-block">Character need to remove items from inventory before selling.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="reg_empty_exp_inventory">Req Empty Exp Inventory</label>
								<div class="controls">
									<select id="reg_empty_exp_inventory" name="reg_empty_exp_inventory" required>
										<option
											value="1" <?php if ($val['reg_empty_exp_inventory'] == 1) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
										<option
											value="0" <?php if ($val['reg_empty_exp_inventory'] == 0) {
											echo 'selected="selected"';
										} ?>>No
										</option>
									</select>
									<p class="help-block">Character need to remove items from expanded inventory before selling.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="reg_empty_personal_store">Req Empty Personal Store</label>
								<div class="controls">
									<select id="reg_empty_personal_store" name="reg_empty_personal_store" required>
										<option
											value="1" <?php if ($val['reg_empty_personal_store'] == 1) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
										<option
											value="0" <?php if ($val['reg_empty_personal_store'] == 0) {
											echo 'selected="selected"';
										} ?>>No
										</option>
									</select>
									<p class="help-block">Character need to remove items from personal store before selling.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="empty_s16_pstore">Remove Pesonal Store Items</label>
								<div class="controls">
									<select id="empty_s16_pstore" name="empty_s16_pstore" required>
										<option
											value="1" <?php if ($val['empty_s16_pstore'] == 1) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
										<option
											value="0" <?php if ($val['empty_s16_pstore'] == 0) {
											echo 'selected="selected"';
										} ?>>No
										</option>
									</select>
									<p class="help-block">Delete items from season16 personal store.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="delete_ruud">Delete Ruud</label>
								<div class="controls">
									<select id="delete_ruud" name="delete_ruud" required>
										<option
											value="1" <?php if ($val['delete_ruud'] == 1) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
										<option
											value="0" <?php if ($val['delete_ruud'] == 0) {
											echo 'selected="selected"';
										} ?>>No
										</option>
									</select>
									<p class="help-block">Delete character ruud on selling.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="delete_zen">Delete Zen</label>
								<div class="controls">
									<select id="delete_zen" name="delete_zen" required>
										<option
											value="1" <?php if ($val['delete_zen'] == 1) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
										<option
											value="0" <?php if ($val['delete_zen'] == 0) {
											echo 'selected="selected"';
										} ?>>No
										</option>
									</select>
									<p class="help-block">Delete character zen on selling.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="allow_remove_before_expires">Character Restore Limit</label>
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
									<p class="help-block">Allow restoring character from market only when it expires.</p>
								</div>
							</div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" name="edit_character_market_settings" id="edit_character_market_settings">Save changes</button>
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
