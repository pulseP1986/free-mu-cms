<?php

class _plugin_gerencianet extends controller implements pluginInterface{
	private $pluginaizer;
	private $vars = [];
	
	/**
	 *
	 * Plugin constructor
	 * Initialize plugin class
	 *
	 */
	 
	public function __construct(){
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
	 
	public function index(){
		if($this->pluginaizer->data()->value('installed') == false){
			throw new Exception('Plugin has not yet been installed.');
		}
		else{
			if($this->pluginaizer->data()->value('installed') == 1){
				if($this->pluginaizer->data()->value('is_public') == 0){
					$this->user_module();
				}
				else{
					$this->public_module();
				}
			}
			else{
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
	// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
	private function user_module(){
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
					}
					else{
						$this->vars['config_not_found'] = __('Plugin configuration not found.');
					}
				}
				
				if($this->vars['plugin_config']['active'] == 0){
					$this->vars['module_disabled'] =  __('This module has been disabled.');
				}
				else{
					$this->load->model('application/plugins/'.$this->pluginaizer->get_plugin_class().'/models/'.$this->pluginaizer->get_plugin_class());;
					$this->vars['packages'] = $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->load_packages(true);
				}
			}
			else{
				$this->vars['config_not_found'] = __('Plugin configuration not found.');
			}
			//set js
			$this->vars['js'] = $this->config->base_url . 'assets/plugins/js/'.$this->pluginaizer->get_plugin_class().'.js?v1';
			//load template
			$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.index', $this->vars);
		}
		else{
			$this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()));
		}
	}
	
	/**
	 *
	 * Generate checkout data and checkout
	 * 
	 * return mixed
	 *
	 */
	
	// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
	public function checkout($id = -1){
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
					}
					else{
						$this->vars['config_not_found'] = __('Plugin configuration not found.');
					}
				}
				if($this->vars['plugin_config']['active'] == 0){
					$this->vars['config_not_found'] = __('This module has been disabled.');
				}
				else{
					$this->load->model('application/plugins/'.$this->pluginaizer->get_plugin_class().'/models/'.$this->pluginaizer->get_plugin_class());
					$id = ($id != -1) ? (int)$id : '';
					if($id == '')
						$this->vars['config_not_found'] = __('Invalid package.');
					else{
						if($this->vars['package'] = $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->check_package($id)){
							require_once(APP_PATH . DS . 'plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'libraries' . DS . 'gatewayGerencia.php');
							try{
								$gateway = new GatewayGerencia($this->vars['plugin_config']);
								
								$webhookUrl = $this->config->base_url.'gerencianet/notify';
								$gateway->registerWebhook($this->vars['plugin_config']['pix_key'], $webhookUrl);

								$body = [
									"calendario" => [
										"expiracao" =>  5 * 3600
									],
									"valor" => [
										"original" => number_format($this->vars['package']['price'], 2, '.', '')
									],
									"chave" => $this->vars['plugin_config']['pix_key']
								];
								$apiResponse = $gateway->createCharge($body); 

								if($apiResponse['txid']){
									if($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->insert_order(
										number_format($this->vars['package']['price'], 2, '.', ''),
										$this->vars['package']['currency'], 
										$this->vars['package']['reward'],
										$apiResponse['txid'],
										$this->pluginaizer->session->userdata(['user' => 'username']),
										$this->pluginaizer->session->userdata(['user' => 'server'])
									)){
										if($this->vars['plugin_config']['sandbox'] == 0){
											header('Location: https://pix.gerencianet.com.br/cob/pagar/' . basename($apiResponse['loc']["location"]));
										}
										else{
											$qrCode = $gateway->generateQRCode($apiResponse["loc"]["id"]);
											$this->vars['qrCodeImage'] = $qrCode['imagemQrcode'];
											$this->vars['qrCode']  = $qrCode['qrcode']; 
											$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.checkout', $this->vars);
										}
									}
									else{
										throw new Exception('[Gerencianet] Error creating order');	
									}
								}
								else{
									throw new Exception('[Gerencianet] Invalid api response');	
								}
								
							}
							catch(\Exception $e){
								$this->vars['error'] = $e->getMessage();
								$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.message', $this->vars);
							}
						} 
						else{
							$this->vars['config_not_found'] = __('Invalid package');
						}
					}
				}
			}
			else{
				$this->vars['config_not_found'] = __('Plugin configuration not found.');
			}

			//load template
			if(isset($this->vars['config_not_found'])){
				$this->vars['error'] = $this->vars['config_not_found'];
				$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.message', $this->vars);
			}
		}
		else{
			$this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()));
		}
	}
	
	// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
	public function notify(){
		$payload = file_get_contents('php://input');
        $webhookCall = json_decode($payload, true);
		
		if (isset($webhookCall['evento']) && isset($webhookCall['data_criacao'])){
			$this->writelog('Webhook test.', 'gerencianet');
            header('HTTP/1.0 200 OK');
            exit;
        }
		
		$this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
		
		if($this->vars['plugin_config'] != false && !empty($this->vars['plugin_config'])){
			$pixPaymentData = isset($webhookCall['pix']) ? $webhookCall['pix'] : [];
			if(empty($pixPaymentData)){
				$this->writelog('Pix payment not received by Webhook.', 'gerencianet');
				exit;
			}
			else{
				$order_id  = $pixPaymentData[0]['txid'];
				$this->load->model('account');	
				$this->load->model('application/plugins/'.$this->pluginaizer->get_plugin_class().'/models/'.$this->pluginaizer->get_plugin_class());	
				if (!$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->check_order_number($order_id)) {
					$this->writelog('Order not found: '. $order_id, 'gerencianet');
					exit;
				}
				else{
					if($this->pluginaizer->data()->value('is_multi_server') == 1){
						if(array_key_exists($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server'], $this->vars['plugin_config'])){
							$this->vars['plugin_config'] = $this->vars['plugin_config'][$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server']];
						}
						else{
							$this->writelog('Plugin configuration not found.', 'gerencianet');
							exit;
						}
					}
					
					$complete_transaction = $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->check_completed_transaction($order_id);
					if($complete_transaction == false){
						$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->insert_transaction_status($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['hash'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['hash'], 'completed');
						$this->pluginaizer->Maccount->add_account_log(
							'Reward ' . $this->pluginaizer->website->translate_credits($this->vars['plugin_config']['reward_type'], 
							$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server']) . ' Gerencianet', 
							$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['credits'], 
							$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['account'], 
							$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server']
						);
						$this->pluginaizer->website->add_credits(
							$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['account'], 
							$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server'], 
							$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['credits'],
							$this->vars['plugin_config']['reward_type'], 
							false,
							$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->get_guid($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['account'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server'])
						);
						
						$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->add_total_recharge($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['account'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['credits']);
				
						if($this->config->values('referral_config', 'reward_on_donation') > 0){
							$ref = $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->findReferral($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['account']);
							if($ref != false){
								$ref_reward = floor(($this->config->values('referral_config', 'reward_on_donation') / 100) * $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['credits']);
								$this->pluginaizer->website->add_credits($ref, $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server'], $ref_reward, $this->vars['plugin_config']['reward_type'], false, $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->get_guid($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['account'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server']));
								$this->pluginaizer->Maccount->add_account_log('Friend donation bonus ' . $this->pluginaizer->website->translate_credits($this->vars['plugin_config']['reward_type'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server']) . '', $ref_reward, $ref, $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server']);
							}
						}
						header('HTTP/1.0 200 OK');
						exit;
					}	
				}
			}
		}
		else{
			$this->writelog('Plugin configuration not found.', 'gerencianet');
			exit;
		}
	}
	
	private function writelog($logentry, $logname){
        $log = '[' . $this->pluginaizer->website->ip() . '] ' . $logentry;
        $logfile = @fopen(APP_PATH . DS . 'logs' . DS . $logname . '_' . date("m-d-y") . '.txt', "a+");
        if ($logfile){
            fwrite($logfile, "[" . date("h:iA") . "] $log\r\n");
            fclose($logfile);
        }
    }
	
	/**
	 *
	 * Load public module data
	 * 
	 * return mixed
	 *
	 */
	
	private function public_module(){
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
	 
	public function admin(){
		//check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//load website helper
			$this->load->helper('website');
			
			$this->load->model('application/plugins/'.$this->pluginaizer->get_plugin_class().'/models/'.$this->pluginaizer->get_plugin_class());;
			
			$this->vars['is_multi_server'] = $this->pluginaizer->data()->value('is_multi_server');
			$this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
			$this->vars['packages'] = $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->load_packages();
			//load any js, css files if required
			$this->vars['js'] = $this->config->base_url . 'assets/plugins/js/'. $this->pluginaizer->get_plugin_class() .'.js';
			//load template
			$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . 'admin' . DS . 'view.index', $this->vars);
		
		}
		else{
			$this->pluginaizer->redirect($this->config->base_url . 'admincp/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/admin');
		}
	}
	
	/**
	 *
	 * Add package
	 * 
	 *
	 * Return mixed
	 */
	
	public function add_package(){
        //check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//load website helper
			$this->load->helper('website');
			
			$this->load->model('application/plugins/'.$this->pluginaizer->get_plugin_class().'/models/'.$this->pluginaizer->get_plugin_class());;
			
            $title = !empty($_POST['title']) ? htmlspecialchars($_POST['title']) : '';
            $price = (isset($_POST['price']) && is_numeric($_POST['price'])) ? $_POST['price'] : '';
            $currency = !empty($_POST['currency']) ? htmlspecialchars($_POST['currency']) : '';
            $reward = (isset($_POST['reward']) && preg_match('/^\d*$/', $_POST['reward'])) ? $_POST['reward'] : '';
            $server = !empty($_POST['server']) ? htmlspecialchars($_POST['server']) : '';
            if($title == '')
                echo $this->pluginaizer->jsone(['error' => 'Invalid package title']);
            else{
                if($price == '')
                    echo $this->pluginaizer->jsone(['error' => 'Invalid package price']);
                else{
                    if($currency == '')
                        echo $this->pluginaizer->jsone(['error' => 'Invalid package currency']);
                    else{
                        if($server == '')
                            echo $this->pluginaizer->jsone(['error' => 'Invalid server selected']);
                        else{
                            if($reward == '')
                               echo  $this->pluginaizer->jsone(['error' => 'Invalid package reward']);
                            else{
                                if($id = $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->add_package($title, $price, $currency, $reward, $server)) {
                                    echo $this->pluginaizer->jsone(['success' => 'Package successfully added', 'id' => $id, 'server' => $server, 'servers' => $this->pluginaizer->website->server_list()]);
                                } 
								else{
                                   echo $this->pluginaizer->jsone(['error' => 'Unable to add new package']);
                                }
                            }
                        }
                    }
                }
            }
        } 
		else{
            $this->pluginaizer->jsone(['error' => 'Please login first!']);
        }
    }
	
	/**
	 *
	 * Edit package
	 * 
	 *
	 * Return mixed
	 */
	
	public function edit_package(){
        //check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//load website helper
			$this->load->helper('website');
			
			$this->load->model('application/plugins/'.$this->pluginaizer->get_plugin_class().'/models/'.$this->pluginaizer->get_plugin_class());;
			
            $id = (isset($_POST['id']) && preg_match('/^\d*$/', $_POST['id'])) ? $_POST['id'] : '';
            $title = !empty($_POST['title']) ? htmlspecialchars($_POST['title']) : '';
            $price = (isset($_POST['price']) && is_numeric($_POST['price'])) ? $_POST['price'] : '';
            $currency = !empty($_POST['currency']) ? htmlspecialchars($_POST['currency']) : '';
            $reward = (isset($_POST['reward']) && preg_match('/^\d*$/', $_POST['reward'])) ? $_POST['reward'] : '';
            $server = !empty($_POST['server']) ? htmlspecialchars($_POST['server']) : '';
            if($id == '')
                echo $this->pluginaizer->jsone(['error' => 'Invalid package id']);
            else{
                if($title == '')
                    echo $this->pluginaizer->jsone(['error' => 'Invalid package title']);
                else{
                    if($price == '')
                        echo $this->pluginaizer->jsone(['error' => 'Invalid package price']);
                    else{
                        if($currency == '')
                            echo $this->pluginaizer->jsone(['error' => 'Invalid package currency']);
                        else{
                            if($server == '')
                                echo $this->pluginaizer->jsone(['error' => 'Invalid server selected']);
                            else{
                                if($reward == '')
                                    echo $this->pluginaizer->jsone(['error' => 'Invalid package reward']);
                                else{
                                    if($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->check_package($id)) {
                                         $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->edit_package($id, $title, $price, $currency, $reward, $server);
                                        echo $this->pluginaizer->jsone(['success' => 'Package successfully edited']);
                                    } 
									else{
                                        echo $this->pluginaizer->jsone(['error' => 'Invalid package']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } 
		else{
            echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
        }
    }
	
	/**
	 *
	 * Delete package
	 * 
	 *
	 * Return mixed
	 */
	
	public function delete_package(){
        //check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//load website helper
			$this->load->helper('website');
			
			$this->load->model('application/plugins/'.$this->pluginaizer->get_plugin_class().'/models/'.$this->pluginaizer->get_plugin_class());;
			
            $id = (isset($_POST['id']) && preg_match('/^\d*$/', $_POST['id'])) ? $_POST['id'] : '';
            if($id == '')
                echo $this->pluginaizer->jsone(['error' => 'Invalid package id']);
            else{
                if($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->check_package($id)) {
                    $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->delete_package($id);
                    echo $this->pluginaizer->jsone(['success' => 'Package successfully removed']);
                } 
				else{
                    echo $this->pluginaizer->jsone(['error' => 'Invalid package']);
                }
            }
        } 
		else{
            echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
        }
    }
	
	/**
	 *
	 * Enable / Disable package
	 * 
	 *
	 * Return mixed
	 */
	
	public function change_status(){
         //check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//load website helper
			$this->load->helper('website');
			
			$this->load->model('application/plugins/'.$this->pluginaizer->get_plugin_class().'/models/'.$this->pluginaizer->get_plugin_class());
			
            $id = (isset($_POST['id']) && preg_match('/^\d*$/', $_POST['id'])) ? $_POST['id'] : '';
            $status = (isset($_POST['status']) && preg_match('/^\d*$/', $_POST['status'])) ? $_POST['status'] : '';
            if($id == '')
                echo $this->pluginaizer->jsone(['error' => 'Invalid package id']);
            else{
                if($status == '')
                    echo $this->pluginaizer->jsone(['error' => 'Invalid package status']);
                else{
                    if($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->check_package($id)) {
                        $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->change_status($id, $status);
                        echo $this->pluginaizer->jsone(['success' => 'Package status changed']);
                    } 
					else{
                        echo $this->pluginaizer->jsone(['error' => 'Invalid package']);
                    }
                }
            }
        } 
		else{
            echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
        }
    }
	
	/**
	 *
	 * Save package order
	 * 
	 *
	 * Return mixed
	 */
	 
	public function save_order(){
         //check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//load website helper
			$this->load->helper('website');			
			$this->load->model('application/plugins/'.$this->pluginaizer->get_plugin_class().'/models/'.$this->pluginaizer->get_plugin_class());;	
            $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->save_order($_POST['order']);
        } 
		else{
             echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
        }
    }
	
	/**
	 *
	 * Generate logs
	 * 
	 * @param int $page
	 * @param string $acc
	 * @param string $server
	 *
	 * Return mixed
	 */
	
	public function logs($page = 1, $acc = '-', $server = 'All'){
		  //check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//load website helper
			$this->load->helper('website');	
			//load paginator
			$this->load->lib('pagination');	
			
			$this->load->model('application/plugins/'.$this->pluginaizer->get_plugin_class().'/models/'.$this->pluginaizer->get_plugin_class());;	
			
			if(isset($_POST['search_transactions'])){
                $server = (isset($_POST['server']) && $_POST['server'] != 'All') ? $_POST['server'] : 'All';
                $acc = isset($_POST['account']) ? $_POST['account'] : '';
                if($acc == '') {
                    $this->vars['error'] = 'Invalid account';
                } 
				else{
                    $this->vars['logs'] = $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->load_transactions(1, 25, $acc, $server);
                    $this->pluginaizer->pagination->initialize(1, 25, $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->count_total_transactions($acc, $server), $this->config->base_url . 'gerencianet/logs/%s/' . $acc . '/' . $server . '');
                    $this->vars['pagination'] =  $this->pluginaizer->pagination->create_links();
                }
            } 
			else{
                $this->vars['logs'] = $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->load_transactions($page, 25, $acc, $server);
                $lk = '';
                if($acc != '')
                    $lk .= '/' . $acc;
                $lk .= '/' . $server;
                $this->pluginaizer->pagination->initialize($page, 25,  $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->count_total_transactions($acc, $server), $this->config->base_url . 'gerencianet/logs/%s' . $lk);
                $this->vars['pagination'] =  $this->pluginaizer->pagination->create_links();
            }
			$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . 'admin' . DS . 'view.logs', $this->vars);
		} 
		else{
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
	
	public function save_settings(){
		//check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			$this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
			if(isset($_POST['server']) && $_POST['server'] != 'all'){
				foreach($_POST AS $key => $val){
					if($key != 'server'){
						$this->vars['plugin_config'][$_POST['server']][$key] = $val;
					}
				}
			}
			else{
				foreach($_POST AS $key => $val){
					if($key != 'server'){
						$this->vars['plugin_config'][$key] = $val;
					}
				}
			}
			if($this->pluginaizer->save_config($this->vars['plugin_config'])){
				echo $this->pluginaizer->jsone(['success' => 'Plugin configuration successfully saved']);
			}
			else{
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
	// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
	public function install(){
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
				'sidebar_public_item' => 0, //add link to module in public sidebar menu, if template supports
				'account_panel_item' => 0, //add link in user account panel
				'donation_panel_item' => 1, //add link in donation page
				'description' => 'Donate with Gerencianet' //description which will see user
			]);
			
			//create plugin config template
			$this->pluginaizer->create_config([
				'active' => 0,
				'client_id' =>  '',
				'client_secret' => '',
				'pix_key' => '',
				'sandbox' => 0,
				'mtls' => 1,
				'reward_type' => 0
			]);
			//add sql scheme if there is any into website database
			//all schemes should be located in plugin_folder/sql_schemes
			$this->pluginaizer->add_sql_scheme('gerencianet_packages');
			$this->pluginaizer->add_sql_scheme('gerencianet_orders');
			$this->pluginaizer->add_sql_scheme('gerencianet_transactions');
			//check for errors
			if(count($this->pluginaizer->error) > 0){
				$data['error'] = $this->pluginaizer->error;
			}
			$data['success'] = 'Plugin installed successfully';
			echo $this->pluginaizer->jsone($data);
		}
		else{
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
	
	public function uninstall(){
		//check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//delete plugin config and remove plugin data
			$this->pluginaizer->delete_config()
			->remove_sql_scheme('gerencianet_packages')
			->remove_sql_scheme('gerencianet_orders')
			->remove_sql_scheme('gerencianet_transactions')
			->remove_plugin();
			//check for errors
			if(count($this->pluginaizer->error) > 0){
				echo $this->pluginaizer->jsone(['error' => $this->pluginaizer->error]);
			}
			echo $this->pluginaizer->jsone(['success' => 'Plugin uninstalled successfully']);
		}
		else{
			echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
		}
	}
	
	public function enable(){
		//check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//enable plugin
			$this->pluginaizer->enable_plugin();
			//check for errors
			if(count($this->pluginaizer->error) > 0){
				echo $this->pluginaizer->jsone(['error' => $this->pluginaizer->error]);
			}
			else{		
				echo $this->pluginaizer->jsone(['success' => 'Plugin successfully enabled.']);
			}
		}
		else{
			echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
		}
	}
	
	public function disable(){
		//check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//disable plugin
			$this->pluginaizer->disable_plugin();
			//check for errors
			if(count($this->pluginaizer->error) > 0){
				echo $this->pluginaizer->jsone(['error' => $this->pluginaizer->error]);
			}
			else{		
				echo $this->pluginaizer->jsone(['success' => 'Plugin successfully disabled.']);
			}
		}
		else{
			echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
		}
	}
	
	public function about(){
		//check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//create plugin info
			$about = $this->pluginaizer->get_about();
			if($about != false){
				$description = '<div class="box-content">
								<dl>
								  <dt>Plugin Name</dt>
								  <dd>'.$about['name'].'</dd>
								  <dt>Version</dt>
								  <dd>'.$about['version'].'</dd>
								  <dt>Description</dt>
								  <dd>'.$about['description'].'</dd>
								  <dt>Developed By</dt>
								  <dd>'.$about['developed_by'].' <a href="'.$about['website'].'" target="_blank">'.$about['website'].'</a></dd>
								</dl>            
							</div>';
			}
			else{
				$description = '<div class="alert alert-info">Unable to find plugin description.</div>';
			}
			echo $this->pluginaizer->jsone(['about' => $description]);
		}
		else{
			echo $this->pluginaizer->jsone(['error' => 'Please login first!']);
		}
	}

}