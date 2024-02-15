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
                    <a href="#packages" aria-controls="packages" role="tab" data-toggle="tab">Merchant Manager</a></li>
                <li><a href="<?php echo $this->config->base_url; ?>merchant/logs" role="tab">Logs</a></li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
    <?php if(isset($js)): ?>
        <script src="<?php echo $js; ?>"></script>
        <script type="text/javascript">
            var Merchant = new Merchant();
            Merchant.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
            $(document).ready(function () {
                $('form[id^="merchant_settings_form_"]').on("submit", function (e) {
                    e.preventDefault();
                    Merchant.saveSettings($(this));
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
                                      id="merchant_settings_form_<?php echo $key; ?>">
                                    <input type="hidden" id="server" name="server" value="<?php echo $key; ?>"/>

                                    <div class="control-group">
                                        <label class="control-label" for="active">Status </label>

                                        <div class="controls">
                                            <select id="active" name="active" class="span5 typeahead" required>
                                                <option value="0" <?php if($val['active'] == 0){
                                                    echo 'selected="selected"';
                                                } ?>>Inactive
                                                </option>
                                                <option value="1" <?php if($val['active'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Active
                                                </option>
                                            </select>

                                            <p class="help-block">Use Merchant module.</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="wcoin_ratio">WCoin Ratio</label>
                                        <div class="controls">
                                            <input type="text" class="span5 typeahead" id="wcoin_ratio"
                                                   name="wcoin_ratio" value="<?php if(isset($val['wcoin_ratio'])){
                                                echo $val['wcoin_ratio'];
                                            } ?>"/>

                                            <p class="help-block">Wcoin exchange ratio. Currency Amount/Wcoins</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="wcoin_bonus_ratio">Wcoin Bonus Ratio </label>

                                        <div class="controls">
                                            <input type="text" class="span5 typeahead" id="wcoin_bonus_ratio"
                                                   name="wcoin_bonus_ratio"
                                                   value="<?php if(isset($val['wcoin_bonus_ratio'])){
                                                       echo $val['wcoin_bonus_ratio'];
                                                   } ?>"/>

                                            <p class="help-block">Wcoin bonus ratio. Currency Amount/Bonus Wcoins</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="wcoin_total_bonus">Wcoin Total Bonus</label>

                                        <div class="controls">
                                            <input type="text" class="span5 typeahead" id="wcoin_total_bonus"
                                                   name="wcoin_total_bonus"
                                                   value="<?php if(isset($val['wcoin_total_bonus'])){
                                                       echo $val['wcoin_total_bonus'];
                                                   } ?>" pattern="\d*"/>

                                            <p>Wcoin bonus percents on total amount.</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="reward_type">Reward Web Currency Type</label>

                                        <div class="controls">
                                            <select id="reward_type" name="reward_type" class="span5 typeahead" required>
                                                <option
                                                        value="1" <?php if(isset($val['reward_type']) && $val['reward_type'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Credits 1
                                                </option>
                                                <option
                                                        value="2" <?php if(isset($val['reward_type']) && $val['reward_type'] == 2){
                                                    echo 'selected="selected"';
                                                } ?>>Credits 2
                                                </option>
                                            </select>

                                            <p>For credits types check your credits settings <a
                                                        href="<?php echo $this->config->base_url; ?>admincp/manage-settings/credits"
                                                        target="_blank">here</a></p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="reward_bonus_ratio">Web Currency Bonus
                                            Ratio </label>

                                        <div class="controls">
                                            <input type="text" class="span5 typeahead" id="reward_bonus_ratio"
                                                   name="reward_bonus_ratio"
                                                   value="<?php if(isset($val['reward_bonus_ratio'])){
                                                       echo $val['reward_bonus_ratio'];
                                                   } ?>"/>

                                            <p class="help-block">Web currency bonus ratio. Currency Amount/Bonus Web
                                                Currency</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="currency_used">Currency </label>

                                        <div class="controls">
                                            <input type="text" class="span5 typeahead" id="currency_used"
                                                   name="currency_used"
                                                   value="<?php if(isset($val['currency_used'])){
                                                       echo $val['currency_used'];
                                                   } ?>"/>

                                            <p class="help-block">What currency is used in donations.</p>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary" name="edit_merchant_settings"
                                                id="edit_merchant_settings">Save changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <div role="tabpanel" class="tab-pane fade in" id="packages">
                    <div class="box-header well">
                        <h2><i class="icon-edit"></i> Merchant List</h2>
                    </div>
                    <div class="box-content">
                        <div id="donate_package_list">
                            <table class="table table-striped table-bordered bootstrap-datatable datatable"
                                   id="donate_sortable_merchant">
                                <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Wallet</th>
                                    <th>Server</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody id="donate_sortable_content" style="cursor: move;">
                                <?php foreach($merchant_list as $merchant): ?>
                                    <tr id="<?php echo $merchant['id']; ?>">
                                        <td>
                                            <input class="input-medium" type="text"
                                                   id="merchant_account_<?php echo $merchant['id']; ?>"
                                                   value="<?php echo $merchant['memb___id']; ?>"/>
                                        </td>
                                        <td class="center">
                                            <input class="input-medium" type="text"
                                                   id="merchant_name_<?php echo $merchant['id']; ?>"
                                                   value="<?php echo $merchant['name']; ?>"/>
                                        </td>
                                        <td class="center">
                                            <input class="input-medium" type="text"
                                                   id="merchant_contact_<?php echo $merchant['id']; ?>"
                                                   value="<?php echo $merchant['contact']; ?>"/>
                                        </td>
                                        <td class="center">
                                            <input class="input-small" type="text"
                                                   id="merchant_wallet_<?php echo $merchant['id']; ?>"
                                                   value="<?php echo $merchant['wallet']; ?>"/></td>
                                        <td class="center">
                                            <select id="merchant_server_<?php echo $merchant['id']; ?>"
                                                    class="input-medium">
                                                <?php
                                                    foreach($server_list as $key => $server):
                                                        ?>
                                                        <option value="<?php echo $key; ?>"
                                                                <?php if($key == $merchant['server']){ ?>selected="selected"<?php } ?>><?php echo $server_list[$key]['title']; ?></option>
                                                    <?php
                                                    endforeach;
                                                ?>
                                            </select>
                                        </td>
                                        <td class="center" id="status_icon_<?php echo $merchant['id']; ?>">
                                            <?php if($merchant['active'] == 1): ?>
                                                <span class="label label-success">Active</span>
                                            <?php else: ?>
                                                <span class="label label-important">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="center">
                                            <?php if($merchant['active'] == 1): ?>
                                                <a class="btn btn-danger" href="#"
                                                   id="status_button_<?php echo $pack['id']; ?>"
                                                   onclick="Merchant.changeStatus(<?php echo $merchant['id']; ?>, 0);">
                                                    <i class="icon-edit icon-white"></i> Disable </a>
                                            <?php else: ?>
                                                <a class="btn btn-success" href="#"
                                                   id="status_button_<?php echo $pack['id']; ?>"
                                                   onclick="Merchant.changeStatus(<?php echo $merchant['id']; ?>, 1);">
                                                    <i class="icon-edit icon-white"></i> Enable </a>
                                            <?php endif; ?>
                                            <a class="btn btn-info" href="#"
                                               onclick="Merchant.edit(<?php echo $merchant['id']; ?>);">
                                                <i class="icon-edit icon-white"></i> Edit </a>
                                            <a class="btn btn-danger" href="#"
                                               onclick="Merchant.delete(<?php echo $merchant['id']; ?>);">
                                                <i class="icon-trash icon-white"></i> Delete </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <table class="table table-striped table-bordered bootstrap-datatable datatable">
                            <tbody>
                            <tr>
                                <td>
                                    <input class="input-medium" type="text" id="merchant_account_new"
                                           placeholder="Account Id" value=""/>
                                </td>
                                <td>
                                    <input class="input-medium" type="text" id="merchant_name_new" placeholder="Name"
                                           value=""/>
                                </td>
                                <td>
                                    <input class="input-medium" type="text" id="merchant_contact_new"
                                           placeholder="Contact" value=""/>
                                </td>
                                <td>
                                    <input class="input-small" type="text" id="merchant_wallet_new" placeholder="0"
                                           value=""/>
                                </td>
                                <td class="center">
                                    <select id="server_new" class="input-medium">
                                        <?php
                                            foreach($server_list as $key => $server):
                                                ?>
                                                <option
                                                        value="<?php echo $key; ?>"><?php echo $server_list[$key]['title']; ?></option>
                                            <?php
                                            endforeach;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <a class="btn btn-success" href="#" onclick="Merchant.add();">
                                        <i class="icon-edit icon-white"></i> Add Merchant </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $this->load->view('admincp' . DS . 'view.footer');
?>
