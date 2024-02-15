<?php
    $this->load->view('admincp' . DS . 'view.header');
    $this->load->view('admincp' . DS . 'view.sidebar');
?>
    <div id="content" class="span10">
        <?php $server_list = ($is_multi_server == 0) ? ['all' => ['title' => 'All']] : $this->website->server_list(); ?>
		<div class="row-fluid">
			<div class="span12">
				<ul class="nav nav-pills">
					<li role="presentation" ><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/admin">Server Settings</a></li>
					<li role="presentation" class="dropdown">
						<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Rewards List<span class="caret"></span></a>
						<ul class="dropdown-menu">
							<?php foreach($server_list AS $key => $val): ?>
							<li><a href="<?php echo $this->config->base_url . $this->request->get_controller(); ?>/rewards-list?server=<?php echo $key;?>"><?php echo $val['title'];?></a></li>
							 <?php endforeach;?>
						</ul>
					</li>
					<li role="presentation" class="dropdown active">
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
        <?php
            if(isset($error)){
                echo '<div class="alert alert-error span12">' . $error . '</div>';
            }
            if(isset($success)){
                echo '<div class="alert alert-success span12">' . $success . '</div>';
            }
        ?>
        <div class="row-fluid">
            <div class="box span12">
                <div class="box-header well">
                    <h2><i class="icon-edit"></i> Search logs by account</h2>
                </div>
                <div class="box-content">
                    <form class="form-horizontal" method="POST" action="">
                        <div class="control-group">
                            <label class="control-label" for="appendedInputButton">Account</label>

                            <div class="controls">
                                <div class="input-append">
                                    <input id="appendedInputButton" size="16" name="account" value="" type="text">
                                    <button class="btn" type="submit" name="search" value="1">
                                        Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="box span12">
                <div class="box-header well">
                    <h2>Logs</h2>
                </div>
                <div class="box-content">
                    <?php
                        if(isset($logs) && !empty($logs)):
                            ?>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Reward id</th>
                                    <th>Character</th>
                                    <th>User</th>
                                    <th>Server</th>
                                    <th>Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    foreach($logs as $key => $value){
                                        echo '<tr>
										<td><a href="'.$this->config->base_url.'accumulated-donation-rewards/edit/' . $value['reward_id'] .'/'. $value['server'] .'">' . $value['reward_id'] . '</a></td>
										<td>' . $value['name'] . '</td>
										<td class="center">' . $value['memb___id'] . '</td>
										<td class="center">' . $this->website->get_title_from_server($value['server']) . '</td>
										<td class="center">' . $value['claim_date'] . '</td>
									  </tr>';
                                    }
                                ?>
                                </tbody>
                            </table>
                            <?php
                            if(isset($pagination)):
                                ?>
                                <div style="padding:10px;text-align:center;">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td><?php echo $pagination; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            <?php
                            endif;
                            ?>
                        <?php
                        else:
                            echo '<div class="alert alert-info">No Logs Found</div>';
                        endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php
    $this->load->view('admincp' . DS . 'view.footer');
?>