<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/add-server">Add Server</a></li>
        </ul>
    </div>
    <div class="row-fluid">
        <div class="box span9">
            <div class="box-header well" data-original-title>
                <h2>Add New Server</h2>

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
                <form class="form-horizontal" method="post"
                      action="<?php echo $this->config->base_url . ACPURL; ?>/add-server">
                    <fieldset>
                        <legend></legend>
                        <div class="control-group">
                            <label class="control-label" for="key">Server Key <span style="color:red;">*</span></label>

                            <div class="controls">
                                <input type="text" class="input-xlarge" name="key" id="key"
                                       value="<?php if(isset($_POST['key'])): echo $_POST['key']; endif; ?>"/>

                                <p>Should be unique for each server, after adding this key can not be changed
                                    anymore</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="title">Server Title <span
                                        style="color:red;">*</span></label>

                            <div class="controls">
                                <input type="text" class="input-xlarge" name="title" id="title"
                                       value="<?php if(isset($_POST['title'])): echo $_POST['title']; endif; ?>"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="char_db">Character DB <span
                                        style="color:red;">*</span></label>

                            <div class="controls">
                                <select id="char_db" name="char_db">
                                    <option value="">Select</option>
                                    <?php
                                        foreach($databases as $value){
                                            if(isset($_POST['char_db']) && $_POST['char_db'] == $value['name']){
                                                echo '<option value="' . $value['name'] . '" selected="selected">' . $value['name'] . "</option>\n";
                                            } else{
                                                echo '<option value="' . $value['name'] . '">' . $value['name'] . "</option>\n";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="account_db">Account DB <span
                                        style="color:red;">*</span></label>

                            <div class="controls">
                                <select id="account_db" name="account_db">
                                    <option value="">Select</option>
                                    <?php
                                        foreach($databases as $value){
                                            if(isset($_POST['account_db']) && $_POST['account_db'] == $value['name']){
                                                echo '<option value="' . $value['name'] . '" selected="selected">' . $value['name'] . "</option>\n";
                                            } else{
                                                echo '<option value="' . $value['name'] . '">' . $value['name'] . "</option>\n";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="version">Version</label>

                            <div class="controls">
                                <input type="text" class="input-xlarge" name="version" id="version"
                                       value="<?php if(isset($_POST['version'])): echo $_POST['version']; endif; ?>"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="exp">Experience</label>

                            <div class="controls">
                                <input type="text" class="input-xlarge" name="exp" id="exp"
                                       value="<?php if(isset($_POST['exp'])): echo $_POST['exp']; endif; ?>"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="drop">Drop</label>

                            <div class="controls">
                                <input type="text" class="input-xlarge" name="drop" id="drop"
                                       value="<?php if(isset($_POST['drop'])): echo $_POST['drop']; endif; ?>"/>
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