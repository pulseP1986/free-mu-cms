<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div class="dmn-content">
	<div class="dmn-page-box">
		<div class="dmn-page-title">
			<h1><?php echo __($about['name']); ?></h1>
		</div>
		<div class="dmn-page-content">
			<div class="row">
				<div class="col-12">   
					<?php 
					if(isset($config_not_found)){
						echo '<div class="alert alert-danger" role="alert">'.$config_not_found.'</div>';
					} 
					else{
						if(isset($module_disabled)){
							echo '<div class="alert alert-danger" role="alert">'.$module_disabled.'</div>';
						} 
						else{
					?>	
					<h2 class="title"><?php echo __($about['user_description']); ?> 
					<span class="float-right">
						<a class="btn btn-primary" href="<?php echo $this->config->base_url;?>achievements"><?php echo __('Character List');?></a>
					</span>
					</h2>
					<div class="mb-5">
						<nav class="nav nav-pills justify-content-center mb-1">
						<?php
							foreach($this->website->server_list() as $key => $servers){
								if($servers['visible'] == 1){
									$selectd = ($server == $key) ? 'active' : '';
						?>
									<a class="nav-item nav-link <?php echo $selectd;?>" href="<?php echo $this->config->base_url . 'achievements/rankings/' . $key; ?>"><?php echo $servers['title']; ?></a>
						<?php
								}
							}
						?>			
						</nav>
						<?php if(!empty($rankings)){ ?>
						<table class="table dmn-rankings-table table-striped">
							<thead>
								<tr>
									<th style="text-align:center;">#</th>
									<th style="text-align:center;"><?php echo __('Name');?></th>
									<th style="text-align:center;"><?php echo __('Class');?></th>
									<th style="text-align:center;"><?php echo __('Points');?></th>
									<th style="text-align:center;"><?php echo __('Achievements Completed');?></th>
								</tr>
							</thead>
							<tbody>
							<?php 
							$i = 1;
							foreach($rankings AS $rank){ 
							?>
								<tr>
									<td style="text-align:center;"><?php echo $i; ?></td>
									<td style="text-align:center;"><a href="<?php echo $this->config->base_url . 'info/character/'. bin2hex($rank['char_data']['Name']) .'/' . $server; ?>"><?php echo $rank['char_data']['Name'];?></a></td>
									<td style="text-align:center;"><?php echo $this->website-> get_char_class($rank['char_data']['Class']);?></td>
									<td style="text-align:center;"><?php echo $rank['ranking_points'];?></td>
									<td style="text-align:center;"><?php echo $rank['achievements_completed'];?> / <?php echo $rank['achievements_total'];?></td>
								</tr>
							<?php 
								$i++;
							} 
							?>
							</tbody>
						</table>	
						<?php } else { ?>
						<div class="w_note"><?php echo __('No players found');?></div>
						<?php } ?>
					</div>
					<?php
						}
					}
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