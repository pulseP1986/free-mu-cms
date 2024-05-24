<?php
    in_file();

    class payment extends controller
    {
        public $vars = [];

        public function __construct(){
            parent::__construct();
            $this->load->helper('website');
            $this->load->lib('session', ['DmNCMS']);
			$this->session->checkSession();
            $this->load->model('account');
			$this->load->model('donate');
        }

        public function index(){
            throw new exception('Nothing to see here!');
        }

        public function paypal($order = ''){
            writelog('Paypal request initialized.', 'Paypal');
            if(count($_POST) > 0){
                $post_data = file_get_contents('php://input');
                $this->Mdonate->set_ipn_listeners($_POST['custom']);
                $this->Mdonate->gen_post_fields($post_data);
                if(function_exists('curl_init')){
                    if($this->Mdonate->post_back_paypal()){
                        foreach($_POST as $key => $value){
                            $this->Mdonate->$key = trim($value);
                        }
                        if($this->Mdonate->validate_paypal_payment()){
							if(mb_strpos($_POST['item_number'], 'bp-') !== false){
								$this->load->model('application/plugins/battle_pass/models/battle_pass');
								$this->vars['time'] = $this->config->values('battle_pass', [$this->Mdonate->order_details['server'], 'battle_pass_start_time']);
								$type = 'Silver';
								if($this->Mdonate->order_details['credits'] == 2){
									$type = 'Platinum';
								}
								$this->Maccount->add_account_log('Purchased '.$type.' For ' . BPASS_CURRENCY, -$this->Mdonate->order_details['amount'], $this->Mdonate->order_details['account'], $this->Mdonate->order_details['server']);
								$this->Mbattle_pass->upgradePass($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->vars['time'], $this->Mdonate->order_details['credits']);																		
							}
							else{
								if(mb_strpos($_POST['item_number'], 'mk-') !== false){
									$this->load->model('application/plugins/mystery_box/models/mystery_box');
									if($this->Mmystery_box->count_total_free_spins($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server']) != false){
										$this->Mmystery_box->update_total_free_spins($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], AMOUNT_OF_KEYS);
									}
									else{
										$this->Mmystery_box->insert_total_free_spins($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], AMOUNT_OF_KEYS);
									}
									$this->Maccount->add_account_log('Purchased Mystery Key For ' . WHEEL_KEYS_CURRENCY, -$this->Mdonate->order_details['amount'], $this->Mdonate->order_details['account'], $this->Mdonate->order_details['server']);
								}
								else{
									$this->Maccount->add_account_log('Reward ' . $this->website->translate_credits($this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paypal', 'reward_type']), $this->Mdonate->order_details['server']) . ' Paypal', $this->Mdonate->order_details['credits'], $this->Mdonate->order_details['account'], $this->Mdonate->order_details['server']);
									$this->Mdonate->reward_user($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->Mdonate->order_details['credits'], $this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paypal', 'reward_type']), $this->Maccount->get_guid($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server']));
									
									if(defined('PARTNER_SYSTEM') && PARTNER_SYSTEM == true ){
										$this->load->model('partner');
										$partner = $this->Mpartner->findLinkedPartner($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server']);
										if($partner != false){
											if($partner['dmn_linked_to'] != NULL){
												$partnerShare = $this->Mpartner->getPartnerShare($partner['dmn_linked_to'], $this->Mdonate->order_details['server']);
												$share = floor(($partnerShare / 100) * $this->Mdonate->vars['mc_gross']);
												$share = $share * PARTNER_BRL_RATIO;
												$this->Mpartner->updateShare($partner['dmn_linked_to'], $this->Mdonate->order_details['server'], $share);
												$this->Mpartner->logShare($partner['dmn_linked_to'], $this->Mdonate->order_details['server'], $share, $this->Mdonate->vars['mc_gross'] *  PARTNER_BRL_RATIO, $this->Mdonate->order_details['account']);
											}
										}
									}
									
									if($this->config->values('referral_config', 'reward_on_donation') > 0){
										$ref = $this->Mdonate->findReferral($this->Mdonate->order_details['account']);
										if($ref != false){
											$ref_reward = floor(($this->config->values('referral_config', 'reward_on_donation') / 100) * $this->Mdonate->order_details['credits']);
											$this->Mdonate->reward_user($ref, $this->Mdonate->order_details['server'], $ref_reward, $this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paypal', 'reward_type']), $this->Maccount->get_guid($ref, $this->Mdonate->order_details['server']));	
											$this->Maccount->add_account_log('Friend donation bonus ' . $this->website->translate_credits($this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paypal', 'reward_type']), $this->Mdonate->order_details['server']) . '', $ref_reward, $ref, $this->Mdonate->order_details['server']);
										}
									}
									
									if($this->config->values('email_config', 'donate_email_user') == 1){
										$this->Mdonate->sent_donate_email($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->Maccount->get_email($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server']), $this->Mdonate->order_details['credits'], $this->website->translate_credits($this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paypal', 'reward_type']), $this->Mdonate->order_details['server']), $this->website->get_user_credits_balance($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paypal', 'reward_type']), $this->Maccount->get_guid($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'])));
									}
									if($this->config->values('email_config', 'donate_email_admin') == 1){
										$this->Mdonate->sent_donate_email_admin($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->config->values('email_config', 'server_email'), $this->Mdonate->order_details['credits'], $this->website->translate_credits($this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paypal', 'reward_type']), $this->Mdonate->order_details['server']), $this->website->get_user_credits_balance($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paypal', 'reward_type']), $this->Maccount->get_guid($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'])), 'PayPal');
									}
								}
							}
                        }																								
					} else{
                        writelog('Unable to proccess payment curl request to paypal failed.', 'Paypal');
                    }
                } else{
                    writelog('Unable to proccess payment php curl extension not found.', 'Paypal');
                }
            } else{
                writelog('No $_POST data returned', 'Paypal');
            }
        }

        public function paycall(){
            if(count($_REQUEST) > 0){
                $custom = isset($_REQUEST["custom_data"]) ? trim(($_REQUEST["custom_data"])) : '';
                $total_amount = isset($_REQUEST["total"]) ? $_REQUEST["total"] : 0;
                $paycall_unique = isset($_REQUEST["paycall_unique"]) ? trim($_REQUEST['paycall_unique']) : 0;
                $business_code = isset($_REQUEST["business_code"]) ? $_REQUEST['business_code'] : 0;
                $this->Mdonate->check_paycall_order($custom);
                if($this->Mdonate->order_details != false){
                    if($business_code == $this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paycall', 'business_code'])){
                        if(!$this->Mdonate->verifiedTransaction($paycall_unique, $this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paycall', 'business_code']), $this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paycall', 'sandbox']))){
                            writelog('Error - Transaction [' . $paycall_unique . '] is not verified !', 'Paycall');
                        } else{
                            if($this->Mdonate->validate_paycall_payment($paycall_unique, $custom, $total_amount)){
                                $this->Maccount->add_account_log('Reward ' . $this->website->translate_credits($this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paycall', 'reward_type']), $this->Mdonate->order_details['server']) . ' Paycall', $this->Mdonate->order_details['credits'], $this->Mdonate->order_details['account'], $this->Mdonate->order_details['server']);
                                $this->Mdonate->reward_user($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->Mdonate->order_details['credits'], $this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paycall', 'reward_type']), $this->Maccount->get_guid($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server']));
                                
								if($this->config->values('referral_config', 'reward_on_donation') > 0){
									$ref = $this->Mdonate->findReferral($this->Mdonate->order_details['account']);
									if($ref != false){
										$ref_reward = floor(($this->config->values('referral_config', 'reward_on_donation') / 100) * $this->Mdonate->order_details['credits']);
										$this->Mdonate->reward_user($ref, $this->Mdonate->order_details['server'], $ref_reward, $this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paycall', 'reward_type']), $this->Maccount->get_guid($ref, $this->Mdonate->order_details['server']));
										$this->Maccount->add_account_log('Friend donation bonus ' . $this->website->translate_credits($this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paycall', 'reward_type']), $this->Mdonate->order_details['server']) . '', $ref_reward, $ref, $this->Mdonate->order_details['server']);
									}
								}
								
								if($this->config->values('email_config', 'donate_email_user') == 1){
                                    $this->Mdonate->sent_donate_email($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->Maccount->get_email($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server']), $this->Mdonate->order_details['credits'], $this->website->translate_credits($this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paycall', 'reward_type']), $this->Mdonate->order_details['server']), $this->website->get_user_credits_balance($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paycall', 'reward_type']), $this->Maccount->get_guid($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'])));
                                }
                                if($this->config->values('email_config', 'donate_email_admin') == 1){
                                    $this->Mdonate->sent_donate_email_admin($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->config->values('email_config', 'server_email'), $this->Mdonate->order_details['credits'], $this->website->translate_credits($this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paycall', 'reward_type']), $this->Mdonate->order_details['server']), $this->website->get_user_credits_balance($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->config->values('donation_config', [$this->Mdonate->order_details['server'], 'paycall', 'reward_type']), $this->Maccount->get_guid($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'])), 'PayCall');
                                }
                            }
                        }
                    } else{
                        writelog('Invalid business code: ' . $business_code, 'Paycall');
                    }
                } else{
                    writelog('Order not found: ' . $custom, 'Paycall');
                }
            } else{
                writelog('No $_REQUEST data returned', 'Paycall');
            }
        }

        public function paymentwall(){
            if(count($_GET) > 0){
                if(!isset($_GET['uid'])){
                    writelog('Error: Uid is not set. Correct format username-server-servername', 'paymentwall');
                    echo 'Uid is not set. Correct format username-server-servername';
                } else{
                    if(preg_match('/\b-server-\b/i', $_GET['uid'])){
                            if(isset($_GET['uid']) && $_GET['uid'] != ''){
                                $acc_serv = explode('-server-', $_GET['uid']);
								
								$this->vars['donation_config'] = $this->config->values('donation_config', [$acc_serv[1], 'paymentwall']);
								
								$this->load->lib('paymentwall');
								$this->paymentwall->setup($this->vars['donation_config']['api_key'], $this->vars['donation_config']['secret_key']);
								
								$pingback = new \Paymentwall_Pingback($_GET, ip());
								
								if($pingback->validate()){
									$virtualCurrency = $pingback->getVirtualCurrencyAmount();
									$order_id = $pingback->getReferenceId();
									
									if($pingback->isDeliverable()){
										if(!$this->Mdonate->check_reference($acc_serv[0], $acc_serv[1], $_GET['ref'])){
											$guid = $this->Maccount->get_guid($acc_serv[0], $acc_serv[1]);
											$reward_type = $this->website->translate_credits($this->vars['donation_config']['reward_type'], $acc_serv[1]);
											$this->Mdonate->log_pw_transaction($acc_serv[0], $acc_serv[1], $_GET['currency'], $_GET['type'], $_GET['ref']);
											$this->Maccount->add_account_log('Reward ' .$reward_type . ' Paymentwall', $_GET['currency'], $acc_serv[0], $acc_serv[1]);
											$this->Mdonate->reward_user($acc_serv[0], $acc_serv[1], $_GET['currency'], $this->vars['donation_config']['reward_type'], $guid);
											$balance = $this->website->get_user_credits_balance($acc_serv[0], $acc_serv[1], $this->vars['donation_config']['reward_type'], $guid);
											
											if($this->config->values('referral_config', 'reward_on_donation') > 0){
												$ref = $this->Mdonate->findReferral($acc_serv[0]);
												if($ref != false){
													$ref_reward = floor(($this->config->values('referral_config', 'reward_on_donation') / 100) * $_GET['currency']);
													$this->Mdonate->reward_user($ref, $this->Mdonate->order_details['server'], $ref_reward, $this->vars['donation_config']['reward_type'], $this->Maccount->get_guid($ref, $this->Mdonate->order_details['server']));
													$this->Maccount->add_account_log('Friend donation bonus ' . $this->website->translate_credits($this->vars['donation_config']['reward_type'], $acc_serv[1]) . '', $ref_reward, $ref, $acc_serv[1]);
												}
											}
											if($this->config->values('email_config', 'donate_email_user') == 1){
												$this->Mdonate->sent_donate_email($acc_serv[0], $acc_serv[1], $this->Maccount->get_email($acc_serv[0], $acc_serv[1]), $_GET['currency'], $reward_type, $balance);
											}
											if($this->config->values('email_config', 'donate_email_admin') == 1){
												$this->Mdonate->sent_donate_email_admin($acc_serv[0], $acc_serv[1], $this->config->values('email_config', 'server_email'), $_GET['currency'], $reward_type, $balance, 'PaymentWall');
											}
											
											$delivery = new \Paymentwall_GenerericApiObject('delivery');

											$response = $delivery->post(array(
												'payment_id' => $_GET['ref'],
												'merchant_reference_id' => md5($_GET['ref']),
												'type' => 'digital',
												'status' => 'delivered',
												'estimated_delivery_datetime' => date('Y/m/d H:i:s O', time()),
												'estimated_update_datetime' => date('Y/m/d H:i:s O', time()),
												'refundable' => true,
												'details' => 'Virtual currency was credited into customer account',
												'shipping_address[email]' => $this->Maccount->get_email($acc_serv[0], $acc_serv[1]),
												'reason' => 'none',
												'attachments[0]' => null
											));
											if(isset($response['success'])){
												 echo 'OK';
											} 
											elseif(isset($response['error'])){
												writelog('Error: ' . print_r($response['error'], true) . ', notice: '.print_r($response['notices'], true).'', 'paymentwall');
												//var_dump($response['error'], $response['notices']);
												echo 'OK';
																 
											}
										}
										else{
											writelog('Error: payment: ' . htmlspecialchars($_GET['ref']) . ' already proccessed', 'paymentwall');
											 echo 'OK';
										}	
									}
									elseif($pingback->isCancelable()){
										$this->Mdonate->change_pw_transaction_status($_GET['currency'], $_GET['reason'], $acc_serv[0], $_GET['ref']);
										if($_GET['reason'] == 2 || $_GET['reason'] == 3){
											$this->Mdonate->block_user($acc_serv[0], $acc_serv[1]);
										}
										$this->Mdonate->decrease_credits($acc_serv[0], $acc_serv[1], $_GET['currency'], $this->vars['donation_config']['reward_type']);
										$this->Maccount->add_account_log('Decrease ' . $this->website->translate_credits($this->vars['donation_config']['reward_type'], $acc_serv[1]) . ' Paymentwall', $_GET['currency'], $acc_serv[0], $acc_serv[1]);
										echo 'OK';
									} 
									elseif($pingback->isUnderReview()) {
									// set "pending" status to order
									}
								}
								else{
									writelog($pingback->getErrorSummary(), 'paymentwall');
									echo $pingback->getErrorSummary();
								}
                            } else{
                                writelog('Error: Missing uid', 'paymentwall');
                                echo 'Error: Missing uid';
                            }
                        
                    } else{
                        writelog('Error: invalid uid ' . htmlspecialchars($_GET['uid']) . '. Correct format username-server-servername', 'paymentwall');
                        echo 'Invalid uid ' . htmlspecialchars($_GET['uid']) . '. Correct format username-server-servername';
                    }																										 
                }
            }
        }

        public function two_checkout(){
            $params = [];
            foreach($_REQUEST as $k => $v){
                $params[$k] = $v;
            }
            $this->load->lib('two_checkout');
            $this->two_checkout->setup($this->config->values('donation_config', [$params['dmn_server'], '2checkout', 'seller_id']), $this->config->values('donation_config', [$params['dmn_server'], '2checkout', 'private_key']));
            $passback = $this->two_checkout->check($params, $this->config->values('donation_config', [$params['dmn_server'], '2checkout', 'private_secret_word']));
            if($passback['response_code'] == 'Success'){
                if($this->vars['order_data'] = $this->Mdonate->get_2checkout_order_data($params['dmn_hash'])){
                    if($this->Mdonate->check_existing_2checkout_transaction($params['order_number'])){
                        echo 'Transaction already processed: ' . $params['order_number'];
                        writelog('Error: Transaction already processed: ' . $params['order_number'], '2checkout');
                    } else{
                        $this->Mdonate->insert_2checkout_transaction($params['order_number'], $this->vars['order_data']['amount'], $this->vars['order_data']['currency'], $this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->vars['order_data']['credits'], $params['email'], $params['dmn_hash']);
                        $this->Maccount->add_account_log('Reward ' . $this->website->translate_credits($this->config->values('donation_config', [$params['dmn_server'], '2checkout', 'reward_type']), $this->vars['order_data']['server']) . ' 2CheckOut', $this->vars['order_data']['credits'], $this->vars['order_data']['account'], $this->vars['order_data']['server']);
                        $this->Mdonate->reward_user($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->vars['order_data']['credits'], $this->config->values('donation_config', [$params['dmn_server'], '2checkout', 'reward_type']), $this->Maccount->get_guid($this->vars['order_data']['account'], $this->vars['order_data']['server']));
                        if($this->config->values('email_config', 'donate_email_user') == 1){
                            $this->Mdonate->sent_donate_email($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->Maccount->get_email($this->vars['order_data']['account'], $this->vars['order_data']['server']), $this->vars['order_data']['credits'], $this->website->translate_credits($this->config->values('donation_config', [$this->vars['order_data']['server'], '2checkout', 'reward_type']), $this->vars['order_data']['server']), $this->website->get_user_credits_balance($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->config->values('donation_config', [$this->vars['order_data']['server'], '2checkout', 'reward_type']), $this->Maccount->get_guid($this->vars['order_data']['account'], $this->vars['order_data']['server'])));
                        }
                        if($this->config->values('email_config', 'donate_email_admin') == 1){
                            $this->Mdonate->sent_donate_email_admin($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->config->values('email_config', 'server_email'), $this->vars['order_data']['credits'], $this->website->translate_credits($this->config->values('donation_config', [$this->vars['order_data']['server'], '2checkout', 'reward_type']), $this->vars['order_data']['server']), $this->website->get_user_credits_balance($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->config->values('donation_config', [$this->vars['order_data']['server'], '2checkout', 'reward_type']), $this->Maccount->get_guid($this->vars['order_data']['account'], $this->vars['order_data']['server'])), '2CheckOut');
                        }
                        header('Location: ' . $this->config->base_url . 'account-panel/logs');
                    }
                } else{
                    echo 'Unable to find order data.';
                    writelog('Error: Unable to find order data.', '2checkout');
                }
            } else{
                echo 'Error: ' . $passback['response_message'];
                writelog('Error: ' . $passback['response_message'] . '', '2checkout');
            }
        }

        public function cuenta_digital(){
            if(count($_REQUEST) > 0){
                $code = isset($_REQUEST['codigo']) ? $_REQUEST['codigo'] : '';
                if($code != ''){
                    $this->vars['order_data'] = $this->Mdonate->get_cuenta_digital_order_data($code);
                    if($this->vars['order_data'] != false){
                        if($this->Mdonate->check_existing_cuenta_digital_transaction($code)){
                            echo 'Transaction already processed: ' . $code;
                            writelog('Transaction already processed: ' . $code, 'CuentaDigital');
                        } else{
                            $this->Mdonate->insert_cuenta_digital_transaction($this->vars['order_data']['amount'], $this->vars['order_data']['currency'], $this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->vars['order_data']['credits'], $code);
                            $this->Maccount->add_account_log('Reward ' . $this->website->translate_credits($this->config->values('donation_config', [$this->vars['order_data']['server'], 'cuenta_digital', 'reward_type']), $this->vars['order_data']['server']) . ' CuentaDigital', $this->vars['order_data']['credits'], $this->vars['order_data']['account'], $this->vars['order_data']['server']);
                            $this->Mdonate->reward_user($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->vars['order_data']['credits'], $this->config->values('donation_config', [$this->vars['order_data']['server'], 'cuenta_digital', 'reward_type']), $this->Maccount->get_guid($this->vars['order_data']['account'], $this->vars['order_data']['server']));
                            
							if($this->config->values('referral_config', 'reward_on_donation') > 0){
								$ref = $this->Mdonate->findReferral($this->vars['order_data']['account']);
								if($ref != false){
									$ref_reward = floor(($this->config->values('referral_config', 'reward_on_donation') / 100) * $this->vars['order_data']['amount']);
									$this->Mdonate->reward_user($ref, $this->Mdonate->order_details['server'], $ref_reward, $this->config->values('donation_config', [$this->vars['order_data']['server'], 'cuenta_digital', 'reward_type']), $this->Maccount->get_guid($ref, $this->Mdonate->order_details['server']));
									$this->Maccount->add_account_log('Friend donation bonus ' . $this->website->translate_credits($this->config->values('donation_config', [$this->vars['order_data']['server'], 'cuenta_digital', 'reward_type']), $this->vars['order_data']['server']) . '', $ref_reward, $ref, $this->vars['order_data']['server']);
								}
							}
							
							if($this->config->values('email_config', 'donate_email_user') == 1){
                                $this->Mdonate->sent_donate_email($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->Maccount->get_email($this->vars['order_data']['account'], $this->vars['order_data']['server']), $this->vars['order_data']['credits'], $this->website->translate_credits($this->config->values('donation_config', [$this->vars['order_data']['server'], 'cuenta_digital', 'reward_type']), $this->vars['order_data']['server']), $this->website->get_user_credits_balance($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->config->values('donation_config', [$this->vars['order_data']['server'], 'cuenta_digital', 'reward_type']), $this->Maccount->get_guid($this->vars['order_data']['account'], $this->vars['order_data']['server'])));
                            }
                            if($this->config->values('email_config', 'donate_email_admin') == 1){
                                $this->Mdonate->sent_donate_email_admin($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->config->values('email_config', 'server_email'), $this->vars['order_data']['credits'], $this->website->translate_credits($this->config->values('donation_config', [$this->vars['order_data']['server'], 'cuenta_digital', 'reward_type']), $this->vars['order_data']['server']), $this->website->get_user_credits_balance($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->config->values('donation_config', [$this->vars['order_data']['server'], 'cuenta_digital', 'reward_type']), $this->Maccount->get_guid($this->vars['order_data']['account'], $this->vars['order_data']['server'])), 'CuentaDigital');
                            }
                            echo 'OK';
                        }
                    } else{
                        echo 'Order not found - ' . htmlspecialchars($code);
                        writelog('Order not found - ' . htmlspecialchars($code), 'CuentaDigital');
                    }
                } else{
                    echo '$_REQUEST[\'codigo\'] was empty';
                    writelog('$_REQUEST[\'codigo\'] was empty', 'CuentaDigital');
                }
            } else{
                echo 'No $_REQUEST data returned';
                writelog('No $_REQUEST data returned', 'CuentaDigital');
            }
        }

        public function interkassa(){
            if(count($_POST) > 0){
                if(isset($_POST['ik_x_userinfo'])){
                    if(preg_match('/\b-server-\b/i', $_POST['ik_x_userinfo'])){
                        $userinfo = explode('-server-', $_POST['ik_x_userinfo']);
                        $this->vars['donation_config'] = $this->config->values('donation_config', [$userinfo[1], 'interkassa']);
                        if($this->vars['donation_config'] != false && $this->vars['donation_config']['active'] == 1){
                            $this->load->lib('interkassa');
                            $shop = Interkassa_Shop::factory(['id' => $this->vars['donation_config']['shop_id'], 'secret_key' => $this->vars['donation_config']['secret_key']]);
                            try{
                                $status = $shop->receiveStatus($_POST);
                            } catch(Interkassa_Exception $e){
                                writelog($e->getMessage(), 'Interkassa');
                                header('HTTP/1.0 400 Bad Request');
                                exit;
                            }
                            $payment = $status->getPayment();
                            if($status->getState() == 'success'){
                                if($this->Mdonate->check_interkassa_order_number($payment->getId())){
                                    if($payment->getAmount() != $this->Mdonate->order_details['amount']){
                                        writelog('Wrong order amount: ' . $payment->getAmount() . ' not found.', 'Interkassa');
                                    } else{
                                        if($this->Mdonate->check_completed_interkassa_transaction($payment->getId())){
                                            writelog('Transaction already proccessed: ' . $payment->getId(), 'Interkassa');
                                            header('HTTP/1.0 400 Bad Request');
                                            exit;
                                        } else{
                                            $this->Mdonate->insert_interkassa_transaction($payment->getId(), $payment->getAmount());
                                            $this->Maccount->add_account_log('Reward ' . $this->website->translate_credits($this->vars['donation_config']['reward_type'], $this->Mdonate->order_details['server']) . ' Interkassa', $this->Mdonate->order_details['credits'], $this->Mdonate->order_details['account'], $this->Mdonate->order_details['server']);
                                            $this->Mdonate->reward_user($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->Mdonate->order_details['credits'], $this->vars['donation_config']['reward_type'], $this->Maccount->get_guid($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server']));
                                            if($this->config->values('email_config', 'donate_email_user') == 1){
                                                $this->Mdonate->sent_donate_email($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->Maccount->get_email($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server']), $this->Mdonate->order_details['credits'], $this->website->translate_credits($this->vars['donation_config']['reward_type'], $this->Mdonate->order_details['server']), $this->website->get_user_credits_balance($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->vars['donation_config']['reward_type'], $this->Maccount->get_guid($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'])));
                                            }
                                            if($this->config->values('email_config', 'donate_email_admin') == 1){
                                                $this->Mdonate->sent_donate_email_admin($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->config->values('email_config', 'server_email'), $this->Mdonate->order_details['credits'], $this->website->translate_credits($this->vars['donation_config']['reward_type'], $this->Mdonate->order_details['server']), $this->website->get_user_credits_balance($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'], $this->vars['donation_config']['reward_type'], $this->Maccount->get_guid($this->Mdonate->order_details['account'], $this->Mdonate->order_details['server'])), 'Interkassa');
                                            }
                                            header('HTTP/1.0 200 OK');
                                        }
                                    }
                                } else{
                                    writelog('Order with id: ' . $payment->getId() . ' not found.', 'Interkassa');
                                    header('HTTP/1.0 400 Bad Request');
                                    exit;
                                }
                            } else{
                                writelog('Wrong payment state: ' . $status->getState(), 'Interkassa');
                                header('HTTP/1.0 400 Bad Request');
                                exit;
                            }
                        } else{
                            writelog('Payment system not configured or disabled', 'Interkassa');
                            header('HTTP/1.0 400 Bad Request');
                            exit;
                        }
                    } else{
                        writelog('Parameter $_POST[\'ik_x_userinfo\'] is formated wrongly', 'Interkassa');
                        header('HTTP/1.0 400 Bad Request');
                        exit;
                    }
                } else{
                    writelog('Missing $_POST[\'ik_x_userinfo\'] parameter', 'Interkassa');
                    header('HTTP/1.0 400 Bad Request');
                    exit;
                }
            } else{
                writelog('No $_POST data returned', 'Interkassa');
                header('HTTP/1.0 400 Bad Request');
                exit;
            }
        }

        public function pagseguro($server = ''){
            if($server == ''){
                echo 'Server variable is not set';
                writelog('Server variable is not set', 'pagseguro_log');
            } else{
                //$server_list = $this->website->server_list();
                if(array_key_exists($server, $this->website->server_list())){
                    //header("access-control-allow-origin: https://sandbox.pagseguro.uol.com.br");
                    //writelog(print_r($_POST, true), 'pagseguro_log');
                    $notificationType = (isset($_POST['notificationType']) && trim($_POST['notificationType']) !== "" ? trim($_POST['notificationType']) : null);
                    $notificationCode = (isset($_POST['notificationCode']) && trim($_POST['notificationCode']) !== "" ? trim($_POST['notificationCode']) : null);
                    //$refference = (isset($_POST['Referencia']) && trim($_POST['Referencia']) !== "" ? trim($_POST['Referencia']) : NULL);
                    //if($refference != null){
                    //	$this->vars['order_data'] = $this->Mdonate->get_pagseguro_order_data($refference);
                    //}
                    //writelog(print_r($_POST, true), 'pagseguro_log');
                    //$server = (isset($_POST['userserver']) && trim($_POST['userserver']) !== "" ? trim($_POST['userserver']) : 'DEFAULT');
                    require_once(APP_PATH . DS . 'libraries' . DS . 'PagSeguroLibrary' . DS . 'PagSeguroLibrary.php');
                    $notificationType = new PagSeguroNotificationType($notificationType);
                    $strType = $notificationType->getTypeFromValue();
                    if(strtoupper($strType) == 'TRANSACTION'){
                        //if(isset($this->vars['order_data']) && $this->vars['order_data'] != false){
                        //	$credentials = new PagSeguroAccountCredentials($this->config->values('donation_config', [$this->vars['order_data']['server'], 'pagseguro', 'email']), $this->config->values('donation_config', [$this->vars['order_data']['server'], 'pagseguro', 'token']));
                        //}
                        //else{
                        $credentials = new PagSeguroAccountCredentials($this->config->values('donation_config', [$server, 'pagseguro', 'email']), $this->config->values('donation_config', [$server, 'pagseguro', 'token']));
                        //}
                        $transaction = PagSeguroNotificationService::checkTransaction($credentials, $notificationCode);
                        $status = $transaction->getStatus();
                        if($status->getValue() == 3){
                            $item = $transaction->getReference();
                            //if(!isset($this->vars['order_data'])){
                            $this->vars['order_data'] = $this->Mdonate->get_pagseguro_order_data($item);
                            //}
                            if($this->vars['order_data'] != false){
                                if($this->Mdonate->check_existing_pagseguro_transaction($notificationCode)){
                                    echo 'Transaction already processed: ' . $item;
                                    writelog('Error: Transaction already processed: ' . $item, 'pagseguro_log');
                                } else{
                                    $this->Mdonate->insert_pagseguro_transaction($_POST['notificationCode'], $this->vars['order_data']['amount'], $this->vars['order_data']['currency'], $this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->vars['order_data']['credits'], $item);
                                    $this->Maccount->add_account_log('Reward ' . $this->website->translate_credits($this->config->values('donation_config', [$this->vars['order_data']['server'], 'pagseguro', 'reward_type']), $this->vars['order_data']['server']) . ' PagSeguro', $this->vars['order_data']['credits'], $this->vars['order_data']['account'], $this->vars['order_data']['server']);
                                    $this->Mdonate->reward_user($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->vars['order_data']['credits'], $this->config->values('donation_config', [$this->vars['order_data']['server'], 'pagseguro', 'reward_type']), $this->Maccount->get_guid($this->vars['order_data']['account'], $this->vars['order_data']['server']));
                                    
									if(defined('PARTNER_SYSTEM') && PARTNER_SYSTEM == true ){
										$this->load->model('partner');
										$partner = $this->Mpartner->findLinkedPartner($this->vars['order_data']['account'], $this->vars['order_data']['server']);
										if($partner != false){
											if($partner['dmn_linked_to'] != NULL){
												$partnerShare = $this->Mpartner->getPartnerShare($partner['dmn_linked_to'], $this->vars['order_data']['server']);
												$share = floor(($partnerShare / 100) * $this->vars['order_data']['amount']);
												$this->Mpartner->updateShare($partner['dmn_linked_to'], $this->vars['order_data']['server'], $share);
												$this->Mpartner->logShare($partner['dmn_linked_to'], $this->vars['order_data']['server'], $share, $this->vars['order_data']['amount'], $this->vars['order_data']['account']);
											}
										}
									}
									
									if($this->config->values('referral_config', 'reward_on_donation') > 0){
										$ref = $this->Mdonate->findReferral($this->vars['order_data']['account']);
										if($ref != false){
											$ref_reward = floor(($this->config->values('referral_config', 'reward_on_donation') / 100) * $this->vars['order_data']['amount']);
											$this->Mdonate->reward_user($ref, $this->Mdonate->order_details['server'], $ref_reward, $this->config->values('donation_config', [$this->vars['order_data']['server'], 'pagseguro', 'reward_type']), $this->Maccount->get_guid($ref, $this->Mdonate->order_details['server']));
											$this->Maccount->add_account_log('Friend donation bonus ' . $this->website->translate_credits($this->config->values('donation_config', [$this->vars['order_data']['server'], 'pagseguro', 'reward_type']), $this->vars['order_data']['server']) . '', $ref_reward, $ref, $this->vars['order_data']['server']);
										}
									}
									
									if($this->config->values('email_config', 'donate_email_user') == 1){
                                        $this->Mdonate->sent_donate_email($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->Maccount->get_email($this->vars['order_data']['account'], $this->vars['order_data']['server']), $this->vars['order_data']['credits'], $this->website->translate_credits($this->config->values('donation_config', [$this->vars['order_data']['server'], 'pagseguro', 'reward_type']), $this->vars['order_data']['server']), $this->website->get_user_credits_balance($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->config->values('donation_config', [$this->vars['order_data']['server'], 'pagseguro', 'reward_type']), $this->Maccount->get_guid($this->vars['order_data']['account'], $this->vars['order_data']['server'])));
                                    }
                                    if($this->config->values('email_config', 'donate_email_admin') == 1){
                                        $this->Mdonate->sent_donate_email_admin($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->config->values('email_config', 'server_email'), $this->vars['order_data']['credits'], $this->website->translate_credits($this->config->values('donation_config', [$this->vars['order_data']['server'], 'pagseguro', 'reward_type']), $this->vars['order_data']['server']), $this->website->get_user_credits_balance($this->vars['order_data']['account'], $this->vars['order_data']['server'], $this->config->values('donation_config', [$this->vars['order_data']['server'], 'pagseguro', 'reward_type']), $this->Maccount->get_guid($this->vars['order_data']['account'], $this->vars['order_data']['server'])), 'PagSeguro');
                                    }
                                    echo 'ok';
                                }
                            } else{
                                echo 'Unable to find order data.';
                                writelog('Error: Unable to find order data.', 'pagseguro_log');
                            }
                        } else{
                            echo 'wrong transaction status.';
                            writelog('Error: wrong transaction status ' . $this->_getStatusTranslation($this->_getStatusString($status->getValue())), 'pagseguro_log');
                        }
                    } else{
                        echo 'authorization';
                        writelog('Error: authorization - ' . $strType . '', 'pagseguro_log');
                    }
                } else{
                    echo 'Server key: ' . $server . ' not found in server list';
                    writelog('Server key: ' . $server . ' not found in server list', 'pagseguro_log');
                }
            }
        }

        private function _getStatusTranslation($status){
            $order_status = ['INITIATED' => 'Initiated', 'WAITING_PAYMENT' => 'Waiting payment', 'IN_ANALYSIS' => 'In analysis', 'PAID' => 'Paid', 'AVAILABLE' => 'Available', 'IN_DISPUTE' => 'In dispute', 'REFUNDED' => 'Refunded', 'CANCELLED' => 'Cancelled'];
            if(isset($order_status[$status]))
                return $order_status[$status];
            return 0;
        }

        private function _getStatusString($statusPagSeguro){
			require_once(APP_PATH . DS . 'libraries' . DS . 'PagSeguroLibrary' . DS . 'PagSeguroLibrary.php');
            $transactionStatus = new PagSeguroTransactionStatus($statusPagSeguro);
            return $transactionStatus->getTypeFromValue();
        }

        public function fortumo(){
            if(count($_GET) > 0){
                //if($this->Mdonate->validate_ip_list($_GET['cuid'], 'fortumo')){
                    if($_GET['sig'] != $sigi = $this->Mdonate->fortumo_sig_check($_GET)){
                        writelog('Error: Invalid signature ' . $_GET['sig'] . '-' . $sigi, 'fortumo');
                        throw new exception('Error: Invalid signature');
                    }
                    if(preg_match("/OK|COMPLETED/i", $_GET['status']) || ((isset($_GET['billing_type']) && preg_match("/MO/i", $_GET['billing_type'])) && preg_match("/pending/i", $_GET['status']))){
                        if($this->Mdonate->check_fortumo_transaction($_GET['payment_id'])){
                            writelog('Error: payment id: ' . $_GET['payment_id'] . ' already rewarded.', 'fortumo');
                        } else{
                            if(preg_match('/\b-server-\b/i', $_GET['cuid'])){
                                $acc_serv = explode('-server-', $_GET['cuid']);
                                $this->Mdonate->log_fortumo_transaction($_GET['payment_id'], $_GET['sender'], $acc_serv[0], $acc_serv[1], $_GET['amount']);
                                $this->Maccount->add_account_log('Reward ' . $this->website->translate_credits($this->config->values('donation_config', [$acc_serv[1], 'fortumo', 'reward_type']), $acc_serv[1]) . ' Fortumo', $_GET['amount'], $acc_serv[0], $acc_serv[1]);
                                $this->Mdonate->reward_user($acc_serv[0], $acc_serv[1], $_GET['amount'], $this->config->values('donation_config', [$acc_serv[1], 'fortumo', 'reward_type']), $this->Maccount->get_guid($acc_serv[0], $acc_serv[1]));
                                if($this->config->values('email_config', 'donate_email_user') == 1){
                                    $this->Mdonate->sent_donate_email($acc_serv[0], $acc_serv[1], $this->Maccount->get_email($acc_serv[0], $acc_serv[1]), $_GET['amount'], $this->website->translate_credits($this->config->values('donation_config', [$acc_serv[1], 'fortumo', 'reward_type']), $acc_serv[1]), $this->website->get_user_credits_balance($acc_serv[0], $acc_serv[1], $this->config->values('donation_config', [$acc_serv[1], 'fortumo', 'reward_type']), $this->Maccount->get_guid($acc_serv[0], $acc_serv[1])));
                                }
                                if($this->config->values('email_config', 'donate_email_admin') == 1){
                                    $this->Mdonate->sent_donate_email_admin($acc_serv[0], $acc_serv[1], $this->config->values('email_config', 'server_email'), $_GET['amount'], $this->website->translate_credits($this->config->values('donation_config', [$acc_serv[1], 'fortumo', 'reward_type']), $acc_serv[1]), $this->website->get_user_credits_balance($acc_serv[0], $acc_serv[1], $this->config->values('donation_config', [$acc_serv[1], 'fortumo', 'reward_type']), $this->Maccount->get_guid($acc_serv[0], $acc_serv[1])), 'Fortumo');
                                }
                            } else{
                                writelog('Error: invalid cuid ' . $_GET['cuid'] . '. Correct format username-server-servername', 'fortumo');
                            }
                        }
                    } else{
                        writelog('Error: payment failed phone: ' . $_GET['sender'] . ', account: ' . $_GET['cuid'] . ', amount credits: ' . $_GET['amount'] . ', status: ' . $_GET['status'], 'fortumo');
                    }
                //} else{
                //    writelog('Error: Unknown IP', 'fortumo');
                 //   throw new exception('Unknown IP');
                //}
            }
        }

        public function paygol(){
            if(count($_GET) > 0){
                if(!in_array(ip(), ['109.70.3.48', '109.70.3.146', '109.70.3.58'])){
                    writelog('Error: Unknown IP', 'paygol');
                    throw new exception('Unknown IP');
                } else{
                    if(preg_match('/\b-server-\b/i', $_GET['custom'])){
                        $acc_serv = explode('-server-', $_GET['custom']);
                        if($_GET['service_id'] == $this->config->values('donation_config', [$acc_serv[1], 'paygol', 'service_id'])){
                            $this->Mdonate->log_paygol_transaction($_GET['message_id'], $_GET['message'], $_GET['shortcode'], $_GET['sender'], $_GET['operator'], $_GET['country'], $_GET['currency'], $_GET['price'], $acc_serv[0], $acc_serv[1]);
                            $this->Maccount->add_account_log('Reward ' . $this->website->translate_credits($this->config->values('donation_config', [$acc_serv[1], 'paygol', 'reward_type']), $acc_serv[1]) . ' Paygol', $this->config->values('donation_config', [$acc_serv[1], 'paygol', 'reward']), $acc_serv[0], $acc_serv[1]);
                            $this->Mdonate->reward_user($acc_serv[0], $acc_serv[1], $this->config->values('donation_config', [$acc_serv[1], 'paygol', 'reward']), $this->config->values('donation_config', [$acc_serv[1], 'paygol', 'reward_type']), $this->Maccount->get_guid($acc_serv[0], $acc_serv[1]));
                            if($this->config->values('email_config', 'donate_email_user') == 1){
                                $this->Mdonate->sent_donate_email($acc_serv[0], $acc_serv[1], $this->Maccount->get_email($acc_serv[0], $acc_serv[1]), $this->config->values('donation_config', [$acc_serv[1], 'paygol', 'reward']), $this->website->translate_credits($this->config->values('donation_config', [$acc_serv[1], 'paygol', 'reward_type']), $acc_serv[1]), $this->website->get_user_credits_balance($acc_serv[0], $acc_serv[1], $this->config->values('donation_config', [$acc_serv[1], 'paygol', 'reward_type']), $this->Maccount->get_guid($acc_serv[0], $acc_serv[1])));
                            }
                            if($this->config->values('email_config', 'donate_email_admin') == 1){
                                $this->Mdonate->sent_donate_email_admin($acc_serv[0], $acc_serv[1], $this->config->values('email_config', 'server_email'), $this->config->values('donation_config', [$acc_serv[1], 'paygol', 'reward']), $this->website->translate_credits($this->config->values('donation_config', [$acc_serv[1], 'paygol', 'reward_type']), $acc_serv[1]), $this->website->get_user_credits_balance($acc_serv[0], $acc_serv[1], $this->config->values('donation_config', [$acc_serv[1], 'paygol', 'reward_type']), $this->Maccount->get_guid($acc_serv[0], $acc_serv[1])), 'PayGol');
                            }
                        } else{
                            writelog('Error: Wrong service id', 'paygol');
                        }
                    } else{
                        writelog('Error: invalid user: ' . $_GET['custom'] . '. Correct format username-server-servername', 'paygol');
                    }
                }
            }
        }
    }