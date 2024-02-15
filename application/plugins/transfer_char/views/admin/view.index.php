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
	var transferChar = new transferChar();
	transferChar.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
	$(document).ready(function(){
		$('form[id^="transfer_char_settings_form_"]').on("submit", function (e){
			e.preventDefault();
			transferChar.saveSettings($(this));
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
                        <form class="form-horizontal" method="POST" action="" id="transfer_char_settings_form_<?php echo $key;?>">
							<input type="hidden" id="server"  name="server" value="<?php echo $key; ?>"/>
                            <div class="control-group">
                                <label class="control-label" for="active">Status </label>
                                <div class="controls">
                                    <select id="active" name="active" required>
                                        <option value="0" <?php if($val['active'] == 0){echo 'selected="selected"';}?>>Inactive</option>
                                        <option value="1" <?php if($val['active'] == 1){echo 'selected="selected"';}?>>Active</option>
                                    </select>
                                    <p class="help-block">Use character transfer module.</p>
                                </div>
                            </div>
							<div class="control-group">
								<label class="control-label" for="allow_transfer_with_guild">Allow Guild</label>
								<div class="controls">
									<select id="allow_transfer_with_guild" name="allow_transfer_with_guild" required>
										<option
											value="0" <?php if ($val['allow_transfer_with_guild'] == 0) {
											echo 'selected="selected"';
										} ?>>No
										</option>
										<option
											value="1" <?php if ($val['allow_transfer_with_guild'] == 1) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
									</select>
									<p class="help-block">Allow transfer characters which have guild</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="allow_transfer_with_gens">Allow Gens</label>
								<div class="controls">
									<select id="allow_transfer_with_gens" name="allow_transfer_with_gens" required>
										<option
											value="0" <?php if ($val['allow_transfer_with_gens'] == 0) {
											echo 'selected="selected"';
										} ?>>No
										</option>
										<option
											value="1" <?php if ($val['allow_transfer_with_gens'] == 1) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
									</select>
									<p class="help-block">Allow transfer characters which have gens</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="max_character_level">Max Level </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="max_character_level"
										   name="max_character_level"
										   value="<?php if(isset($val['max_character_level'])){ echo $val['max_character_level']; } ?>" required />

									<p class="help-block">
									 Character max level allowed
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
								<label class="control-label" for="price">Price </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="price"
										   name="price"
										   value="<?php echo $val['price']; ?>" required />

									<p class="help-block">
									 Character transfer price
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="price_type">Price Type</label>

								<div class="controls">
									<select id="price_type" name="price_type" required>
										<option value="1" <?php if($val['price_type'] == 1){
											echo 'selected="selected"';
										} ?>>Credits 1
										</option>
										<option value="2" <?php if($val['price_type'] == 2){
											echo 'selected="selected"';
										} ?>>Credits 2
										</option>
									</select>

									<p>For credits types check your credits settings <a
												href="<?php echo $this->config->base_url; ?>admincp/manage-settings/credits"
												target="_blank">here</a></p>
								</div>
							</div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" name="edit_transfer_char_settings" id="edit_transfer_char_settings">Save changes</button>
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
