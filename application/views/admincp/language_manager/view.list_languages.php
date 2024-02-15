<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/languages">Languages</a></li>
        </ul>
    </div>
    <div class="row-fluid">
        <div class="box span9">
            <div class="box-header well" data-original-title>
                <h2>Language List</h2>

                <div class="box-icon">
                    <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                </div>
            </div>
            <div class="box-content">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Language</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        if(!empty($languages['localizations'])){
							foreach($languages['localizations'] as $key => $status):
								$default = '';
								if($key == $languages['default_localization']){
									$default = '<span class="label label-info">Default</span>';
								}
								$st = '<span class="label label-success">Active</span>';
								if($status == 0){
									$st = '<span class="label label-important">Inactive</span>';
								}
								echo '<tr id="language_' . $key . '"> <td>' . $this->locales->nativeByCode1($key) . '['.$key.'] '.$default.' '.$st.'</td><td>';
								echo '&nbsp;<a class="btn btn-info" href="' . $this->config->base_url . ACPURL . '/translate/' . $key . '"><i class="icon-edit icon-white"></i> Translate</a>';
								echo '&nbsp;<a class="btn btn-primary" href="' . $this->config->base_url . ACPURL . '/export-language/' . $key . '"><i class="icon-edit icon-white"></i> Export</a>';
								echo '&nbsp;<a class="btn btn-warning" href="#" onclick="App.deleteLanguage(\'' . $key . '\');"><i class="icon-trash icon-white"></i> Delete</a>';
								if($status == 1){
									echo '&nbsp;<a class="btn btn-danger" href="' . $this->config->base_url . ACPURL . '/disable-language/' . $key . '"><i class="icon-edit icon-white"></i> Disable</a>';
								}
								else{
									echo '&nbsp;<a class="btn btn-success" href="' . $this->config->base_url . ACPURL . '/enable-language/' . $key . '"><i class="icon-edit icon-white"></i> Enable</a>';
								}
								if($key != $languages['default_localization']){
									echo '&nbsp;<a class="btn btn-inverse" href="' . $this->config->base_url . ACPURL . '/set-default-language/' . $key . '"><i class="icon-edit icon-white"></i> Set as Default</a>';
								}
								echo '</td></tr>';
							endforeach;
						}
                    ?>
                    </tbody>
                </table>
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