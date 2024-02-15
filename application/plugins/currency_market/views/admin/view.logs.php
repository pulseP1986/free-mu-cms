<?php
    $this->load->view('admincp' . DS . 'view.header');
    $this->load->view('admincp' . DS . 'view.sidebar');
?>
    <div id="content" class="span10">
        <div>
            <ul class="breadcrumb">
                <li><a href="<?php echo $this->config->base_url; ?>currency-market/admin">Currency Market Settings</a> 
				<span class="divider">/</span></li>
				<li><a href="<?php echo $this->config->base_url; ?>currency-market/logs">Currency Market Logs</a>
                </li>
            </ul>
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
                            <label class="control-label" for="server">Server </label>

                            <div class="controls">
                                <select id="server" name="server">
                                    <option value="All">All Servers</option>
                                    <?php
                                        foreach($this->website->server_list() as $key => $value){
                                            echo '<option value="' . $key . '">' . $value['title'] . "</option>\n";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="appendedInputButton">Account</label>

                            <div class="controls">
                                <div class="input-append">
                                    <input id="appendedInputButton" size="16" name="account" value="" type="text">
                                    <button class="btn" type="submit" name="search_currency_market" value="1">
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
                                    <th>Price</th>
                                    <th>Reward</th>
                                    <th>Seller</th>
                                    <th>Buyer</th>
									<th>Server</th>
                                    <th>Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    foreach($logs as $key => $value){
                                        echo '<tr>
										<td>' . $value['price'] . ' - ' . $value['price_type'] . '</td>
										<td>' . $value['reward'] . ' - ' . $value['reward_type'] . '</td>
										<td class="center">' . $value['seller'] . '</td>
										<td class="center">' . $value['buyer'] . '</td>
										<td class="center">' . $this->website->get_title_from_server($value['server']) . '</td>
										<td class="center">' . $value['purchase_date'] . '</td>
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