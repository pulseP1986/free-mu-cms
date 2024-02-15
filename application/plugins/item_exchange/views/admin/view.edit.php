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
				<li role="presentation" class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Exchange List<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/exchange-list?server=<?php echo $key;?>"><?php echo $val['title'];?></a></li>
						 <?php endforeach;?>
                    </ul>
                </li>
				<li role="presentation" class="dropdown active">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Reward List<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/reward-list?server=<?php echo $key;?>"><?php echo $val['title'];?></a></li>
						 <?php endforeach;?>
                    </ul>
                </li>
				<li role="presentation" class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Logs<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/logs?server=<?php echo $key;?>">Exchange/Reward <?php echo $val['title'];?></a></li>
						<li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/logs-points?server=<?php echo $key;?>">Points <?php echo $val['title'];?></a></li>
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
					<h2><i class="icon-edit"></i>Edit Reward</h2>
				</div>
				<div class="box-content">
						<form class="form-horizontal" method="POST" action="" id="add_reward">
							<div class="control-group">
								<label class="control-label" for="currency_amount">Currency Amount </label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="currency_amount" name="currency_amount" value="<?php if(isset($achData['currency_amount'])){ echo $achData['currency_amount']; } else { echo 0; }  ?>" required />
									<p class="help-block">Custom currency amount required for reward.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="credits1">Reward Credits 1</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="credits1" name="credits1" value="<?php if(isset($achData['credits1'])){ echo $achData['credits1']; } else { echo 0; }  ?>" required />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="credits2">Reward Credits 2</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="credits2" name="credits2" value="<?php if(isset($achData['credits2'])){ echo $achData['credits2']; } else { echo 0; } ?>" required />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="wcoin">Reward Wcoins</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="wcoin" name="wcoin" value="<?php if(isset($achData['wcoin'])){ echo $achData['wcoin']; } else { echo 0; } ?>" required />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="goblin">Reward Goblin Points</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="goblin" name="goblin" value="<?php if(isset($achData['goblin'])){ echo $achData['goblin']; } else { echo 0; } ?>" required />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="credits3">Reward Zen in WebWallet</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="credits3" name="credits3" value="<?php if(isset($achData['credits3'])){ echo $achData['credits3']; } else { echo 0; } ?>" required />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="vip_type">Reward Vip Type</label>
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
							<div class="control-group" id="items3">
								<label class="control-label" for="items">Reward Items</label>
								<div class="controls" id="itemlist">
									<?php 
									if(!empty($achData['items'])){ 
										$i = 0;
										foreach($achData['items'] AS $data){
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
							<div class="control-group" id="items4">
								<label class="control-label" for="items">Reward Items Hex</label>
								<div class="controls" id="itemlist_hex">
									<?php 
									if(!empty($achData['items2'])){ 
										$i = 0;
										foreach($achData['items2'] AS $data){
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
							$(document).on("click", ".removeItemHex", function(e) {
								e.preventDefault();
								var id = $(this).attr('id').split('_')[1];
								if(id == 1)
									return false;
								$('#itemhex_'+id).empty();
								$('#itemhex_'+id).hide();
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
							<th>Reward id</th>
							<th>Currency Amount</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					 <?php
						if(!empty($reward_list[$server])){
							ksort($reward_list[$server]);
							foreach($reward_list[$server] AS $id => $settings){
								?>
								<tr>          
									<td><?php echo $id;?></td>
									<td><?php echo $settings['currency_amount'];?></td>
									<td>
										<a class="btn btn-info" href="<?php echo $this->config->base_url . str_replace('_', '-', $this->request->get_controller()) .'/edit/' . $id;?>/<?php echo $server;?>">
											<i class="icon-edit icon-white"></i>  
											Edit                                            
										</a>
										<a class="btn btn-danger" onclick="ask_url('Are you sure to delete reward?', '<?php echo $this->config->base_url . str_replace('_', '-', $this->request->get_controller()) .'/delete/' . $id;?>/<?php echo $server;?>')" href="#">
											<i class="icon-trash icon-white"></i> 
											Delete
										</a>
										<?php if($settings['status'] == 1){ ?>
										<a class="btn btn-danger" href="<?php echo $this->config->base_url . str_replace('_', '-', $this->request->get_controller()) .'/change-status/' . $id .'-'.(0);?>/<?php echo $server;?>">
											<i class="icon-edit icon-white"></i> 
											Disable
										</a>
										<?php } else { ?>
										<a class="btn btn-success" href="<?php echo $this->config->base_url . str_replace('_', '-', $this->request->get_controller()) .'/change-status/' . $id .'-'.(1);?>/<?php echo $server;?>">
											<i class="icon-edit icon-white"></i> 
											Enable
										</a>
										<?php } ?>
									</td>
								</tr>        
								<?php
							}
						} else{
							echo '<tr><td colspan="6"><div class="alert alert-info">No rewards found.</div></td></tr>';
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
