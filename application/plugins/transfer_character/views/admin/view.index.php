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
				<li role="presentation" class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Logs<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/logs?server=<?php echo $key;?>"><?php echo $val['title'];?></a></li>
						 <?php endforeach;?>
                    </ul>
                </li>
			</ul>
            <div class="clearfix"></div>
        </div>
    </div>
	<?php if(isset($js)): ?>
	<script src="<?php echo $js;?>"></script>
	<script type="text/javascript">	
	var pluginJs = new transferChar();
	pluginJs.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
	$(document).ready(function(){
		$('form[id^="settings_form_"]').on("submit", function (e){
			e.preventDefault();
			pluginJs.saveSettings($(this));
		});
	});
	</script>
	<?php endif;?>
	<div class="row-fluid">
        <div class="box span12">
            <div class="tab-content">
				<?php 
				$i = 0;	
				$servers = [];
				foreach($server_list AS $key => $data):				
					$val = ($is_multi_server == 0) ? $plugin_config : (isset($plugin_config[$key]) ? $plugin_config[$key] : false);
					$i++;
				?>
                <div role="tabpanel" class="tab-pane fade in <?php if($i == 1){?>active<?php }?>" id="<?php echo $key;?>">
                    <div class="box-header well">
                        <h2><i class="icon-edit"></i> <?php echo $data['title'];?> Server Settings</h2>
                    </div>
                    <div class="box-content">
                        <form class="form-horizontal" method="POST" action="" id="settings_form_<?php echo $key;?>">
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
								<label class="control-label" for="to">Transfer To</label>
								<div class="controls">
									<select id="to" name="to" required>
										<option value="">Select</option>
										<?php foreach($server_list AS $key2 => $data2){ ?>		
										<?php if($key != $key2){ ?>	
										<option value="<?php echo $key2;?>" <?php if ($val['to'] == $key2) { echo 'selected="selected"'; } ?>><?php echo $data2['title'];?></option>
										<?php } ?>
										<?php } ?>
									</select>
									<p class="help-block">Allow transfer to server</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="transfer_items">Transfer Items</label>
								<div class="controls">
									<select id="transfer_items" name="transfer_items" required>
										<option
											value="0" <?php if ($val['transfer_items'] == 0) {
											echo 'selected="selected"';
										} ?>>No
										</option>
										<option
											value="1" <?php if ($val['transfer_items'] == 1) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
									</select>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="transfer_muun">Transfer Muuns</label>
								<div class="controls">
									<select id="transfer_muun" name="transfer_muun" required>
										<option
											value="0" <?php if ($val['transfer_muun'] == 0) {
											echo 'selected="selected"';
										} ?>>No
										</option>
										<option
											value="1" <?php if ($val['transfer_muun'] == 1) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
									</select>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="transfer_gens">Transfer Gens</label>
								<div class="controls">
									<select id="transfer_gens" name="transfer_gens" required>
										<option
											value="0" <?php if ($val['transfer_gens'] == 0) {
											echo 'selected="selected"';
										} ?>>No
										</option>
										<option
											value="1" <?php if ($val['transfer_gens'] == 1) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
									</select>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="delete_char">Delete Char</label>
								<div class="controls">
									<select id="delete_char" name="delete_char" required>
										<option
											value="0" <?php if ($val['delete_char'] == 0) {
											echo 'selected="selected"';
										} ?>>No
										</option>
										<option
											value="1" <?php if ($val['delete_char'] == 1) {
											echo 'selected="selected"';
										} ?>>Yes
										</option>
									</select>
									<p class="help-block">Delete character from old serve after transfer.</p>
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

									<p>For credits types check your credits settings <a href="<?php echo $this->config->base_url; ?>admincp/manage-settings/credits" target="_blank">here</a></p>
								</div>
							</div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" name="edit_settings" id="edit_settings">Save changes</button>
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
