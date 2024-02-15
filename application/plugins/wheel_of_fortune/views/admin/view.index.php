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
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Rewards<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/rewards?server=<?php echo $key;?>"><?php echo $val['title'];?></a></li>
						 <?php endforeach;?>
                    </ul>
                </li>
				<li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/logs">Logs</a></li>
			</ul>
            <div class="clearfix"></div>
        </div>
    </div>
	<?php if(isset($js)): ?>
	<script src="<?php echo $js;?>"></script>
	<script type="text/javascript">	
	var wheelOfFortune = new wheelOfFortune();
	wheelOfFortune.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
	$(document).ready(function(){
		$('form[id^="settings_form_"]').on("submit", function (e){
			e.preventDefault();
			wheelOfFortune.saveSettings($(this));
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
                                    <p class="help-block">Use wheel of fortune module.</p>
                                </div>
                            </div>
							<div class="control-group">
								<label class="control-label" for="spin_price">Spin Price </label>
								<div class="controls">
									<input type="text" class="span6" id="spin_price" name="spin_price" value="<?php if(isset($val['spin_price'])){ echo $val['spin_price']; } ?>" placeholder="500" required />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="spin_currency">Currency Required </label>
								<div class="controls">
									<select id="spin_currency" name="spin_currency" required>
										<option value="1" <?php if(isset($val['spin_currency']) && $val['spin_currency'] == 1){ echo 'selected="selected"'; } ?>>Credits 1</option>
										<option value="2" <?php if(isset($val['spin_currency']) && $val['spin_currency'] == 2){ echo 'selected="selected"'; } ?>>Credits 2</option>
										<option value="5" <?php if(isset($val['spin_currency']) && $val['spin_currency'] == 5){ echo 'selected="selected"'; } ?>>Credits 3</option>
										<option value="3" <?php if(isset($val['spin_currency']) && $val['spin_currency'] == 3){ echo 'selected="selected"'; } ?>>WCoin</option>
										<option value="4" <?php if(isset($val['spin_currency']) && $val['spin_currency'] == 4){ echo 'selected="selected"'; } ?>>GoblinPoint</option>
									</select>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="max_spins_per_day">Spins Per Day </label>
								<div class="controls">
									<input type="text" class="span6" id="max_spins_per_day" name="max_spins_per_day" value="<?php if(isset($val['max_spins_per_day'])){ echo $val['max_spins_per_day']; } ?>" placeholder="5" required />
									<p class="help-block">Maximum spins per day.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="total_rewards">Total Rewards </label>
								<div class="controls">
									<input type="text" class="span6" id="total_rewards" name="total_rewards" value="<?php if(isset($val['total_rewards'])){ echo $val['total_rewards']; } ?>" />
									<p class="help-block">Total rewards in wheel.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="webhook_url">Discord Webhook Url </label>
								<div class="controls">
									<input type="text" class="span6" id="webhook_url" name="webhook_url" value="<?php if(isset($val['webhook_url'])){ echo $val['webhook_url']; } ?>" placeholder="" />
									<p class="help-block">Sent notification of winning in discord.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="style">Style </label>
								<div class="controls">
									<select class="style" id="style" name="style" required>
										<option value="1" <?php if(isset($val['style']) && $val['style'] == 1){ echo 'selected="selected"'; } ?>>Style 1</option>
										<option value="2" <?php if(isset($val['style']) && $val['style'] == 2){ echo 'selected="selected"'; } ?>>Style 2</option>
										<option value="3" <?php if(isset($val['style']) && $val['style'] == 3){ echo 'selected="selected"'; } ?>>Style 3</option>
									</select>
									<p class="help-block">Style1 - <a href="https://dmncms.net/screenshots/monthly_2021_10/Screenshot_15.jpg.57f5fb3e8ebad6c2ca18510aa8f1e5f3.jpg" target="_blank">View</a></p>
									<p class="help-block">Style2 - <a href="https://dmncms.net/screenshots/monthly_2023_05/Screenshot_3.png.113e2bbd8de771cffdfd0cbab0f17fe2.png" target="_blank">View</a></p>
									<p class="help-block">Style3 - <a href="https://dmncms.net/screenshots/monthly_2023_05/1655688736_FireShotCapture043-DmNMuCMS-127.0.0.1.jpg.96580553d7ea99532f8473b732be8090.jpg" target="_blank">View</a></p>
								</div>
							</div>
							<div class="special_award" style="display:none;">
								<div class="control-group">
									<label class="control-label" for="special_award_id">Special Award Id </label>
									<div class="controls">
										<input type="text" class="span6" id="special_award_id" name="special_award_id" value="<?php if(isset($val['special_award_id'])){ echo $val['special_award_id']; } ?>" />
										<p class="help-block">Special award id from rewards.</p>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label" for="spins_required_for_special_award">Required Spins </label>
									<div class="controls">
										<input type="text" class="span6" id="spins_required_for_special_award" name="spins_required_for_special_award" value="<?php if(isset($val['spins_required_for_special_award'])){ echo $val['spins_required_for_special_award']; } ?>" />
										<p class="help-block">Required spins for special reward.</p>
									</div>
								</div>
							</div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" name="edit_settings" id="edit_settings">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endforeach;?>
				<script>
				$(document).ready(function() {
					$('.style').on("change", function() {
						if ($(this).val() == 2) {
							$('.special_award').show();
						} else {
							$('.special_award').hide();
						}
					});
					var style = $(".style option:selected").val();
					if (style == 2) {
						$('.special_award').show();
					} else {
						$('.special_award').hide();
					}
				});
				</script>
            </div>
        </div>
    </div>
</div>
<?php
$this->load->view('admincp' . DS . 'view.footer');
?>
