<?php

    class _plugin_merchant extends controller implements pluginInterface
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
                            $this->vars['config_not_found'] = __('Plugin configuration not found.');
                        }
                    }
                    if($this->vars['plugin_config']['active'] == 0){
                        $this->vars['module_disabled'] = __('This module has been disabled.');
                    } else{
                        $this->load->model('application/plugins/' . $this->pluginaizer->get_plugin_class() . '/models/' . $this->pluginaizer->get_plugin_class());
                        $this->vars['data'] = $this->pluginaizer->Mmerchant->check_merchant($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
                        if($this->vars['data'] != false){
                            if($this->vars['data']['active'] == 0){
                                $this->vars['not_allowed'] = __('Your merchant account has been disabled.');
                            }
                        } else{
                            $this->vars['not_allowed'] = __('This module can be accessed only by merchants.');
                        }
                    }
                } else{
                    $this->vars['config_not_found'] = __('Plugin configuration not found.');
                }
                //set js
                $this->vars['js'] = $this->config->base_url . 'assets/plugins/js/' . $this->pluginaizer->get_plugin_class() . '.js?v3';
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.merchant', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()));
            }
        }

        /**
         *
         * Change currency
         *
         * return mixed
         *
         */
        public function change_currency()
        {
            //check if visitor has user privilleges
            if($this->pluginaizer->session->is_user()){
                //load website helper
                $this->load->helper('website');
                $this->load->model('account');
                $this->load->model('application/plugins/' . $this->pluginaizer->get_plugin_class() . '/models/' . $this->pluginaizer->get_plugin_class());
                $this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
                if($this->vars['plugin_config'] != false && !empty($this->vars['plugin_config'])){
                    if($this->pluginaizer->data()->value('is_multi_server') == 1){
                        if(array_key_exists($this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config'])){
                            $this->vars['plugin_config'] = $this->vars['plugin_config'][$this->pluginaizer->session->userdata(['user' => 'server'])];
                            $this->vars['about'] = $this->pluginaizer->get_about();
                            $this->vars['about']['user_description'] = $this->pluginaizer->data()->value('description');
                        } else{
                            $this->pluginaizer->jsone(['error' => __('Plugin configuration not found.')]);
                        }
                    }
                    if($this->vars['plugin_config']['active'] == 0){
                        echo $this->pluginaizer->jsone(['error' => __('This module has been disabled.')]);
                    } else{
                        $data = $this->pluginaizer->Mmerchant->check_merchant($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
                        if($data != false){
                            if($data['active'] == 0){
                                echo $this->pluginaizer->jsone(['error' => __('Your merchant account has been disabled.')]);
                                return;
                            }
                        } else{
                            echo $this->pluginaizer->jsone(['error' => __('This module can be accessed only by merchants.')]);
                            return;
                        }
                        if(count($_POST) > 0){
                            $this->vars['table_config'] = $this->config->values('table_config', $this->pluginaizer->session->userdata(['user' => 'server']));
                            $account = isset($_POST['account']) ? $_POST['account'] : '';
                            $credits = isset($_POST['credits']) ? (int)$_POST['credits'] : '';
                            list($currency, $wcoin) = explode('/', $this->vars['plugin_config']['wcoin_ratio']);
                            list($currency_required, $wcoin_bonus) = explode('/', $this->vars['plugin_config']['wcoin_bonus_ratio']);
                            list($currency_web_required, $webcurrency_bonus) = explode('/', $this->vars['plugin_config']['reward_bonus_ratio']);
                            $this->vars['total'] = (int)(($credits / $currency) * $wcoin);
                            $this->vars['wcoin_bonus_times'] = floor($credits / $currency_required);
                            $this->vars['wcoin_bonus'] = (int)($this->vars['wcoin_bonus_times'] * $wcoin_bonus);
                            $this->vars['total_bonus'] = ($this->vars['plugin_config']['wcoin_total_bonus'] != 0) ? (int)(($this->vars['plugin_config']['wcoin_total_bonus'] / 100) * $this->vars['total']) : 0;
                            $this->vars['total'] += $this->vars['total_bonus'];
                            $this->vars['total'] += $this->vars['wcoin_bonus'];
                            $this->vars['webcurrency_bonus_times'] = floor($credits / $currency_web_required);
                            $this->vars['webcurrency_bonus'] = (int)($this->vars['webcurrency_bonus_times'] * $webcurrency_bonus);
                            //pre($this->vars['webcurrency_bonus']);die();
                            if($account == ''){
                                echo $this->pluginaizer->jsone(['error' => __('Please enter account id.')]);
                                return;
                            }
                            if($credits == ''){
                                echo $this->pluginaizer->jsone(['error' => __('Please enter amount.')]);
                                return;
                            }
                            if($data['wallet'] < $credits){
                                echo $this->pluginaizer->jsone(['error' => __('You have insufficient funds.')]);
                                return;
                            }
                            $account_data = $this->pluginaizer->Mmerchant->check_account($account, $this->pluginaizer->session->userdata(['user' => 'server']));
                            if($account_data != false){
                                if($this->vars['total'] != 0){
                                    $this->pluginaizer->Mmerchant->add_wcoins($this->vars['total'], $account_data['memb_guid'], $account, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['table_config']['wcoins']);
                                    $this->pluginaizer->Mmerchant->add_account_log('Reward wcoin from merchant', $this->vars['total'], $account, $this->pluginaizer->session->userdata(['user' => 'server']));
                                }
                                if($this->vars['webcurrency_bonus'] != 0){
                                    $this->pluginaizer->website->add_credits($account, $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['webcurrency_bonus'], $this->vars['plugin_config']['reward_type'], false, $account_data['memb_guid']);
                                    $this->pluginaizer->Mmerchant->add_account_log('Reward ' . $this->pluginaizer->website->translate_credits($this->vars['plugin_config']['reward_type'], $this->pluginaizer->session->userdata(['user' => 'server'])) . ' from merchant', $this->vars['webcurrency_bonus'], $account, $this->pluginaizer->session->userdata(['user' => 'server']));
                                }
                                $this->pluginaizer->Mmerchant->deduct_merchant_money($this->pluginaizer->session->userdata(['user' => 'username']), $credits, $this->pluginaizer->session->userdata(['user' => 'server']));
                                $this->pluginaizer->Mmerchant->add_merchant_log($this->pluginaizer->session->userdata(['user' => 'username']), $credits, $this->vars['plugin_config']['currency_used'], $account, $this->pluginaizer->session->userdata(['user' => 'server']));
                                echo $this->pluginaizer->jsone(['success' => __('Wcoins successfully added.')]);
                            } else{
                                echo $this->pluginaizer->jsone(['error' => __('Account id not found.')]);
                            }
                        }
                    }
                } else{
                    $this->pluginaizer->jsone(['error' => __('Plugin configuration not found.')]);
                }
            } else{
                echo $this->pluginaizer->jsone(['error' => __('Please login into website.')]);
            }
        }

        /**
         *
         * Add merchant
         *
         *
         * Return mixed
         */
        public function add_merchant()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //load website helper
                $this->load->helper('website');
                $this->load->model('application/plugins/' . $this->pluginaizer->get_plugin_class() . '/models/' . $this->pluginaizer->get_plugin_class());
                $account = !empty($_POST['account']) ? htmlspecialchars($_POST['account']) : '';
                $name = !empty($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
                $contact = !empty($_POST['contact']) ? htmlspecialchars($_POST['contact']) : '';
                $wallet = (isset($_POST['wallet']) && preg_match('/^\d*$/', $_POST['wallet'])) ? $_POST['wallet'] : 0;
                $server = !empty($_POST['server']) ? htmlspecialchars($_POST['server']) : '';
                if($account == '')
                    echo $this->pluginaizer->jsone(['error' => 'Invalid merchant account']); else{
                    if(!$this->pluginaizer->Mmerchant->check_account($account, $server))
                        echo $this->pluginaizer->jsone(['error' => 'Merchant account not found']); else{
                        if($this->pluginaizer->Mmerchant->check_merchant($account, $server) != false)
                            echo $this->pluginaizer->jsone(['error' => 'Merchant account already exists']); else{
                            if($name == '')
                                echo $this->pluginaizer->jsone(['error' => 'Invalid merchant name']); else{
                                if($server == '')
                                    echo $this->pluginaizer->jsone(['error' => 'Invalid server selected']); else{
                                    if($id = $this->pluginaizer->Mmerchant->add_merchant($account, $name, $contact, $wallet, $server)){
                                        echo $this->pluginaizer->jsone(['success' => 'Merchant successfully added', 'id' => $id, 'server' => $server, 'servers' => $this->pluginaizer->website->server_list()]);
                                    } else{
                                        echo $this->pluginaizer->jsone(['error' => 'Unable to add new merchant']);
                                    }
                                }
                            }
                        }
                    }
                }
            } else{
                $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }

        /**
         *
         * Edit merchant
         *
         *
         * Return mixed
         */
        public function edit_merchant()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //load website helper
                $this->load->helper('website');
                $this->load->model('application/plugins/' . $this->pluginaizer->get_plugin_class() . '/models/' . $this->pluginaizer->get_plugin_class());
                $id = (isset($_POST['id']) && preg_match('/^\d*$/', $_POST['id'])) ? $_POST['id'] : '';
                $account = !empty($_POST['account']) ? htmlspecialchars($_POST['account']) : '';
                $name = !empty($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
                $contact = !empty($_POST['contact']) ? htmlspecialchars($_POST['contact']) : '';
                $wallet = (isset($_POST['wallet']) && preg_match('/^\d*$/', $_POST['wallet'])) ? $_POST['wallet'] : 0;
                $server = !empty($_POST['server']) ? htmlspecialchars($_POST['server']) : '';
                if($id == '')
                    echo $this->pluginaizer->jsone(['error' => 'Invalid merchant id']); else{
                    if($account == '')
                        echo $this->pluginaizer->jsone(['error' => 'Invalid merchant account']); else{
                        if(!$this->pluginaizer->Mmerchant->check_account($account, $server))
                            echo $this->pluginaizer->jsone(['error' => 'Merchant account not found']); else{
                            if($this->pluginaizer->Mmerchant->check_merchant($account, $server, $id) != false)
                                echo $this->pluginaizer->jsone(['error' => 'Merchant account already exists']); else{
                                if($name == '')
                                    echo $this->pluginaizer->jsone(['error' => 'Invalid merchant name']); else{
                                    if($server == '')
                                        echo $this->pluginaizer->jsone(['error' => 'Invalid server selected']); else{
                                        if($this->pluginaizer->Mmerchant->check_merchant_id($id) != false){
                                            $this->pluginaizer->Mmerchant->edit_merchant($id, $account, $name, $contact, $wallet, $server);
                                            echo $this->pluginaizer->jsone(['success' => 'Merchant successfully edited']);
                                        } else{
                                            echo $this->pluginaizer->jsone(['error' => 'Invalid merchant']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else{
                echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }

        /**
         *
         * Delete merchant
         *
         *
         * Return mixed
         */
        public function delete_merchant()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //load website helper
                $this->load->helper('website');
                $this->load->model('application/plugins/' . $this->pluginaizer->get_plugin_class() . '/models/' . $this->pluginaizer->get_plugin_class());
                $id = (isset($_POST['id']) && preg_match('/^\d*$/', $_POST['id'])) ? $_POST['id'] : '';
                if($id == '')
                    echo $this->pluginaizer->jsone(['error' => 'Invalid merchant id']); else{
                    if($this->pluginaizer->Mmerchant->check_merchant_id($id) != false){
                        $this->pluginaizer->Mmerchant->delete_merchant($id);
                        echo $this->pluginaizer->jsone(['success' => 'Merchant successfully removed']);
                    } else{
                        echo $this->pluginaizer->jsone(['error' => 'Invalid merchant']);
                    }
                }
            } else{
                echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }

        /**
         *
         * Enable / Disable merchant
         *
         *
         * Return mixed
         */
        public function change_status()
        {
            //check if visitor has administrator privilleges
            if($this->pluginaizer->session->is_admin()){
                //load website helper
                $this->load->helper('website');
                $this->load->model('application/plugins/' . $this->pluginaizer->get_plugin_class() . '/models/' . $this->pluginaizer->get_plugin_class());
                $id = (isset($_POST['id']) && preg_match('/^\d*$/', $_POST['id'])) ? $_POST['id'] : '';
                $status = (isset($_POST['status']) && preg_match('/^\d*$/', $_POST['status'])) ? $_POST['status'] : '';
                if($id == '')
                    echo $this->pluginaizer->jsone(['error' => 'Invalid merchant id']); else{
                    if($status == '')
                        echo $this->pluginaizer->jsone(['error' => 'Invalid merchant status']); else{
                        if($this->pluginaizer->Mmerchant->check_merchant_id($id) != false){
                            $this->pluginaizer->Mmerchant->change_status($id, $status);
                            echo $this->pluginaizer->jsone(['success' => 'Merchant status changed']);
                        } else{
                            echo $this->pluginaizer->jsone(['error' => 'Invalid merchant']);
                        }
                    }
                }
            } else{
                echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
            }
        }

        /**
         *
         * Generate merchant logs
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
                $this->load->model('application/plugins/' . $this->pluginaizer->get_plugin_class() . '/models/' . $this->pluginaizer->get_plugin_class());
                if(isset($_POST['search_merchant_logs'])){
                    $server = (isset($_POST['server']) && $_POST['server'] != 'All') ? $_POST['server'] : 'All';
                    $acc = isset($_POST['account']) ? $_POST['account'] : '';
                    if($acc == ''){
                        $this->vars['error'] = 'Invalid account';
                    } else{
                        $this->vars['logs'] = $this->pluginaizer->Mmerchant->load_logs(1, 25, $acc, $server);
                        $this->pluginaizer->pagination->initialize(1, 25, $this->pluginaizer->Mmerchant->count_total_logs($acc, $server), $this->config->base_url . 'merchant/%s/' . $acc . '/' . $server . '');
                        $this->vars['pagination'] = $this->pluginaizer->pagination->create_links();
                    }
                } else{
                    $this->vars['logs'] = $this->pluginaizer->Mmerchant->load_logs($page, 25, $acc, $server);
                    $lk = '';
                    if($acc != '')
                        $lk .= '/' . $acc;
                    $lk .= '/' . $server;
                    $this->pluginaizer->pagination->initialize($page, 25, $this->pluginaizer->Mmerchant->count_total_logs($acc, $server), $this->config->base_url . 'merchant/logs/%s' . $lk);
                    $this->vars['pagination'] = $this->pluginaizer->pagination->create_links();
                }
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . 'admin' . DS . 'view.logs', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'admincp/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/logs');
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
                $this->load->model('application/plugins/' . $this->pluginaizer->get_plugin_class() . '/models/' . $this->pluginaizer->get_plugin_class());
                $this->vars['is_multi_server'] = $this->pluginaizer->data()->value('is_multi_server');
                $this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
                $this->vars['merchant_list'] = $this->pluginaizer->Mmerchant->load_merchants();
                //load any js, css files if required
                $this->vars['js'] = $this->config->base_url . 'assets/plugins/js/' . $this->pluginaizer->get_plugin_class() . '.js';
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . 'admin' . DS . 'view.index', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'admincp/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/admin');
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
                if(isset($_POST['server']) && $_POST['server'] != 'all'){
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
                    'description' => 'Work as server merchant.' //description which will see user
                ]);
                //create plugin config template
                $this->pluginaizer->create_config(['active' => 0, 'wcoin_ratio' => "1/3", 'wcoin_bonus_ratio' => "500/100", 'wcoin_total_bonus' => "0", 'reward_type' => 1, 'reward_bonus_ratio' => '100/1', 'currency_used' => 'USD']);
                $this->pluginaizer->add_sql_scheme('merchant_list');
                $this->pluginaizer->add_sql_scheme('merchant_logs');
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
                $this->pluginaizer->delete_config()->remove_sql_scheme('merchant_list')->remove_sql_scheme('merchant_logs')->remove_plugin();
                //check for errors
                if(count($this->pluginaizer->error) > 0){
                    echo $this->pluginaizer->jsone(['error' => $this->pluginaizer->error]);
                }
                echo $this->pluginaizer->jsone(['success' => 'Plugin uninstalled successfully']);
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