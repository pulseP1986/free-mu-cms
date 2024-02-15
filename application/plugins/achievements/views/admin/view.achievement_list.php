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
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Achievement List<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/achievement-list?server=<?php echo $key;?>" aria-controls="paypalsettings"><?php echo $val['title'];?></a></li>
						 <?php endforeach;?>
                    </ul>
                </li>
				<li role="presentation" class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Logs<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/logs?server=<?php echo $key;?>" aria-controls="paypalsettings"><?php echo $val['title'];?></a></li>
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
		$("#achievementlist_sortable").find("tbody#achievementlist_sortable_content").sortable({
			placeholder: 'ui-state-highlight',
			opacity: 0.6,
			cursor: 'move',

			update: function() {
				achievements.saveOrder('<?php echo $server;?>');
			}

		});
	});
	</script>
	<?php endif;?>
	<div class="row-fluid">
        <div class="box span12">
            <div class="tab-content">          
				<div class="box-header well">
					<h2><i class="icon-edit"></i> Create Achievement</h2>
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
					<form class="form-horizontal" method="POST" action="" id="create_achievement">
						<div class="control-group">
							<label class="control-label" for="title"> Title</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="title" name="title" value="<?php if(isset($_POST['title'])){ echo $_POST['title']; } ?>" required />
								<p class="help-block">Achievement title</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="desc"> Description</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="desc" name="desc" value="<?php if(isset($_POST['desc'])){ echo $_POST['desc']; } ?>" required />
								<p class="help-block">Achievement description</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="image"> Image</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="image" name="image" value="<?php if(isset($_POST['image'])){ echo $_POST['image']; } ?>" placeholder="http://" />
								<p class="help-block">Achievement image url</p>
							</div>
						</div>
						<div class="control-group">
                            <label class="control-label">Category </label>

                            <div class="controls">
                                <select name="category">
                                    <?php
                                        $catConfig = $this->config->values('achievement_category');
                                        $newCats = [];
                                        foreach($catConfig as $key => $cat){
                                            if(isset($cat['parent_id']) && $cat['parent_id'] != ''){
                                                $newCats[$cat['parent_id']][$key] = $key;
                                            }
                                           
                                        }
                                        
                                        
                                        foreach($catConfig as $key => $cat){
                                            if(isset($newCats[$key])){
                                                echo '<optgroup label="'.$cat['name'].'">';
                                                     foreach($newCats[$key] as $k => $c){
                                                          echo '<option value="' . $k . '">' . $catConfig[$k]['name'] . '</option>' . "\n";
                                                          unset($catConfig[$k]);
                                                     }
                                                echo '</optgroup>';
                                            } 
                                            else{  
                                                if(!isset($cat['parent_id']) || $cat['parent_id'] == ''){
                                                    echo '<option value="' . $key . '">' . $cat['name'] . '</option>' . "\n";
                                                }
                                            }
                                        }
                                    ?>
                                </select>

                                <p class="help-block">Guide Category.</p>
                            </div>
                        </div>
						<div class="control-group">
							<label class="control-label" for="points"> Points</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="points" name="points" value="<?php if(isset($_POST['points'])){ echo $_POST['points']; } ?>" required placeholder="10" />
								<p class="help-block">How many ranking points will be given for completion</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="period">Period </label>
							<div class="controls">
								<select class="span3" id="period" name="period" required>
									<option value="0" <?php if(isset($_POST['period']) && $_POST['period'] == 0){ echo 'selected="selected"'; } ?>>Permanent</option>
									<option value="1" <?php if(isset($_POST['period']) && $_POST['period'] == 1){ echo 'selected="selected"'; } ?>>Daily Reset</option>
									<option value="2" <?php if(isset($_POST['period']) && $_POST['period'] == 2){ echo 'selected="selected"'; } ?>>Weekly Reset</option>
									<option value="3" <?php if(isset($_POST['period']) && $_POST['period'] == 3){ echo 'selected="selected"'; } ?>>Monhtly Reset</option>
								</select>
							</div>
						</div>
						<div class="control-group">
								<label class="control-label" for="min_lvl">Min Level</label>

								<div class="controls">
									<input type="text" class="span6" id="min_lvl" name="min_lvl" value="<?php if(isset($_POST['min_lvl'])){ echo $_POST['min_lvl']; } ?>" placeholder="1" />
									<p class="help-block">Min lvl required.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="min_mlvl">Min MasterLevel</label>

								<div class="controls">
									<input type="text" class="span6" id="min_mlvl" name="min_mlvl" value="<?php if(isset($_POST['min_mlvl'])){ echo $_POST['min_mlvl']; } ?>" placeholder="1" />
									<p class="help-block">Min master lvl required.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="min_res">Min Reset</label>

								<div class="controls">
									<input type="text" class="span6" id="min_res" name="min_res" value="<?php if(isset($_POST['min_res'])){ echo $_POST['min_res']; } ?>" placeholder="0" />
									<p class="help-block">Min reset required.</p>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="min_gres">Min GrandReset</label>

								<div class="controls">
									<input type="text" class="span6" id="min_gres" name="min_gres" value="<?php if(isset($_POST['min_gres'])){ echo $_POST['min_gres']; } ?>" placeholder="0" />
									<p class="help-block">Min grand reset required.</p>
								</div>
							</div>
						<div class="control-group">
							<label class="control-label" for="class">Class</label>
							<div class="controls">
								<?php
									$class = [];
									if(isset($_POST['class']) && $_POST['class'] != ''){
										$class = $_POST['class'];
									}
								?>
								<select class="span3" id="class" name="class[]" multiple data-rel="chosen">
									<?php foreach($class_list AS $id => $classData){ ?>
										<option value="<?php echo $id;?>" <?php if(in_array($id, $class)){ echo 'selected="selected"'; } ?>><?php echo $classData['long'];?>(<?php echo $id; ?>)</option>
									<?php } ?>
								</select>	
								<p>Allowed for class. Leave empty if no class requirements.</p>	
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="achievement_type">Type </label>
							<div class="controls">
								<select class="span3" id="achievement_type" name="achievement_type" required>
									<option value="" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == ''){ echo 'selected="selected"'; } ?>>Select</option>
									<option value="0" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 0){ echo 'selected="selected"'; } ?>>Do Nothing</option>
									<option value="1" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 1){ echo 'selected="selected"'; } ?>>Collect Zen</option>
									<option value="2" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 2){ echo 'selected="selected"'; } ?>>Collect Ruud</option>
									<option value="3" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 3){ echo 'selected="selected"'; } ?>>Collect WCoins</option>
									<option value="4" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 4){ echo 'selected="selected"'; } ?>>Collect GoblinPoints</option>
									<option value="5" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 5){ echo 'selected="selected"'; } ?>>Vote</option>
									<option value="6" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 6){ echo 'selected="selected"'; } ?>>Donate</option>
									<option value="7" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 7){ echo 'selected="selected"'; } ?>>Kill Monsters</option>
									<option value="8" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 8){ echo 'selected="selected"'; } ?>>Kill Players</option>
									<option value="9" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 9){ echo 'selected="selected"'; } ?>>Collect Items</option>
									<option value="10" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 10){ echo 'selected="selected"'; } ?>>Level</option>
									<option value="11" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 11){ echo 'selected="selected"'; } ?>>Master Level</option>
									<option value="12" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 12){ echo 'selected="selected"'; } ?>>Reset</option>
									<option value="13" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 13){ echo 'selected="selected"'; } ?>>Grand Reset</option>
									<option value="14" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 14){ echo 'selected="selected"'; } ?>>Refer a friend</option>
									<option value="15" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 15){ echo 'selected="selected"'; } ?>>BloodCastle</option>
									<option value="16" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 16){ echo 'selected="selected"'; } ?>>DevilSquare</option>
									<option value="17" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 17){ echo 'selected="selected"'; } ?>>ChaosCastle</option>
									<option value="18" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 18){ echo 'selected="selected"'; } ?>>IllusionTemple</option>
									<option value="19" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 19){ echo 'selected="selected"'; } ?>>Duels</option>
									<option value="20" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 20){ echo 'selected="selected"'; } ?>>Gens</option>
									<option value="21" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 21){ echo 'selected="selected"'; } ?>>Buy Shop Item</option>
									<option value="22" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 22){ echo 'selected="selected"'; } ?>>Sell Market Item</option>
									<option value="23" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 23){ echo 'selected="selected"'; } ?>>Complete Achievements</option>
									<option value="24" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 24){ echo 'selected="selected"'; } ?>>Maxed Out</option>
									<option value="25" <?php if(isset($_POST['achievement_type']) && $_POST['achievement_type'] == 25){ echo 'selected="selected"'; } ?>>Online Time</option>
								</select>
							</div>
						</div>
						<div class="control-group" id="amount" style="display:none;">
							<label class="control-label" for="amount">Amount to Collect</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="amount" name="amount" value="<?php if(isset($_POST['amount'])){ echo $_POST['amount']; } ?>" placeholder="100" />				
							</div>
							<br />
							<label class="control-label" for="decreaset_amount">Decrease amount </label>
							<div class="controls">
								<select class="span3" id="decreaset_amount" name="decreaset_amount">									
									<option value="0" <?php if(isset($_POST['decreaset_amount']) && $_POST['decreaset_amount'] == 0){ echo 'selected="selected"'; } ?>>No</option>
									<option value="1" <?php if(isset($_POST['decreaset_amount']) && $_POST['decreaset_amount'] == 1){ echo 'selected="selected"'; } ?>>Yes</option>
								</select>
								<p>Decrease collected amount on complition</p>
							</div>
						</div>
						<div class="control-group" id="stats" style="display:none;">
							<label class="control-label" for="total_stats">Req Amount</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_stats" name="total_stats" value="<?php if(isset($_POST['total_stats'])){ echo $_POST['total_stats']; } ?>" placeholder="100" />								
							</div>
						</div>
						<div class="control-group" id="maxed" style="display:none;">
							<label class="control-label" for="total_level">Req Level</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_level" name="total_level" value="<?php if(isset($_POST['total_level'])){ echo $_POST['total_level']; } ?>" placeholder="1" />								
							</div>
							<label class="control-label" for="total_mlevel">Req MLevel</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_mlevel" name="total_mlevel" value="<?php if(isset($_POST['total_mlevel'])){ echo $_POST['total_mlevel']; } ?>" placeholder="1" />								
							</div>
							<label class="control-label" for="total_res">Req Resets</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_res" name="total_res" value="<?php if(isset($_POST['total_res'])){ echo $_POST['total_res']; } ?>" placeholder="1" />								
							</div>
							<label class="control-label" for="total_gres">Req GResets</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_gres" name="total_gres" value="<?php if(isset($_POST['total_gres'])){ echo $_POST['total_gres']; } ?>" placeholder="1" />								
							</div>
						</div>
						<div class="control-group" id="referrals" style="display:none;">
							<label class="control-label" for="total_ref">Req Referrals</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_ref" name="total_ref" value="<?php if(isset($_POST['total_ref'])){ echo $_POST['total_ref']; } ?>" placeholder="10" />								
							</div>
						</div>
						<div class="control-group" id="gens" style="display:none;">
							<label class="control-label" for="total_contr">Req Contribution</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_contr" name="total_contr" value="<?php if(isset($_POST['total_contr'])){ echo $_POST['total_contr']; } ?>" placeholder="1000" />								
							</div>
						</div>
						<div class="control-group" id="items1" style="display:none;">
							<label class="control-label" for="total_items_buy">Amount of items</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_items_buy" name="total_items_buy" value="<?php if(isset($_POST['total_items_buy'])){ echo $_POST['total_items_buy']; } ?>" placeholder="10" />	
								<p>How many items required to buy from shop to complete achievement</p>
							</div>
						</div>
						<div class="control-group" id="items2" style="display:none;">
							<label class="control-label" for="total_items_sell">Amount of items</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_items_sell" name="total_items_sell" value="<?php if(isset($_POST['total_items_sell'])){ echo $_POST['total_items_sell']; } ?>" placeholder="10" />	
								<p>How many items required to sell in market to complete achievement</p>
							</div>
						</div>
						<div class="control-group" id="kill" style="display:none;">
							<label class="control-label" for="total_kills">Total Kills</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_kills" name="total_kills" value="<?php if(isset($_POST['total_kills'])){ echo $_POST['total_kills']; } ?>" placeholder="100" />
								<p>Total player kills required for achievement to complete</p>									
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
								<p>Total votes required for achievement to complete</p>									
							</div>
						</div>
						<div class="control-group" id="donate" style="display:none;">
							<label class="control-label" for="total_donate">Total Donate</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_donate" name="total_donate" value="<?php if(isset($_POST['total_donate'])){ echo $_POST['total_donate']; } ?>" placeholder="10" />
								<p>Total completed donate transactions for achievement to complete</p>		
							</div>
						</div>
						<div class="control-group" id="events1" style="display:none;">
							<label class="control-label" for="total_score">Req Score</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_score" name="total_score" value="<?php if(isset($_POST['total_score'])){ echo $_POST['total_score']; } ?>" placeholder="1000" />
								<p>Total score required for achievement to complete</p>		
							</div>
						</div>
						<div class="control-group" id="events2" style="display:none;">
							<label class="control-label" for="total_wins">Req Wins</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="total_wins" name="total_wins" value="<?php if(isset($_POST['total_wins'])){ echo $_POST['total_wins']; } ?>" placeholder="100" />
								<p>Total wins required for achievement to complete</p>		
							</div>
						</div>
						<div class="control-group" id="monsters" style="display:none;">
							<label class="control-label" for="monsters">Monsters</label>
							<div class="controls">
								<?php
									$monsters = [];
									if(isset($_POST['monsters']) && $_POST['monsters'] != ''){
										$monsters = $_POST['monsters'];
									}
								?>
								<select id="monsters" name="monsters[]" multiple data-rel="chosen">
									<?php foreach($monster_list AS $id => $monster){ ?>
										<option value="<?php echo $id;?>" <?php if(in_array($id, $monsters)){ echo 'selected="selected"'; } ?>><?php echo $monster['name'];?></option>
									<?php } ?>
								</select>									
							</div>
							<label class="control-label" for="mamount">Amount to Kill</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="mamount" name="mamount" value="<?php if(isset($_POST['mamount'])){ echo $_POST['mamount']; } ?>" placeholder="10" />				
							</div>
						</div>
						<div class="control-group" id="items3" style="display:none;">
							<label class="control-label" for="items">Items</label>
							<div class="controls" id="itemlist">
								<?php 
								if(isset($_POST['item_count'])){ 
									$i = 0;
									foreach($_POST['item_count'] AS $data){
								?>
								<div id="item_<?php echo $i + 1;?>" <?php if($i > 0){?>style="margin-top:2px;"<?php } ?>>
									Count: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_count[]" value="<?php echo $_POST['item_count'][$i];?>" required /> 
									Category: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_category[]" value="<?php echo $_POST['item_category'][$i];?>" required /> 
									Index: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_index[]" value="<?php echo $_POST['item_index'][$i];?>" required /> 
									Level: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_level[]" value="<?php echo $_POST['item_level'][$i];?>" placeholder="0-15" /> 
									Skill: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_skill[]" value="<?php echo $_POST['item_skill'][$i];?>" placeholder="0/1" /> 
									Luck: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_luck[]" value="<?php echo $_POST['item_luck'][$i];?>" placeholder="0/1" /> 
									Option: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_option[]" value="<?php echo $_POST['item_option'][$i];?>" placeholder="0-7" /> 
									Excellent: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_excellent[]" value="<?php echo $_POST['item_excellent'][$i];?>" placeholder="1,1,1,1,1,1" /> 
									Ancient: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_ancient[]" value="<?php echo $_POST['item_ancient'][$i];?>" placeholder="0/5/6/9/10" /> 
									<button class="btn btn-danger removeItem" name="removeItem" id="remove_<?php echo $i + 1;?>"> <i class="icon-remove"></i></button>
									<?php if($i == 0){?><button class="btn btn-success" name="addItem" id="addItem"><i class="icon-plus"></i></button><?php } ?>
								</div>
								<?php
										$i++;
									}
								} else { 
								?>
								<div id="item_1">
									Count: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_count[]" value="0" required /> 
									Category: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_category[]" value="0" required /> 
									Index: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_index[]" value="0" required /> 
									Level: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_level[]" value="" placeholder="0-15" /> 
									Skill: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_skill[]" value="" placeholder="0/1" /> 
									Luck: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_luck[]" value="" placeholder="0/1" /> 
									Option: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_option[]" value="" placeholder="0-7" /> 
									Excellent: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_excellent[]" value="" placeholder="1,1,1,1,1,1" /> 
									Ancient: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_ancient[]" value="" placeholder="0/5/6/9/10" /> 
									<button class="btn btn-danger removeItem" name="removeItem" id="remove_1"> <i class="icon-remove"></i></button>
									<button class="btn btn-success" name="addItem" id="addItem"><i class="icon-plus"></i></button>
								</div>
								<?php } ?>	
							</div>
						</div>
						<script>
						$(document).ready(function() {
							$('#addItem').on("click", function(e) {
								e.preventDefault();
								var divId = parseInt($('#itemlist').children().last().attr('id').split('_')[1]) + 1;

								var html = '<div id="item_'+divId+'" style="margin-top:2px;">';
								html += 'Count: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_count[]" value="0" required />';
								html += ' Category: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_category[]" value="0" required />';
								html += ' Index: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_index[]" value="0" required />';
								html += ' Level: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_level[]" value="" placeholder="0-15" />';
								html += ' Skill: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_skill[]" value="" placeholder="0/1" />';
								html += ' Luck: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_luck[]" value="" placeholder="0/1" />';
								html += ' Option: <input class="form-control" style="width:40px; display: inline;" type="text" name="item_option[]" value="" placeholder="0-7" />';
								html += ' Excellent: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_excellent[]" value="" placeholder="1,1,1,1,1,1" />';
								html += ' Ancient: <input class="form-control" style="width:70px; display: inline;" type="text" name="item_ancient[]" value="" placeholder="0/5/6/9/10" />';
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
							$('#achievement_type').on("change", function() {
								if ($(this).val() == 1 || $(this).val() == 2 || $(this).val() == 3 || $(this).val() == 4) {
									$('#amount').show();
								} else {
									$('#amount').hide();
								}
								if ($(this).val() == 5) {
									$('#vote').show();
								} else {
									$('#vote').hide();
								}
								if ($(this).val() == 6) {
									$('#donate').show();
								} else {
									$('#donate').hide();
								}
								if ($(this).val() == 7) {
									$('#monsters').show();
								} else {
									$('#monsters').hide();
								}
								if ($(this).val() == 8 || $(this).val() == 18) {
									$('#kill').show();
								} else {
									$('#kill').hide();
								}
								if ($(this).val() == 8) {
									$('#kill2').show();
								} else {
									$('#kill2').hide();
								}
								if ($(this).val() == 9) {
									$('#items3').show();
								} else {
									$('#items3').hide();
								}
								if ($(this).val() == 10 || $(this).val() == 11 || $(this).val() == 12 || $(this).val() == 13 || $(this).val() == 23 || $(this).val() == 25) {
									$('#stats').show();
								} else {
									$('#stats').hide();
								}
								if ($(this).val() == 24) {
									$('#maxed').show();
								} else {
									$('#maxed').hide();
								}
								if ($(this).val() == 14) {
									$('#referrals').show();
								} else {
									$('#referrals').hide();
								}
								if ($(this).val() == 15 || $(this).val() == 16) {
									$('#events1').show();
								} else {
									$('#events1').hide();
								}
								if ($(this).val() == 17 || $(this).val() == 19) {
									$('#events2').show();
								} else {
									$('#events2').hide();
								}
								if ($(this).val() == 20) {
									$('#gens').show();
								} else {
									$('#gens').hide();
								}
								if ($(this).val() == 21) {
									$('#items1').show();
								} else {
									$('#items1').hide();
								}
								if ($(this).val() == 22) {
									$('#items2').show();
								} else {
									$('#items2').hide();
								}
							});
							
							var achievement_type = $("#achievement_type option:selected").val();
							if (achievement_type == 1 || achievement_type == 2 || achievement_type == 3 || achievement_type == 4) {
								$('#amount').show();
							} else {
								$('#amount').hide();
							}
							if (achievement_type == 5) {
								$('#vote').show();
							} else {
								$('#vote').hide();
							}
							if (achievement_type == 6) {
								$('#donate').show();
							} else {
								$('#donate').hide();
							}
							if (achievement_type == 7) {
								$('#monsters').show();
							} else {
								$('#monsters').hide();
							}
							if (achievement_type == 8 || achievement_type == 18) {
								$('#kill').show();
							} else {
								$('#kill').hide();
							}
							if (achievement_type == 8) {
								$('#kill2').show();
							} else {
								$('#kill2').hide();
							}
							if (achievement_type == 9) {
								$('#items3').show();
							} else {
								$('#items3').hide();
							}
							if (achievement_type == 10 || achievement_type == 11 || achievement_type == 12 || achievement_type == 13 || achievement_type == 23 || achievement_type == 25) {
								$('#stats').show();
							} else {
								$('#stats').hide();
							}
							if (achievement_type == 24) {
								$('#maxed').show();
							} else {
								$('#maxed').hide();
							}
							if (achievement_type == 14) {
								$('#referrals').show();
							} else {
								$('#referrals').hide();
							}
							if (achievement_type == 15 || achievement_type == 16) {
								$('#events1').show();
							} else {
								$('#events1').hide();
							}
							if (achievement_type == 17 || achievement_type == 19) {
								$('#events2').show();
							} else {
								$('#events2').hide();
							}
							if (achievement_type == 20) {
								$('#gens').show();
							} else {
								$('#gens').hide();
							}
							if (achievement_type == 21) {
								$('#items1').show();
							} else {
								$('#items1').hide();
							}
							if (achievement_type == 22) {
								$('#items2').show();
							} else {
								$('#items2').hide();
							}
						});
						</script>
						<div class="form-actions">
							<button type="submit" class="btn btn-primary" name="add_achievements" id="add_achievements">Submt</button>
						</div>
					</form>
				</div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="box span12">
			<div class="box-header well">
				<h2><i class="icon-edit"></i> <?php echo $this->website->get_title_from_server($server); ?> Achievement List</h2>
			</div>
			<div class="box-content">
				<table class="table"  id="achievementlist_sortable">
					<thead>
						<tr>
							<th>Title</th>
							<th>Description</th>
							<th>Type</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody id="achievementlist_sortable_content" style="cursor: move;">
            <?php
                if(!empty($achievements)){
					//arsort($achievements);
                    foreach($achievements AS $key => $settings){
                        ?>
						<tr id="<?php echo $key; ?>">          
							<td><?php echo $settings['title'];?></td>
							<td><?php echo $settings['desc'];?></td>
							<td><?php echo $this->Machievements->achivementTypeToReadable($settings['achievement_type']);?></td>
							<td>
								<a class="btn btn-info" href="<?php echo $this->config->base_url . 'achievements/edit/' . $settings['id'];?>/<?php echo $server;?>">
									<i class="icon-edit icon-white"></i>  
									Edit                                            
								</a>
								<a class="btn btn-danger" onclick="ask_url('Are you sure to delete achievement?', '<?php echo $this->config->base_url . 'achievements/delete/' . $settings['id'];?>/<?php echo $server;?>')" href="#">
									<i class="icon-trash icon-white"></i> 
									Delete
								</a>
								<a class="btn btn-success" href="<?php echo $this->config->base_url . 'achievements/rewards/' . $settings['id'];?>/<?php echo $server;?>">
									<i class="icon-edit icon-white"></i> 
									Rewards
								</a>
							</td>
						</tr>        
                        <?php
                    }
                } else{
                    echo '<tr><td colspan="4"><div class="alert alert-info">No achievements.</div></td></tr>';
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
