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
                <h2><i class="icon-edit"></i> Add New Grand Reset Settings</h2>
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
                            <label class="control-label" for="sreset">Starting GReset</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="sreset" name="sreset"
                                       value="<?php if(isset($_POST['sreset'])){
                                           echo $_POST['sreset'];
                                       } ?>" placeholder="0"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="ereset">Ending GReset</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="ereset" name="ereset"
                                       value="<?php if(isset($_POST['ereset'])){
                                           echo $_POST['ereset'];
                                       } ?>" placeholder="9999"/>
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
                            <label class="control-label" for="money_x_reset">Is Zen Multiplied by Grand Reset</label>

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
                            <label class="control-label" for="reset">Required reset</label>

                            <div class="controls">
                                <input type="text" class="span3 typeahead" id="reset" name="reset"
                                       value="<?php if(isset($_POST['reset'])){
                                           echo $_POST['reset'];
                                       } ?>" placeholder="100"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="clear_all_resets">Clear All Resets</label>

                            <div class="controls">
                                <select name="clear_all_resets" id="clear_all_resets">
                                    <option value="0"
                                            <?php if(isset($_POST['clear_all_resets']) && 0 == $_POST['clear_all_resets']){ ?>selected="selected"<?php } ?>>
                                        No
                                    </option>
                                    <option value="1"
                                            <?php if(isset($_POST['clear_all_resets']) && 1 == $_POST['clear_all_resets']){ ?>selected="selected"<?php } ?>>
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
                            <label class="control-label" for="bonus_points_save">Bonus LevelUp Point Save</label>

                            <div class="controls">
                                <select name="bonus_points_save" id="bonus_points_save">
                                    <option value="0"
                                            <?php if(isset($_POST['bonus_points_save']) && 0 == $_POST['bonus_points_save']){ ?>selected="selected"<?php } ?>>
                                        No
                                    </option>
                                    <option value="1"
                                            <?php if(isset($_POST['bonus_points_save']) && 1 == $_POST['bonus_points_save']){ ?>selected="selected"<?php } ?>>
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
                                            <?php if(isset($_POST['bonus_reset_stats']) && 0 == $_POST['bonus_reset_stats']){ ?>selected="selected"<?php } ?>>
                                        No
                                    </option>
                                    <option value="1"
                                            <?php if(isset($_POST['bonus_reset_stats']) && 1 == $_POST['bonus_reset_stats']){ ?>selected="selected"<?php } ?>>
                                        Yes
                                    </option>
                                </select>

                                <p>Add bonus stats earned by reseting character</p>
                            </div>
                        </div>
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

                                <p>Clear master level and experience on grand reset</p>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" name="add_greset_settings">Add settings
                            </button>
                            <button type="reset" class="btn">Cancel</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>