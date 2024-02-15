<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div id="content">
    <div id="box1">
        <div class="title1">
            <h1><?php echo __($about['name']); ?></h1>
        </div>
        <div id="content_center">
            <div class="box-style1" style="margin-bottom:55px;">
                <h2 class="title"><?php echo __($about['user_description']); ?></h2>
                <div class="entry">
                    <?php
                        echo '<div class="i_note">' . __('Thank you for your purchase. The payment is pending and will be credited shortly.') . ' </div>';
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>
	