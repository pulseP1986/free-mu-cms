<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/manage-settings/reset">Reset Settings</a></li>
        </ul>
    </div>
    <div class="row-fluid">
        <div class="box span12">
            <div class="box-header well">
                <h2><i class="icon-edit"></i> Add New Reset Settings</h2>
            </div>
            <div class="box-content">
                <?php
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
                                                <?php if(isset($_POST['server']) && $key == $_POST['server']){ ?>selected="selected"<?php } ?>><?php echo $servers[$key]['title']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="sreset">Starting Reset</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="sreset" name="sreset"
                                       value="<?php if(isset($_POST['sreset'])){
                                           echo $_POST['sreset'];
                                       } ?>" placeholder="0"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="ereset">Ending Reset</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="ereset" name="ereset"
                                       value="<?php if(isset($_POST['ereset'])){
                                           echo $_POST['ereset'];
                                       } ?>" placeholder="9999"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="grand_reset">Required Grand Reset</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="grand_reset" name="grand_reset"
                                       value="<?php if(isset($_POST['grand_reset'])){
                                           echo $_POST['grand_reset'];
                                       } ?>" placeholder="0"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="money">Required Zen</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="money" name="money"
                                       value="<?php if(isset($_POST['money'])){
                                           echo $_POST['money'];
                                       } ?>" placeholder="9999"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="money_x_reset">Is Zen Multiplied by Resets</label>

                            <div class="controls">
                                <select name="money_x_reset" id="money_x_reset">
                                    <option value="0"
                                            <?php if(isset($_POST['money_x_reset']) && 0 == $_POST['money_x_reset']){ ?>selected="selected"<?php } ?>>
                                        No
                                    </option>
                                    <option value="1"
                                            <?php if(isset($_POST['money_x_reset']) && 1 == $_POST['money_x_reset']){ ?>selected="selected"<?php } ?>>
                                        Yes
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="level">Required level</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="level" name="level"
                                       value="<?php if(isset($_POST['level'])){
                                           echo $_POST['level'];
                                       } ?>" placeholder="400"/>
                            </div>
                        </div>
						<div class="control-group">
							<label class="control-label" for="mlevel">Required Master level</label>

							<div class="controls">
								<input type="text" class="span3 typeahead" id="mlevel" name="mlevel"
									   value="<?php if(isset($_POST['mlevel'])){
										   echo $_POST['mlevel'];
									   } ?>" placeholder="0"/>
							</div>
						</div>
						<div class="control-group">
                            <label class="control-label" for="level_after_reset">Level after reset</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="level_after_reset" name="level_after_reset"
                                       value="<?php if(isset($_POST['level_after_reset'])){
                                           echo $_POST['level_after_reset'];
                                       } ?>" placeholder="1"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="reset_cooldown">Reset CoolDown</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="reset_cooldown" name="reset_cooldown"
                                       value="<?php if(isset($_POST['reset_cooldown'])){
                                           echo $_POST['reset_cooldown'];
                                       } ?>" placeholder="60"/>

                                <p>How often can make resets, value should be set in seconds</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="clear_magic">Clear MagicList</label>

                            <div class="controls">
                                <select name="clear_magic" id="clear_magic">
                                    <option value="0"
                                            <?php if(isset($_POST['clear_magic']) && 0 == $_POST['clear_magic']){ ?>selected="selected"<?php } ?>>
                                        No
                                    </option>
                                    <option value="1"
                                            <?php if(isset($_POST['clear_magic']) && 1 == $_POST['clear_magic']){ ?>selected="selected"<?php } ?>>
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
                                            <?php if(isset($_POST['clear_inventory']) && 0 == $_POST['clear_inventory']){ ?>selected="selected"<?php } ?>>
                                        No
                                    </option>
                                    <option value="1"
                                            <?php if(isset($_POST['clear_inventory']) && 1 == $_POST['clear_inventory']){ ?>selected="selected"<?php } ?>>
                                        Yes
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="clear_exp_inventory">Clear Expanded Inventory</label>

                            <div class="controls">
                                <select name="clear_exp_inventory" id="clear_exp_inventory">
                                    <option value="0"
                                            <?php if(isset($_POST['clear_exp_inventory']) && 0 == $_POST['clear_exp_inventory']){ ?>selected="selected"<?php } ?>>
                                        No
                                    </option>
                                    <option value="1"
                                            <?php if(isset($_POST['clear_exp_inventory']) && 1 == $_POST['clear_exp_inventory']){ ?>selected="selected"<?php } ?>>
                                        Yes
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="clear_equipment">Clear Equipment</label>

                            <div class="controls">
                                <select name="clear_equipment" id="clear_equipment">
                                    <option value="0"
                                            <?php if(isset($_POST['clear_equipment']) && 0 == $_POST['clear_equipment']){ ?>selected="selected"<?php } ?>>
                                        No
                                    </option>
                                    <option value="1"
                                            <?php if(isset($_POST['clear_equipment']) && 1 == $_POST['clear_equipment']){ ?>selected="selected"<?php } ?>>
                                        Yes
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="clear_store">Clear Personal Store</label>

                            <div class="controls">
                                <select name="clear_store" id="clear_store">
                                    <option value="0"
                                            <?php if(isset($_POST['clear_store']) && 0 == $_POST['clear_store']){ ?>selected="selected"<?php } ?>>
                                        No
                                    </option>
                                    <option value="1"
                                            <?php if(isset($_POST['clear_store']) && 1 == $_POST['clear_store']){ ?>selected="selected"<?php } ?>>
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
                                            <?php if(isset($_POST['clear_stats']) && 0 == $_POST['clear_stats']){ ?>selected="selected"<?php } ?>>
                                        No
                                    </option>
                                    <option value="1"
                                            <?php if(isset($_POST['clear_stats']) && 1 == $_POST['clear_stats']){ ?>selected="selected"<?php } ?>>
                                        Yes
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="new_stat_points">New Stat Points</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="new_stat_points" name="new_stat_points"
                                       value="<?php if(isset($_POST['new_stat_points'])){
                                           echo $_POST['new_stat_points'];
                                       } ?>" placeholder="0"/>

                                <p>Every stat changed to this value if Clear Stats is activated</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="clear_level_up">Clear LevelUp Points</label>

                            <div class="controls">
                                <select name="clear_level_up" id="clear_level_up">
                                    <option value="0"
                                            <?php if(isset($_POST['clear_level_up']) && 0 == $_POST['clear_level_up']){ ?>selected="selected"<?php } ?>>
                                        No
                                    </option>
                                    <option value="1"
                                            <?php if(isset($_POST['clear_level_up']) && 1 == $_POST['clear_level_up']){ ?>selected="selected"<?php } ?>>
                                        Yes
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="new_free_points">New LevelUp Points</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="new_free_points" name="new_free_points"
                                       value="<?php if(isset($_POST['new_free_points'])){
                                           echo $_POST['new_free_points'];
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
                                    <input type="text" size="16" class="bonus_lvl_up" name="bonus_lvl_up_<?php echo $key;?>" value="<?php if(isset($_POST['bonus_lvl_up_'.$key.''])){ echo $_POST['bonus_lvl_up_'.$key.'']; } ?>" placeholder="0"/>
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
                            <label class="control-label" for="bonus_credits">Bonus Credits</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="bonus_credits" name="bonus_credits"
                                       value="<?php if(isset($_POST['bonus_credits'])){
                                           echo $_POST['bonus_credits'];
                                       } ?>" placeholder="0"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="bonus_gcredits">Bonus Gold Credits</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="bonus_gcredits" name="bonus_gcredits"
                                       value="<?php if(isset($_POST['bonus_gcredits'])){
                                           echo $_POST['bonus_gcredits'];
                                       } ?>" placeholder="0"/>
                            </div>
                        </div>
						<div class="control-group">
                            <label class="control-label" for="bonus_ruud">Bonus Ruud</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="bonus_ruud" name="bonus_ruud"
                                       value="<?php if(isset($_POST['bonus_ruud'])){
                                           echo $_POST['bonus_ruud'];
                                       } ?>" placeholder="0"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="bonus_gr_points">Bonus GR StatPoints</label>

                            <div class="controls">
                                <select name="bonus_gr_points" id="bonus_gr_points">
                                    <option value="0"
                                            <?php if(isset($_POST['bonus_gr_points']) && 0 == $_POST['bonus_gr_points']){ ?>selected="selected"<?php } ?>>
                                        No
                                    </option>
                                    <option value="1"
                                            <?php if(isset($_POST['bonus_gr_points']) && 1 == $_POST['bonus_gr_points']){ ?>selected="selected"<?php } ?>>
                                        Yes
                                    </option>
                                </select>

                                <p>Add earned bonus grand reset points</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="clear_masterlevel">Clear MasterLevel</label>

                            <div class="controls">
                                <select name="clear_masterlevel" id="clear_masterlevel">
                                    <option value="0"
                                            <?php if(isset($_POST['clear_masterlevel']) && 0 == $_POST['clear_masterlevel']){ ?>selected="selected"<?php } ?>>
                                        No
                                    </option>
                                    <option value="1"
                                            <?php if(isset($_POST['clear_masterlevel']) && 1 == $_POST['clear_masterlevel']){ ?>selected="selected"<?php } ?>>
                                        Yes
                                    </option>
                                </select>

                                <p>Clear master level and experience on reset</p>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" name="add_reset_settings">Add settings
                            </button>
                            <button type="reset" class="btn">Cancel</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>