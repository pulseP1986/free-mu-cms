<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/manage-settings/reset">Reset Settings</a></li>
        </ul>
    </div>
	<div class="row-fluid">
        <div class="box span12">
            <div class="box-header well">
                <h2><i class="icon-edit"></i> Add Reset Items</h2>
            </div>
            <div class="box-content">
                <?php
                    if(isset($error)){
                        if(is_array($error)){
                            foreach($error AS $note){
                                echo '<div class="alert alert-error">' . $note . '</div>';
                            }
                        } else{
                            echo '<div class="alert alert-error">' . $error . '</div>';
                        }
                    }
                    if(isset($success)){
                        echo '<div class="alert alert-success">' . $success . '</div>';
                    }
					
                ?>
			</div>
		</div>
    </div>
</div>			