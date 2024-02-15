<!DOCTYPE html>
<!--[if lt IE 8]>
<html class="ie7" lang="en"><![endif]-->
<!--[if IE 8]>
<html lang="en"><![endif]-->
<!--[if gt IE 8]><!-->
<html lang="en"><!--<![endif]-->
<head>
    <meta charset="UTF-8">
    <meta name="author" content="neo6 - Salvis87@inbox.lv"/>
    <meta name="keywords" content="<?php echo $this->meta->request_meta_keywords(); ?>"/>
    <meta name="description" content="<?php echo $this->meta->request_meta_description(); ?>"/>
    <meta property="og:title" content="<?php echo $this->meta->request_meta_title(); ?>"/>
    <meta property="og:image"
          content="<?php echo $this->config->base_url; ?>assets/<?php echo $this->config->config_entry("main|template"); ?>/images/cms_logo.png"/>
    <meta property="og:url" content="<?php echo $this->config->base_url; ?>"/>
    <meta property="og:description" content="<?php echo $this->meta->request_meta_description(); ?>"/>
    <meta property="og:type" content="website">
    <title><?php echo $this->meta->request_meta_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $this->config->base_url; ?>assets/<?php echo $this->config->config_entry("main|template"); ?>/images/favicon.ico"/>
    <?php if(isset($css)): ?>
    <?php endif; ?>
    <?php
        if(isset($css)):
            if(is_array($css)):
                foreach($css AS $style):
                    ?>
                    <link rel="stylesheet" href="<?php echo $style; ?>" type="text/css"/>
                <?php
                endforeach;
            else:
                ?>
                <link rel="stylesheet" href="<?php echo $css; ?>" type="text/css"/>
            <?php
            endif;
        endif;
    ?>
</head>
<body>
<?php
    if(isset($config_not_found)):
        echo '<div style="margin: 0;padding: 0;text-align: center;"><div style="margin: 0 auto;text-align: center;width: 500px;padding-top:200px;"><div class="e_note">' . $config_not_found . '</div></div></div>';
    else:
        if(isset($module_disabled)):
            echo '<div style="margin: 0;padding: 0;text-align: center;"><div style="margin: 0 auto;text-align: center;width: 500px;padding-top:200px;"><div class="e_note">' . $module_disabled . '</div></div></div>';
        else:
            if(isset($prize_list_not_found)):
                echo '<div style="margin: 0;padding: 0;text-align: center;"><div style="margin: 0 auto;text-align: center;width: 500px;padding-top:200px;"><div class="i_note">' . $prize_list_not_found . '</div></div></div>';
            else:
                ?>
                <div id="PageContainer">
                    <div id="PageContainerInner">
                        <div id="prizes_list">
                            <?php
                                foreach($prizes as $prize){ ?>
                                    <div id="trPrize_<?php echo $prize['id']; ?>" class="trPrize">
                                        <div class="tdReels">
                                            <div
                                                    class="reel1 reelIcon <?php echo $prize['image1']['image_name']; ?>"></div>
                                            <div
                                                    class="reel2 reelIcon <?php echo $prize['image2']['image_name']; ?>"></div>
                                            <div
                                                    class="reel3 reelIcon <?php echo $prize['image3']['image_name']; ?>"></div>
                                            <div class="clearer"></div>
                                        </div>
                                        <span class="tdPayout"
                                              data-basePayout="<?php echo $prize['payout_winnings']; ?>"><?php echo (float)$prize['payout_winnings'] * $plugin_config['min_bet']; ?></span>

                                        <div class="clearer"></div>
                                    </div>
                                <?php }
                            ?>
                        </div>
                        <div id="slotMachineContainer">
                            <div id="ReelContainer">
                                <div id="reel1" class="reel"></div>
                                <div id="reel2" class="reel"></div>
                                <div id="reel3" class="reel"></div>
                                <div id="reelOverlay"></div>
                            </div>
                            <div id="loggedOutMessage" style="display: none;">
                                <span class="large">Sorry, you have been logged off.</span><br/>
                                <b>No bids</b> have been deducted from this spin, because you're not logged in anymore.
                                Please
                                <a href="/login">login</a> and try again.
                            </div>
                            <div id="failedRequestMessage" style="display: none;">
                                <span class="large">Sorry, we're unable to display your spin because your connection to our server was lost. </span><br/>
                                Rest assured that your spin was not wasted. Please check your connection and
                                <a href="#"
                                   onclick="window.location.reload();">refresh</a> to try again.
                            </div>
                            <div id="canNotChangeBet" style="display: none;">
                                <span class="large">Sorry you can not change bet on free spins.</span>
                            </div>
                            <div id="betContainer">
                                <span id="lastWin"></span> <span id="credits"><?php echo $credits; ?></span>
                                <span id="bet"><?php echo $minBet; ?></span>
                                <span id="dayWinnings"><?php echo $dayWinnings; ?></span>
                                <span id="lifetimeWinnings"><?php echo $lifetimeWinnings; ?></span>

                                <div id="betSpinUp"></div>
                                <div id="betSpinDown"></div>
                            </div>

                            <div id="spinButton"></div>
                        </div>
                        <?php if($freeSpins > 0): ?>
                            <div id="freeSpins">
                                <div class="i_note">You have <span style="color:red;"
                                                                   id="user_free_spins"><?php echo $freeSpins; ?></span>
                                    free spins.
                                </div>
                            </div>
                        <?php endif; ?>
                        <div id="soundOffButton"></div>
                        <script type="text/javascript">
                            var minBet = <?php echo $minBet; ?>;
                            var maxBet = <?php echo $maxBet; ?>;
                            var windowID = <?php echo $windowID; ?>;
                        </script>
                        <script
                                src="<?php echo $this->config->base_url; ?>assets/<?php echo $this->config->config_entry("main|template"); ?>/js/jquery-1.8.3.min.js"></script>
                        <script
                                src="<?php echo $this->config->base_url; ?>assets/<?php echo $this->config->config_entry("main|template"); ?>/js/jquery-ui.min.js"></script>
                        <?php
                            if(isset($js)):
                                if(is_array($js)):
                                foreach($js AS $script):
                                    ?>
                                    <script src="<?php echo $script; ?>"></script>
                                <?php
                                    endforeach;
                                    else:
                                ?>
                                    <script src="<?php echo $js; ?>"></script>
                                <?php
                                endif;
                            endif;
                        ?>
                        <script type="text/javascript">
                            slotMachine.setUrl('<?php echo $this->config->base_url . $this->request->get_controller();?>');
                            slotMachine.setBaseUrl('<?php echo $this->config->base_url;?>');
                            slotMachine.init();
                        </script>
                    </div>
                </div>
            <?php
            endif;
        endif;
    endif;
?>
</body>
</html>
