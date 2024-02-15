<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/import-languages">Import Languages</a></li>
        </ul>
    </div>
    <div class="row-fluid">
        <div class="box span9">
            <div class="box-header well" data-original-title>
                <h2>Import Translation</h2>

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
                <form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="import_language" value="1"/>
                    <fieldset>
                        <legend></legend>
						<div class="control-group">
                            <label class="control-label" for="short_code">Language</label>
                            <div class="controls">
                                <select id="short_code" name="short_code">
                                    <?php foreach($all_languages AS $key => $lang){ ?>
									<option value="<?php echo $lang[0];?>"><?php echo $lang[5];?></option>
									<?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="language">Select File</label>
                            <div class="controls">
                                <input class="input-file uniform_on" id="language" name="language" type="file" accept=".json,.po,.mo">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Import</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
        <div class="box span3">
            <div class="box-header well">
                <h2><i class="icon-edit"></i> Language Manager</h2>
            </div>
            <div class="box-content">
                <?php
                    $this->load->view('admincp' . DS . 'view.panel_language_manager');
                ?>
            </div>
        </div>
    </div>
</div>