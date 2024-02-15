<?php
    in_file();

    class Mtransfer_credits extends model
    {
        public $error = false, $vars = [], $characters = [], $total_characters, $char_info = [], $gens_family;
        private $price, $per_page, $chars, $char_list = [], $pos;

        public function __contruct()
        {
            parent::__construct();
        }

        public function __set($key, $val)
        {
            $this->vars[$key] = $val;
        }

        public function __isset($name)
        {
            return isset($this->vars[$name]);
        }
		
		public function checkAccount($name, $server)
        {
            $stmt = $this->website->db('account', $server)->prepare('SELECT memb_guid FROM MEMB_INFO WHERE (memb___id Collate Database_Default = :username Collate Database_Default)');
            $stmt->execute([':username' => $name]);
            return $stmt->fetch();
        }
		
		public function add_log($total, $type, $from, $server, $to, $message){
			$stmt = $this->website->db('web')->prepare('INSERT INTO DmN_CreditTransferLogs (amount, type, fromAccount, toAccount, transferDate, server, message) VALUES (:amount, :type, :fromAccount, :toAccount, :transferDate, :server, :message)');
			$stmt->execute([
				':amount' => $total, 
				':type' => $type, 
				':fromAccount' => $from, 
				':toAccount' => $to, 
				':transferDate' => time(), 
				':server' => $server,
				':message' => $message
			]);
		}
		
		public function myTransfer($user, $server){
			return  $this->website->db('web')->query('SELECT amount, type, transferDate, toAccount, message FROM DmN_CreditTransferLogs WHERE fromAccount = \''.$this->website->db('web')->sanitize_var($user).'\' AND server = \''.$this->website->db('web')->sanitize_var($server).'\'')->fetch_all();
		}
		
		public function myReceivedTransfer($user, $server){
			return  $this->website->db('web')->query('SELECT amount, type, transferDate, fromAccount, message FROM DmN_CreditTransferLogs WHERE toAccount = \''.$this->website->db('web')->sanitize_var($user).'\' AND server = \''.$this->website->db('web')->sanitize_var($server).'\'')->fetch_all();
		}
    }
