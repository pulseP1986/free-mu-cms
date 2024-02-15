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
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Rewards List<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/rewards-list?server=<?php echo $key;?>"><?php echo $val['title'];?></a></li>
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
	<?php
	if(isset($not_found)){
		echo '<div class="alert alert-error">' . $not_found . '</div>';
	}
	else{
		if(isset($error)){
			echo '<div class="alert alert-error">' . $error . '</div>';
		}
		if(isset($success)){
			echo '<div class="alert alert-success">' . $success . '</div>';
		}
	?>
	<div class="row-fluid">
        <div class="box span12">
            <div class="tab-content">          
				<div class="box-header well">
					<h2><i class="icon-edit"></i>Add Reward</h2>
				</div>
				<div class="box-content">
						<form class="form-horizontal" method="POST" action="" id="add_reward">	
							<div class="control-group">
								<label class="control-label" for="vip_type">Vip Type</label>
								<div class="controls">
									<?php if($config['vip_type'] == 1){ ?>
									<?php $vip_packages = $this->Madmin->load_vip_packages(); ?>
									<?php if(!empty($vip_packages)){ ?>
									<select id="vip_type" name="vip_type" required>
										<?php foreach($vip_packages AS $package){ ?>
										<option value="<?php echo $package['id'];?>" <?php if(isset($_POST['vip_type']) && $_POST['vip_type'] == $package['id']){ echo 'selected="selected"'; } ?>><?php echo $package['package_title'];?></option>
										<?php } ?>
									</select>
									<?php } else { ?>
									No vip packages found.
									<?php } ?>
									<?php } else { ?>
									<select id="vip_type" name="vip_type" required>
										<option value="0" <?php if(isset($_POST['vip_type']) && $_POST['vip_type'] == 0){ echo 'selected="selected"'; } ?>>None</option>
										<option value="1" <?php if(isset($_POST['vip_type']) && $_POST['vip_type'] == 1){ echo 'selected="selected"'; } ?>>Bronze</option>
										<option value="2" <?php if(isset($_POST['vip_type']) && $_POST['vip_type'] == 2){ echo 'selected="selected"'; } ?>>Silver</option>
										<option value="3" <?php if(isset($_POST['vip_type']) && $_POST['vip_type'] == 3){ echo 'selected="selected"'; } ?>>Gold</option>
										<option value="4" <?php if(isset($_POST['vip_type']) && $_POST['vip_type'] == 4){ echo 'selected="selected"'; } ?>>Platinum</option>
									</select>
									<?php } ?>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="reward_type">Reward Type</label>
								<div class="controls">
									<select id="reward_type" name="reward_type" required>
										<option value="1" <?php if(isset($_POST['reward_type']) && $_POST['reward_type'] == 1){ echo 'selected="selected"'; } ?>>Daily</option>
										<option value="2" <?php if(isset($_POST['reward_type']) && $_POST['reward_type'] == 2){ echo 'selected="selected"'; } ?>>Weekly</option>
										<option value="3" <?php if(isset($_POST['reward_type']) && $_POST['reward_type'] == 3){ echo 'selected="selected"'; } ?>>Monhtly</option>
									</select>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="min_vip_days">Min Vip Days </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="min_vip_days" name="min_vip_days" value="<?php if(isset($_POST['min_vip_days'])){ echo $_POST['min_vip_days']; } else { echo 1; }  ?>" required />
									<p class="help-block">Minimum vip days left required for get reward.</p>
								</div>
								
							</div>
							<div class="control-group">
								<label class="control-label" for="credits1"> Credits 1</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="credits1" name="credits1" value="<?php if(isset($_POST['credits1'])){ echo $_POST['credits1']; } else { echo 0; }  ?>" required />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="credits2"> Credits 2</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="credits2" name="credits2" value="<?php if(isset($_POST['credits2'])){ echo $_POST['credits2']; } else { echo 0; } ?>" required />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="wcoin"> Wcoins</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="wcoin" name="wcoin" value="<?php if(isset($_POST['wcoin'])){ echo $_POST['wcoin']; } else { echo 0; } ?>" required />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="goblin"> Goblin Points</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="goblin" name="goblin" value="<?php if(isset($_POST['goblin'])){ echo $_POST['goblin']; } else { echo 0; } ?>" required />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="zen"> Zen on Character</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="zen" name="zen" value="<?php if(isset($_POST['zen'])){ echo $_POST['zen']; } else { echo 0; } ?>" required />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="credits3"> Zen in WebWallet</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="credits3" name="credits3" value="<?php if(isset($_POST['credits3'])){ echo $_POST['credits3']; } else { echo 0; } ?>" required />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="ruud"> Ruud</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="ruud" name="ruud" value="<?php if(isset($_POST['ruud'])){ echo $_POST['ruud']; } else { echo 0; } ?>" required />
								</div>
							</div>
							<div class="control-group" id="items3">
								<label class="control-label" for="items">Items</label>
								<div class="controls" id="itemlist">
									<?php 
									if(!empty($_POST['items'])){ 
										$i = 0;
										foreach($_POST['items'] AS $data){
									?>
									<div id="item_<?php echo $i + 1;?>" <?php if($i > 0){?>style="margin-top:2px;"<?php } ?>>
										Category: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_category[]" value="<?php echo $data['cat'];?>" /> 
										Index: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_index[]" value="<?php echo $data['id'];?>" /> 
										Dur: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_dur[]" value="<?php echo $data['dur'];?>" /> 
										Level: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_level[]" value="<?php echo $data['lvl'];?>" placeholder="0-15" /> 
										Skill: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_skill[]" value="<?php echo $data['skill'];?>" placeholder="0/1" /> 
										Luck: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_luck[]" value="<?php echo $data['luck'];?>" placeholder="0/1" /> 
										Option: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_option[]" value="<?php echo $data['opt'];?>" placeholder="0-7" /> 
										Excellent: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_excellent[]" value="<?php echo $data['exe'];?>" placeholder="1,1,1,1,1,1" /> 
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
										Luck: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_luck[]" value="" placeholder="0/1" /> 
										Option: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_option[]" value="" placeholder="0-7" /> 
										Excellent: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_excellent[]" value="" placeholder="1,1,1,1,1,1" /> 
										Ancient: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_ancient[]" value="" placeholder="0/5/6/9/10" /> 
										Expire (minutes): <input class="form-control" style="width:40px; display: inline;" type="text" name="item_expires[]" value="" placeholder="" /> 
										<button class="btn btn-danger removeItem" name="removeItem" id="remove_1"> <i class="icon-remove"></i></button>
										<button class="btn btn-success" name="addItem" id="addItem"><i class="icon-plus"></i></button>
									</div>
									<?php } ?>	
								</div>
							</div>
							<div class="form-actions">
								<button type="submit" class="btn btn-primary" name="add_reward" id="add_reward">Submt</button>
							</div>
						</form>
						<script>
						$(document).ready(function() {
							$('#addItem').on("click", function(e) {
								e.preventDefault();
								var divId = parseInt($('#itemlist').children().last().attr('id').split('_')[1]) + 1;

								var html = '<div id="item_'+divId+'" style="margin-top:2px;">';
								html += ' Category: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_category[]" value="" />';
								html += ' Index: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_index[]" value="" />';
								html += ' Dur: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_dur[]" value="" />';
								html += ' Level: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_level[]" value="" placeholder="0-15" />';
								html += ' Skill: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_skill[]" value="" placeholder="0/1" />';
								html += ' Luck: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_luck[]" value="" placeholder="0/1" />';
								html += ' Option: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_option[]" value="" placeholder="0-7" />';
								html += ' Excellent: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_excellent[]" value="" placeholder="1,1,1,1,1,1" />';
								html += ' Ancient: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_ancient[]" value="" placeholder="0/5/6/9/10" />';
								html += ' Expire (minutes): <input class="form-control" style="width:40px; display: inline;" type="text" name="item_expires[]" value="" placeholder="" />'; 
								html += ' <button class="btn btn-danger removeItem"name="removeItem" id="remove_'+divId+'"> <i class="icon-remove"></i></button>';
								html += '</div>';
								$('#itemlist').append(html);
							});
							$(document).on("click", ".removeItem", function(e) {
								e.preventDefault();
								var id = $(this).attr('id').split('_')[1];
								if(id == 1)
									return false;
								$('#item_'+id).empty();
								$('#item_'+id).hide();
							});
						});
						</script>
				</div>
            </div>
        </div>
    </div>
	<div class="row-fluid">
        <div class="box span12">
            <div class="tab-content">          
				<div class="box-header well">
					<h2><i class="icon-edit"></i><?php echo $this->website->get_title_from_server($server); ?> Rewards List</h2>
				</div>
				<div class="box-content">
					<table class="table">
					<thead>
						<tr>
							<th>Vip</th>
							<th>Reward Type</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					 <?php
						if(!empty($reward_list[$server])){
							ksort($reward_list[$server]);
							foreach($reward_list[$server] AS $vip => $settings){
								ksort($settings);
								foreach($settings AS $type => $details){
								?>
								<tr>          
									<td><?php echo $this->Mvip_rewards->get_vip_package_title($vip, $config['vip_type']);?></td>
									<td><?php echo $this->Mvip_rewards->typeToReadable($type);?></td>
									<td>
										<a class="btn btn-info" href="<?php echo $this->config->base_url . 'vip-rewards/edit/' . $vip .'-'.$type;?>/<?php echo $server;?>">
											<i class="icon-edit icon-white"></i>  
											Edit                                            
										</a>
										<a class="btn btn-danger" onclick="ask_url('Are you sure to delete reward?', '<?php echo $this->config->base_url . 'vip-rewards/delete/' . $vip .'-'.$type;?>/<?php echo $server;?>')" href="#">
											<i class="icon-trash icon-white"></i> 
											Delete
										</a>
										<?php if($details['status'] == 1){ ?>
										<a class="btn btn-danger" href="<?php echo $this->config->base_url . 'vip-rewards/change-status/' . $vip .'-'.$type.'-'.(0);?>/<?php echo $server;?>">
											<i class="icon-edit icon-white"></i> 
											Disable
										</a>
										<?php } else { ?>
										<a class="btn btn-success" href="<?php echo $this->config->base_url . 'vip-rewards/change-status/' . $vip .'-'.$type.'-'.(1);?>/<?php echo $server;?>">
											<i class="icon-edit icon-white"></i> 
											Enable
										</a>
										<?php } ?>
									</td>
								</tr>        
								<?php
								}
							}
						} else{
							echo '<tr><td colspan="3"><div class="alert alert-info">No rewards found.</div></td></tr>';
						}
					?>
					</tbody>
					</table>
				</div>
            </div>
        </div>
    </div>
	<?php	
	} 
	?>
</div>
<?php
$this->load->view('admincp' . DS . 'view.footer');
?>
