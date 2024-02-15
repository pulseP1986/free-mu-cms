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
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Achievement List<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/achievement-list?server=<?php echo $key;?>"><?php echo $val['title'];?></a></li>
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
	var achievements = new achievements();
	achievements.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
	$(document).ready(function(){
		$('form[id^="achievements_settings_form_"]').on("submit", function (e){
			e.preventDefault();
			achievements.saveSettings($(this));
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
                        <form class="form-horizontal" method="POST" action="" id="achievements_settings_form_<?php echo $key;?>">
							<input type="hidden" id="server"  name="server" value="<?php echo $key; ?>"/>
                            <div class="control-group">
                                <label class="control-label" for="active">Status </label>
                                <div class="controls">
                                    <select id="active" name="active" required>
                                        <option value="0" <?php if($val['active'] == 0){echo 'selected="selected"';}?>>Inactive</option>
                                        <option value="1" <?php if($val['active'] == 1){echo 'selected="selected"';}?>>Active</option>
                                    </select>
                                    <p class="help-block">Use achievement module.</p>
                                </div>
                            </div>
							<div class="control-group">
								<label class="control-label" for="required_level">Req Level </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="required_level" name="required_level" value="<?php echo $val['required_level']; ?>" required />

									<p class="help-block">
									Required character level for unlock
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="required_mlevel">Req Master Level </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="required_mlevel" name="required_mlevel" value="<?php echo $val['required_mlevel']; ?>" required />

									<p class="help-block">
									Required character master level for unlock
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="required_resets">Req Resets </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="required_resets" name="required_resets" value="<?php echo $val['required_resets']; ?>" required />

									<p class="help-block">
									Required character resets for unlock
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="required_gresets">Req Grand Resets </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="required_gresets" name="required_gresets" value="<?php echo $val['required_gresets']; ?>" required />

									<p class="help-block">
									Required character grand resets for unlock
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="required_zen">Req Zen </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="required_zen" name="required_zen" value="<?php echo $val['required_zen']; ?>" required />

									<p class="help-block">
									Required character zen for unlock
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="required_ruud">Req Ruud </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="required_ruud" name="required_ruud" value="<?php echo $val['required_ruud']; ?>" required />

									<p class="help-block">
									Required character ruud for unlock
									</p>
								</div>
							</div>
							<div class="control-group">
                                <label class="control-label" for="rankings_active">Rankings Status </label>
                                <div class="controls">
                                    <select id="rankings_active" name="rankings_active" required>
                                        <option value="0" <?php if(isset($val['rankings_active']) && $val['rankings_active'] == 0){echo 'selected="selected"';}?>>Inactive</option>
                                        <option value="1" <?php if(isset($val['rankings_active']) && $val['rankings_active'] == 1){echo 'selected="selected"';}?>>Active</option>
                                    </select>
                                    <p class="help-block">Use achievement rankings module.</p>
                                </div>
                            </div>
							<div class="control-group">
								<label class="control-label" for="rankings_amount">Ranking Count </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="rankings_amount" name="rankings_amount" value="<?php echo $val['rankings_amount']; ?>" required />

									<p class="help-block">
									How many players display in rankings
									</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="rankings_cache_time">Ranking Cache </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="rankings_cache_time" name="rankings_cache_time" value="<?php echo $val['rankings_cache_time']; ?>" required />

									<p class="help-block">
									Cache time in seconds
									</p>
								</div>
							</div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" name="edit_achievements_settings" id="edit_achievements_settings">Save changes</button>
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
