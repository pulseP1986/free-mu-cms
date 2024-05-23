<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/manage-plugins">Plugin Manager</a></li>
        </ul>
    </div>
    <div class="row-fluid">
        <div class="box span12">
            <div class="box-header well">
                <h2><i class="icon-edit"></i> Current plugins</h2>
            </div>
            <div class="box-content">
                <table class="table table-striped table-bordered bootstrap-datatable datatable" id="current_plugin_sortable">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody id="current_plugin_sortable_content">
                        <?php if($plugin_config != false){ ?>
                            <tr class="not-sort">
                                <th colspan="3">Public plugins</th>
                            </tr>
                            <?php
                                $has_public  = false;
                                foreach($plugin_config AS $name => $plugin){
                                    if(!isset($plugin['rankings_panel_item'])){
                                        $plugin['rankings_panel_item'] = 0;
                                    }
                                    if($plugin['is_public'] == 1 && $plugin['rankings_panel_item'] == 0){
                                        $has_public  = true;
                                        if(mb_substr($plugin['admin_module_url'], 0, 4) !== "http"){
                                            $plugin['admin_module_url'] = $this->config->base_url . $plugin['admin_module_url'];
                                        }
                            ?>
                                <tr id="<?php echo $name; ?>">
                                    <td style="width:500px;"><a href="<?php echo $plugin['admin_module_url']; ?>"><?php echo $plugin['about']['name']; ?></a></td>
                                    <td style="width:100px;" class="center" id="status_icon">
                                        <?php if($plugin['installed'] == 1){ ?>
                                        <span class="label label-success">Active</span>
                                        <?php } else { ?>
                                        <span class="label label-important">Inactive</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($plugin['installed'] == 1){ ?>
                                        <a id="status_button" class="btn btn-warning not-sort" href="#" onclick="App.disablePlugin('<?php echo $name; ?>');"><i class="icon-edit icon-white"></i>Disable</a>
                                        <?php } else { ?>
                                        <a id="status_button" class="btn btn-success not-sort" href="#" onclick="App.enablePlugin('<?php echo $name; ?>');"><i class="icon-edit icon-white"></i>Enable</a>
                                        <?php } ?>
                                        <a id="uninstall_button" class="btn btn-danger not-sort" href="#" onclick="App.uninstallPlugin('<?php echo $name; ?>');"><i class="icon-remove icon-white"></i>Remove</a>
                                        <a id="about_button" class="btn btn-inverse not-sort" href="#" onclick="App.aboutPlugin('<?php echo $name; ?>');"><i class="icon-leaf icon-white"></i> About</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            <?php } ?>
                            <?php if($has_public == false){ ?>
                            <tr class="not-sort">
                                <td colspan="3">No Plugins</td>
                            </tr>
                            <?php } ?>
                            <tr class="not-sort">
                                <th colspan="3">Account plugins</th>
                            </tr>
                            <?php
                                $has_account  = false;
                                foreach($plugin_config AS $name => $plugin){
                                    if(!isset($plugin['rankings_panel_item'])){
                                        $plugin['rankings_panel_item'] = 0;
                                    }
                                    if($plugin['is_public'] == 0 && $plugin['donation_panel_item'] == 0 && $plugin['rankings_panel_item'] == 0){
                                        $has_account = true;
                                        if(mb_substr($plugin['admin_module_url'], 0, 4) !== "http"){
                                            $plugin['admin_module_url'] = $this->config->base_url . $plugin['admin_module_url'];
                                        }
                            ?>
                                <tr id="<?php echo $name; ?>">
                                    <td style="width:500px;"><a href="<?php echo $plugin['admin_module_url']; ?>"><?php echo $plugin['about']['name']; ?></a></td>
                                    <td style="width:100px;" class="center" id="status_icon">
                                        <?php if($plugin['installed'] == 1){ ?>
                                        <span class="label label-success">Active</span>
                                        <?php } else { ?>
                                        <span class="label label-important">Inactive</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($plugin['installed'] == 1){ ?>
                                        <a id="status_button" class="btn btn-warning not-sort" href="#" onclick="App.disablePlugin('<?php echo $name; ?>');"><i class="icon-edit icon-white"></i>Disable</a>
                                        <?php } else { ?>
                                        <a id="status_button" class="btn btn-success not-sort" href="#" onclick="App.enablePlugin('<?php echo $name; ?>');"><i class="icon-edit icon-white"></i>Enable</a>
                                        <?php } ?>
                                        <a id="uninstall_button" class="btn btn-danger not-sort" href="#" onclick="App.uninstallPlugin('<?php echo $name; ?>');"><i class="icon-remove icon-white"></i>Remove</a>
                                        <a id="about_button" class="btn btn-inverse not-sort" href="#" onclick="App.aboutPlugin('<?php echo $name; ?>');"><i class="icon-leaf icon-white"></i> About</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            <?php } ?>
                            <?php if($has_account == false){ ?>
                            <tr class="not-sort">
                                <td colspan="3">No Plugins</td>
                            </tr>
                            <?php } ?>
                            <tr class="not-sort">
                                <th colspan="3">Donation plugins</th>
                            </tr>
                            <?php
                                $has_donation  = false;
                                foreach($plugin_config AS $name => $plugin){
                                    if(!isset($plugin['rankings_panel_item'])){
                                        $plugin['rankings_panel_item'] = 0;
                                    }
                                    if($plugin['is_public'] == 0 && $plugin['donation_panel_item'] == 1){
                                        $has_donation = true;
                                        if(mb_substr($plugin['admin_module_url'], 0, 4) !== "http"){
                                            $plugin['admin_module_url'] = $this->config->base_url . $plugin['admin_module_url'];
                                        }
                            ?>
                                <tr id="<?php echo $name; ?>">
                                    <td style="width:500px;"><a href="<?php echo $plugin['admin_module_url']; ?>"><?php echo $plugin['about']['name']; ?></a></td>
                                    <td style="width:100px;" class="center" id="status_icon">
                                        <?php if($plugin['installed'] == 1){ ?>
                                        <span class="label label-success">Active</span>
                                        <?php } else { ?>
                                        <span class="label label-important">Inactive</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($plugin['installed'] == 1){ ?>
                                        <a id="status_button" class="btn btn-warning not-sort" href="#" onclick="App.disablePlugin('<?php echo $name; ?>');"><i class="icon-edit icon-white"></i>Disable</a>
                                        <?php } else { ?>
                                        <a id="status_button" class="btn btn-success not-sort" href="#" onclick="App.enablePlugin('<?php echo $name; ?>');"><i class="icon-edit icon-white"></i>Enable</a>
                                        <?php } ?>
                                        <a id="uninstall_button" class="btn btn-danger not-sort" href="#" onclick="App.uninstallPlugin('<?php echo $name; ?>');"><i class="icon-remove icon-white"></i>Remove</a>
                                        <a id="about_button" class="btn btn-inverse not-sort" href="#" onclick="App.aboutPlugin('<?php echo $name; ?>');"><i class="icon-leaf icon-white"></i> About</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            <?php } ?>
                            <?php if($has_donation == false){ ?>
                            <tr class="not-sort">
                                <td colspan="3">No Plugins</td>
                            </tr>
                            <?php } ?>
                            <tr class="not-sort">
                                <th colspan="3">Rankings plugins</th>
                            </tr>
                            <?php
                                $has_rankings  = false;
                                foreach($plugin_config AS $name => $plugin){
                                    if(!isset($plugin['rankings_panel_item'])){
                                        $plugin['rankings_panel_item'] = 0;
                                    }
                                    if($plugin['rankings_panel_item'] == 1){
                                        $has_rankings  = true;
                                        if(mb_substr($plugin['admin_module_url'], 0, 4) !== "http"){
                                            $plugin['admin_module_url'] = $this->config->base_url . $plugin['admin_module_url'];
                                        }
                            ?>
                                <tr id="<?php echo $name; ?>">
                                    <td style="width:500px;"><a href="<?php echo $plugin['admin_module_url']; ?>"><?php echo $plugin['about']['name']; ?></a></td>
                                    <td style="width:100px;" class="center" id="status_icon">
                                        <?php if($plugin['installed'] == 1){ ?>
                                        <span class="label label-success">Active</span>
                                        <?php } else { ?>
                                        <span class="label label-important">Inactive</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($plugin['installed'] == 1){ ?>
                                        <a id="status_button" class="btn btn-warning not-sort" href="#" onclick="App.disablePlugin('<?php echo $name; ?>');"><i class="icon-edit icon-white"></i>Disable</a>
                                        <?php } else { ?>
                                        <a id="status_button" class="btn btn-success not-sort" href="#" onclick="App.enablePlugin('<?php echo $name; ?>');"><i class="icon-edit icon-white"></i>Enable</a>
                                        <?php } ?>
                                        <a id="uninstall_button" class="btn btn-danger not-sort" href="#" onclick="App.uninstallPlugin('<?php echo $name; ?>');"><i class="icon-remove icon-white"></i>Remove</a>
                                        <a id="about_button" class="btn btn-inverse not-sort" href="#" onclick="App.aboutPlugin('<?php echo $name; ?>');"><i class="icon-leaf icon-white"></i> About</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            <?php } ?>
                            <?php if($has_rankings == false){ ?>
                            <tr class="not-sort">
                                <td colspan="3">No Plugins</td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr id="no_current_plugins">
                                <td colspan="3">
                                    <div class="alert alert-info">No plugins</div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="box span12">
            <div class="box-header well">
                <h2><i class="icon-edit"></i> Available plugins</h2>
            </div>
            <div class="box-content">
                <table class="table table-striped table-bordered bootstrap-datatable datatable" id="available_plugin_sortable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="available_plugin_sortable_content">
                    <?php 
                    if(isset($available_data) && !empty($available_data)){
                        foreach($available_data AS $name => $plugin){
                    ?>
                        <tr id="<?php echo $name; ?>">
                            <td style="width:500px;"><?php echo $plugin['about']['name']; ?></td>
                            <td style="width:100px;" class="center">
                                <span class="label">Not Installed</span>
                            </td>
                            <td>
                                <a id="status_button" class="btn btn-success" href="#" onclick="App.installPlugin('<?php echo $name; ?>');"><i class="icon-download-alt icon-white"></i>Install</a>
                                <a id="about_button" class="btn btn-inverse" href="#" onclick="App.aboutPlugin('<?php echo $name; ?>');"><i class="icon-leaf icon-white"></i> About</a>
                            </td>
                        </tr>
                    <?php }} else { ?>
                        <tr id="no_available_plugins">
                            <td colspan="3">
                                <div class="alert alert-info">No plugins</div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
	