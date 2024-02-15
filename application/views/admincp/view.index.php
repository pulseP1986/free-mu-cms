<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li>Dashboard</li>
        </ul>
    </div>
    <div class="sortable row-fluid">
        <a class="well span3 top-block" href="<?php echo $this->config->base_url . ACPURL; ?>/account-manager">
            <div>Total Accounts</div>
            <div><?php echo $total_acccounts; ?></div>
        </a>
        <a class="well span3 top-block" href="<?php echo $this->config->base_url . ACPURL; ?>/character-manager">
            <div>Total Characters</div>
            <div><?php echo $total_characters; ?></div>
        </a>
        <a class="well span3 top-block" href="#">
            <div>Total Guilds</div>
            <div><?php echo $total_guilds; ?></div>
        </a>
        <a class="well span3 top-block" href="#">
            <div>Online</div>
            <div><?php echo $total_online; ?></div>
        </a>
    </div>
    <?php
        if(!empty($tickets)){
            echo '<div class="alert alert-success span11">You have ' . count($tickets) . ' new support requests. <a href="' . $this->config->base_url . ACPURL . '/support-requests">Click To View</a></div>';
        }
        if(is_numeric($license[8]) && $license[8] <= time()){
            echo '<div class="alert alert-error span11">Your license has expired please renew license <a href="http://dmncms.net/clients/purchases/' . $license[9] . '-' . $license[11] . '/?do=renew" target="_blank">here</a>.</div>';
        } else{
            if(is_numeric($license[8]) && $license[8] <= time() + ((3600 * 24) * 2)){
                echo '<div class="alert alert-error span11">Your license will expire within 2 days please renew license <a href="http://dmncms.net/clients/purchases/' . $license[9] . '-' . $license[11] . '/?do=renew" target="_blank">here</a>.</div>';
            } else{
                if($license[8] == 'License not activated'){
                    echo '<div class="alert alert-error span11">Please activate your license.</div>';
                } else{
                    if(!is_numeric($license[8]) && $license[8] != 'LifeTime'){
                        echo '<div class="alert alert-error span11">Your license has expired please renew license <a href="http://dmncms.net/clients/purchases/" target="_blank">here</a>.</div>';
                    }
                }
            }
        }
        if($cron == false){
            echo '<div class="alert alert-error span11">Your scheduler is not working. please check your <a href="' . $this->config->base_url . ACPURL . '/manage-settings/scheduler" target="_blank">settings</a> and fix it.</div>';
        } else{
            if(time() - $cron > 720 * 2){
                echo '<div class="alert alert-error span11">Your scheduler last run on ' . date(DATETIME_FORMAT, $cron) . '. please check your <a href="' . $this->config->base_url . ACPURL . '/manage-settings/scheduler" target="_blank">settings</a> and fix it.</div>';
            }
        }
		if($available_upgrades == false){
			if($lattest_version > $version){
				echo '<div class="alert alert-success span11">Upgraded version available. <a href="' . $this->config->base_url . 'setup/index.php?action=upgrade/index/' . urlencode($lattest_version) . '">UPGRADE NOW</a></div>';
			}
		}
    ?>
    <div style="clear:both;"></div>
    <?php
        if($available_upgrades != false){
            ?>
            <div class="box-content alerts span11">
                <div class="alert alert-info ">
                    <?php if(isset($available_upgrades['lattest_version'])){ ?>
                        <h4 class="alert-heading" style="font-size:16px;">
                            Version <?php echo key($available_upgrades['lattest_version']); ?> of the DmN MuCMS is now
                            available.</h4>
                        <p>
                        <p><b>Whats New?</b></p>
                        <?php echo $available_upgrades['lattest_version'][key($available_upgrades['lattest_version'])]['change_log']; ?>
                        <?php if($available_upgrades['lattest_version'][key($available_upgrades['lattest_version'])]['is_auto_update'] == 0){ ?>
                            <p>This version requires manual upgrade, <a
                                        href="<?php echo $this->config->base_url; ?>setup/index.php?action=upgrade/index/<?php echo urlencode(key($available_upgrades['lattest_version'])); ?>">UPGRADE
                                    NOW!</a></p>
                            <?php
                        } else{
                            echo '<p><a href="">UPGRADE NOW!</a></p>';
                        }
                        ?>
                        </p>
                        <div style="paddint-top:5px;"></div>
                    <?php } ?>
                    <?php
                        if(isset($available_upgrades['sub_versions'])){
                            asort($available_upgrades['sub_versions']);
                            foreach($available_upgrades['sub_versions'] AS $key => $value){
                                ?>
                                <h4 class="alert-heading" style="font-size:16px;">Version <?php echo $key; ?> of the DmN MuCMS
                                    is now available.</h4>
                                <p>
                                <p><b>Whats New?</b></p>
                                <?php echo $value['change_log']; ?>
                                <?php if($value['is_auto_update'] == 0){ ?>
                                    <p>This version requires manual upgrade, <a
                                                href="<?php echo $this->config->base_url; ?>setup/index.php?action=upgrade/index/<?php echo urlencode($key); ?>">UPGRADE
                                            NOW!</a></p>
                                    <?php
                                } else{
                                    echo '<p><a href="">UPGRADE NOW!</a></p>';
                                }
                                ?>
                                </p>
                                <div style="paddint-top:5px;"></div>
                                <?php
                            }
                        }
                    ?>
					
                </div>
            </div>
            <?php
        }
    ?>
    <div class="box span11">
        <div class="box-header well">
            <h2><i class="icon-list"></i> License Information</h2>

        </div>
        <div class="box-content">
            <table class="table">
                <tbody>
                <tr>
                    <td><span class="green">CMS Version</span></td>
                    <td><?php echo $version; ?> [ <a href="#" id="run_cron_task_1" data-task="CheckUpdates">Check
                            For Updates</a> ]
                    </td>
                </tr>
                <tr>
                    <td><span class="green">License Key</span></td>
                    <td><?php echo substr($license[0], 0, -12) . '************'; ?></td>
                </tr>
                <tr>
                    <td><span class="green">Customer Email</span></td>
                    <td><?php echo $license[1]; ?></td>
                </tr>
                <tr>
                    <td><span class="green">Registered Domain(s)</span></td>
                    <td>
                        <?php
                            if($license[8] == 'LifeTime'){
								if(is_string($license[4])){
									$domains = $license[4];
								}
								else{
									$domains = unserialize($license[4]);
								}
                                if(is_string($domains)){
                                    echo $domains;
                                } else{
                                    echo implode('<br />', array_unique($domains));
                                }
                            } else{
                                echo $license[4];
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><span class="green">Registered Path(s)</span></td>
                    <td>
                        <?php
                            if($license[8] == 'LifeTime'){
								if(is_string($license[5])){
									$paths = $license[5];
								}
								else{
									$paths = unserialize($license[5]);
								}
                                if(is_string($paths)){
                                    echo $paths;
                                } else{
                                    echo implode('<br />', array_unique($paths));
                                }
                            } else{
                                echo $license[5];
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><span class="green">License Expiry Date</span></td>
                    <td><?php echo is_numeric($license[8]) ? date(DATETIME_FORMAT, $license[8]) : $license[8]; ?></td>
                </tr>
                <?php if($license[8] != 'LifeTime'): ?>
                    <tr>
                        <td><span class="green">Renew License</span></td>
                        <td>
                            <?php
                                $prepend = '';
                                if($license[9] != 'undefined'){
                                    $prepend .= $license[9] . '-' . $license[11] . '/?do=renew';
                                }
                            ?>
                            <a href="http://dmncms.net/clients/purchases/<?php echo $prepend; ?>"
                               target="_blank">Click Here</a>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td><span class="green">Release License</span></td>
                    <td><a href="<?php echo $this->config->base_url . ACPURL; ?>/release-license">Click Here</a></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!--/span-->
    <div class="box span6">
        <div class="box-header well">
            <h2><i class="icon-list"></i> Registration Statistics</h2>

        </div>
        <div class="box-content">
            <ul class="dashboard-list">
                <?php
                    if($this->website->is_multiple_accounts() == true):
                        foreach($this->website->server_list() AS $key => $server):
                            ?>
                            <li>
                                <a href="#">
                                    <span class="green"><?php echo $stats[$key]['reg_day']; ?></span> Registrations
                                    Today <?php echo $this->website->get_title_from_server($key); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="red"><?php echo $stats[$key]['reg_week']; ?></span> Registrations This
                                    Week <?php echo $this->website->get_title_from_server($key); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="blue"><?php echo $stats[$key]['reg_month']; ?></span> Registrations This
                                    Month <?php echo $this->website->get_title_from_server($key); ?>
                                </a>
                            </li>
                        <?php
                        endforeach;
                    else:
                        ?>
                        <li>
                            <a href="#"> <span class="green"><?php echo $stats['reg_day']; ?></span> Registrations Today
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <span class="red"><?php echo $stats['reg_week']; ?></span> Registrations This Week </a>
                        </li>
                        <li>
                            <a href="#">
                                <span class="blue"><?php echo $stats['reg_month']; ?></span> Registrations This Month
                            </a>
                        </li>
                        <li>
                            <a href="#"> <span class="green"><?php echo $stats['activ_day']; ?></span> Activations Today
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <span class="red"><?php echo $stats['activ_week']; ?></span> Activations This Week </a>
                        </li>
                        <li>
                            <a href="#">
                                <span class="blue"><?php echo $stats['activ_month']; ?></span> Activations This Month
                            </a>
                        </li>
                    <?php
                    endif;
                ?>
            </ul>
        </div>
    </div>
    <!--/span-->
    <div class="box span5">
        <div class="box-header well">
            <h2><i class="icon-list"></i> Admin Login Logs</h2>

        </div>
        <div class="box-content">
            <table class="table">
                <thead>
                <tr>
                    <th>Username</th>
                    <th>Ip</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    if(count($login_attemts) > 0):
                        foreach($login_attemts as $attemt):
                            ?>
                            <tr>
                                <td><span class="green"><?php echo $attemt['memb___id']; ?></span></td>
                                <td><?php echo $attemt['ip']; ?></td>
                                <td><?php echo date(DATETIME_FORMAT, strtotime($attemt['time'])); ?></td>
                            </tr>
                        <?php
                        endforeach;
                    endif;
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <!--/span-->
</div>