<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/game-server-list-manager">Game server List Manager</a></li>
        </ul>
    </div>
    <div class="row-fluid">
        <div class="box span9">
            <div class="box-header well" data-original-title>
                <h2>Add New Game Server</h2>

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
                <form class="form-horizontal" method="post" action="">
                    <fieldset>
                        <legend></legend>
                        <div class="control-group">
                            <label class="control-label" for="name">GameServer Title <span style="color:red;">*</span></label>
                            <div class="controls">
                                <input type="text" class="input-xlarge" name="name" id="name" value="<?php if(isset($_POST['name'])): echo $_POST['name']; endif; ?>"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="ip">GameServer IP <span style="color:red;">*</span></label>
                            <div class="controls">
                                <input type="text" class="input-xlarge" name="ip" id="ip" value="<?php if(isset($_POST['ip'])): echo $_POST['ip']; endif; ?>"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="port">GameServer Port <span style="color:red;">*</span></label>
                            <div class="controls">
                                <input type="text" class="input-xlarge" name="port" id="port" value="<?php if(isset($_POST['port'])): echo $_POST['port']; endif; ?>"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="gs_list">GameServer Names <span style="color:red;">*</span></label>
                            <div class="controls">
                                <input type="text" style="width: 300px;" class="input-xlarge" data-role="tagsinput" name="gs_list" id="gs_list" value="<?php if(isset($_POST['gs_list'])): echo $_POST['gs_list']; endif; ?>"/>
                                <p>Can be located in ServerInfo.dat or GameServer.ini</p>
                            </div>
                        </div>
						<div class="control-group">
						<label class="control-label" for="bound_to">Bound To <span style="color:red;">*</span></label>
							<div class="controls">
								<select name="bound_to" id="bound_to">
									<?php foreach($server_list as $key => $server): ?>
									<option value="<?php echo $key; ?>" <?php if(isset($_POST['bound_to']) && $_POST['bound_to'] == $key){ ?>selected="selected"<?php } ?>><?php echo $server['title']; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
                        <div class="control-group">
                            <label class="control-label" for="max_online">Max Players</label>
                            <div class="controls">
                                <input type="text" class="input-xlarge" name="max_online" id="max_online" value="<?php if(isset($_POST['max_online'])): echo $_POST['max_online']; endif; ?>"/>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="add_server" class="btn btn-primary">Add</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>