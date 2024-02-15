	<div id="copyright"><?php echo __('Copyright'); ?> &copy; <?php echo date('Y'); ?> <?php echo $this->config->config_entry('main|servername'); ?><br /><?php echo __('Powered By'); ?> <a href="//dmncms.net" target="_blank" style="color: #F1AA6C">DmN MuCMS</a></div>
	<script src="<?php echo $this->config->base_url; ?>assets/<?php echo $this->config->config_entry('main|template'); ?>/js/jed.js"></script>
	<script src="<?php echo $this->config->base_url; ?>assets/<?php echo $this->config->config_entry('main|template'); ?>/js/jquery.tooltip.js"></script>
	<script src="<?php echo $this->config->base_url; ?>assets/<?php echo $this->config->config_entry('main|template'); ?>/js/app.js"></script>	
	<script type="text/javascript">
    var DmNConfig = {
        base_url: '<?php echo $this->config->base_url;?>',
        tmp_dir: '<?php echo $this->config->config_entry('main|template');?>'
    };
	$(document).ready(
		App.ajaxSetup(),
        App.locale = <?php echo $this->translations;?>, 
		App.initLocalization()
    );
	</script>
</body>
</html>