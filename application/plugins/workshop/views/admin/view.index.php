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
                        <li role="presentation" <?php if($i == 1){ ?> class="active"<?php } ?>><a
                                    href="#<?php echo $key; ?>" aria-controls="<?php echo $key; ?>" role="tab"
                                    data-toggle="tab"><?php echo $val['title']; ?> Server Settings</a></li>
                    <?php endforeach; ?>
                <li role="presentation">
                <li><a href="<?php echo $this->config->base_url; ?>workshop/logs" role="tab">Logs</a></li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
    <?php if(isset($js)): ?>
        <script src="<?php echo $js; ?>"></script>
        <script type="text/javascript">
            var workshop = new workshop();
            workshop.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
            $(document).ready(function () {
                $('form[id^="workshop_settings_form_"]').on("submit", function (e) {
                    e.preventDefault();
                    workshop.saveSettings($(this));
                });
            });
        </script>
    <?php endif; ?>
    <div class="row-fluid">
        <div class="box span12">
            <div class="tab-content">
                <?php
                    $i = 0;
                    foreach($server_list AS $key => $data):
                        $val = ($is_multi_server == 0) ? $plugin_config : (isset($plugin_config[$key]) ? $plugin_config[$key] : false);
                        $i++;
                        ?>
                        <div role="tabpanel" class="tab-pane fade in <?php if($i == 1){ ?>active<?php } ?>"
                             id="<?php echo $key; ?>">
                            <div class="box-header well">
                                <h2><i class="icon-edit"></i> <?php echo $data['title']; ?> Server Settings</h2>
                            </div>
                            <div class="box-content">
                                <form class="form-horizontal" method="POST" action=""
                                      id="workshop_settings_form_<?php echo $key; ?>">
                                    <input type="hidden" id="server" name="server" value="<?php echo $key; ?>"/>

                                    <div class="control-group">
                                        <label class="control-label" for="active">Status </label>

                                        <div class="controls">
                                            <select id="active" name="active" required>
                                                <option value="0" <?php if($val['active'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>Inactive
                                                </option>
                                                <option value="1" <?php if($val['active'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Active
                                                </option>
                                            </select>

                                            <p class="help-block">Use workshop module.</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="allow_upgrade_level">Level Upgrade</label>
                                        <div class="controls">
                                            <select id="allow_upgrade_level" name="allow_upgrade_level" required>
                                                <option value="0" <?php if($val['allow_upgrade_level'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>No
                                                </option>
                                                <option value="1" <?php if($val['allow_upgrade_level'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Yes
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="min_level_required">Min Level Required</label>
                                        <div class="controls">
                                            <select id="min_level_required" name="min_level_required" required>
                                                <?php for($a = 0; $a <= 15; $a++): ?>
                                                    <option
                                                            value="<?php echo $a; ?>" <?php if($val['min_level_required'] == $a){
                                                        echo 'selected="selected"';
                                                    } ?>><?php echo $a; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="max_level_allowed">Max Level Allowed</label>
                                        <div class="controls">
                                            <select id="max_level_allowed" name="max_level_allowed" required>
                                                <?php for($a = 0; $a <= 15; $a++): ?>
                                                    <option
                                                            value="<?php echo $a; ?>" <?php if($val['max_level_allowed'] == $a){
                                                        echo 'selected="selected"';
                                                    } ?>><?php echo $a; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="level_price">Level Price </label>
                                        <div class="controls">
                                            <input type="text" class="span3 typeahead" id="level_price" name="level_price"
                                                   value="<?php echo $val['level_price']; ?>" title="Only numbers"
                                                   pattern="\d*" required/>
                                            <p class="help-block">1 Level Price.</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="allow_upgrade_option">Option Upgrade</label>
                                        <div class="controls">
                                            <select id="allow_upgrade_option" name="allow_upgrade_option" required>
                                                <option value="0" <?php if($val['allow_upgrade_option'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>No
                                                </option>
                                                <option value="1" <?php if($val['allow_upgrade_option'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Yes
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="min_option_required">Min Option Required</label>
                                        <div class="controls">
                                            <select id="min_option_required" name="min_option_required" required>
                                                <?php for($a = 0; $a <= 7; $a++): ?>
                                                    <option
                                                            value="<?php echo $a; ?>" <?php if($val['min_option_required'] == $a){
                                                        echo 'selected="selected"';
                                                    } ?>><?php echo $a * 4; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="max_option_allowed">Max Option Allowed</label>
                                        <div class="controls">
                                            <select id="max_option_allowed" name="max_option_allowed" required>
                                                <?php for($a = 0; $a <= 7; $a++): ?>
                                                    <option
                                                            value="<?php echo $a; ?>" <?php if($val['max_option_allowed'] == $a){
                                                        echo 'selected="selected"';
                                                    } ?>><?php echo $a * 4; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="option_price">Option Price </label>
                                        <div class="controls">
                                            <input type="text" class="span3 typeahead" id="option_price" name="option_price"
                                                   value="<?php echo $val['option_price']; ?>" title="Only numbers"
                                                   pattern="\d*" required/>
                                            <p class="help-block">1 Option Price.</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="allow_add_luck">Allow Adding Luck</label>
                                        <div class="controls">
                                            <select id="allow_add_luck" name="allow_add_luck" required>
                                                <option value="0" <?php if($val['allow_add_luck'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>No
                                                </option>
                                                <option value="1" <?php if($val['allow_add_luck'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Yes
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="luck_price">Luck Price </label>
                                        <div class="controls">
                                            <input type="text" class="span3 typeahead" id="luck_price" name="luck_price"
                                                   value="<?php echo $val['luck_price']; ?>" title="Only numbers"
                                                   pattern="\d*" required/>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="allow_add_skill">Allow Adding Skill</label>
                                        <div class="controls">
                                            <select id="allow_add_skill" name="allow_add_skill" required>
                                                <option value="0" <?php if($val['allow_add_skill'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>No
                                                </option>
                                                <option value="1" <?php if($val['allow_add_skill'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Yes
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="skill_price">Skill Price </label>
                                        <div class="controls">
                                            <input type="text" class="span3 typeahead" id="skill_price" name="skill_price"
                                                   value="<?php echo $val['skill_price']; ?>" title="Only numbers"
                                                   pattern="\d*" required/>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="allow_add_exe">Allow Adding Exe</label>
                                        <div class="controls">
                                            <select id="allow_add_exe" name="allow_add_exe" required>
                                                <option value="0" <?php if($val['allow_add_exe'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>No
                                                </option>
                                                <option value="1" <?php if($val['allow_add_exe'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Yes
                                                </option>
                                            </select>
                                        </div>
                                    </div>
									<div class="control-group">
                                        <label class="control-label" for="allow_remove_exe">Allow Removing Exe</label>
                                        <div class="controls">
                                            <select id="allow_remove_exe" name="allow_remove_exe" required>
                                                <option value="0" <?php if($val['allow_remove_exe'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>No
                                                </option>
                                                <option value="1" <?php if($val['allow_remove_exe'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Yes
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="max_exe_opt">Max Exe Option Allowed</label>
                                        <div class="controls">
                                            <select id="max_exe_opt" name="max_exe_opt" required>
                                                <?php for($a = 0; $a <= 6; $a++): ?>
                                                    <option
                                                            value="<?php echo $a; ?>" <?php if($val['max_exe_opt'] == $a){
                                                        echo 'selected="selected"';
                                                    } ?>><?php echo $a; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="exe_opt_price">Exe Price </label>
                                        <div class="controls">
                                            <input type="text" class="span3 typeahead" id="exe_opt_price"
                                                   name="exe_opt_price" value="<?php echo $val['exe_opt_price']; ?>"
                                                   title="Only numbers" pattern="\d*" required/>
                                        </div>
                                    </div>
									<div class="control-group">
                                        <label class="control-label" for="remove_exe_opt_price">Exe Remove Price </label>
                                        <div class="controls">
                                            <input type="text" class="span3 typeahead" id="remove_exe_opt_price"
                                                   name="remove_exe_opt_price" value="<?php echo $val['remove_exe_opt_price']; ?>"
                                                   title="Only numbers" pattern="\d*" required/>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="allow_add_ancient">Allow Adding Ancient</label>
                                        <div class="controls">
                                            <select id="allow_add_ancient" name="allow_add_ancient" required>
                                                <option value="0" <?php if($val['allow_add_ancient'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>No
                                                </option>
                                                <option value="1" <?php if($val['allow_add_ancient'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Yes
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="ancient_opt_price">Ancient Price </label>
                                        <div class="controls">
                                            <input type="text" class="span3 typeahead" id="ancient_opt_price"
                                                   name="ancient_opt_price" value="<?php echo $val['ancient_opt_price']; ?>"
                                                   title="Only numbers" pattern="\d*" required/>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="allow_add_refinery">Allow Adding Refinery</label>
                                        <div class="controls">
                                            <select id="allow_add_refinery" name="allow_add_refinery" required>
                                                <option value="0" <?php if($val['allow_add_refinery'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>No
                                                </option>
                                                <option value="1" <?php if($val['allow_add_refinery'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Yes
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="refinery_opt_price">Refinery Price </label>
                                        <div class="controls">
                                            <input type="text" class="span3 typeahead" id="refinery_opt_price"
                                                   name="refinery_opt_price"
                                                   value="<?php echo $val['refinery_opt_price']; ?>" title="Only numbers"
                                                   pattern="\d*" required/>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="allow_add_harmony">Allow Adding Harmony</label>
                                        <div class="controls">
                                            <select id="allow_add_harmony" name="allow_add_harmony" required>
                                                <option value="0" <?php if($val['allow_add_harmony'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>No
                                                </option>
                                                <option value="1" <?php if($val['allow_add_harmony'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Yes
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="harmony_opt_price">Harmony Price </label>
                                        <div class="controls">
                                            <input type="text" class="span3 typeahead" id="harmony_opt_price"
                                                   name="harmony_opt_price" value="<?php echo $val['harmony_opt_price']; ?>"
                                                   title="Only numbers" pattern="\d*" required/>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="allow_add_socket">Allow Adding Sockets</label>
                                        <div class="controls">
                                            <select id="allow_add_socket" name="allow_add_socket" required>
                                                <option value="0" <?php if($val['allow_add_socket'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>No
                                                </option>
                                                <option value="1" <?php if($val['allow_add_socket'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Yes
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="check_socket_part_type">Check Item Part </label>
                                        <div class="controls">
                                            <select id="check_socket_part_type" name="check_socket_part_type">
                                                <option value="0" <?php if($val['check_socket_part_type'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>No
                                                </option>
                                                <option value="1" <?php if($val['check_socket_part_type'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Yes
                                                </option>
                                            </select>
                                            <p class="help-block">Check if socket is allowed for exe part type.</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="allow_equal_seed">Equal Seed Sockets </label>
                                        <div class="controls">
                                            <select id="allow_equal_seed" name="allow_equal_seed">
                                                <option value="0" <?php if($val['allow_equal_seed'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>No
                                                </option>
                                                <option value="1" <?php if($val['allow_equal_seed'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Yes
                                                </option>
                                            </select>

                                            <p class="help-block">Allow select equal seed sockets.</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="allow_equal_sockets">Equal Sockets </label>
                                        <div class="controls">
                                            <select id="allow_equal_sockets" name="allow_equal_sockets">
                                                <option value="0" <?php if($val['allow_equal_sockets'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>No
                                                </option>
                                                <option value="1" <?php if($val['allow_equal_sockets'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Yes
                                                </option>
                                            </select>

                                            <p class="help-block">Allow selecting equal sockets.</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="socket_opt_price">Socket Price </label>
                                        <div class="controls">
                                            <input type="text" class="span3 typeahead" id="socket_opt_price"
                                                   name="socket_opt_price" value="<?php echo $val['socket_opt_price']; ?>"
                                                   title="Only numbers" pattern="\d*" required/>
                                        </div>
                                    </div>
									 <div class="control-group">
										<label class="control-label" for="black_list_items">Blacklist Items</label>

										<div class="controls">
											<input type="text" class="input-xlarge" data-role="tagsinput" name="black_list_items" id="black_list_items" value="<?php if(isset($val['black_list_items'])): echo $val['black_list_items']; endif; ?>"/>

											<p>Items in this category will be not allowed for upgrade. Format: category id seperated by comma</p>
										</div>
									</div>
                                    <div class="control-group">
                                        <label class="control-label" for="payment_method">Payment Method</label>
                                        <div class="controls">
                                            <select id="payment_method" name="payment_method" required>
                                                <option value="1" <?php if($val['payment_method'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Credits 1
                                                </option>
                                                <option value="2" <?php if($val['payment_method'] == 2){
                                                    echo 'selected="selected"';
                                                } ?>>Credits 2
                                                </option>
                                            </select>
                                            <p>For credits types check your credits settings <a
                                                        href="<?php echo $this->config->base_url; ?>admincp/manage-settings/credits"
                                                        target="_blank">here</a></p>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary"
                                                name="edit_workshop_settings"
                                                id="edit_workshop_settings">Save changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php
    $this->load->view('admincp' . DS . 'view.footer');
?>
