<?php

    class BulkEmail extends Job
    {
		public $vars = [];
        private $registry, $config, $load, $email_list, $max_recipients = 75, $transport, $mailer, $recipientList;

        public function __construct()
        {
            $this->registry = controller::get_instance();
            $this->config = $this->registry->config;
            $this->load = $this->registry->load;
        }

        public function execute()
        {
            $this->load->helper('website');
            $this->load->model('account');
            $this->send_mail();
        }

        private function send_mail()
        {
            $this->get_email_list();
            if(!empty($this->email_list)){
                $i = 0;
                $sent_to = 0;
                $failed = 0;
                $success = 0;
                $is_finished = 0;
				
				$this->connectSmtp();
				$this->recipientList = [];
				
				if(!isset($this->vars['error'])){
					foreach($this->email_list AS $emails){
						$this->recipientList = unserialize($this->get_recipient_list_from_file($emails['seo_subject']));
						$sending_started = $emails['sending_started'] != null ? $emails['sending_started'] : time();
						$count_recipients = count($this->recipientList);
						foreach($this->recipientList AS $key => $val){
							$i++;
							$count_recipients--;
							
							unset($this->recipientList[$key]);
							
							if(filter_var($val['mail_addr'], FILTER_VALIDATE_EMAIL) != false){
								$this->send($val['mail_addr'], $emails['subject'], str_replace(['{memb___id}', '{server_name}', '{site_url}'], [$val['memb___id'], $this->config->config_entry('main|servername'), $this->config->base_url . '../'], $emails['body']));
								if(isset($this->vars['error'])){
									$failed += 1;
									writelog($this->vars['error'], 'bulk_mail');
								} 
								else{
									$success += 1;
								}
							}
							else{
								$failed += 1;
								writelog('invalid email address: '.$val['mail_addr'], 'bulk_mail');
							}
							
							if($count_recipients == 0){
								$sending_finished = time();
								$is_finished = 1;
								$this->update_email_list($emails['id'], $this->recipientList, $sending_started, $sending_finished, $success, $failed, $is_finished, $emails['seo_subject']);
								break;
							}
							if($i == $this->max_recipients){
								$sending_finished = time();
								$this->update_email_list($emails['id'], $this->recipientList, $sending_started, $sending_finished, $success, $failed, $is_finished, $emails['seo_subject']);
								break;
							}
							
						}
					}
				}
            }
        }

        private function get_email_list()
        {
            $this->email_list = $this->registry->website->db('web')->query('SELECT id, subject, body, sending_started, sending_finished, sent_to, failed, seo_subject FROM DmN_Bulk_Emails WHERE is_finished = 0 ORDER BY sending_started ASC, id ASC')->fetch_all();
        }

        private function update_email_list($id, $recipient_list, $sending_started, $sending_finished, $success, $failed, $is_finished, $seo_subject)
        {
            $this->update_recipient_list($recipient_list, $seo_subject);
            $stmt = $this->registry->website->db('web')->prepare('UPDATE DmN_Bulk_Emails SET sending_started = :sending_started, sending_finished = :sending_finished, sent_to = sent_to + :sent_to, failed = failed + :failed, is_finished = :is_finished WHERE id = :id');
            $stmt->execute([':sending_started' => $sending_started, ':sending_finished' => $sending_finished, ':sent_to' => $success, ':failed' => $failed, ':is_finished' => $is_finished, ':id' => $id]);
        }

        private function get_recipient_list_from_file($subject)
        {
            $file = APP_PATH . DS . 'data' . DS . 'bulk_email_recipient_list' . DS . $subject . '.txt';
            if(file_exists($file)){
                return file_get_contents($file);
            }
            return serialize([]);
        }

        private function update_recipient_list($recipient_list, $seo_subject){
            $file = APP_PATH . DS . 'data' . DS . 'bulk_email_recipient_list' . DS . $seo_subject . '.txt';
            $add_recipient_list = @file_put_contents($file, serialize($recipient_list));
            if($add_recipient_list != false){
                return true;
            }
            return false;
        }
		
		private function connectSmtp(){
			try{
				$this->vars['config'] = $this->config->values('email_config');
				if(!$this->vars['config'])
					throw new \Exception('Email settings not configured.');
				if(!isset($this->vars['config']['server_email']) || $this->vars['config']['server_email'] == '')
					throw new \Exception('Website email is not set.');
				
				if($this->vars['config']['mail_mode'] == 0){
					$this->transport = Swift_SmtpTransport::newInstance($this->vars['config']['smtp_server'], (int)$this->vars['config']['smtp_port']);
				}
				if($this->vars['config']['mail_mode'] == 1){
					$this->transport = Swift_MailTransport::newInstance();
				}
				if($this->vars['config']['mail_mode'] == 2){
					$this->transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
				}
				if($this->vars['config']['mail_mode'] == 3){
					$this->transport = SwiftSparkPost\Transport::newInstance($this->vars['config']['smtp_password']);
				}
				if($this->vars['config']['smtp_use_ssl'] == 1 && $this->vars['config']['mail_mode'] == 0){
					$this->transport->setEncryption('ssl');
				}
				if($this->vars['config']['smtp_use_ssl'] == 2 && $this->vars['config']['mail_mode'] == 0){
					$this->transport->setEncryption('tls');
				}
				if($this->vars['config']['smtp_username'] != '' && $this->vars['config']['mail_mode'] == 0){
					$this->transport->setUsername($this->vars['config']['smtp_username']);
				}
				if($this->vars['config']['smtp_password'] != '' && $this->vars['config']['mail_mode'] == 0){
					$this->transport->setPassword($this->vars['config']['smtp_password']);
				}	
				$this->mailer = Swift_Mailer::newInstance($this->transport);
			}
			catch (\Exception $e){
				writelog($e->getMessage(), 'bulk_mail');
				exit;
			}
			catch(\Swift_ConnectionException $e){
				writelog($e->getMessage(), 'bulk_mail');
				exit;
			}
		}
					
		private function send($recipients, $subject, $message){
			try{
				$message = (new \Swift_Message)->setSubject($subject)->setFrom([$this->vars['config']['server_email'] => $this->config->config_entry('main|servername')])->setTo([$recipients])->setBody($message)->setContentType('text/html');
				if(!$this->mailer->send($message, $failures)){
					$this->vars['error'] = 'Failed sending email to '.print_r($failures, 1);
				}
			}
			catch(\Swift_Message_MimeException $e){
				writelog('There was an unexpected problem building the email. Error-Text: ' . $e->getMessage(), 'bulk_mail');
				exit;
				
			}
			catch(\Swift_TransportException $e){
				writelog($e->getMessage(), 'bulk_mail');
				exit;
			}	
		}		
    }