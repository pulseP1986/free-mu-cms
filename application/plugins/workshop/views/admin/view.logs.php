<?php
    $this->load->view('admincp' . DS . 'view.header');
    $this->load->view('admincp' . DS . 'view.sidebar');
?>
    <div id="content" class="span10">
        <div>
            <ul class="breadcrumb">
                <li><a href="<?php echo $this->config->base_url; ?>workshop/admin">Workshop Settings</a> <span
                            class="divider">/</span></li>
                <li><a href="<?php echo $this->config->base_url; ?>workshop/logs">Workshop Logs</a>
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
                                    <button class="btn" type="submit" name="search_workshop_logs" value="1">
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
                    <script>
                        $(document).ready(function () {
                            $('span[id^="log_item_before_"]').each(function () {
                                App.initializeTooltip($(this), true, 'warehouse/item_info');
                            });
                            $('span[id^="log_item_after_"]').each(function () {
                                App.initializeTooltip($(this), true, 'warehouse/item_info');
                            });
                        })
                    </script>
                    <?php
                        if(isset($logs) && !empty($logs)):
                            ?>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Char</th>
                                    <th>Item Before</th>
                                    <th>Item After</th>
                                    <th>Price</th>
                                    <th>Upgrade Date</th>
                                    <th>Server</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $i = 0;
                                    foreach($logs as $key => $value){
                                        $i++;
                                        echo '<tr>
										<td>' . $value['account'] . '</td>
							<td>' . $value['char'] . '</td>
							<td><span id="log_item_before_' . $i . '" data-info="' . $value['hex_before'] . '">' . $value['hex_before'] . '</span></td>
							<td><span id="log_item_after_' . $i . '" data-info="' . $value['hex_after'] . '">' . $value['hex_after'] . '</span></td>
							<td class="center">' . $value['price'] . ' - ' . $this->website->translate_credits($value['payment_method'], $value['server']) . '</td>
							<td class="center">' . $value['upgrade_date'] . '</td>
							<td>' . $this->website->get_title_from_server($value['server']) . '</td>
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