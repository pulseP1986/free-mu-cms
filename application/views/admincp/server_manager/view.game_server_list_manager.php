<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/game-server-list-manager">Game Server List</a></li>
        </ul>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <p class="left">
                <a href="<?php echo $this->config->base_url . ACPURL; ?>/add-game-server" class="btn btn-large btn-primary">
                    Add New GameServer</a>
            </p>

            <div class="clearfix"></div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="box span12">
            <div class="box-header well" data-original-title>
                <h2>List GameServers</h2>

                <div class="box-icon">
                    <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                </div>
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
                <table class="table" id="game_serverlist_sortable">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Ip</th>
						<th>Port</th>
						<th>GameServer List</th>
						<th>Bound To</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="game_serverlist_sortable_content" style="cursor: move;">
                    <?php
                        foreach($game_server_list AS $key => $server){
                            ?>
                            <tr id="<?php echo $key; ?>">
                                <td><?php echo $server['name']; ?></td>
                                <td><?php echo $server['ip']; ?></td>
								<td><?php echo $server['port']; ?></td>
								<td><?php echo $server['gs_list']; ?></td>
								<td><?php echo $this->website->get_value_from_server($server['bound_to'], 'title'); ?></td>
                                <td class="center" id="status_icon_<?php echo $key; ?>">
                                    <?php if($server['visible'] == 1): ?>
                                        <span class="label label-success">Visible</span>
                                    <?php else: ?>
                                        <span class="label label-important">Hidden</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($server['visible'] == 1): ?>
                                        <a class="btn btn-danger" href="#" id="server_status_button_<?php echo $key; ?>" onclick="App.changeGameServerStatus('<?php echo $key; ?>', 0);"> <i class="icon-edit icon-white"></i> Disable </a>
                                    <?php else: ?>
                                        <a class="btn btn-success" href="#" id="server_status_button_<?php echo $key; ?>" onclick="App.changeGameServerStatus('<?php echo $key; ?>', 1);"> <i class="icon-edit icon-white"></i> Enable </a>
                                    <?php endif; ?>
                                    <a class="btn btn-warning" href="#" onclick="App.deleteGameServer('<?php echo $key; ?>');"> <i class="icon-edit icon-white"></i> Remove </a> 
									<a class="btn btn-primary" href="<?php echo $this->config->base_url . ACPURL; ?>/edit-game-server/<?php echo $key; ?>"> <i class="icon-edit icon-white"></i> Edit </a>
                                </td>
                            </tr>
                            <?php
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>