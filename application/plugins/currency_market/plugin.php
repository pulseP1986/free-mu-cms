<?php
    in_file();

    class _plugin_currency_market extends controller implements pluginInterface
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
        public function index($page = 1)
        {
            if($this->pluginaizer->data()->value('installed') == false){
                throw new Exception('Plugin has not yet been installed.');
            } else{
                if($this->pluginaizer->data()->value('installed') == 1){
                    if($this->pluginaizer->data()->value('is_public') == 0){
                        $this->user_module($page);
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
        private function user_module($page = 1)
        {
            //check if visitor has user privilleges
            if($this->pluginaizer->session->is_user()){
                //load website helper
                $this->load->helper('website');
                //load paginator
                $this->load->lib('pagination');
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
						if(isset($_GET['filter']) && $_GET['filter'] == 'zen'){
							$_SESSION['zen_filder'] = (int)$_GET['order'];
						}
						if(isset($_GET['filter']) && $_GET['filter'] == 'credits'){
							$_SESSION['credits_filder'] = (int)$_GET['order'];
						}
						if(isset($_POST['runfilter'])){
							if(isset($_POST['filters'])){
								$_SESSION['filters'] = implode(',', $_POST['filters']);
							}
							else{
								if(isset($_SESSION['filters'])){
									unset($_SESSION['filters']);
								}
							}
						}
						
                        $this->load->model('application/plugins/currency_market/models/currency_market');
                        $this->pluginaizer->Mcurrency_market->count_total_zen($this->pluginaizer->session->userdata(['user' => 'server']));
                        $this->pluginaizer->pagination->initialize($page, $this->vars['plugin_config']['currency_per_page'], $this->pluginaizer->Mcurrency_market->total, $this->config->base_url . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/index/%s');
                        $this->vars['items1'] = $this->pluginaizer->Mcurrency_market->load_market_zen($page, $this->vars['plugin_config']['currency_per_page'], $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config']['sale_tax']);
                        $this->vars['pagination'] = $this->pluginaizer->pagination->create_links();
						
						$this->pluginaizer->Mcurrency_market->count_total_credits($this->pluginaizer->session->userdata(['user' => 'server']));
                        $this->vars['items2'] = $this->pluginaizer->Mcurrency_market->load_market_credits($page, $this->vars['plugin_config']['currency_per_page'], $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config']['sale_tax']);
                    }
                } else{
                    $this->vars['config_not_found'] = __('Plugin configuration not found.');
                }
                //set js
                $this->vars['js'] = $this->config->base_url . 'assets/plugins/js/currency_market.js';
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.currency_market', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()));
            }
        }

        public function sell_currency()
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
                        $this->load->model('application/plugins/currency_market/models/currency_market');
                        $this->vars['char_list'] = $this->pluginaizer->Mcurrency_market->load_char_list($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
                    }
                } else{
                    $this->vars['config_not_found'] = __('Plugin configuration not found.');
                }
                //set js
                $this->vars['js'] = $this->config->base_url . 'assets/plugins/js/currency_market.js';
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.sell_currency', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/currency-market');
            }
        }
		
		public function sell_zen(){
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
                            echo $this->pluginaizer->jsone(['error' => __('Plugin configuration not found.')]);
							exit;
                        }
                    }
                    if($this->vars['plugin_config']['active'] == 0){
                        echo $this->pluginaizer->jsone(['error' => __('This module has been disabled.')]);
						exit;
                    } else{
						$this->load->model('application/plugins/currency_market/models/currency_market');
						if(count($_POST) > 0){
							$amount_zen = (isset($_POST['amount_zen']) ? ctype_digit($_POST['amount_zen']) ? $_POST['amount_zen'] : '' : '');
							$mcharacter = trim($_POST['mcharacter']);
							$time = (isset($_POST['time']) ? ctype_digit($_POST['time']) ? $_POST['time'] : '' : '');
							$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
							$price = (isset($_POST['price']) ? ctype_digit($_POST['price']) ? $_POST['price'] : '' : '');
							if($amount_zen == ''){
								echo $this->pluginaizer->jsone(['error' => __('Please enter amount.')]);
								exit;
							}
							else{
								if($price == '' || $price <= 0){
									echo $this->pluginaizer->jsone(['error' => __('Please enter price.')]);
									exit;
								}	
								else{
									if($price < $this->vars['plugin_config']['sale_price_minimum_credits']){
										echo $this->pluginaizer->jsone(['error' => vsprintf(__('Minimum price can be %d'), [$this->vars['plugin_config']['sale_price_minimum_credits']])]);
									}
									else{
										if($price > $this->vars['plugin_config']['sale_price_maximum_credits']){
											echo $this->pluginaizer->jsone(['error' => vsprintf(__('Maximum price can be %d'), [$this->vars['plugin_config']['sale_price_maximum_credits']])]);
										}
										else{
											if($payment_method == '' || $payment_method <= 0){
												echo $this->pluginaizer->jsone(['error' => __('Please select payment method.')]);
												exit;
											}
											else{
												if(strpos($this->vars['plugin_config']['zen_sell_currencies'], $payment_method) === false) {
													echo $this->pluginaizer->jsone(['error' => __('Invalid payment method.')]);
													exit;
												}
												else{
													if($time == '' || !in_array($time, [1, 2, 3, 4, 5, 7, 14])){
														echo $this->pluginaizer->jsone(['error' => __('Please select time.')]);
														exit;
													}
													else{
														 if(!$this->pluginaizer->Mcurrency_market->check_char($mcharacter, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
															echo $this->pluginaizer->jsone(['error' => __('Merchant character not found.')]); 
															exit;
														 }
														 else{
															$status = $this->pluginaizer->website->get_user_credits_balance($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), 3, $this->pluginaizer->session->userdata(['user' => 'id']));
															if($status['credits'] < $amount_zen){
																echo $this->pluginaizer->jsone(['error' => __('Not enough zen in zen wallet.')]); 
																exit;
															}
															else{
																$this->pluginaizer->Mcurrency_market->add_zen_into_market($amount_zen, $mcharacter, $time, $payment_method, $price, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
																$this->pluginaizer->website->charge_credits($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $amount_zen, 3);
																$this->pluginaizer->Mcurrency_market->add_account_log('Added zen into market', -(int)$amount_zen, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
																echo $this->pluginaizer->jsone(['success' => __('Zen added into market.')]); 
																exit;
															}
														}
													}
												}
											}
										}
									}								
								}
							}							
						}
					}
                } else{
                    echo $this->pluginaizer->jsone(['error' => __('Plugin configuration not found.')]);
					exit;
                }
            } else{
               echo $this->pluginaizer->jsone(['error' => 'Please login.']);
			   exit;
            }
		}
		
		public function sell_credits(){
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
                            echo $this->pluginaizer->jsone(['error' => __('Plugin configuration not found.')]);
							exit;
                        }
                    }
                    if($this->vars['plugin_config']['active'] == 0){
                        echo $this->pluginaizer->jsone(['error' => __('This module has been disabled.')]);
						exit;
                    } else{
						$this->load->model('application/plugins/currency_market/models/currency_market');
						if(count($_POST) > 0){
							$amount_credits = (isset($_POST['amount_credits']) ? ctype_digit($_POST['amount_credits']) ? $_POST['amount_credits'] : '' : '');
							$credits_type = isset($_POST['credits_type']) ? $_POST['credits_type'] : '';
							$mcharacter = trim($_POST['mcharacter']);
							$time = (isset($_POST['time']) ? ctype_digit($_POST['time']) ? $_POST['time'] : '' : '');
							$price = (isset($_POST['pricec']) ? ctype_digit($_POST['pricec']) ? $_POST['pricec'] : '' : '');
							if($amount_credits == ''){
								echo $this->pluginaizer->jsone(['error' => __('Please enter amount.')]);
								exit;
							}
							else{
								if($price == '' || $price <= 0){
									echo $this->pluginaizer->jsone(['error' => __('Please enter price.')]);
									exit;
								}	
								else{
									$check1 = ($credits_type == 2 || $credits_type == 4) ? $this->vars['plugin_config']['sale_price_minimum_zen'] : $this->vars['plugin_config']['sale_price_minimum_credits'];
									$check2 = ($credits_type == 2 || $credits_type == 4) ? $this->vars['plugin_config']['sale_price_maximum_zen'] : $this->vars['plugin_config']['sale_price_maximum_credits'];
									if($price < $check1){
										echo $this->pluginaizer->jsone(['error' => vsprintf(__('Minimum price can be %d'), [$check1])]);
									}
									else{
										if($price > $check2){
											echo $this->pluginaizer->jsone(['error' => vsprintf(__('Maximum price can be %d'), [$check2])]);
										}
										else{
											if($credits_type == '' || $credits_type <= 0){
												echo $this->pluginaizer->jsone(['error' => __('Please select credits type.')]);
												exit;
											}
											else{
												if(strpos($this->vars['plugin_config']['credits_sell_currencies'], $credits_type) === false) {
													echo $this->pluginaizer->jsone(['error' => __('Invalid credits type.')]);
													exit;
												}
												else{
													if($time == '' || !in_array($time, [1, 2, 3, 4, 5, 7, 14])){
														echo $this->pluginaizer->jsone(['error' => __('Please select time.')]);
														exit;
													}
													else{
														 if(!$this->pluginaizer->Mcurrency_market->check_char($mcharacter, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']))){
															echo $this->pluginaizer->jsone(['error' => __('Merchant character not found.')]); 
															exit;
														 }
														 else{
															if($credits_type == 1){
																$credits_type = 2;
																$reward_type = 1;
															}	
															elseif($credits_type == 2){
																$credits_type = 3;
																$reward_type = 1;
															}	
															elseif($credits_type == 3){
																$credits_type = 1;
																$reward_type = 2;
															}
															elseif($credits_type == 4){
																$credits_type = 3;	
																$reward_type = 2;
															}
															$status = $this->pluginaizer->website->get_user_credits_balance($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $reward_type, $this->pluginaizer->session->userdata(['user' => 'id']));
															if($status['credits'] < $amount_credits){
																echo $this->pluginaizer->jsone(['error' => sprintf(__('You have insufficient amount of %s'), $this->pluginaizer->website->translate_credits($reward_type, $this->pluginaizer->session->userdata(['user' => 'server'])))]);
																exit;
															}
															else{
																$this->pluginaizer->Mcurrency_market->add_credits_into_market($amount_credits, $reward_type, $mcharacter, $time, $credits_type, $price, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
																$this->pluginaizer->website->charge_credits($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $amount_credits, $reward_type);
																$this->pluginaizer->Mcurrency_market->add_account_log('Added '.$this->pluginaizer->website->translate_credits($credits_type, $this->pluginaizer->session->userdata(['user' => 'server'])).' into market', -(int)$amount_credits, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
																echo $this->pluginaizer->jsone(['success' => __('Credits added into market.')]); 
																exit;
															}
														}
													}
												}
											}
										}
									}								
								}	
							}	
						}
					}
                } else{
                    echo $this->pluginaizer->jsone(['error' => __('Plugin configuration not found.')]);
					exit;
                }
            } else{
               echo $this->pluginaizer->jsone(['error' => 'Please login.']);
			   exit;
            }
		}

        public function buy($id = '')
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
                        $this->load->model('application/plugins/currency_market/models/currency_market');
						$this->pluginaizer->website->db('web')->beginTransaction();
                        if($this->vars['sale_info'] = $this->pluginaizer->Mcurrency_market->check_sale_in_market($id, $this->pluginaizer->session->userdata(['user' => 'server']))){
                            if($this->vars['sale_info']['sold'] == 1)
                                $this->vars['error'] = __('This sale has been sold already.'); 
							else{
                                if($this->vars['sale_info']['removed'] == 1)
                                    $this->vars['error'] = __('This sale has been removed.'); 
								else{
                                    if(strtotime($this->vars['sale_info']['active_till']) < time())
                                        $this->vars['error'] = __('This sale has expire.');
									else{
										if(isset($_POST['buy'])){
											if($this->vars['sale_info']['seller_acc'] == $this->pluginaizer->session->userdata(['user' => 'username']))
												$this->vars['purchase_error'] = __('You can not purchase own sale.'); 
											else{
												$this->status = $this->pluginaizer->website->get_user_credits_balance($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['sale_info']['price_type'], $this->pluginaizer->session->userdata(['user' => 'id']));
												$this->price_with_tax = round($this->vars['sale_info']['price'] + ($this->vars['sale_info']['price'] / 100) * $this->vars['plugin_config']['sale_tax'], 0);
												if($this->status['credits'] < $this->price_with_tax){
													$this->vars['purchase_error'] = sprintf(__('You have insufficient amount of %s'), $this->pluginaizer->website->translate_credits($this->vars['sale_info']['price_type'], $this->pluginaizer->session->userdata(['user' => 'server'])));
												}
												if(!isset($this->vars['purchase_error'])){
													$this->pluginaizer->website->charge_credits($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->price_with_tax, $this->vars['sale_info']['price_type'], $this->pluginaizer->session->userdata(['user' => 'id']));
													$this->pluginaizer->website->add_credits($this->vars['sale_info']['seller_acc'], $this->vars['sale_info']['server'], round($this->vars['sale_info']['price'], 0), $this->vars['sale_info']['price_type'], false, $this->pluginaizer->Mcurrency_market->get_guid($this->vars['sale_info']['seller_acc'], $this->vars['sale_info']['server']));
													$this->pluginaizer->website->add_credits($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), round($this->vars['sale_info']['reward'], 0), $this->vars['sale_info']['reward_type'], false, $this->pluginaizer->session->userdata(['user' => 'id']));				
													$this->pluginaizer->Mcurrency_market->add_account_log('Bought '.$this->pluginaizer->website->translate_credits($this->vars['sale_info']['reward_type'], $this->pluginaizer->session->userdata(['user' => 'server'])).' For ' . $this->pluginaizer->website->translate_credits($this->vars['sale_info']['price_type'], $this->pluginaizer->session->userdata(['user' => 'server'])), -$this->price_with_tax, $this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
													$this->pluginaizer->Mcurrency_market->add_account_log('Sold '.$this->pluginaizer->website->translate_credits($this->vars['sale_info']['reward_type'], $this->pluginaizer->session->userdata(['user' => 'server'])).' For ' . $this->pluginaizer->website->translate_credits($this->vars['sale_info']['price_type'], $this->pluginaizer->session->userdata(['user' => 'server'])), $this->vars['sale_info']['price'], $this->vars['sale_info']['seller_acc'], $this->vars['sale_info']['server']);
													$this->pluginaizer->Mcurrency_market->update_sale_set_purchased($id, $this->pluginaizer->session->userdata(['user' => 'username']));
													$this->pluginaizer->website->db('web')->commit();
													header('Location: ' . $this->config->base_url . 'currency-market/success');
											   }
											}	
										}
                                    }
                                }
                            }
                        } else{
                            $this->vars['error'] = __('Sale not found.');
                        }
                    }
                } else{
                    $this->vars['config_not_found'] = __('Plugin configuration not found.');
                }
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.buy', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/buy/' . $id);
            }
        }

        public function sale_history()
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
                        $this->load->model('application/plugins/currency_market/models/currency_market');
                        $this->vars['items'] = $this->pluginaizer->Mcurrency_market->load_market_history($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']));
                    }
                } else{
                    $this->vars['config_not_found'] = __('Plugin configuration not found.');
                }
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.history', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/sale-history');
            }
        }

        public function remove($id)
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
                        $this->load->model('application/plugins/currency_market/models/currency_market');
						$this->pluginaizer->website->db('web')->beginTransaction();
                        if($this->vars['sale_info'] = $this->pluginaizer->Mcurrency_market->check_sale_in_market($id, $this->pluginaizer->session->userdata(['user' => 'server']))){
                            if($this->vars['sale_info']['sold'] == 1)
                                $this->vars['error'] = __('This item has been sold already.'); 
							else{
                                if($this->vars['sale_info']['removed'] == 1)
                                    $this->vars['error'] = __('This item has been removed.'); 
								else{
                                    if($this->vars['sale_info']['seller_acc'] != $this->pluginaizer->session->userdata(['user' => 'username']))
                                        $this->vars['error'] = __('This sale is not yours.'); 
									else{
                                        if($this->vars['plugin_config']['allow_remove_before_expires'] == 0 && strtotime($this->vars['sale_info']['active_till']) > time())
                                            $this->vars['error'] = __('Your not allowed to remove this item while it is not expired.'); 
										else{
											$this->pluginaizer->Mcurrency_market->update_sale_set_removed($id, $this->pluginaizer->session->userdata(['user' => 'username']));
											$this->pluginaizer->website->add_credits($this->pluginaizer->session->userdata(['user' => 'username']), $this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['sale_info']['reward'], $this->vars['sale_info']['reward_type'], false, $this->pluginaizer->session->userdata(['user' => 'id']));
											$this->pluginaizer->website->db('web')->commit();		
											$this->vars['success'] = __('Sale successfully restored.');
                                        }
                                    }
                                }
                            }
                        } else{
                            $this->vars['error'] = __('Sale not found.');
                        }
                    }
                } else{
                    $this->vars['config_not_found'] = __('Plugin configuration not found.');
                }
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.remove', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/sale-history');
            }
        }

        public function success()
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
                        $this->vars['success'] = __('Purchase has been successfull.');
                    }
                } else{
                    $this->vars['config_not_found'] = __('Plugin configuration not found.');
                }
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.success', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/success');
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
                $this->load->model('application/plugins/currency_market/models/currency_market');
                $this->vars['is_multi_server'] = $this->pluginaizer->data()->value('is_multi_server');
                $this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
                //load any js, css files if required
                $this->vars['js'] = $this->config->base_url . 'assets/plugins/js/currency_market.js';
                //load template
                $this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . 'admin' . DS . 'view.index', $this->vars);
            } else{
                $this->pluginaizer->redirect($this->config->base_url . 'admincp/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/admin');
            }
        }
		
		/**
         *
         * Generate coinbase logs
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
                $this->load->model('application/plugins/currency_market/models/currency_market');
                if(isset($_POST['search_currency_market'])){
                    $server = (isset($_POST['server']) && $_POST['server'] != 'All') ? $_POST['server'] : 'All';
                    $acc = isset($_POST['account']) ? $_POST['account'] : '';
                    if($acc == ''){
                        $this->vars['error'] = 'Invalid account';
                    } else{
                        $this->vars['logs'] = $this->pluginaizer->Mcurrency_market->load_logs(1, 25, $acc, $server);
                        $this->pluginaizer->pagination->initialize(1, 25, $this->pluginaizer->Mcurrency_market->count_total_logs($acc, $server), $this->config->base_url . 'currency-market/logs/%s/' . $acc . '/' . $server . '');
                        $this->vars['pagination'] = $this->pluginaizer->pagination->create_links();
                    }
                } else{
                    $this->vars['logs'] = $this->pluginaizer->Mcurrency_market->load_logs($page, 25, $acc, $server);
                    $lk = '';
                    if($acc != '')
                        $lk .= '/' . $acc;
                    $lk .= '/' . $server;
                    $this->pluginaizer->pagination->initialize($page, 25, $this->pluginaizer->Mcurrency_market->count_total_logs($acc, $server), $this->config->base_url . 'currency-market/logs/%s' . $lk);
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
                if(isset($_POST['server']) && $_POST['server'] != 'all'){
                    foreach($_POST AS $key => $val){
                        if($key != 'server'){
                            if($key == 'credits_sell_currencies' || $key == 'zen_sell_currencies'){
								$this->vars['plugin_config'][$_POST['server']][$key] = implode(',', $val);
							}
							else{
								$this->vars['plugin_config'][$_POST['server']][$key] = $val;
							}
                        }
                    }
                } else{
                    foreach($_POST AS $key => $val){
                        if($key != 'server'){
                           if($key == 'credits_sell_currencies' || $key == 'zen_sell_currencies'){
								$this->vars['plugin_config'][$key] = implode(',', $val);
							}
							else{
								$this->vars['plugin_config'][$key] = $val;
							}
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
                    'sidebar_user_item' => 0, //add link to module in user sidebar
                    'sidebar_public_item' => 1, //add link to module in public sidebar menu, if template supports
                    'account_panel_item' => 1, //add link in user account panel
                    'donation_panel_item' => 0, //add link in donation page
                    'description' => 'Sell & Buy Currency' //description which will see user
                ]);
                //create plugin config template
                $this->pluginaizer->create_config(['active' => 0, 'currency_per_page' => 25, 'sale_tax' => 0, 'sale_price_minimum_zen' => 50000, 'sale_price_minimum_credits' => 500, 'sale_price_maximum_zen' => 50000000, 'sale_price_maximum_credits' => 50000, 'allow_remove_before_expires' => 0, 'zen_sell_currencies' => '1,2', 'credits_sell_currencies' => '2,4']);
                //add sql scheme if there is any into website database
                //all schemes should be located in plugin_folder/sql_schemes
                $this->pluginaizer->add_sql_scheme('currency_market');
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
                $this->pluginaizer->delete_config()->remove_sql_scheme('currency_market')->remove_sql_scheme('currency_market_logs')->remove_plugin();
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