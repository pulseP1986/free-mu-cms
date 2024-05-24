<?php
    in_file();

    class Monline_exchange extends model
    {
        public $vars = [];

        public function __contruct(){
            parent::__construct();
        }

        public function __set($key, $val){
            $this->vars[$key] = $val;
        }

        public function __isset($name){
            return isset($this->vars[$name]);
        }
		
		public function load_online_hours($user, $server){
            $stmt = $this->website->db('web')->prepare('SELECT SUM(OnlineMinutes) AS OnlineMinutes FROM DmN_OnlineCheck WHERE memb___id = :acc ' . $this->website->server_code($this->website->get_servercode($server)) . '');
            $stmt->execute([':acc' => $user]);
            return $stmt->fetch();
        }

		public function exchange_online_hours($user, $server, $minutes_left = 0){
			$stmt = $this->website->db('web')->prepare('UPDATE DmN_OnlineCheck SET OnlineMinutes = 0 WHERE  memb___id = :acc ' . $this->website->server_code($this->website->get_servercode($server)) . '');
			$stmt->execute([':acc' => $user]);
			if($minutes_left > 0){
				$stmt = $this->website->db('web')->prepare('UPDATE DmN_OnlineCheck SET OnlineMinutes = :minutes WHERE memb___id = :acc AND ServerName = :server_name');
				$stmt->execute([':minutes' => $minutes_left, ':acc' => $user, ':server_name' => $this->website->get_first_server_code($server)]);
			}
        }
    }
