<?php

    class _plugin_slots extends controller implements pluginInterface
    {
        private $pluginaizer;
        private $vars = [];

        /**
         *
         * Plugin constructor
         * Initialize plugin class
         *
         */
        public function __construct()
        {
            //initialize parent constructor
            parent::__construct();
            //initialize pluginaizer
            $this->pluginaizer = $this->load_class('plugin');
            //set plugin class name
            $this->pluginaizer->set_plugin_class(substr(get_class($this), 8));
        }

        /**
         *
         * Main module body
         * All main things related to user side
         *
         *
         * Return mixed
         */
        public function index()
        {
            if($this->pluginaizer->data()->value('installed') == false){
                throw new Exception('Plugin has not yet been installed.');
            } else{
                if($this->pluginaizer->data()->value('installed') == 1){
                    if($this->pluginaizer->data()->value('is_public') == 0){
                        $this->user_module();
                    } else{
                        $this->public_module();
                    }
                } else{
                    throw new Exception('Plugin has been disabled.');
                }
            }
        }

        /**
         *
         * Load user module data
         *
         * return mixed
         *
         */
        private function user_module()
        {
            //check if visitor has user privilleges
            if($this->pluginaizer->session->is_user()){
                //load website helper
                $this->load->helper('website');
                //load plugin config
                $this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
                if($this->vars['plugin_config'] != false && !empty($this->vars['plugin_config'])){
                    if($this->pluginaizer->data()->value('is_multi_server') == 1){
                        if(array_key_exists($this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config'])){
                            $this->vars['plugin_config'] = $this->vars['plugin_config'][$this->pluginaizer->session->userdata(['user' => 'server'])];
                            $this->vars['about'] = $this->pluginaizer->get_about();
                            $this->vars['about']['user_description'] = $this->pluginaizer->data()->value('description');
                        } else{
                            $this->vars['config_not_found'] = __('Plugin configuration not found. <a href="' . $this->config->base_url . '">Return Home</a>');
                        }
                    }
                    if($this->vars['plugin_config']['active'] == 0){
                        $this->vars['module_disabled'] = __('This module has been disabled. <a href="' . $this->config->base_url . '">Return Home</a>');
                    } else{
                        $this->load->model('application/plugins/slots/models/slots');
                        $this->vars['freeSpins'] = $this->pluginaizer->Mslots->get_free_spins($this->pluginaizer->session->userdata(['user' => 'id']), $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config']['free_spins']);
                        if($this->vars['freeSpins'] === false){
                            $this->vars['freeSpins'] = $this->vars['plugin_config']['free_spins'];
                        }
                        $this->vars['windowID'] = rand();
                        $this->vars['minBet'] = $this->vars['plugin_config']['min_bet'];
                        $this->vars['maxBet'] = $this->vars['plugin_config']['max_bet'];
                        $this->vars['dayWinnings'] = (float)0;
                        $this->vars['lifetimeWinnings'] = (float)$this->pluginaizer->Mslots->lifetime_winnings($this->pluginaizer->session->userdata(['user' => 'id']), $this->pluginaizer->session->userdata(['user' => 'server']));
                        $this->vars['prizes_list'] = $this->pluginaizer->Mslots->get_prizes($this->pluginaizer->session->userdata(['user' => 'server']));
                        $status = $this->pluginaizer->website->get_user_credits_balance($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config']['credits_type'], $this->pluginaizer->session->userdata(['user' => 'id']));
                        $this->vars['credits'] = $status['credits'];
                        if(!empty($this->vars['prizes_list'])){
                            $this->vars['prizes'] = [];
                            foreach($this->vars['prizes_list'] AS $prize_list){
                                $this->vars['prizes'][] = ['id' => $prize_list['id'], 'payout_winnings' => $prize_list['payout_winnings'], 'image1' => ['image_name' => 'prize_' . str_replace(['/', '.', '*'], ['slash', 'dot', 'star'], $prize_list['reel1'])], 'image2' => ['image_name' => 'prize_' . str_replace(['/', '.', '*'], ['slash', 'dot', 'star'], $prize_list['reel2'])], 'image3' => ['image_name' => 'prize_' . str_replace(['/', '.', '*'], ['slash', 'dot', 'star'], $prize_list['reel3'])]];
                            }
                        } else{
                            $this->vars['prize_list_not_found'] = __('Slot Machine not configured for this server. <a href="' . $this->config->base_url . '">Return Home</a>');
                        }
                    }
                } else{
                    $this->vars['config_not_found'] = __('Plugin configuration not found. <a href="' . $this->config->base_url . '">Return Home</a>');
                }
                //set css
                $this->vars['css'] = $this->config->base_url . 'assets/plugins/css/slots.css';
                //set js
                $this->vars['js'] = [$this->config->base_url . 'assets/plugins/js/soundmanager2.js', $this->config->base_url . 'assets/plugins/js/slots.js'];
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.slots', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()));
            }
        }

        /**
         *
         * Spin wheel
         *
         * return mixed
         *
         */
        public function spin()
        {
            //check if visitor has user privilleges
            if($this->pluginaizer->session->is_user()){
                //load website helper
                $this->load->helper('website');
                //load plugin config
                $this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
                if($this->vars['plugin_config'] != false && !empty($this->vars['plugin_config'])){
                    if($this->pluginaizer->data()->value('is_multi_server') == 1){
                        if(array_key_exists($this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config'])){
                            $this->vars['plugin_config'] = $this->vars['plugin_config'][$this->pluginaizer->session->userdata(['user' => 'server'])];
                            $this->vars['about'] = $this->pluginaizer->get_about();
                            $this->vars['about']['user_description'] = $this->pluginaizer->data()->value('description');
                        } else{
                            echo $this->pluginaizer->jsone(['success' => false, 'error' => __('Plugin configuration not found. <a href="' . $this->config->base_url . '">Return Home</a>')]);
                        }
                    }
                    if($this->vars['plugin_config']['active'] == 0){
                        echo $this->pluginaizer->jsone(['success' => false, 'error' => __('This module has been disabled. <a href="' . $this->config->base_url . '">Return Home</a>')]);
                    } else{
						$this->load->model('account');	
                        $this->load->model('application/plugins/slots/models/slots');
                        $this->pluginaizer->Mslots->random_mechanism($this->vars['plugin_config']['mechanism']);
                        $this->vars['bet'] = (isset($_POST['bet']) ? $_POST['bet'] : $this->vars['plugin_config']['min_bet']);
                        $this->vars['bet'] = min(max($this->vars['plugin_config']['min_bet'], $this->vars['bet']), $this->vars['plugin_config']['max_bet']);
                        $this->vars['windowID'] = (isset($_POST['windowID']) ? $_POST['windowID'] : "");
                        $this->vars['plugin_config']['credits_name'] = $this->pluginaizer->website->translate_credits($this->vars['plugin_config']['credits_type'], $this->pluginaizer->session->userdata(['user' => 'server']));
                        $this->vars['freeSpins'] = $this->pluginaizer->Mslots->get_free_spins($this->pluginaizer->session->userdata(['user' => 'id']), $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
                        if($this->vars['freeSpins'] > 0){
                            $this->pluginaizer->Mslots->decrease_free_spins($this->pluginaizer->session->userdata(['user' => 'id']), $this->pluginaizer->session->userdata(['user' => 'server']), 1);
                        }
                        $status = $this->pluginaizer->website->get_user_credits_balance($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config']['credits_type'], $this->pluginaizer->session->userdata(['user' => 'id']));
                        if(($status['credits'] < $this->vars['bet']) && $this->vars['freeSpins'] <= 0){
                            echo $this->pluginaizer->jsone(['success' => false, 'error' => sprintf(__('You have insufficient amount of %s'), $this->vars['plugin_config']['credits_name'])]);
                        } else{
                            if($this->vars['freeSpins'] <= 0){
                                $this->pluginaizer->website->charge_credits($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['bet'], $this->vars['plugin_config']['credits_type'], $this->pluginaizer->session->userdata(['user' => 'id']));
                            }
                            $this->pluginaizer->Mslots->increment_slot_machine_spins($this->pluginaizer->session->userdata(['user' => 'id']), $this->pluginaizer->session->userdata(['user' => 'server']));
                            $data = $this->pluginaizer->Mslots->spin($this->pluginaizer->session->userdata(['user' => 'id']), $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['bet'], $this->vars['windowID']);
                            if($data['prize'] != null){
                                $this->pluginaizer->website->add_credits($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $data['prize']['payoutCredits'], $this->vars['plugin_config']['credits_type'], false, $this->pluginaizer->session->userdata(['user' => 'id']));
								$this->pluginaizer->Mslots->increase_winnings($this->pluginaizer->session->userdata(['user' => 'id']), $data['prize']['payoutWinnings'], $this->pluginaizer->session->userdata(['user' => 'server']));
                                $data['lastWin'] = $data['prize']['payoutWinnings'];
								$this->pluginaizer->Maccount->add_account_log(
									'Payout ' . $this->pluginaizer->website->translate_credits($this->vars['plugin_config']['credits_type'], 
									$this->pluginaizer->session->userdata(['user' => 'server'])) . ' Spin & Win', 
									$data['prize']['payoutWinnings'], 
									$this->pluginaizer->session->userdata(['user' => 'username']), 
									$this->pluginaizer->session->userdata(['user' => 'server'])
								);
                            }
                            $data['success'] = true;
                            $status = $this->pluginaizer->website->get_user_credits_balance($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config']['credits_type'], $this->pluginaizer->session->userdata(['user' => 'id']));
                            $data['credits'] = (float)$status['credits'];
                            $data['dayWinnings'] = (float)0;
                            $data['lifetimeWinnings'] = (float)$this->pluginaizer->Mslots->lifetime_winnings($this->pluginaizer->session->userdata(['user' => 'id']), $this->pluginaizer->session->userdata(['user' => 'server']));
                            echo $this->pluginaizer->jsone($data);
                        }
                    }
                } else{
                    echo $this->pluginaizer->jsone(['success' => false, 'error' => __('Plugin configuration not found. <a href="' . $this->config->base_url . '">Return Home</a>')]);
                }
            } else{
                echo $this->pluginaizer->jsone(['error' => __('Please login into website. <a href="' . $this->config->base_url . '">Return Home</a>')]);
            }
        }

        /**
         *
         * Load public module data
         *
         * return mixed
         *
         */
        private function public_module()
        {
            // public module not used in this plugin
        }

        /**
         *
         * Main admin module body
         * All main things related to admincp
         *
         *
         * Return mixed
         */
        public function admin()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //load website helper
                $this->load->helper('website');
                $this->vars['is_multi_server'] = $this->pluginaizer->data()->value('is_multi_server');
                $this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
                //load any js, css files if required
                $this->vars['js'] = $this->config->base_url . 'assets/plugins/js/slots.js';
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . 'admin' . DS . 'view.index', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'admincp/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/admin');
            }
        }

        /**
         *
         * Generate slots logs
         *
         * @param int $page
         * @param string $acc
         * @param string $server
         *
         * Return mixed
         */
        public function logs($page = 1, $acc = '-', $server = 'All')
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //load website helper
                $this->load->helper('website');
                //load paginator
                $this->load->lib('pagination');
                $this->load->model('application/plugins/slots/models/slots');
                if(isset($_POST['search_slots_logs'])){
                    $server = (isset($_POST['server']) && $_POST['server'] != 'All') ? $_POST['server'] : 'All';
                    $acc = isset($_POST['account']) ? $_POST['account'] : '';
                    if($acc == ''){
                        $this->vars['error'] = 'Invalid account';
                    } else{
                        $this->vars['logs'] = $this->pluginaizer->Mslots->load_logs(1, 25, $acc, $server);
                        $this->pluginaizer->pagination->initialize(1, 25, $this->pluginaizer->Mslots->count_total_logs($acc, $server), $this->config->base_url . 'slots/logs/%s/' . $acc . '/' . $server . '');
                        $this->vars['pagination'] = $this->pluginaizer->pagination->create_links();
                    }
                } else{
                    $this->vars['logs'] = $this->pluginaizer->Mslots->load_logs($page, 25, $acc, $server);
                    $lk = '';
                    if($acc != '')
                        $lk .= '/' . $acc;
                    $lk .= '/' . $server;
                    $this->pluginaizer->pagination->initialize($page, 25, $this->pluginaizer->Mslots->count_total_logs($acc, $server), $this->config->base_url . 'slots/logs/%s' . $lk);
                    $this->vars['pagination'] = $this->pluginaizer->pagination->create_links();
                }
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . 'admin' . DS . 'view.logs', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'admincp/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/logs');
            }
        }

        /**
         *
         * Save plugin settings
         *
         *
         * Return mixed
         */
        public function save_settings()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                $this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
				$this->load->model('application/plugins/slots/models/slots');
                if(isset($_POST['server']) && $_POST['server'] != 'all'){
					$prizes = $this->pluginaizer->Mslots->checkPrizes($_POST['server']);
					if($prizes <= 0){
						 $this->pluginaizer->Mslots->insertPrizes($_POST['server']);
					}
					$reels = $this->pluginaizer->Mslots->checkReels($_POST['server']);
					if($reels <= 0){
						 $this->pluginaizer->Mslots->insertReels($_POST['server']);
					}
					
                    foreach($_POST AS $key => $val){
                        if($key != 'server'){
                            $this->vars['plugin_config'][$_POST['server']][$key] = $val;
                        }
                    }
                } else{
                    foreach($_POST AS $key => $val){
                        if($key != 'server'){
                            $this->vars['plugin_config'][$key] = $val;
                        }
                    }
                }
                if($this->pluginaizer->save_config($this->vars['plugin_config'])){
                    echo $this->pluginaizer->jsone(['success' => 'Plugin configuration successfully saved']);
                } else{
                    echo $this->pluginaizer->jsone(['error' => $this->pluginaizer->error]);
                }
            }
        }

        /**
         *
         * Plugin installer
         * Admin module for plugin installation
         * Set plugin data, create plugin config template, create sql schemes
         *
         */
        public function install()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //create plugin info
                $this->pluginaizer->set_about()->add_plugin([
					'installed' => 1, 
					'module_url' => str_replace('_', '-', $this->pluginaizer->get_plugin_class()), //link to module
                    'admin_module_url' => str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/admin', //link to admincp module
                    'is_public' => 0, //if is public module or requires to login
                    'is_multi_server' => 1, //will this plugin have different config for each server, multi server is supported only by not user modules
                    'main_menu_item' => 0, //add link to module in main website menu,
                    'sidebar_user_item' => 1, //add link to module in user sidebar
                    'sidebar_public_item' => 0, //add link to module in public sidebar menu, if template supports
                    'account_panel_item' => 1, //add link in user account panel
                    'donation_panel_item' => 0, //add link in donation page
                    'description' => 'Win credits easy' //description which will see user
                ]);
                //create plugin config template
                $this->pluginaizer->create_config(['active' => 0, 'min_bet' => 1, 'max_bet' => 99, 'mechanism' => 1, 'free_spins' => 0, 'credits_type' => 0]);
                //add sql scheme if there is any into website database
                //all schemes should be located in plugin_folder/sql_schemes
				$this->pluginaizer->add_sql_scheme('DmN_Slots_Prizes');
				$this->pluginaizer->add_sql_scheme('DmN_Slots_Reels');
				$this->pluginaizer->add_sql_scheme('DmN_Slots_Spins');
				$this->pluginaizer->add_sql_scheme('DmN_Slots_Users');
                //check for errors
                if(count($this->pluginaizer->error) > 0){
                    $data['error'] = $this->pluginaizer->error;
                }
                $data['success'] = 'Plugin installed successfully';
                echo $this->pluginaizer->jsone($data);
            } else{
                echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }

        /**
         *
         * Plugin uninstaller
         * Admin module for plugin uninstall
         * Remove plugin data, delete plugin config, delete sql schemes
         *
         */
        public function uninstall()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //delete plugin config and remove plugin data
                $this->pluginaizer->delete_config()
				->remove_sql_scheme('DmN_Slots_Prizes')
				->remove_sql_scheme('DmN_Slots_Reels')
				->remove_sql_scheme('DmN_Slots_Spins')
				->remove_sql_scheme('DmN_Slots_Users')
				->remove_plugin();
                //check for errors
                if(count($this->pluginaizer->error) > 0){
                    $data['error'] = $this->pluginaizer->error;
                }
                $data['success'] = 'Plugin uninstalled successfully';
                echo $this->pluginaizer->jsone($data);
            } else{
                echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }

        public function enable()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //enable plugin
                $this->pluginaizer->enable_plugin();
                //check for errors
                if(count($this->pluginaizer->error) > 0){
                    echo $this->pluginaizer->jsone(['error' => $this->pluginaizer->error]);
                } else{
                    echo $this->pluginaizer->jsone(['success' => 'Plugin successfully enabled.']);
                }
            } else{
                echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }

        public function disable()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //disable plugin
                $this->pluginaizer->disable_plugin();
                //check for errors
                if(count($this->pluginaizer->error) > 0){
                    echo $this->pluginaizer->jsone(['error' => $this->pluginaizer->error]);
                } else{
                    echo $this->pluginaizer->jsone(['success' => 'Plugin successfully disabled.']);
                }
            } else{
                echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }

        public function about()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //create plugin info
                $about = $this->pluginaizer->get_about();
                if($about != false){
                    $description = '<div class="box-content">
								<dl>
								  <dt>Plugin Name</dt>
								  <dd>' . $about['name'] . '</dd>
								  <dt>Version</dt>
								  <dd>' . $about['version'] . '</dd>
								  <dt>Description</dt>
								  <dd>' . $about['description'] . '</dd>
								  <dt>Developed By</dt>
								  <dd>' . $about['developed_by'] . ' <a href="' . $about['website'] . '" target="_blank">' . $about['website'] . '</a></dd>
								</dl>            
							</div>';
                } else{
                    $description = '<div class="alert alert-info">Unable to find plugin description.</div>';
                }
                echo $this->pluginaizer->jsone(['about' => $description]);
            } else{
                echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }

    }