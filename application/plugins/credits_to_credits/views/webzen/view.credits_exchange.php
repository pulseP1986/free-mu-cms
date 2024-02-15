<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.header');
?>
<div id="content">
    <div id="box1">
        <?php
            if(isset($config_not_found)):
                echo '<div class="box-style1"><div class="entry"><div class="e_note">' . $config_not_found . '</div></div></div>';
            else:
                if(isset($module_disabled)):
                    echo '<div class="box-style1"><div class="entry"><div class="e_note">' . $module_disabled . '</div></div></div>';
                else:
                    ?>
                    <div class="title1">
                        <h1><?php echo __($about['name']); ?></h1>
                    </div>
                    <div id="content_center">
                        <div class="box-style1" style="margin-bottom:55px;">
                            <h2 class="title"><?php echo __($about['user_description']); ?></h2>

                            <div class="entry">
                                <?php
                                    if(isset($config_not_found)):
                                        echo '<div class="e_note">' . $config_not_found . '</div>';
                                    else:
                                        if(isset($module_disabled)):
                                            echo '<div class="e_note">' . $module_disabled . '</div>';
                                        else:
                                        if(isset($js)):
                                            ?>
                                            <script src="<?php echo $js; ?>"></script>
                                        <?php endif;
                                        ?>
                                            <script>
                                                var creditsExchange = new creditsExchange();
                                                creditsExchange.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
                                                $(document).ready(function () {
                                                    $('#credits_exchange_form').on("submit", function (e) {
                                                        e.preventDefault();
                                                        creditsExchange.submit($(this));
                                                    });
                                                });
                                            </script>

                                        <?php
                                            list($cred, $cred2) = explode('/', $plugin_config['ratio']);
                                            $cred2_trans = ($plugin_config['exchange_type'] == 1) ? $this->website->translate_credits(2, $this->session->userdata(['user' => 'server'])) : $this->website->translate_credits(1, $this->session->userdata(['user' => 'server']));
                                        ?>
                                            <div class="i_note"><?php echo __('Exchange info'); ?>:
                                                <?php
                                                    echo __('You can get') . ' ' . $cred2 . ' ' . $cred2_trans . ' for ' . $cred . ' ' . $this->website->translate_credits($plugin_config['exchange_type'], $this->session->userdata(['user' => 'server']));
                                                ?>
                                            </div>
                                            <div class="form">
                                                <form method="post" action="" id="credits_exchange_form">
                                                    <table>
                                                        <tr>
                                                            <td style="width:150px;"><?php echo __('Amount of') . ' ' . $this->website->translate_credits($plugin_config['exchange_type'], $this->session->userdata(['user' => 'server'])); ?>
                                                                :
                                                            </td>
                                                            <td><input type="text" id="game_currency" name="game_currency"
                                                                       value=""
                                                                       class="text"
                                                                       onblur="creditsExchange.calculateCurrency($('#game_currency').val(), '<?php echo $plugin_config['ratio']; ?>');"
                                                                       onkeyup="creditsExchange.calculateCurrency($('#game_currency').val(), '<?php echo $plugin_config['ratio']; ?>');"/>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo __('Will Receive'); ?> <?php echo $cred2_trans; ?>
                                                                :
                                                            </td>
                                                            <td><input type="text" id="cred2"
                                                                       name="cred2" value="" class="text"
                                                                       disabled/></td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>
                                                                <button type="submit" id="exchange_credits"
                                                                        name="exchange_credits" disabled="disabled"
                                                                        class="button-style"><?php echo __('Submit'); ?></button>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </form>
                                            </div>
                                        <?php
                                        endif;
                                    endif;
                                ?>
                            </div>
                        </div>
                    </div>
                <?php
                endif;
            endif;
        ?>
    </div>
</div>
<?php
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.right_sidebar');
    $this->load->view($this->config->config_entry('main|template') . DS . 'view.footer');
?>

