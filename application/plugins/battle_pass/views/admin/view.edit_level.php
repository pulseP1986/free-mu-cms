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
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Battle Pass Levels<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/pass-levels?server=<?php echo $key;?>" aria-controls="paypalsettings"><?php echo $val['title'];?></a></li>
						 <?php endforeach;?>
                    </ul>
                </li>
				<li role="presentation" class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Logs<span class="caret"></span></a>
                    <ul class="dropdown-menu">
						<?php foreach($server_list AS $key => $val): ?>
                        <li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/logs?server=<?php echo $key;?>" aria-controls="paypalsettings"><?php echo $val['title'];?></a></li>
						 <?php endforeach;?>
                    </ul>
                </li>
			</ul>
            <div class="clearfix"></div>
        </div>
    </div>
	<?php if(isset($js)): ?>
	<script src="<?php echo $js;?>"></script>
	<script type="text/javascript">	
	var battlePass = new battlePass();
	battlePass.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
	$(document).ready(function(){
		$("#battle_pass_sortable").find("tbody#battle_pass_sortable_content").sortable({
			placeholder: 'ui-state-highlight',
			opacity: 0.6,
			cursor: 'move',

			update: function() {
				battlePass.saveOrder('<?php echo $server;?>');
			}

		});
	});
	</script>
	<?php endif;?>
	<div class="row-fluid">
        <div class="box span12">
            <div class="tab-content">          
				<div class="box-header well">
					<h2><i class="icon-edit"></i> Edit Level</h2>
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
					?>
					<form class="form-horizontal" method="POST" action="" id="create_level">
						<div class="control-group">
							<label class="control-label" for="title"> Title</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="title" name="title" value="<?php if(isset($achData['title'])){ echo $achData['title']; } ?>" required />
								<p class="help-block">Level title</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="free_pass_image"> Free Image</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="free_pass_image" name="free_pass_image" value="<?php if(isset($achData['free_pass_image'])){ echo $achData['free_pass_image']; } ?>" placeholder="http://" />
								<p class="help-block">Free level reward image</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="free_pass_wcoins"> Wcoins Bonus</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="free_pass_wcoins" name="free_pass_wcoins" value="<?php if(isset($achData['free_pass_wcoins'])){ echo $achData['free_pass_wcoins']; } ?>" placeholder="0" />
								<p class="help-block">WCoins bonus for free level completion</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="silver_pass_image"> Silver Image</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="silver_pass_image" name="silver_pass_image" value="<?php if(isset($achData['silver_pass_image'])){ echo $achData['silver_pass_image']; } ?>" placeholder="http://" />
								<p class="help-block">Silver level reward image</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="silver_pass_wcoins"> Wcoins Bonus</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="silver_pass_wcoins" name="silver_pass_wcoins" value="<?php if(isset($achData['silver_pass_wcoins'])){ echo $achData['silver_pass_wcoins']; } ?>" placeholder="0" />
								<p class="help-block">WCoins bonus for silver level completion</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="platinum_pass_image"> Platinum Image</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="platinum_pass_image" name="platinum_pass_image" value="<?php if(isset($achData['platinum_pass_image'])){ echo $achData['platinum_pass_image']; } ?>" placeholder="http://" />
								<p class="help-block">Platinum level reward image</p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="platinum_pass_wcoins"> Wcoins Bonus</label>
							<div class="controls">
								<input type="text" class="span3 typeahead" id="platinum_pass_wcoins" name="platinum_pass_wcoins" value="<?php if(isset($achData['platinum_pass_wcoins'])){ echo $achData['platinum_pass_wcoins']; } ?>" placeholder="0" />
								<p class="help-block">WCoins bonus for platinum level completion</p>
							</div>
						</div>
						<div class="form-actions">
							<button type="submit" class="btn btn-primary" name="edit_level" id="edit_level">Submit</button>
						</div>
					</form>
					<?php } ?>
				</div>
            </div>
        </div>
    </div>
	<div class="row-fluid">
        <div class="box span12">
			<div class="box-header well">
				<h2><i class="icon-edit"></i> <?php echo $this->website->get_title_from_server($server); ?> Level List</h2>
			</div>
			<div class="box-content">
				<table class="table"  id="battle_pass_sortable">
					<thead>
						<tr>
							<th>Title</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody id="battle_pass_sortable_content" style="cursor: move;">
            <?php
                if(!empty($battle_pass_levels)){
                    foreach($battle_pass_levels AS $key => $settings){
                        ?>
						<tr id="<?php echo $key; ?>">          
							<td><?php echo $settings['title'];?></td>
							<td>
								<a class="btn btn-info" href="<?php echo $this->config->base_url . 'battle-pass/edit-level/' . $settings['id'];?>/<?php echo $server;?>">
									<i class="icon-edit icon-white"></i>  
									Edit                                            
								</a>
								<a class="btn btn-danger" onclick="ask_url('Are you sure to delete level?', '<?php echo $this->config->base_url . 'battle-pass/delete-level/' . $settings['id'];?>/<?php echo $server;?>')" href="#">
									<i class="icon-trash icon-white"></i> 
									Delete
								</a>
								<a class="btn btn-inverse" href="<?php echo $this->config->base_url . 'battle-pass/requirements/' . $settings['id'];?>/<?php echo $server;?>">
									<i class="icon-edit icon-white"></i> 
									Requirements
								</a>
								<a class="btn btn-success" href="<?php echo $this->config->base_url . 'battle-pass/rewards/' . $settings['id'];?>/<?php echo $server;?>">
									<i class="icon-edit icon-white"></i> 
									Rewards
								</a>
							</td>
						</tr>        
                        <?php
                    }
                } else{
                    echo '<tr><td colspan="2"><div class="alert alert-info">No levels.</div></td></tr>';
                }
            ?>
					</tbody>
                </table>
			</div>
        </div>
    </div>
</div>
<?php
$this->load->view('admincp' . DS . 'view.footer');
?>
