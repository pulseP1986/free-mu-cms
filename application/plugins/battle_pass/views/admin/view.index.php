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
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Battle Pass Levels<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/pass-levels?server=<?php echo $key;?>"><?php echo $val['title'];?></a></li>
						 <?php endforeach;?>
                    </ul>
                </li>
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
	var battlePass = new battlePass();
	battlePass.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
	$(document).ready(function(){
		$('form[id^="settings_form_"]').on("submit", function (e){
			e.preventDefault();
			battlePass.saveSettings($(this));
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
                        <form class="form-horizontal" method="POST" action="" id="settings_form_<?php echo $key;?>">
							<input type="hidden" id="server"  name="server" value="<?php echo $key; ?>"/>
                            <div class="control-group">
                                <label class="control-label" for="active">Status </label>
                                <div class="controls">
                                    <select id="active" name="active" required>
                                        <option value="0" <?php if($val['active'] == 0){echo 'selected="selected"';}?>>Inactive</option>
                                        <option value="1" <?php if($val['active'] == 1){echo 'selected="selected"';}?>>Active</option>
                                    </select>
                                    <p class="help-block">Use battle pass module.</p>
                                </div>
                            </div>
							<div class="control-group">
								<label class="control-label" for="battle_pass_start_time">Start Time </label>
								<div class="controls">
									<input type="text" class="span3 typeahead datetimepicker" id="battle_pass_start_time" name="battle_pass_start_time" value="<?php echo $val['battle_pass_start_time']; ?>" required />
									<p class="help-block">
									 From what date battle pass starts to run.
									</p>
									<p class="help-block"><span style="color:red;">Changing Start Time will reset all user progress.</span></p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="battle_pass_end_time">End Time </label>
								<div class="controls">
									<input type="text" class="span3 typeahead datetimepicker" id="battle_pass_end_time" name="battle_pass_end_time" value="<?php echo $val['battle_pass_end_time']; ?>" required />
									<p class="help-block">
									 When battle pass session will end.
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="sale_start_time">Sale Start Time </label>
								<div class="controls">
									<input type="text" class="span3 typeahead datetimepicker" id="sale_start_time" name="sale_start_time" value="<?php echo $val['sale_start_time']; ?>" required />
									<p class="help-block">
									 From what date will be able to upgrade battle pass.
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="sale_end_time">Sale End Time </label>
								<div class="controls">
									<input type="text" class="span3 typeahead datetimepicker" id="sale_end_time" name="sale_end_time" value="<?php echo $val['sale_end_time']; ?>" required />
									<p class="help-block">
									 When battle pass upgrade will be closed.
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="silver_pass_upgrade_price">Silver Pass </label>
								<div class="controls">
									<input type="text" class="span6 typeahead" id="silver_pass_upgrade_price" name="silver_pass_upgrade_price" value="<?php echo $val['silver_pass_upgrade_price']; ?>"/>
									<p class="help-block">Silver pass upgrade price.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="silver_pass_payment_type">Payment Type</label>
								<div class="controls">
									<select id="silver_pass_payment_type" name="silver_pass_payment_type" required>
										<option value="1" <?php if($val['silver_pass_payment_type'] == 1){ echo 'selected="selected"'; } ?>>Credits 1</option>
										<option value="2" <?php if($val['silver_pass_payment_type'] == 2){ echo 'selected="selected"'; } ?>>Credits 2</option>
									</select>
									<p>For credits types check your credits settings <a href="<?php echo $this->config->base_url; ?>admincp/manage-settings/credits" target="_blank">here</a></p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="platinum_pass_upgrade_price">Platinum Pass </label>
								<div class="controls">
									<input type="text" class="span6 typeahead" id="platinum_pass_upgrade_price" name="platinum_pass_upgrade_price" value="<?php echo $val['platinum_pass_upgrade_price']; ?>"/>
									<p class="help-block">Platinum pass upgrade price.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="platinum_pass_payment_type">Payment Type</label>
								<div class="controls">
									<select id="platinum_pass_payment_type" name="platinum_pass_payment_type" required>
										<option value="1" <?php if($val['platinum_pass_payment_type'] == 1){ echo 'selected="selected"'; } ?>>Credits 1</option>
										<option value="2" <?php if($val['platinum_pass_payment_type'] == 2){ echo 'selected="selected"'; } ?>>Credits 2</option>
									</select>
									<p>For credits types check your credits settings <a href="<?php echo $this->config->base_url; ?>admincp/manage-settings/credits" target="_blank">here</a></p>
								</div>
							</div>
							<div class="control-group">
                                <label class="control-label" for="allow_reset_pass_progress">Reset Battle Pass </label>
                                <div class="controls">
                                    <select id="allow_reset_pass_progress" name="allow_reset_pass_progress" required>
                                        <option value="0" <?php if($val['allow_reset_pass_progress'] == 0){echo 'selected="selected"';}?>>No</option>
                                        <option value="1" <?php if($val['allow_reset_pass_progress'] == 1){echo 'selected="selected"';}?>>Yes</option>
                                    </select>
                                    <p class="help-block">Allow reseting battle pass progress to start again.</p>
                                </div>
                            </div>
							<div class="control-group">
								<label class="control-label" for="reset_pass_progress_price">Reset Price </label>
								<div class="controls">
									<input type="text" class="span6 typeahead" id="reset_pass_progress_price" name="reset_pass_progress_price" value="<?php echo $val['reset_pass_progress_price']; ?>"/>
									<p class="help-block">Battle pass progress reset price.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="reset_pass_progress_payment_type">Payment Type</label>
								<div class="controls">
									<select id="reset_pass_progress_payment_type" name="reset_pass_progress_payment_type" required>
										<option value="1" <?php if($val['reset_pass_progress_payment_type'] == 1){ echo 'selected="selected"'; } ?>>Credits 1</option>
										<option value="2" <?php if($val['reset_pass_progress_payment_type'] == 2){ echo 'selected="selected"'; } ?>>Credits 2</option>
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
