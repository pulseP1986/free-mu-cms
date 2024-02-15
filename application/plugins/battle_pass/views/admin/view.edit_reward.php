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
                <li role="presentation" ><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/admin">Server Settings</a></li>
				<li role="presentation" class="dropdown active">
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
		$("#battle_pass_sortable").find("tbody#battle_pass_sortable_content").sortable({
			placeholder: 'ui-state-highlight',
			opacity: 0.6,
			cursor: 'move',

			update: function() {
				battlePass.saveRewardOrder('<?php echo $aid;?>','<?php echo $server;?>');
			}

		});
	});
	</script>
	<?php endif;?>
	<div class="row-fluid">
        <div class="box span12">
            <div class="tab-content">          
				<div class="box-header well">
					<h2><i class="icon-edit"></i> Edit Reward</h2>
				</div>
				<div class="box-content">
					<?php
					if(isset($error)){
						echo '<div class="alert alert-error">' . $error . '</div>';
					}
					if(isset($success)){
						echo '<div class="alert alert-success">' . $success . '</div>';
					}
					?>
					<form class="form-horizontal" method="POST" action="">
						<div class="control-group">
							<label class="control-label" for="pass_type">Pass Type </label>
							<div class="controls">
								<select class="span3" id="pass_type" name="pass_type" required>
									<option value="0" <?php if(isset($achData['pass_type']) && $achData['pass_type'] == 0){ echo 'selected="selected"'; } ?>>Free</option>
									<option value="1" <?php if(isset($achData['pass_type']) && $achData['pass_type'] == 1){ echo 'selected="selected"'; } ?>>Silver</option>
									<option value="2" <?php if(isset($achData['pass_type']) && $achData['pass_type'] == 2){ echo 'selected="selected"'; } ?>>Platinum</option>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="title"> Title</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="title" name="title" value="<?php if(isset($achData['title'])){ echo $achData['title']; } ?>" required />
								<p class="help-block">Reward title</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="reward_type">Type </label>
							<div class="controls">
								<select class="span3" id="reward_type" name="reward_type" required>
									<option value="" <?php if(isset($achData['reward_type']) && $achData['reward_type'] == ''){ echo 'selected="selected"'; } ?>>Select</option>
									<option value="1" <?php if(isset($achData['reward_type']) && $achData['reward_type'] == 1){ echo 'selected="selected"'; } ?>>Credits 1</option>
									<option value="2" <?php if(isset($achData['reward_type']) && $achData['reward_type'] == 2){ echo 'selected="selected"'; } ?>>Credits 2</option>
									<option value="3" <?php if(isset($achData['reward_type']) && $achData['reward_type'] == 3){ echo 'selected="selected"'; } ?>>WCoins</option>
									<option value="4" <?php if(isset($achData['reward_type']) && $achData['reward_type'] == 4){ echo 'selected="selected"'; } ?>>Goblin Points</option>
									<option value="5" <?php if(isset($achData['reward_type']) && $achData['reward_type'] == 5){ echo 'selected="selected"'; } ?>>Zen on Character</option>
									<option value="6" <?php if(isset($achData['reward_type']) && $achData['reward_type'] == 6){ echo 'selected="selected"'; } ?>>Zen in WebWallet</option>
									<option value="7" <?php if(isset($achData['reward_type']) && $achData['reward_type'] == 7){ echo 'selected="selected"'; } ?>>Ruud</option>
									<option value="8" <?php if(isset($achData['reward_type']) && $achData['reward_type'] == 8){ echo 'selected="selected"'; } ?>>Vip Package</option>
									<option value="9" <?php if(isset($achData['reward_type']) && $achData['reward_type'] == 9){ echo 'selected="selected"'; } ?>>Item</option>
									<option value="10" <?php if(isset($achData['reward_type']) && $achData['reward_type'] == 10){ echo 'selected="selected"'; } ?>>Item hex</option>
									<option value="11" <?php if(isset($achData['reward_type']) && $achData['reward_type'] == 11){ echo 'selected="selected"'; } ?>>Buff Item</option>
									<option value="12" <?php if(isset($achData['reward_type']) && $achData['reward_type'] == 12){ echo 'selected="selected"'; } ?>>Wheel Spin</option>
								</select>
							</div>
						</div>
						<div class="control-group" id="amount" style="display:none;">
							<label class="control-label" for="amount">Reward Amount</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="amount" name="amount" value="<?php if(isset($achData['amount'])){ echo $achData['amount']; } ?>" placeholder="100" />				
							</div>
						</div>
						<div class="control-group" id="vip" style="display:none;">
							<label class="control-label" for="total_stats">Package</label>
							<div class="controls">
								<?php $vip_packages = $this->Madmin->load_vip_packages(); ?>
									<?php if(!empty($vip_packages)){ ?>
									<select id="vip_type" name="vip_type">
										<option value="">Select</option>
										<?php foreach($vip_packages AS $package){ ?>
										<option value="<?php echo $package['id'];?>" <?php if(isset($achData['vip_type']) && $achData['vip_type'] == $package['id']){ echo 'selected="selected"'; } ?>><?php echo $package['package_title'];?></option>
										<?php } ?>
									</select>
									<?php } else { ?>
									<select id="vip_type" name="vip_type">
										<option value="">Select</option>
									</select>
									<?php } ?>								
							</div>
						</div>
						<div class="control-group" id="buff_item" style="display:none;">
							<label class="control-label" for="buff">Items</label>
							<div class="controls" id="bufflist">
								<?php 
								if(!empty($achData['buffs'])){ 
									$i = 0;
									foreach($achData['buffs'] AS $data){
								?>
								<div id="buff_<?php echo $i + 1;?>" <?php if($i > 0){?>style="margin-top:2px;"<?php } ?>>
									Category: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_category_buff[]" value="<?php echo $data['cat'];?>" /> 
									Index: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_index_buff[]" value="<?php echo $data['id'];?>" /> 
									Expire (minutes): <input class="form-control" style="width:40px; display: inline;" type="text" name="item_expires_buff[]" value="<?php echo $data['expires'];?>" placeholder="" /> 
									<button class="btn btn-danger removeBuff" name="removeItem" id="bremove_<?php echo $i + 1;?>"> <i class="icon-remove"></i></button>
									<?php if($i == 0){?><button class="btn btn-success" name="addBuff" id="addBuff"><i class="icon-plus"></i></button><?php } ?>
								</div>
								<?php
										$i++;
									}
								} else { 
								?>
								<div id="buff_1">
									Category: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_category_buff[]" value="" /> 
									Index: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_index_buff[]" value="" /> 
									Expire (minutes): <input class="form-control" style="width:40px; display: inline;" type="text" name="item_expires_buff[]" value="" placeholder="" /> 
									<button class="btn btn-danger removeBuff" name="removeBuff" id="bremove_1"> <i class="icon-remove"></i></button>
									<button class="btn btn-success" name="addBuff" id="addBuff"><i class="icon-plus"></i></button>
								</div>
								<?php } ?>	
							</div>
						</div>
						<div id="single_item" style="display:none;">
							<div class="control-group">
								<label class="control-label" for="items">Items</label>
								<div class="controls" id="itemlist">
									<?php 
									if(!empty($achData['items']) && !isset($achData['items'][0]['hex'])){ 
										$i = 0;
										foreach($achData['items'] AS $data){
									?>
									<div id="item_<?php echo $i + 1;?>" <?php if($i > 0){?>style="margin-top:2px;"<?php } ?>>
										Category: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_category[]" value="<?php echo $data['cat'];?>" /> 
										Index: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_index[]" value="<?php echo $data['id'];?>" /> 
										Dur: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_dur[]" value="<?php echo $data['dur'];?>" /> 
										Level: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_level[]" value="<?php echo $data['lvl'];?>" placeholder="0-15" /> 
										Skill: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_skill[]" value="<?php echo $data['skill'];?>" placeholder="0/1" /> 
										Luck: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_luck[]" value="<?php echo $data['luck'];?>" placeholder="0/1" /><br />
										Option: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_option[]" value="<?php echo $data['opt'];?>" placeholder="0-7" /> 
										Excellent: <input class="form-control" style="width:90px; display: inline;" type="text" name="item_excellent[]" value="<?php echo $data['exe'];?>" placeholder="1,1,1,1,1,1|0-6" /> 
										Ancient: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_ancient[]" value="<?php echo $data['anc'];?>" placeholder="0/5/6/9/10" /> 
										Expire (minutes): <input class="form-control" style="width:40px; display: inline;" type="text" name="item_expires[]" value="<?php echo $data['expires'];?>" placeholder="" /> 
										<button class="btn btn-danger removeItem" name="removeItem" id="remove_<?php echo $i + 1;?>"> <i class="icon-remove"></i></button>
										<?php if($i == 0){?><button class="btn btn-success" name="addItem" id="addItem"><i class="icon-plus"></i></button><?php } ?>
									</div>
									<?php
											$i++;
										}
									} else { 
									?>
									<div id="item_1">
										Category: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_category[]" value="" /> 
										Index: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_index[]" value="" /> 
										Dur: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_dur[]" value="" /> 
										Level: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_level[]" value="" placeholder="0-15" /> 
										Skill: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_skill[]" value="" placeholder="0/1" /> 
										Luck: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_luck[]" value="" placeholder="0/1" /><br /> 
										Option: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_option[]" value="" placeholder="0-7" /> 
										Excellent: <input class="form-control" style="width:90px; display: inline;" type="text" name="item_excellent[]" value="" placeholder="1,1,1,1,1,1|0-6" /> 
										Ancient: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_ancient[]" value="" placeholder="0/5/6/9/10" /> 
										Expire (minutes): <input class="form-control" style="width:40px; display: inline;" type="text" name="item_expires[]" value="" placeholder="" /> 
										<button class="btn btn-danger removeItem" name="removeItem" id="remove_1"> <i class="icon-remove"></i></button>
										<button class="btn btn-success" name="addItem" id="addItem"><i class="icon-plus"></i></button>
									</div>
									<?php } ?>	
								</div>
							</div>
							<div class="control-group">
								<div class="controls">
									<label class="checkbox inline">
										<input type="checkbox" name="display_item_code" data-no-uniform="true" value="1" <?php if(isset($achData['display_item_title']) && $achData['display_item_title'] == 1){ echo 'checked'; } ?>>
										Display reward title instead of items list
									</label>
									<div style="clear:both"></div>
								</div>
							</div>
						</div>	
						<div id="single_item_hex" style="display:none;">	
							<div class="control-group">
								<label class="control-label" for="items">Items</label>
								<div class="controls" id="itemlist_hex">
									<?php 
									if(!empty($achData['items']) && !isset($achData['items'][0]['cat'])){ 
										$i = 0;
										foreach($achData['items'] AS $data){
									?>
									<div id="itemhex_<?php echo $i + 1;?>" <?php if($i > 0){?>style="margin-top:2px;"<?php } ?>>
										Hex: <input class="form-control" style="width:500px; display: inline;" type="text" name="item_hex[]" value="<?php echo $data['hex'];?>" /> 
										<button class="btn btn-danger removeItemHex" name="removeItemHex" id="removehex_<?php echo $i + 1;?>"> <i class="icon-remove"></i></button>
										<?php if($i == 0){?><button class="btn btn-success" name="addItemHex" id="addItemHex"><i class="icon-plus"></i></button><?php } ?>
									</div>
									<?php
											$i++;
										}
									} else { 
									?>
									<div id="itemhex_1">
										Hex: <input class="form-control" style="width:500px; display: inline;" type="text" name="item_hex[]" value="" /> 
										<button class="btn btn-danger removeItemHex" name="removeItemHex" id="removehex_1"> <i class="icon-remove"></i></button>
										<button class="btn btn-success" name="addItemHex" id="addItemHex"><i class="icon-plus"></i></button>
									</div>
									<?php } ?>		
								</div>
							</div>
							<div class="control-group">
								<div class="controls">
									<label class="checkbox inline">
										<input type="checkbox" name="display_item_code_hex" data-no-uniform="true" value="1" <?php if(isset($achData['display_item_title']) && $achData['display_item_title'] == 1){ echo 'checked'; } ?>>
										Display reward title instead of items list
									</label>
									<div style="clear:both"></div>
								</div>
							</div>
						</div>
						<div class="form-actions">
							<button type="submit" class="btn btn-primary" name="edit_reward" id="edit_reward">Submit</button>
						</div>
					</form>
					<script>
						$(document).ready(function() {
							$('#addBuff').on("click", function(e) {
								e.preventDefault();
								var divId = parseInt($('#bufflist').children().last().attr('id').split('_')[1]) + 1;

								var html = '<div id="buff_'+divId+'" style="margin-top:2px;"><hr />';
								html += ' Category: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_category_buff[]" value="" />';
								html += ' Index: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_index_buff[]" value="" />';
								html += ' Expire (minutes): <input class="form-control" style="width:40px; display: inline;" type="text" name="item_expires_buff[]" value="" placeholder="" />'; 
								html += ' <button class="btn btn-danger removeBuff" name="removeBuff" id="bremove_'+divId+'"> <i class="icon-remove"></i></button>';
								html += '</div>';
								$('#bufflist').append(html);
							});
							$('#addItem').on("click", function(e) {
								e.preventDefault();
								var divId = parseInt($('#itemlist').children().last().attr('id').split('_')[1]) + 1;

								var html = '<div id="item_'+divId+'" style="margin-top:2px;"><hr />';
								html += ' Category: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_category[]" value="" />';
								html += ' Index: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_index[]" value="" />';
								html += ' Dur: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_dur[]" value="" />';
								html += ' Level: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_level[]" value="" placeholder="0-15" />';
								html += ' Skill: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_skill[]" value="" placeholder="0/1" />';
								html += ' Luck: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_luck[]" value="" placeholder="0/1" /><br />';
								html += ' Option: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_option[]" value="" placeholder="0-7" />';
								html += ' Excellent: <input class="form-control" style="width:90px; display: inline;" type="text" name="item_excellent[]" value="" placeholder="1,1,1,1,1,1|0-6" />';
								html += ' Ancient: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_ancient[]" value="" placeholder="0/5/6/9/10" />';
								html += ' Expire (minutes): <input class="form-control" style="width:40px; display: inline;" type="text" name="item_expires[]" value="" placeholder="" />'; 
								html += ' <button class="btn btn-danger removeItem" name="removeItem" id="remove_'+divId+'"> <i class="icon-remove"></i></button>';
								html += '</div>';
								$('#itemlist').append(html);
							});
							$('#addItemHex').on("click", function(e) {
								e.preventDefault();
								var divId = parseInt($('#itemlist_hex').children().last().attr('id').split('_')[1]) + 1;

								var html = '<div id="itemhex_'+divId+'" style="margin-top:2px;">';
								html += ' Hex: <input class="form-control" style="width:500px; display: inline;" type="text" name="item_hex[]" value="" />';
								html += ' <button class="btn btn-danger removeItemHex" name="removeItemHex" id="removehex_'+divId+'"> <i class="icon-remove"></i></button>';
								html += '</div>';
								$('#itemlist_hex').append(html);
							});
							$(document).on("click", ".removeItem", function(e) {
								e.preventDefault();
								var id = $(this).attr('id').split('_')[1];
								if(id == 1)
									return false;
								$('#item_'+id).empty();
								$('#item_'+id).hide();
							});
							$(document).on("click", ".removeBuff", function(e) {
								e.preventDefault();
								var id = $(this).attr('id').split('_')[1];
								if(id == 1)
									return false;
								$('#buff_'+id).empty();
								$('#buff_'+id).hide();
							});
							$(document).on("click", ".removeItemHex", function(e) {
								e.preventDefault();
								var id = $(this).attr('id').split('_')[1];
								if(id == 1)
									return false;
								$('#itemhex_'+id).empty();
								$('#itemhex_'+id).hide();
							});
							
							$('#reward_type').on("change", function() {
								if ($(this).val() == 1 || $(this).val() == 2 || $(this).val() == 3 || $(this).val() == 4  || $(this).val() == 5  || $(this).val() == 6  || $(this).val() == 7 || $(this).val() == 12) {
									$('#amount').show();
								} else {
									$('#amount').hide();
								}
								if ($(this).val() == 8) {
									$('#vip').show();
								} else {
									$('#vip').hide();
								}
								if ($(this).val() == 9) {
									$('#single_item').show();
								} else {
									$('#single_item').hide();
								}
								if ($(this).val() == 10) {
									$('#single_item_hex').show();
								} else {
									$('#single_item_hex').hide();
								}
								if ($(this).val() == 11) {
									$('#buff_item').show();
								} else {
									$('#buff_item').hide();
								}
							});
							
							var coupon_type = $("#reward_type option:selected").val();
							if (coupon_type == 1 || coupon_type == 2 || coupon_type == 3 || coupon_type == 4 || coupon_type == 5 || coupon_type == 6 || coupon_type == 7 || coupon_type == 12) {
								$('#amount').show();
							} else {
								$('#amount').hide();
							}
							if (coupon_type == 8) {
								$('#vip').show();
							} else {
								$('#vip').hide();
							}
							if (coupon_type == 9) {
								$('#single_item').show();
							} else {
								$('#single_item').hide();
							}
							if (coupon_type == 10) {
								$('#single_item_hex').show();
							} else {
								$('#single_item_hex').hide();
							}
							if (coupon_type == 11) {
								$('#buff_item').show();
							} else {
								$('#buff_item').hide();
							}
						});
						</script>
				</div>
            </div>
        </div>
    </div>
	<div class="row-fluid">
        <div class="box span12">
			<div class="box-header well">
				<h2><i class="icon-edit"></i> <?php echo $battle_pass[$server][$achKey]['title']; ?> Rewards List</h2>
			</div>
			<div class="box-content">
				<table class="table"  id="battle_pass_sortable">
					<thead>
						<tr>
							<th>Title</th>
							<th>Pass Type</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody id="battle_pass_sortable_content" style="cursor: move;">
					<?php
						if(!empty($reward_list[$server][$aid])){
							foreach($reward_list[$server][$aid] AS $key => $settings){
								?>
								<tr id="<?php echo $key; ?>">          
									<td><?php echo $settings['title'];?></td>
									<td><?php 
									if(isset($settings['pass_type'])){
										switch($settings['pass_type']){
											case 0;
												echo 'Free';
											break;
											case 1;
												echo 'Silver';
											break;
											case 2;
												echo 'Platinum';
											break;
										}
									}
									else{
										echo 'Unknown';
									}
									?>
									</td>
									<td>
										<a class="btn btn-info" href="<?php echo $this->config->base_url . 'battle-pass/edit-reward/'.$aid.'/' . $key;?>/<?php echo $server;?>">
											<i class="icon-edit icon-white"></i>  
											Edit                                            
										</a>
										<a class="btn btn-danger" onclick="ask_url('Are you sure to delete reward?', '<?php echo $this->config->base_url . 'battle-pass/delete-reward/'.$aid.'/' . $key;?>/<?php echo $server;?>')" href="#">
											<i class="icon-trash icon-white"></i> 
											Delete
										</a>
									</td>
								</tr>        
								<?php
							}
						} else{
							echo '<tr><td colspan="2"><div class="alert alert-info">No rewards.</div></td></tr>';
						}
					?>
					</tbody>
                </table>
			</div>
        </div>
    </div>
</div>
<?php
$this->load->view('admincp' . DS . 'view.footer');
?>
