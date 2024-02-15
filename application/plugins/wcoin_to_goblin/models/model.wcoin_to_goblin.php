<?php

    class Mwcoin_to_goblin extends model
    {
        private $characters = [];

        public function __contruct()
        {
            parent::__construct();
        }
		
		public function get_user_wcoins_balance($account, $server)
        {
            $stmt = $this->website->db('game', $server)->prepare('SELECT GoblinPoint FROM CashShopData WHERE AccountId = :account');
            $stmt->execute([':account' => $account]);
            if($wcoins = $stmt->fetch()){
                return $wcoins['GoblinPoint'];
            }
            return false;
        }
		
		public function add_goblinpoint($amount = 0, $account, $server)
        {
            $stmt = $this->website->db('game', $server)->prepare('UPDATE CashShopData SET WcoinC = WcoinC + :wcoins WHERE AccountId = :account');
            $stmt->execute([':wcoins' => $amount, ':account' => $account]);
            if($stmt->rows_affected() == 0){
                $stmt = $this->website->db('game', $server)->prepare('INSERT INTO CashShopData (AccountId, WcoinC, WcoinP, GoblinPoint) values (:user, :wcoins, 0, 0)');
                $stmt->execute([':user' => $account, ':wcoins' => $amount]);
            }
        }

        public function remove_wcoins($amount = 0, $account, $server)
        {
            $stmt = $this->website->db('game', $server)->prepare('UPDATE CashShopData SET GoblinPoint = GoblinPoint - :wcoins WHERE AccountId = :account');
            $stmt->execute([':wcoins' => $amount, ':account' => $account]);
        }

        public function check_connect_stat($account, $server)
        {
            $stmt = $this->website->db('account', $server)->prepare('SELECT ConnectStat FROM MEMB_STAT WHERE memb___id = :user');
            $stmt->execute([':user' => $account]);
            if($status = $stmt->fetch()){
                return ($status['ConnectStat'] == 0);
            }
            return true;
        }
    }
