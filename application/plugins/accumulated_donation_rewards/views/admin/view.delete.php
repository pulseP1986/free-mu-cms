<?php
$this->load->view('admincp' . DS . 'view.header');
$this->load->view('admincp' . DS . 'view.sidebar');
?>
<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url; ?>admincp">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url; ?>admincp/manage-plugins">Manage Plugins</a></li>
        </ul>
    </div>
	<?php $server_list = ($is_multi_server == 0) ? ['all' => ['title' => 'All']] : $this->website->server_list(); ?>
	<div class="row-fluid">
        <div class="span12">
            <ul class="nav nav-pills">
                <li role="presentation" ><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/admin">Server Settings</a></li>
				<li role="presentation" class="dropdown active">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Reward List<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/rewards-list?server=<?php echo $key;?>"><?php echo $val['title'];?></a></li>
						 <?php endforeach;?>
                    </ul>
                </li>
				<li role="presentation" class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Logs<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/logs?server=<?php echo $key;?>"><?php echo $val['title'];?></a></li>
						 <?php endforeach;?>
                    </ul>
                </li>
			</ul>
            <div class="clearfix"></div>
        </div>
    </div>
	<div class="row-fluid">
        <div class="box span12">
            <div class="tab-content">          
				<div class="box-header well">
					<h2><i class="icon-edit"></i> Remove Reward</h2>
				</div>
				<div class="box-content">
					<?php
					if(isset($not_found)){
						echo '<div class="alert alert-error">' . $not_found . '</div>';
					}
					else{
						if(isset($error)){
							echo '<div class="alert alert-error">' . $error . '</div>';
						}
						if(isset($success)){
							echo '<div class="alert alert-success">' . $success . '</div>';
						}
					} 
					?>
				</div>
            </div>
        </div>
    </div>
</div>
<?php
$this->load->view('admincp' . DS . 'view.footer');
?>
