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
                <li role="presentation"><a href="#packages" aria-controls="packages" role="tab" data-toggle="tab">Package
                        Manager</a></li>
                <li><a href="<?php echo $this->config->base_url; ?>nganluong/logs" role="tab">Logs</a></li>
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
    <?php if(isset($js)): ?>
        <script src="<?php echo $js; ?>"></script>
        <script type="text/javascript">
            var nganLuong = new nganLuong();
            nganLuong.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
            $(document).ready(function () {
                $('form[id^="nganluong_settings_form_"]').on("submit", function (e) {
                    e.preventDefault();
                    nganLuong.saveSettings($(this));
                });
                $("#donate_sortable_nganluong").find("tbody#donate_sortable_content").sortable({
                    placeholder: 'ui-state-highlight',
                    opacity: 0.6,
                    cursor: 'move',
                    update: function () {
                        nganLuong.saveOrder();
                    }
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
                                      id="nganluong_settings_form_<?php echo $key; ?>">
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
                                            <p class="help-block">Use NganLuong module.</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="receiver_email">Receiver Email </label>
                                        <div class="controls">
                                            <input type="text" class="span6 typeahead" id="receiver_email"
                                                   name="receiver_email" value="<?php echo $val['receiver_email']; ?>"/>
                                            <p class="help-block">Your receiver email in NganLuong payment system.</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="merchant_id">Merchant Id </label>
                                        <div class="controls">
                                            <input type="text" class="span6 typeahead" id="merchant_id" name="merchant_id"
                                                   value="<?php echo $val['merchant_id']; ?>"/>
                                            <p class="help-block">Your merchant id in NganLuong payment system.</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="pass">Merchant Pass </label>
                                        <div class="controls">
                                            <input type="text" class="span6 typeahead" id="pass" name="pass"
                                                   value="<?php echo $val['pass']; ?>"/>
                                            <p class="help-block">Your integration pass</p>
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label" for="reward_type">Reward Type</label>
                                        <div class="controls">
                                            <select id="reward_type" name="reward_type" required>
                                                <option
                                                        value="1" <?php if($val['reward_type'] == 1){
                                                    echo 'selected="selected"';
                                                } ?>>Credits 1
                                                </option>
                                                <option
                                                        value="2" <?php if($val['reward_type'] == 2){
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
                                        <button type="submit" class="btn btn-primary" name="edit_nganluong_settings"
                                                id="edit_nganluong_settings">Save changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <div role="tabpanel" class="tab-pane fade in" id="packages">
                    <div class="box-header well">
                        <h2><i class="icon-edit"></i> NganLuong Packages</h2>
                    </div>
                    <div class="box-content">
                        <div id="donate_package_list">
                            <table class="table table-striped table-bordered bootstrap-datatable datatable"
                                   id="donate_sortable_nganluong">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Currency</th>
                                    <th>Reward Points</th>
                                    <th>Server</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody id="donate_sortable_content" style="cursor: move;">
                                <?php foreach($packages_nganluong as $pack): ?>
                                    <tr id="<?php echo $pack['id']; ?>">
                                        <td><input class="input-medium" type="text"
                                                   id="pack_title_<?php echo $pack['id']; ?>"
                                                   value="<?php echo $pack['package']; ?>"/></td>
                                        <td class="center"><input class="input-small" type="text"
                                                                  id="pack_price_<?php echo $pack['id']; ?>"
                                                                  value="<?php echo $pack['price']; ?>"/></td>
                                        <td class="center">
                                            <input class="input-small" type="text"
                                                   id="pack_currency_<?php echo $pack['id']; ?>" placeholder="Currency"
                                                   value="<?php echo $pack['currency']; ?>"/>
                                        </td>
                                        <td class="center"><input class="input-small" type="text"
                                                                  id="pack_reward_<?php echo $pack['id']; ?>"
                                                                  value="<?php echo $pack['reward']; ?>"/></td>
                                        <td class="center">
                                            <select id="pack_server_<?php echo $pack['id']; ?>" class="input-medium">
                                                <?php
                                                    foreach($server_list as $key => $server):
                                                        ?>
                                                        <option value="<?php echo $key; ?>"
                                                                <?php if($key == $pack['server']){ ?>selected="selected"<?php } ?>><?php echo $server_list[$key]['title']; ?></option>
                                                    <?php
                                                    endforeach;
                                                ?>
                                            </select>
                                        </td>
                                        <td class="center" id="status_icon_<?php echo $pack['id']; ?>">
                                            <?php if($pack['status'] == 1): ?>
                                                <span class="label label-success">Active</span>
                                            <?php else: ?>
                                                <span class="label label-important">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="center">
                                            <?php if($pack['status'] == 1): ?>
                                                <a class="btn btn-danger" href="#"
                                                   id="status_button_<?php echo $pack['id']; ?>"
                                                   onclick="nganLuong.changeStatus(<?php echo $pack['id']; ?>, 0);">
                                                    <i class="icon-edit icon-white"></i> Disable
                                                </a>
                                            <?php else: ?>
                                                <a class="btn btn-success" href="#"
                                                   id="status_button_<?php echo $pack['id']; ?>"
                                                   onclick="nganLuong.changeStatus(<?php echo $pack['id']; ?>, 1);">
                                                    <i class="icon-edit icon-white"></i> Enable
                                                </a>
                                            <?php endif; ?>
                                            <a class="btn btn-info" href="#"
                                               onclick="nganLuong.edit(<?php echo $pack['id']; ?>);">
                                                <i class="icon-edit icon-white"></i> Edit
                                            </a>
                                            <a class="btn btn-danger" href="#"
                                               onclick="nganLuong.delete(<?php echo $pack['id']; ?>);">
                                                <i class="icon-trash icon-white"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <table class="table table-striped table-bordered bootstrap-datatable datatable">
                            <tbody>
                            <tr>
                                <td><input class="input-medium" type="text" id="title_new" placeholder="Title"
                                           value=""/></td>
                                <td><input class="input-small" type="text" id="price_new" placeholder="Price" value=""/>
                                </td>
                                <td><input class="input-small" type="text" id="currency_new" placeholder="Currency"
                                           value=""/></td>
                                <td><input class="input-small" type="text" id="reward_new" placeholder="Reward"
                                           value=""/></td>
                                <td class="center">
                                    <select id="server_new" class="input-medium">
                                        <?php
                                            foreach($server_list as $key => $server):
                                                ?>
                                                <option value="<?php echo $key; ?>"><?php echo $server_list[$key]['title']; ?></option>
                                            <?php
                                            endforeach;
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <a class="btn btn-success" href="#" onclick="nganLuong.addPackage();">
                                        <i class="icon-edit icon-white"></i> Add Package
                                    </a>
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
