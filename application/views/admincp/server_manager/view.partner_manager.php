<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/account-manager">Account Manager</a></li>
        </ul>
    </div>
    <div class="row-fluid">
        <div class="box span12">
            <div class="box-header well" data-original-title>
                <h2>Edit Partner: <?php echo $account; ?></h2>

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
                    if($account == ''){
                        echo '<div class="alert alert-error">Account not found.</div>';
                    } else{
                        ?>
                        <form class="form-horizontal" method="post" action="">
                            <fieldset>
                                <legend></legend>
								<div class="control-group">
                                    <label class="control-label" for="is_partner">Is Partner</label>
                                    <div class="controls">
                                        <select id="is_partner" name="is_partner">
                                            <option value="0" <?php if($account_data['dmn_partner'] == 0){ ?>selected<?php } ?>>No</option>
                                            <option value="1" <?php if($account_data['dmn_partner'] == 1){ ?>selected<?php } ?>>Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="twitch">Twitch Username </label>
                                    <div class="controls">
                                        <input type="twitch" class="input-xlarge" name="twitch" id="twitch" value="<?php echo $account_data['dmn_twitch_link']; ?>"/>
                                    </div>
                                </div>
								<div class="control-group">
                                    <label class="control-label" for="daily_coins">Daily Coins Limit </label>
                                    <div class="controls">
                                        <input type="daily_coins" class="input-xlarge" name="daily_coins" id="daily_coins" value="<?php echo $account_data['dmn_daily_coins']; ?>"/>
                                    </div>
                                </div>
								<div class="control-group">
									<label class="control-label" for="daily_coins_type">Daily Coins Type</label>
									<div class="controls">
										<select id="daily_coins_type" name="daily_coins_type">
											<option value="1" <?php if($account_data['dmn_daily_coins_type'] == 1){ ?>selected<?php } ?>>Credits 1</option>
											<option value="2" <?php if($account_data['dmn_daily_coins_type'] == 2){ ?>selected<?php } ?>>Credits 2</option>
										</select>
									</div>
								</div>
								<div class="control-group">
                                    <label class="control-label" for="purchase_share">Purchases Share % </label>
                                    <div class="controls">
                                        <input type="purchase_share" class="input-xlarge" name="purchase_share" id="purchase_share" value="<?php echo $account_data['dmn_purchases_share']; ?>"/>
                                    </div>
                                </div>
								<div class="control-group">
                                    <label class="control-label" for="share_url">Share Slug </label>
                                    <div class="controls">
                                        <input type="share_url" class="input-xlarge" name="share_url" id="share_url" value="<?php echo $account_data['dmn_share_url']; ?>"/>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">Edit</button>
                                </div>
                            </fieldset>
                        </form>
                        <?php
                    }
                ?>
            </div>
        </div>
	</div>
	<div class="row-fluid">	
		<div class="box span12">
        <div class="box-header well">
            <h2><i class="icon-list"></i> Information</h2>
        </div>
        <div class="box-content">
            <table class="table">
                <tbody>
                <tr>
                    <td><span class="green">Share Url</span></td>
                    <td><?php if($account_data['dmn_share_url'] != NULL){ echo $this->config->base_url; ?>partner/link/<?php echo $account_data['dmn_share_url']; ?>/<?php echo $server;?><?php  } else { ?>Unknown<?php } ?></td>
                </tr>
                <tr>
                    <td><span class="green">Twitch Url</span></td>
                    <td><?php if($account_data['dmn_twitch_link'] != NULL){ ?>https://www.twitch.tv/<?php echo $account_data['dmn_twitch_link']; ?><?php  } else { ?>Unknown<?php } ?></td>
                </tr>
				<tr>
                    <td><span class="green">Twitch Status</span></td>
                    <td><?php if($account_data['dmn_twitch_link'] != NULL){ ?><?php echo $this->website->checkTwitchStatus($account_data['dmn_twitch_link']);?><?php  } else { ?>Unknown<?php } ?></td>
                </tr>
				<tr>
                    <td><span class="green">Purchases Referred</span></td>
                    <td><?php echo $purchasesReffered;?></td>
                </tr>
				<tr>
                    <td><span class="green">Total Amount</span></td>
                    <td><?php echo $totalAmount; ?> $</td>
                </tr>
				<tr>
                    <td><span class="green">Shares Amount</span></td>
                    <td><?php echo $sharesAmount; ?> $</td>
                </tr>
				<tr>
                    <td><span class="green">Accounts Referred</span></td>
                    <td><?php echo $accountsReffered; ?></td>
                </tr>
                </tbody>
            </table>
        </div>
		</div>
    </div>
	<?php if(!empty($streamLogs)){ ?>
	<div class="row-fluid">
        <div class="box span12">
            <div class="box-header well" data-original-title>
                <h2>Streaming Logs</h2>

                <div class="box-icon">
                    <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                </div>
            </div>
            <div class="box-content">
                <?php
                        echo '<table class="table">
						  <thead>
							  <tr>
								  <th>Date</th>
								  <th>Streaming Time</th>    
							  </tr>
						  </thead>   
						  <tbody>';
                        foreach($streamLogs as $key => $value){
                            echo '<tr>
								<td>' . htmlspecialchars($value['day']) . '</td>
								<td>' . gmdate('H:i:s', $value['time']) . '</td>
							  </tr>';
                        }
                        echo '</tbody></table>';
                ?>
            </div>
        </div>
    </div>
	<?php } ?>
</div>