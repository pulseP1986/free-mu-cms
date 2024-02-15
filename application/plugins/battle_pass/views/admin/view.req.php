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
	
	</script>
	<?php endif;?>
	<div class="row-fluid">
        <div class="box span12">
            <div class="tab-content">          
				<div class="box-header well">
					<h2><i class="icon-edit"></i> Add Requirement</h2>
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
					<form class="form-horizontal" method="POST" action="<?php echo $this->config->base_url;?>battle-pass/requirements/<?php echo $aid;?>/<?php echo $server;?>">
						<div class="control-group">
							<label class="control-label" for="title"> Title</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="title" name="title" value="<?php if(isset($_POST['title'])){ echo $_POST['title']; } ?>" required />
								<p class="help-block">Requirement title</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="req_type">Type </label>
							<div class="controls">
								<select class="span3" id="req_type" name="req_type" required>
									<option value="" <?php if(isset($_POST['req_type']) && $_POST['req_type'] == ''){ echo 'selected="selected"'; } ?>>Select</option>
									<option value="1" <?php if(isset($_POST['req_type']) && $_POST['req_type'] == 1){ echo 'selected="selected"'; } ?>>Vote</option>
									<option value="11" <?php if(isset($_POST['req_type']) && $_POST['req_type'] == 11){ echo 'selected="selected"'; } ?>>Vote Specific</option>
									<option value="12" <?php if(isset($_POST['req_type']) && $_POST['req_type'] == 12){ echo 'selected="selected"'; } ?>>Play Event (IGCN)</option>
									<option value="2" <?php if(isset($_POST['req_type']) && $_POST['req_type'] == 2){ echo 'selected="selected"'; } ?>>Donate</option>
									<option value="3" <?php if(isset($_POST['req_type']) && $_POST['req_type'] == 3){ echo 'selected="selected"'; } ?>>Kill Monsters</option>
									<option value="4" <?php if(isset($_POST['req_type']) && $_POST['req_type'] == 4){ echo 'selected="selected"'; } ?>>Kill Players (IGCN)</option>
									<option value="5" <?php if(isset($_POST['req_type']) && $_POST['req_type'] == 5){ echo 'selected="selected"'; } ?>>Reset</option>
									<option value="6" <?php if(isset($_POST['req_type']) && $_POST['req_type'] == 6){ echo 'selected="selected"'; } ?>>Grand Reset</option>
									<option value="7" <?php if(isset($_POST['req_type']) && $_POST['req_type'] == 7){ echo 'selected="selected"'; } ?>>Buy Shop Item</option>
									<option value="8" <?php if(isset($_POST['req_type']) && $_POST['req_type'] == 8){ echo 'selected="selected"'; } ?>>Sell Market Item</option>
									<option value="9" <?php if(isset($_POST['req_type']) && $_POST['req_type'] == 9){ echo 'selected="selected"'; } ?>>Online Time</option>
									<option value="10" <?php if(isset($_POST['req_type']) && $_POST['req_type'] == 10){ echo 'selected="selected"'; } ?>>Collect Item</option>
								</select>
							</div>
						</div>
						<div class="control-group" id="stats" style="display:none;">
							<label class="control-label" for="total_stats">Req Amount</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_stats" name="total_stats" value="<?php if(isset($_POST['total_stats'])){ echo $_POST['total_stats']; } ?>" placeholder="100" />								
							</div>
						</div>
						<div class="control-group" id="items1" style="display:none;">
							<label class="control-label" for="total_items_buy">Amount of items</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_items_buy" name="total_items_buy" value="<?php if(isset($_POST['total_items_buy'])){ echo $_POST['total_items_buy']; } ?>" placeholder="10" />	
								<p>How many items required to buy from shop to complete requirement</p>
							</div>
						</div>
						<div class="control-group" id="items2" style="display:none;">
							<label class="control-label" for="total_items_sell">Amount of items</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_items_sell" name="total_items_sell" value="<?php if(isset($_POST['total_items_sell'])){ echo $_POST['total_items_sell']; } ?>" placeholder="10" />	
								<p>How many items required to sell in market to complete requirement</p>
							</div>
						</div>
						<div class="control-group" id="kill" style="display:none;">
							<label class="control-label" for="total_kills">Total Kills</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_kills" name="total_kills" value="<?php if(isset($_POST['total_kills'])){ echo $_POST['total_kills']; } ?>" placeholder="100" />
								<p>Total player kills required for requirement to complete</p>									
							</div>
						</div>
						<div class="control-group" id="kill2" style="display:none;">
							<label class="control-label" for="unique">Only Unique</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="unique" name="unique" value="<?php if(isset($_POST['unique'])){ echo $_POST['unique']; } ?>" placeholder="1|5" />
								<p>Count only unique characters with min resets (supported by IGCN).</p>	
								<p>Example: 1|5 first value 1/0 on or off, second value min res amount</p>							
							</div>
						</div>
						<div class="control-group" id="vote" style="display:none;">
							<label class="control-label" for="total_votes">Total Votes</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_votes" name="total_votes" value="<?php if(isset($_POST['total_votes'])){ echo $_POST['total_votes']; } ?>" placeholder="100" />
								<p>Total votes required for requirement to complete</p>									
							</div>
						</div>
						<div id="vote2" style="display:none;">
							<div class="control-group">
								<label class="control-label" for="total_votes2">Total Votes</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="total_votes" name="total_votes2" value="<?php if(isset($_POST['total_votes2'])){ echo $_POST['total_votes2']; } ?>" placeholder="100" />
									<p>Total votes required for requirement to complete</p>									
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="vote_type">Type</label>
								<div class="controls">
									<?php
										$vtype = '';
										if(isset($_POST['vote_type']) && $_POST['vote_type'] != ''){
											$vtype = $_POST['vote_type'];
										}
									?>
									<select class="span3 typeahead" id="vote_type" name="vote_type">
										<option value="1" <?php if($vtype == 1){ echo 'selected'; } ?>>XtremeTop</option>
										<option value="2" <?php if($vtype == 2){ echo 'selected'; } ?>>Gtop100</option>
										<option value="3" <?php if($vtype == 3){ echo 'selected'; } ?>>TopG</option>
										<option value="4" <?php if($vtype == 4){ echo 'selected'; } ?>>Top100Arena</option>
										<option value="5" <?php if($vtype == 5){ echo 'selected'; } ?>>MuOnline.us</option>
										<option value="6" <?php if($vtype == 6){ echo 'selected'; } ?>>dmncms.net</option>
									</select>									
								</div>
							</div>
						</div>
						<div id="event" style="display:none;">
							<div class="control-group">
								<label class="control-label" for="enter_count">Times Played</label>
								<div class="controls">
									<input type="text" class="span3 typeahead" id="enter_count" name="enter_count" value="<?php if(isset($_POST['enter_count'])){ echo $_POST['enter_count']; } ?>" placeholder="100" />
									<p>Total times user has entered in current day</p>									
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="event_type">Type</label>
								<div class="controls">
									<?php
										$etype = '';
										if(isset($_POST['event_type']) && $_POST['event_type'] != ''){
											$etype = $_POST['event_type'];
										}
									?>
									<select class="span3 typeahead" id="event_type" name="event_type">
										<option value="1" <?php if($etype == 1){ echo 'selected'; } ?>>BloodCastle</option>
										<option value="2" <?php if($etype == 2){ echo 'selected'; } ?>>ChaosCastle</option>
										<option value="3" <?php if($etype == 3){ echo 'selected'; } ?>>DevilSquare</option>
										<option value="4" <?php if($etype == 4){ echo 'selected'; } ?>>DoppelGanger</option>
										<option value="5" <?php if($etype == 5){ echo 'selected'; } ?>>ImperialGuardian</option>
										<option value="6" <?php if($etype == 6){ echo 'selected'; } ?>>IllusionTempleRenewal</option>
									</select>									
								</div>
							</div>
						</div>
						<div class="control-group" id="donate" style="display:none;">
							<label class="control-label" for="total_donate">Total Donate</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_donate" name="total_donate" value="<?php if(isset($_POST['total_donate'])){ echo $_POST['total_donate']; } ?>" placeholder="10" />
								<p>Total completed donate transactions for requirement to complete</p>		
							</div>
						</div>
						<div class="control-group" id="monsters" style="display:none;">
							<label class="control-label" for="monsters">Monsters</label>
							<div class="controls">
								<?php
									$monsters = '';
									if(isset($_POST['monsters']) && $_POST['monsters'] != ''){
										$monsters = $_POST['monsters'];
									}
								?>
								<select class="span3 typeahead" id="monsters" name="monsters">
									<?php foreach($monster_list AS $id => $monster){ ?>
										<option value="<?php echo $id;?>" <?php if($monsters == $id){ echo 'selected="selected"'; } ?>><?php echo $monster['name'];?></option>
									<?php } ?>
								</select>									
							</div>
							<label class="control-label" for="mamount">Amount to Kill</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="mamount" name="mamount" value="<?php if(isset($_POST['mamount'])){ echo $_POST['mamount']; } ?>" placeholder="10" />				
							</div>
						</div>
						<div class="control-group" id="items3" style="display:none;">
							<label class="control-label" for="items">Item</label>
							<div class="controls" id="itemlist">
								<div id="item_1">
									Count: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_count" value="<?php echo (isset($_POST['item_count'])) ? $_POST['item_count'] : '';?>" /> 
									Category: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_category" value="<?php echo (isset($_POST['item_category'])) ? $_POST['item_category'] : '';?>" /> 
									Index: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_index" value="<?php echo (isset($_POST['item_index'])) ? $_POST['item_index'] : '';?>" /> 
									Level: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_level" value="<?php echo (isset($_POST['item_level'])) ? $_POST['item_level'] : '';?>" placeholder="0-15" /> 
									Skill: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_skill" value="<?php echo (isset($_POST['item_skill'])) ? $_POST['item_skill'] : '';?>" placeholder="0/1" /> 
									Luck: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_luck" value="<?php echo (isset($_POST['item_luck'])) ? $_POST['item_luck'] : '';?>" placeholder="0/1" /> 
									Option: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_option" value="<?php echo (isset($_POST['item_option'])) ? $_POST['item_option'] : '';?>" placeholder="0-7" /> 
									Excellent: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_excellent" value="<?php echo (isset($_POST['item_excellent'])) ? $_POST['item_excellent'] : '';?>" placeholder="1,1,1,1,1,1" /> 
									Ancient: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_ancient" value="<?php echo (isset($_POST['item_ancient'])) ? $_POST['item_ancient'] : '';?>" placeholder="0/5/6/9/10" /> 
								</div>	
							</div>
						</div>
						<div class="form-actions">
							<button type="submit" class="btn btn-primary" name="add_req" id="add_req">Submit</button>
						</div>
					</form>
					<script>
					$(document).ready(function() {
						$('#req_type').on("change", function() {
							if ($(this).val() == 1) {
								$('#vote').show();
							} else {
								$('#vote').hide();
							}
							if ($(this).val() == 11) {
								$('#vote2').show();
							} else {
								$('#vote2').hide();
							}
							if ($(this).val() == 12) {
								$('#event').show();
							} else {
								$('#event').hide();
							}
							if ($(this).val() == 2) {
								$('#donate').show();
							} else {
								$('#donate').hide();
							}
							if ($(this).val() == 3) {
								$('#monsters').show();
							} else {
								$('#monsters').hide();
							}
							if ($(this).val() == 4) {
								$('#kill').show();
								$('#kill2').show();
							} else {
								$('#kill').hide();
								$('#kill2').hide();
							}
							if ($(this).val() == 5 || $(this).val() == 6 || $(this).val() == 9) {
								$('#stats').show();
							} else {
								$('#stats').hide();
							}
							if ($(this).val() == 7) {
								$('#items1').show();
							} else {
								$('#items1').hide();
							}
							if ($(this).val() == 8) {
								$('#items2').show();
							} else {
								$('#items2').hide();
							}
							if ($(this).val() == 10) {
								$('#items3').show();
							} else {
								$('#items3').hide();
							}
						});
						
						var req_type = $("#req_type option:selected").val();
						if (req_type == 1) {
							$('#vote').show();
						} else {
							$('#vote').hide();
						}
						if (req_type == 11) {
							$('#vote2').show();
						} else {
							$('#vote2').hide();
						}
						if (req_type == 12) {
							$('#event').show();
						} else {
							$('#event').hide();
						}
						if (req_type == 2) {
							$('#donate').show();
						} else {
							$('#donate').hide();
						}
						if (req_type == 3) {
							$('#monsters').show();
						} else {
							$('#monsters').hide();
						}
						if (req_type == 4) {
							$('#kill').show();
							$('#kill2').show();
						} else {
							$('#kill').hide();
							$('#kill2').hide();
						}
						if (req_type == 5 || req_type == 6 || req_type == 9) {
							$('#stats').show();
						} else {
							$('#stats').hide();
						}
						if (req_type == 7) {
							$('#items1').show();
						} else {
							$('#items1').hide();
						}
						if (req_type == 8) {
							$('#items2').show();
						} else {
							$('#items2').hide();
						}
						if (req_type == 10) {
							$('#items3').show();
						} else {
							$('#items3').hide();
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
				<h2><i class="icon-edit"></i> <?php echo $battle_pass[$server][$achKey]['title']; ?> Requirement List</h2>
			</div>
			<div class="box-content">
				<table class="table"  id="battle_pass_sortable">
					<thead>
						<tr>
							<th>Title</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody id="battle_pass_sortable_content" style="cursor: move;">
					<?php
						if(!empty($achData)){
							foreach($achData AS $key => $settings){
								?>
								<tr id="<?php echo $key; ?>">          
									<td><?php echo $settings['title'];?></td>
									<td>
										<a class="btn btn-info" href="<?php echo $this->config->base_url . 'battle-pass/edit-requirement/'.$aid.'/' . $key;?>/<?php echo $server;?>">
											<i class="icon-edit icon-white"></i>  
											Edit                                            
										</a>
										<a class="btn btn-danger" onclick="ask_url('Are you sure to delete requirement?', '<?php echo $this->config->base_url . 'battle-pass/delete-requirement/'.$aid.'/' . $key;?>/<?php echo $server;?>')" href="#">
											<i class="icon-trash icon-white"></i> 
											Delete
										</a>
									</td>
								</tr>        
								<?php
							}
						} else{
							echo '<tr><td colspan="2"><div class="alert alert-info">No requirements.</div></td></tr>';
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
