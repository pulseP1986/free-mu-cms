<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/manage-settings/greset">Grand Reset Settings</a>
            </li>
        </ul>
    </div>
    <div class="row-fluid">
        <div class="box span12">
            <div class="box-header well">
                <h2><i class="icon-edit"></i> Edit Grand Reset Settings</h2>
            </div>
            <div class="box-content">
                <?php
                    if(isset($not_found)){
                        echo '<div class="alert alert-error">' . $not_found . '</div>';
                    } else{
                        if(isset($error)){
                            if(is_array($error)){
                                foreach($error AS $note){
                                    echo '<div class="alert alert-error">' . $note . '</div>';
                                }
                            } else{
                                echo '<div class="alert alert-error">' . $error . '</div>';
                            }
                        }
                        if(isset($success)){
                            echo '<div class="alert alert-success">' . $success . '</div>';
                        }
                        ?>
                        <form class="form-horizontal" method="POST" action="">
                            <fieldset>
                                <legend></legend>
                                <div class="control-group">
                                    <label class="control-label" for="server">Server</label>

                                    <div class="controls">
                                        <select name="server" id="server">
                                            <?php foreach($servers as $key => $server): ?>
                                                <option value="<?php echo $key; ?>"
                                                        <?php if(isset($selected_server) && $key == $selected_server){ ?>selected="selected"<?php } ?>><?php echo $servers[$key]['title']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="sreset">Starting GReset</label>

                                    <div class="controls">
                                        <input type="text" class="span3 typeahead" id="sreset" name="sreset"
                                               value="<?php if(isset($r_config['sreset'])){
                                                   echo $r_config['sreset'];
                                               } ?>" placeholder="0"/>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="ereset">Ending GReset</label>

                                    <div class="controls">
                                        <input type="text" class="span3 typeahead" id="ereset" name="ereset"
                                               value="<?php if(isset($r_config['ereset'])){
                                                   echo $r_config['ereset'];
                                               } ?>" placeholder="9999"/>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="money">Required Zen</label>

                                    <div class="controls">
                                        <input type="text" class="span3 typeahead" id="money" name="money"
                                               value="<?php if(isset($r_config['money'])){
                                                   echo $r_config['money'];
                                               } ?>" placeholder="9999"/>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="money_x_reset">Is Zen Multiplied by GResets</label>

                                    <div class="controls">
                                        <select name="money_x_reset" id="money_x_reset">
                                            <option value="0"
                                                    <?php if(isset($r_config['money_x_reset']) && 0 == $r_config['money_x_reset']){ ?>selected="selected"<?php } ?>>
                                                No
                                            </option>
                                            <option value="1"
                                                    <?php if(isset($r_config['money_x_reset']) && 1 == $r_config['money_x_reset']){ ?>selected="selected"<?php } ?>>
                                                Yes
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="level">Required level</label>

                                    <div class="controls">
                                        <input type="text" class="span3 typeahead" id="level" name="level"
                                               value="<?php if(isset($r_config['level'])){
                                                   echo $r_config['level'];
                                               } ?>" placeholder="400"/>
                                    </div>
                                </div>
								<div class="control-group">
                                    <label class="control-label" for="mlevel">Required Master level</label>

                                    <div class="controls">
                                        <input type="text" class="span3 typeahead" id="mlevel" name="mlevel"
                                               value="<?php if(isset($r_config['mlevel'])){
                                                   echo $r_config['mlevel'];
                                               } ?>" placeholder="0"/>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="reset">Required reset</label>

                                    <div class="controls">
                                        <input type="text" class="span3 typeahead" id="reset" name="reset"
                                               value="<?php if(isset($r_config['reset'])){
                                                   echo $r_config['reset'];
                                               } ?>" placeholder="100"/>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="clear_all_resets">Clear All Resets</label>

                                    <div class="controls">
                                        <select name="clear_all_resets" id="clear_all_resets">
                                            <option value="0"
                                                    <?php if(isset($r_config['clear_all_resets']) && 0 == $r_config['clear_all_resets']){ ?>selected="selected"<?php } ?>>
                                                No
                                            </option>
                                            <option value="1"
                                                    <?php if(isset($r_config['clear_all_resets']) && 1 == $r_config['clear_all_resets']){ ?>selected="selected"<?php } ?>>
                                                Yes
                                            </option>
                                        </select>

                                        <p>Clear all character resets or only resets required above.</p>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="clear_magic">Clear MagicList</label>

                                    <div class="controls">
                                        <select name="clear_magic" id="clear_magic">
                                            <option value="0"
                                                    <?php if(isset($r_config['clear_magic']) && 0 == $r_config['clear_magic']){ ?>selected="selected"<?php } ?>>
                                                No
                                            </option>
                                            <option value="1"
                                                    <?php if(isset($r_config['clear_magic']) && 1 == $r_config['clear_magic']){ ?>selected="selected"<?php } ?>>
                                                Yes
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="clear_inventory">Clear Inventory</label>

                                    <div class="controls">
                                        <select name="clear_inventory" id="clear_inventory">
                                            <option value="0"
                                                    <?php if(isset($r_config['clear_inventory']) && 0 == $r_config['clear_inventory']){ ?>selected="selected"<?php } ?>>
                                                No
                                            </option>
                                            <option value="1"
                                                    <?php if(isset($r_config['clear_inventory']) && 1 == $r_config['clear_inventory']){ ?>selected="selected"<?php } ?>>
                                                Yes
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="clear_stats">Clear Stats</label>

                                    <div class="controls">
                                        <select name="clear_stats" id="clear_stats">
                                            <option value="0"
                                                    <?php if(isset($r_config['clear_stats']) && 0 == $r_config['clear_stats']){ ?>selected="selected"<?php } ?>>
                                                No
                                            </option>
                                            <option value="1"
                                                    <?php if(isset($r_config['clear_stats']) && 1 == $r_config['clear_stats']){ ?>selected="selected"<?php } ?>>
                                                Yes
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="new_stat_points">New Stat Points</label>

                                    <div class="controls">
                                        <input type="text" class="span3 typeahead" id="new_stat_points"
                                               name="new_stat_points"
                                               value="<?php if(isset($r_config['new_stat_points'])){
                                                   echo $r_config['new_stat_points'];
                                               } ?>" placeholder="0"/>

                                        <p>Every stat changed to this value if Clear Stats is activated</p>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="clear_level_up">Clear LevelUp Points</label>

                                    <div class="controls">
                                        <select name="clear_level_up" id="clear_level_up">
                                            <option value="0"
                                                    <?php if(isset($r_config['clear_level_up']) && 0 == $r_config['clear_level_up']){ ?>selected="selected"<?php } ?>>
                                                No
                                            </option>
                                            <option value="1"
                                                    <?php if(isset($r_config['clear_level_up']) && 1 == $r_config['clear_level_up']){ ?>selected="selected"<?php } ?>>
                                                Yes
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="new_free_points">New LevelUp Points</label>

                                    <div class="controls">
                                        <input type="text" class="span3 typeahead" id="new_free_points"
                                               name="new_free_points"
                                               value="<?php if(isset($r_config['new_free_points'])){
                                                   echo $r_config['new_free_points'];
                                               } ?>" placeholder="0"/>

                                        <p>LevelUpPoints changed to this value if Clear LevelUp Points is activated</p>
                                    </div>
                                </div>
								<?php 
								$i = 0;
								foreach($this->website->get_char_class(0, false, true) AS $key => $class){ 
								?>
								<div class="control-group">
									<label class="control-label" for="bonus_lvl_up_<?php echo $class['short'];?>">Bonus LevelUp <?php echo strtoupper($class['short']);?> (<?php echo $key;?>)</label>

									<div class="controls">
										<div class="input-append">
											<input type="text" size="16" class="bonus_lvl_up" name="bonus_lvl_up_<?php echo $key;?>" value="<?php if(isset($r_config['bonus_points'][$key])){ echo $r_config['bonus_points'][$key]; } ?>" placeholder="0"/>
											<?php if($i == 0){ ?><button class="btn" type="button" id="apply_to_all_classes">Apply To All Classes</button><?php } ?>
										</div>
										<?php if($i == 0){ ?><p>Bonus LevelUp Points after reset character this value is multiplied by resets</p><?php } ?>
									</div>
								</div>
								<?php 
									$i++;
								} 
								?> 
                                <div class="control-group">
                                    <label class="control-label" for="bonus_points_save">Bonus LevelUp Point Save</label>

                                    <div class="controls">
                                        <select name="bonus_points_save" id="bonus_points_save">
                                            <option value="0"
                                                    <?php if(isset($r_config['bonus_points_save']) && 0 == $r_config['bonus_points_save']){ ?>selected="selected"<?php } ?>>
                                                No
                                            </option>
                                            <option value="1"
                                                    <?php if(isset($r_config['bonus_points_save']) && 1 == $r_config['bonus_points_save']){ ?>selected="selected"<?php } ?>>
                                                Yes
                                            </option>
                                        </select>

                                        <p>Multiply bonus points after each grand reset</p>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="bonus_reset_stats">Bonus Reset Stats</label>

                                    <div class="controls">
                                        <select name="bonus_reset_stats" id="bonus_reset_stats">
                                            <option value="0"
                                                    <?php if(isset($r_config['bonus_reset_stats']) && 0 == $r_config['bonus_reset_stats']){ ?>selected="selected"<?php } ?>>
                                                No
                                            </option>
                                            <option value="1"
                                                    <?php if(isset($r_config['bonus_reset_stats']) && 1 == $r_config['bonus_reset_stats']){ ?>selected="selected"<?php } ?>>
                                                Yes
                                            </option>
                                        </select>

                                        <p>Add bonus stats earned by reseting character</p>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="bonus_credits">Bonus Credits</label>

                                    <div class="controls">
                                        <input type="text" class="span3 typeahead" id="bonus_credits"
                                               name="bonus_credits"
                                               value="<?php if(isset($r_config['bonus_credits'])){
                                                   echo $r_config['bonus_credits'];
                                               } ?>" placeholder="0"/>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="bonus_gcredits">Bonus Gold Credits</label>

                                    <div class="controls">
                                        <input type="text" class="span3 typeahead" id="bonus_gcredits"
                                               name="bonus_gcredits"
                                               value="<?php if(isset($r_config['bonus_gcredits'])){
                                                   echo $r_config['bonus_gcredits'];
                                               } ?>" placeholder="0"/>
                                    </div>
                                </div>
								<div class="control-group">
									<label class="control-label" for="bonus_ruud">Bonus Ruud</label>

									<div class="controls">
										<input type="text" class="span3 typeahead" id="bonus_ruud" name="bonus_ruud"
											   value="<?php if(isset($r_config['bonus_ruud'])){
												   echo $r_config['bonus_ruud'];
											   } ?>" placeholder="0"/>
									</div>
								</div>
                                <div class="control-group">
                                    <label class="control-label" for="clear_masterlevel">Clear MasterLevel</label>

                                    <div class="controls">
                                        <select name="clear_masterlevel" id="clear_masterlevel">
                                            <option value="0"
                                                    <?php if(isset($r_config['clear_masterlevel']) && 0 == $r_config['clear_masterlevel']){ ?>selected="selected"<?php } ?>>
                                                No
                                            </option>
                                            <option value="1"
                                                    <?php if(isset($r_config['clear_masterlevel']) && 1 == $r_config['clear_masterlevel']){ ?>selected="selected"<?php } ?>>
                                                Yes
                                            </option>
                                        </select>

                                        <p>Clear master level on reset</p>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary" name="edit_settings">Edit settings
                                    </button>
                                    <button type="reset" class="btn">Cancel</button>
                                </div>
                            </fieldset>
                        </form>
                        <?php
                    }
                ?>
            </div>
        </div>
    </div>
</div>