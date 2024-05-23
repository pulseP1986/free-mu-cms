<?php
    in_file();

    class Mhide_info extends model
    {
        public $error = false, $vars = [], $characters = [], $total_characters, $char_info = [], $gens_family;
        private $price, $per_page, $chars, $char_list = [], $pos;

        public function __contruct(){
            parent::__construct();
        }

        public function __set($key, $val){
            $this->vars[$key] = $val;
        }

        public function __isset($name){
            return isset($this->vars[$name]);
        }
		
		public function check_hide_time($user, $server){
            $stmt = $this->website->db('web')->prepare('SELECT until_date FROM DmN_Hidden_Chars WHERE account = :name AND server = :server');
            $stmt->execute([':name' => $user, ':server' => $server]);
            if($info = $stmt->fetch()){
                if($info['until_date'] > time()){
                    return date(DATETIME_FORMAT, $info['until_date']);
                } 
				else{
                    $this->delete_expired_hide($user, $server);
                    return false;
                }
            } 
			else{
                return false;
            }
        }
		
		public function delete_expired_hide($user, $server){
            $stmt = $this->website->db('web')->prepare('DELETE FROM DmN_Hidden_Chars WHERE account = :name AND server = :server');
            $stmt->execute([':name' => $user, ':server' => $server]);
        }
		
		public function add_hide($user, $server, $days){
            $stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Hidden_Chars (account, until_date, server) VALUES (:account, :until_date, :server)');
            $stmt->execute([':account' => $user, ':until_date' => time() + (3600 * 24) * $days, ':server' => $server]);
        }
		
		public function extend_hide($user, $server, $date, $days){
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Hidden_Chars SET until_date = :until_date WHERE account = :account AND server = :server');
            $stmt->execute([':until_date' => strtotime($date) + (3600 * 24) * $days, ':account' => $user, ':server' => $server]);
        }
    }
