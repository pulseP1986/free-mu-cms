<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url; ?>admincp">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url; ?>admincp/languages">Languages</a></li>
        </ul>
    </div>
    <div class="row-fluid">
        <div class="box span9">
            <div class="box-header well" data-original-title>
                <h2>Import missing translations</h2>

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