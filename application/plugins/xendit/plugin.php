<?php

class _plugin_xendit extends controller implements pluginInterface{
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
					$this->vars['pass'] = false;
					$this->vars['keys'] = false;
					if(isset($_GET['type'])){
						if($_GET['type'] == 'pass'){
							$this->vars['pass'] = true;
						}
						if($_GET['type'] == 'keys'){
							$this->vars['keys'] = true;
						}
					}
					if($this->vars['pass'] != false){
                        $this->load->model('application/plugins/battle_pass/models/battle_pass');
                        $this->vars['time'] = $this->config->values('battle_pass', [$this->session->userdata(['user' => 'server']), 'battle_pass_start_time']);
                        $this->vars['pass'] = $this->Mbattle_pass->checkPassType($this->session->userdata(['user' => 'username']), $this->session->userdata(['user' => 'server']), $this->vars['time']);
						if($this->vars['pass'] == false){
							$this->vars['pass']['pass_type'] = 0;
						}
                        if($this->vars['pass']['pass_type'] == 2){
                            $this->vars['error'] = __('You already have platinum pass.');
                        }
                    }
					else{
						$this->load->model('application/plugins/'.$this->pluginaizer->get_plugin_class().'/models/'.$this->pluginaizer->get_plugin_class());;
						$this->vars['packages_xendit'] = $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->load_packages(true);
					}
				}
			}
			else{
				$this->vars['config_not_found'] = __('Plugin configuration not found.');
			}
			//set js
			$this->vars['js'] = $this->config->base_url . 'assets/plugins/js/xendit.js?v1';
			//load template
			if($this->vars['pass'] != false){
				$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.bpass', $this->vars);
			}
			else{
				if($this->vars['keys'] != false){
					$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.keys', $this->vars);
				}
				else{
					$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.xendit', $this->vars);
				}
			}
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
						$this->pluginaizer->jsone(['error' => __('Plugin configuration not found.')]);
					}
				}
				if($this->vars['plugin_config']['active'] == 0){
					echo $this->pluginaizer->jsone(['error' => __('This module has been disabled.')]);
				}
				else{
					$this->load->model('application/plugins/'.$this->pluginaizer->get_plugin_class().'/models/'.$this->pluginaizer->get_plugin_class());;
					$id = ($id != -1) ? $id : '';
					if($id == '')
						echo $this->pluginaizer->jsone(['error' => __('Invalid Xendit package.')]);
					else{
						if(in_array($id, ['b1', 'b2'])){
							$this->vars['package'] = [
								'reward' => ($id == 'b1') ? 1 : 2,
								'price' => ($id == 'b1') ? BPASS_SILVER_PRICE : BPASS_PLATINUM_PRICE,
								'currency' => BPASS_CURRENCY
							];
							
							$item_number = 'bp-'.md5($this->pluginaizer->session->userdata(['user' => 'username']) . $this->vars['package']['price'] . $this->vars['package']['currency'] . uniqid(microtime(), 1));
						}
						else{
							if(in_array($id, ['k1'])){
								$this->vars['package'] = [
									'reward' => AMOUNT_OF_KEYS,
									'price' => WHEEL_KEYS_PRICE,
									'currency' => WHEEL_KEYS_CURRENCY
								];
								
								$item_number = 'mk-'.md5($this->pluginaizer->session->userdata(['user' => 'username']) . $this->vars['package']['price'] . $this->vars['package']['currency'] . uniqid(microtime(), 1));
							
							}
							else{
								$this->vars['package'] = $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->check_package($id);
								if($this->vars['package'] == false){
									echo $this->pluginaizer->jsone(['error' => 'Invalid package']);
									exit;
								}
								
								$item_number = md5($this->pluginaizer->session->userdata(['user' => 'username']) . $this->vars['package']['price'] . $this->vars['package']['currency'] . uniqid(microtime(), 1));	
							}
						}
						
						require_once(APP_PATH . DS . 'plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'libraries' . DS . 'xendit.php');
						
						Xendit::set_secret_key($this->vars['plugin_config']['secret_key']);
						Xendit::set_public_key($this->vars['plugin_config']['public_key']);
						
						 $request_payload = array(
							'external_id' => $item_number,
							'amount' => number_format($this->vars['package']['price'], 2, '.', ''),
							'payer_email' => $this->pluginaizer->session->userdata(['user' => 'email']),
							'description' => 'Payment for order #' . $item_number ,
							'client_type' => 'INTEGRATION',
							'success_redirect_url' => $this->config->base_url .'xendit/success',
							'failure_redirect_url' => $this->config->base_url .'xendit/error'
						);
						
						$request_url = '/payment/xendit/invoice';
						$request_options = array(
							'store_name' => 'Store'
						);
						
						try {
							$response = Xendit::request($request_url, Xendit::METHOD_POST, $request_payload, $request_options);

							if (isset($response['error_code'])) {
								$message = $response['message'];

								if (isset($response['code'])) {
									$message .= " Code: " . $response['code'];
								}
								throw new Exception($message);
							}
							else {
								if($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->insert_order(
									number_format($this->vars['package']['price'], 2, '.', ''),
									$this->vars['package']['currency'], 
									$this->vars['package']['reward'],
									$item_number,
									$this->pluginaizer->session->userdata(['user' => 'username']),
									$this->pluginaizer->session->userdata(['user' => 'server'])
								)){
									header('Location: '.$response['invoice_url']);
								}
								else{
									throw new exception($json['error']);	
								}
							}

						} catch (Exception $e) {
							$this->vars['error'] = $e->getMessage();
							$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.message', $this->vars);
						}
					}
				}
			}
			else{
				$this->pluginaizer->jsone(['error' => __('Plugin configuration not found.')]);
			}
		}
		else{
			echo $this->pluginaizer->jsone(['error' => __('Please login into website.')]);
		}
	}
	
	public function error(){
		if($this->pluginaizer->session->is_user()){
			$this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
			if($this->vars['plugin_config'] != false && !empty($this->vars['plugin_config'])){
				if($this->pluginaizer->data()->value('is_multi_server') == 1){
					if(array_key_exists($this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config'])){
						$this->vars['plugin_config'] = $this->vars['plugin_config'][$this->pluginaizer->session->userdata(['user' => 'server'])];
						$this->vars['about'] = $this->pluginaizer->get_about();
						$this->vars['about']['user_description'] = $this->pluginaizer->data()->value('description');
					}
					else{
						$this->pluginaizer->jsone(['error' => __('Plugin configuration not found.')]);
					}
				}
				if($this->vars['plugin_config']['active'] == 0){
					echo $this->pluginaizer->jsone(['error' => __('This module has been disabled.')]);
				}
				else{
					$this->vars['error'] = 'Something went wrong, please contact administrator.';
					$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.message', $this->vars);
				}
			}	
		}
		else{
			$this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()));
		}
	}
	
	public function success(){
		if($this->pluginaizer->session->is_user()){
			$this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
			if($this->vars['plugin_config'] != false && !empty($this->vars['plugin_config'])){
				if($this->pluginaizer->data()->value('is_multi_server') == 1){
					if(array_key_exists($this->pluginaizer->session->userdata(['user' => 'server']), $this->vars['plugin_config'])){
						$this->vars['plugin_config'] = $this->vars['plugin_config'][$this->pluginaizer->session->userdata(['user' => 'server'])];
						$this->vars['about'] = $this->pluginaizer->get_about();
						$this->vars['about']['user_description'] = $this->pluginaizer->data()->value('description');
					}
					else{
						$this->pluginaizer->jsone(['error' => __('Plugin configuration not found.')]);
					}
				}
				if($this->vars['plugin_config']['active'] == 0){
					echo $this->pluginaizer->jsone(['error' => __('This module has been disabled.')]);
				}
				else{
					$this->vars['success'] = 'Payment was successfully received, Enjoy the game!';
					$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . $this->config->config_entry('main|template') . DS . 'view.message', $this->vars);
				}
			}	
		}
		else{
			$this->pluginaizer->redirect($this->config->base_url . 'account-panel/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()));
		}
	}
	
	// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
	public function notify(){

		$original_response = json_decode(file_get_contents('php://input'), true);
		
		$order_id = isset($original_response['external_id']) ? $original_response['external_id'] : '';
		
		$this->vars['plugin_config'] = $this->pluginaizer->plugin_config();
		if($this->vars['plugin_config'] != false && !empty($this->vars['plugin_config'])){
			$this->load->model('account');	
			$this->load->model('application/plugins/'.$this->pluginaizer->get_plugin_class().'/models/'.$this->pluginaizer->get_plugin_class());;	
			if (!$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->check_order_number($order_id)) {
				$this->writelog('Order not found: '. $order_id, 'xendit');
				header('HTTP/1.1 422 Unprocessable Entity');
				die('Order not found: '. $order_id);
			}
			else{
				if($this->pluginaizer->data()->value('is_multi_server') == 1){
					if(array_key_exists($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server'], $this->vars['plugin_config'])){
						$this->vars['plugin_config'] = $this->vars['plugin_config'][$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server']];
					}
					else{
						$this->writelog('Plugin configuration not found.', 'xendit');
						header('HTTP/1.1 422 Unprocessable Entity');
						die(__('Plugin configuration not found.'));
					}
				}
				
				require_once(APP_PATH . DS . 'plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'libraries' . DS . 'xendit.php');
				
				try{
					$invoice_id = $original_response['id'];
					
					Xendit::set_secret_key($this->vars['plugin_config']['secret_key']);
					$store_name = 'test store';
					$request_url = '/payment/xendit/invoice/' . $invoice_id;
					$request_options = array(
						'store_name' => $store_name
					);
					$response = Xendit::request($request_url, Xendit::METHOD_GET, array(), $request_options);
					
					if(isset($response['error_code'])){
						$message = 'Could not get xendit invoice. Invoice id: ' . $invoice_id . '. Cancelling order.';
						$this->writelog($message, 'xendit');
						die($message);
					}
					
					if($response['status'] === 'PAID' || $response['status'] === 'SETTLED'){
						$complete_transaction = $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->check_completed_transaction($order_id);
						if($complete_transaction == false){
							
							$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->insert_transaction_status($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['hash'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['hash'], 'completed');
							if(mb_strpos($order_id, 'bp-') !== false){
								$this->load->model('application/plugins/battle_pass/models/battle_pass');
								$this->vars['time'] = $this->config->values('battle_pass', [$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server'], 'battle_pass_start_time']);
								$type = 'Silver';
								if($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['credits'] == 2){
									$type = 'Platinum';
								}
								$this->pluginaizer->Maccount->add_account_log('Purchased '.$type.' For ' . BPASS_CURRENCY, -$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['amount'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['account'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server']);
								$this->pluginaizer->Mbattle_pass->upgradePass($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['account'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server'], $this->vars['time'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['credits']);																		
							
							}
							else{
								if(mb_strpos($order_id, 'mk-') !== false){
									$this->load->model('application/plugins/wheel_of_fortune/models/wheel_of_fortune');
									if($this->pluginaizer->Mwheel_of_fortune->count_total_free_spins($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['account'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server']) != false){
										$this->pluginaizer->Mwheel_of_fortune->update_total_free_spins($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['account'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server'], AMOUNT_OF_KEYS);
									}
									else{
										$this->pluginaizer->Mwheel_of_fortune->insert_total_free_spins($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['account'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server'], AMOUNT_OF_KEYS);
									}
									$this->Maccount->add_account_log('Purchased Wheel Keys For ' . WHEEL_KEYS_CURRENCY, -$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['amount'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['account'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server']);
								}
								else{
									$this->pluginaizer->Maccount->add_account_log(
										'Reward ' . $this->pluginaizer->website->translate_credits($this->vars['plugin_config']['reward_type'], 
										$this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server']) . ' Xendit', 
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
									
									if(defined('PARTNER_SYSTEM') && PARTNER_SYSTEM == true){
										$this->load->model('partner');
										$partner = $this->Mpartner->findLinkedPartner($this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['account'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server']);
										if($partner != false){
											if($partner['dmn_linked_to'] != NULL){
												$partnerShare = $this->Mpartner->getPartnerShare($partner['dmn_linked_to'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server']);
												$share = floor(($partnerShare / 100) * $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['amount']);
												$this->Mpartner->updateShare($partner['dmn_linked_to'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server'], $share);
												$this->Mpartner->logShare($partner['dmn_linked_to'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['server'], $share, $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['amount'], $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->order_details['account']);
											}
										}
									}
								}
							}
						}
					} 
					else {
						$this->writelog('Invoice not paid or settled. Cancelling order. Invoice ID: ' . $response['id'], 'xendit');
						header('HTTP/1.1 422 Unprocessable Entity');
						die('Invoice not paid or settled. Cancelling order. Invoice ID: ' . $response['id']);
					}
					
				}
				catch (Exception $e) {
					$this->writelog($e->getMessage(), 'xendit');
					header('HTTP/1.1 422 Unprocessable Entity');
					die($e->getMessage());
				}
				
			}
		}
		else{
			$this->writelog('Plugin configuration not found.', 'xendit');
			header('HTTP/1.1 422 Unprocessable Entity');
			die(__('Plugin configuration not found.'));
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
			$this->vars['packages_xendit'] = $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->load_packages();
			//load any js, css files if required
			$this->vars['js'] = $this->config->base_url . 'assets/plugins/js/xendit.js';
			//load template
			$this->load->view('plugins' . DS . $this->pluginaizer->get_plugin_class() . DS . 'views' . DS . 'admin' . DS . 'view.index', $this->vars);
		
		}
		else{
			$this->pluginaizer->redirect($this->config->base_url . 'admincp/login?return=' . str_replace('_', '-', $this->pluginaizer->get_plugin_class()) . '/admin');
		}
	}
	
	/**
	 *
	 * Add xendit package
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
	 * Edit xendit package
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
	 * Delete xendit package
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
	 * Enable / Disable xendit package
	 * 
	 *
	 * Return mixed
	 */
	
	public function change_status(){
         //check if visitor has administrator privilleges
		if($this->pluginaizer->session->is_admin()){
			//load website helper
			$this->load->helper('website');
			
			$this->load->model('application/plugins/'.$this->pluginaizer->get_plugin_class().'/models/'.$this->pluginaizer->get_plugin_class());;
			
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
	 * Save xendit package order
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
	 * Generate xendit logs
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
                    $this->pluginaizer->pagination->initialize(1, 25, $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->count_total_transactions($acc, $server), $this->config->base_url . 'xendit/logs/%s/' . $acc . '/' . $server . '');
                    $this->vars['pagination'] =  $this->pluginaizer->pagination->create_links();
                }
            } 
			else{
                $this->vars['logs'] = $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->load_transactions($page, 25, $acc, $server);
                $lk = '';
                if($acc != '')
                    $lk .= '/' . $acc;
                $lk .= '/' . $server;
                $this->pluginaizer->pagination->initialize($page, 25,  $this->pluginaizer->{'M'.$this->pluginaizer->get_plugin_class()}->count_total_transactions($acc, $server), $this->config->base_url . 'xendit/logs/%s' . $lk);
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
				'description' => 'Donate with Xendit' //description which will see user
			]);
			
			//create plugin config template
			$this->pluginaizer->create_config([
				'active' => 0,
				'public_key' =>  '',
				'secret_key' => '',
				'reward_type' => 0
			]);
			//add sql scheme if there is any into website database
			//all schemes should be located in plugin_folder/sql_schemes
			$this->pluginaizer->add_sql_scheme('xendit_packages');
			$this->pluginaizer->add_sql_scheme('xendit_orders');
			$this->pluginaizer->add_sql_scheme('xendit_transactions');
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
			->remove_sql_scheme('xendit_packages')
			->remove_sql_scheme('xendit_orders')
			->remove_sql_scheme('xendit_transactions')
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