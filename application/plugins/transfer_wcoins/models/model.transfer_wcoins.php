<?php
    in_file();

    class Mtransfer_wcoins extends model
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
		
		public function add_log($total, $from, $server, $to, $message){
			$stmt = $this->website->db('web')->prepare('INSERT INTO DmN_WCoinTransferLogs (amount, fromAccount, toAccount, transferDate, server, message) VALUES (:amount, :fromAccount, :toAccount, :transferDate, :server, :message)');
			$stmt->execute([
				':amount' => $total,
				':fromAccount' => $from, 
				':toAccount' => $to, 
				':transferDate' => time(), 
				':server' => $server,
				':message' => $message
			]);
		}
		
		public function myTransfer($user, $server){
			return  $this->website->db('web')->query('SELECT amount, transferDate, toAccount, message FROM DmN_WCoinTransferLogs WHERE fromAccount = \''.$this->website->db('web')->sanitize_var($user).'\' AND server = \''.$this->website->db('web')->sanitize_var($server).'\'')->fetch_all();
		}
		
		public function myReceivedTransfer($user, $server){
			return  $this->website->db('web')->query('SELECT amount, transferDate, fromAccount, message FROM DmN_WCoinTransferLogs WHERE toAccount = \''.$this->website->db('web')->sanitize_var($user).'\' AND server = \''.$this->website->db('web')->sanitize_var($server).'\'')->fetch_all();
		}
		
		public function add_wcoins($user, $server, $amount = 0, $config = [])
        {
            $stmt = $this->website->db($config['db'], $server)->prepare('UPDATE ' . $config['table'] . ' SET ' . $config['column'] . ' = ' . $config['column'] . ' + :wcoins WHERE ' . $config['identifier_column'] . ' = :account');
            $stmt->execute([':wcoins' => $amount, ':account' => $user]);
            if($stmt->rows_affected() == 0){
                $stmt = $this->website->db($config['db'], $server)->prepare('INSERT INTO ' . $config['table'] . ' (' . $config['identifier_column'] . ', ' . $config['column'] . ') values (:user, :wcoins)');
                $stmt->execute([':user' => $user, ':wcoins' => $amount]);
            }
        }

        public function remove_wcoins($user, $server, $amount = 0, $config = [])
        {
            $stmt = $this->website->db($config['db'], $server)->prepare('UPDATE ' . $config['table'] . ' SET ' . $config['column'] . ' = ' . $config['column'] . ' - :wcoins WHERE ' . $config['identifier_column'] . ' = :account');
            $stmt->execute([':wcoins' => $amount, ':account' => $user]);
        }

        public function get_wcoins($user, $server, $config = [])
        {
            $stmt = $this->website->db($config['db'], $server)->prepare('SELECT ' . $config['column'] . ' FROM ' . $config['table'] . ' WHERE ' . $config['identifier_column'] . ' = :account');
            $stmt->execute([':account' => $user]);
            if($wcoins = $stmt->fetch()){
                return $wcoins[$config['column']];
            }
            return false;
        }
    }
