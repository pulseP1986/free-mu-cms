<?php
    in_file();

    class Mcurrency_market extends model
    {
        public $error = false, $vars = [], $characters = [], $total;
        private $price, $per_page, $items, $char_list = [], $pos;

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

        public function load_char_list($account, $server)
        {
            $stmt = $this->website->db('game', $server)->prepare('SELECT Name, cLevel, Class FROM Character WHERE AccountId = :account');
            $stmt->execute([':account' => $account]);
            $i = 0;
            while($row = $stmt->fetch()){
                $this->characters[] = ['name' => $row['Name'], 'level' => $row['cLevel'], 'Class' => $row['Class']];
                $i++;
            }
            if($i > 0){
                return $this->characters;
            } else{
                return false;
            }
        }       

        public function check_char($char, $account, $server)
        {
            $stmt = $this->website->db('game', $server)->prepare('SELECT Name FROM Character WHERE AccountId = :user AND Name = :char');
            $stmt->execute([':user' => $account, ':char' => $char]);
            return $stmt->fetch();
        }

        public function count_total_zen($server)
        {
            $this->total = $this->website->db('web')->snumrows('SELECT COUNT(id) AS count FROM DmN_Currency_Market WHERE active_till > GETDATE() AND sold != 1 AND removed != 1 AND reward_type = 3 AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\'');
        }
		
		public function count_total_credits($server)
        {
			$rewardType = '';
			if(isset($_SESSION['filters'])){
				$rewardType = 'reward_type IN ('.$this->website->db('web')->sanitize_var($_SESSION['filters']).') AND ';
			}
            $this->total = $this->website->db('web')->snumrows('SELECT COUNT(id) AS count FROM DmN_Currency_Market WHERE '.$rewardType.'active_till > GETDATE() AND sold != 1 AND removed != 1 AND reward_type IN(1,2) AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\'');
        }

        public function load_market_zen($page, $per_page = 25, $server, $tax = 0)
        {
            $this->per_page = ($page <= 1) ? 0 : $per_page * ($page - 1);
			$order = 'id DESC';
			if(isset($_SESSION['zen_filder']) && $_SESSION['zen_filder'] == 1){
				$order = 'reward DESC';
			}
			if(isset($_SESSION['zen_filder']) && $_SESSION['zen_filder'] == 0){
				$order = 'reward ASC';
			}
            $this->items = $this->website->db('web')->query('SELECT Top ' . $this->website->db('web')->sanitize_var($per_page) . ' id, add_date, active_till, price, price_type, reward, reward_type, seller FROM DmN_Currency_Market WHERE active_till > GETDATE() AND sold != 1  AND removed != 1 AND reward_type = 3 AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\' AND id Not IN (SELECT Top ' . $this->website->db('web')->sanitize_var($this->per_page) . ' id FROM DmN_Currency_Market WHERE active_till > GETDATE() AND sold != 1  AND removed != 1 AND reward_type = 3 AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\' ORDER BY id DESC) ORDER BY '.$order.'');
            $this->pos = ($page == 1) ? 1 : (int)(($page - 1) * $per_page) + 1;
			$data = [];
            foreach($this->items->fetch_all() as $value){
                $data[] = [
					'icon' => (date("F j, Y", strtotime($value['add_date'])) == date("F j, Y", time())) ? '<img src="' . $this->config->base_url . 'assets/' . $this->config->config_entry('main|template') . '/images/new.png" />' : $this->pos, 
					'price' => round(($value['price'] / 100) * $tax + $value['price']) . ' ' . $this->website->translate_credits($value['price_type'], $server), 
					'reward' => $this->website->zen_format($value['reward']) . ' Zen', 
					'id' => $value['id'], 
					'pos' => $this->pos, 
					'seller' => $value['seller'], 
					'end' => date("F j, Y", strtotime($value['active_till']))
					
				];
                $this->pos++;
            }
            return $data;
        }
		
		public function load_market_credits($page, $per_page = 25, $server, $tax = 0)
        {
            $this->per_page = ($page <= 1) ? 0 : $per_page * ($page - 1);
            $order = 'id DESC';
			if(isset($_SESSION['credits_filder']) && $_SESSION['credits_filder'] == 1){
				$order = 'reward DESC';
			}
			if(isset($_SESSION['credits_filder']) && $_SESSION['credits_filder'] == 0){
				$order = 'reward ASC';
			}
			$rewardType = '';
			if(isset($_SESSION['filters'])){
				$rewardType = 'reward_type IN ('.$this->website->db('web')->sanitize_var($_SESSION['filters']).') AND ';
			}
			$this->items = $this->website->db('web')->query('SELECT Top ' . $this->website->db('web')->sanitize_var($per_page) . ' id, add_date, active_till, price, price_type, reward, reward_type, seller FROM DmN_Currency_Market WHERE '.$rewardType.'active_till > GETDATE() AND sold != 1  AND removed != 1 AND reward_type IN(1,2) AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\' AND id Not IN (SELECT Top ' . $this->website->db('web')->sanitize_var($this->per_page) . ' id FROM DmN_Currency_Market WHERE '.$rewardType.'active_till > GETDATE() AND sold != 1  AND removed != 1 AND reward_type IN(1,2) AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\' ORDER BY id DESC) ORDER BY '.$order.'');
            $this->pos = ($page == 1) ? 1 : (int)(($page - 1) * $per_page) + 1;
			$data = [];
            foreach($this->items->fetch_all() as $value){
                $data[] = [
					'icon' => (date("F j, Y", strtotime($value['add_date'])) == date("F j, Y", time())) ? '<img src="' . $this->config->base_url . 'assets/' . $this->config->config_entry('main|template') . '/images/new.png" />' : $this->pos, 
					'price' => ($value['price_type'] == 3) ? $this->website->zen_format(round(($value['price'] / 100) * $tax + $value['price'])) . ' Zen' : round(($value['price'] / 100) * $tax + $value['price']) . ' ' . $this->website->translate_credits($value['price_type'], $server), 
					'reward' => $value['reward'] . ' ' . $this->website->translate_credits($value['reward_type'], $server), 
					'id' => $value['id'], 
					'pos' => $this->pos, 
					'seller' => $value['seller'], 
					'end' => date("F j, Y", strtotime($value['active_till']))
					
				];
                $this->pos++;
            }
            return $data;
        }

        public function load_market_history($account, $server)
        {
            return $this->website->db('web')->query('SELECT id, add_date, active_till, price, price_type, reward, reward_type, seller, sold, removed FROM DmN_Currency_Market WHERE server = \'' . $this->web_db->sanitize_var($server) . '\' AND seller_acc = \'' . $this->website->db('web')->sanitize_var($account) . '\' ORDER BY id DESC')->fetch_all();
        }

        public function add_zen_into_market($amount, $mcharacter, $time, $payment_method, $price, $account, $server)
        {
            $stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Currency_Market (price, price_type, reward, reward_type, seller, add_date, active_till, seller_acc, server) VALUES (:price, :price_type, :reward, :reward_type, :seller, GETDATE(), :end_date, :seller_acc, :server)');
            return $stmt->execute([
				':price' => $price, 
				':price_type' => $payment_method, 
				':reward' => $amount, 
				':reward_type' => 3, 
				':seller' => $mcharacter, 
				':end_date' => date('Ymd H:i:s', strtotime('+' . $time . ' days', time())), 
				':seller_acc' => $account,
				':server' => $server,
			]);
        }
		
		public function add_credits_into_market($amount, $rtype, $mcharacter, $time, $ptype, $price, $account, $server)
        {
            $stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Currency_Market (price, price_type, reward, reward_type, seller, add_date, active_till, seller_acc, server) VALUES (:price, :price_type, :reward, :reward_type, :seller, GETDATE(), :end_date, :seller_acc, :server)');
            return $stmt->execute([
				':price' => $price, 
				':price_type' => $ptype, 
				':reward' => $amount, 
				':reward_type' => $rtype, 
				':seller' => $mcharacter, 
				':end_date' => date('Ymd H:i:s', strtotime('+' . $time . ' days', time())), 
				':seller_acc' => $account,
				':server' => $server,
			]);
        }

        public function update_sale_set_purchased($id, $buyer)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Currency_Market SET sold = 1, buyer = :buyer, purchase_date = GETDATE() WHERE id = :id');
            return $stmt->execute([':buyer' => $buyer, ':id' => $id]);
        }

        public function update_sale_set_removed($id, $buyer)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Currency_Market SET removed = 1, buyer = :buyer WHERE id = :id');
            return $stmt->execute([':buyer' => $buyer, ':id' => $id]);
        }

        public function check_sale_in_market($id, $server)
        {
            $stmt = $this->website->db('web')->prepare('SELECT TOP 1 add_date, active_till, server, price, price_type, reward, reward_type, sold, seller, removed, seller_acc FROM DmN_Currency_Market WITH (UPDLOCK) WHERE id = :id AND server = :server');
            $stmt->execute([':id' => $id, ':server' => $server]);
            return $stmt->fetch();
        }

        public function get_guid($user = '', $server)
        {
            $stmt = $this->website->db('account', $server)->prepare('SELECT memb_guid FROM MEMB_INFO WHERE memb___id = :user');
            $stmt->execute([':user' => $user]);
            $info = $stmt->fetch();
            return $info['memb_guid'];
        }

        public function add_account_log($log, $credits, $acc, $server)
        {
            $stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Account_Logs (text, amount, date, account, server, ip) VALUES (:text, :amount, GETDATE(), :acc, :server, :ip)');
            $stmt->execute([':text' => $log, ':amount' => $credits, ':acc' => $acc, ':server' => $server, ':ip' => $this->website->ip()]);
            $stmt->close_cursor();
        }
		
		public function load_logs($page = 1, $per_page = 25, $acc = '', $server = 'All')
        {
            if(($acc == '' || $acc == '-') && $server == 'All')
                $items = $this->website->db('web')->query('SELECT Top ' . $this->website->db('web')->sanitize_var($per_page) . '  id, server, price, price_type, reward, reward_type, seller_acc, buyer, purchase_date FROM DmN_Currency_Market WHERE sold = 1 AND id Not IN (SELECT Top ' . $this->website->db('web')->sanitize_var($per_page * ($page - 1)) . ' id FROM DmN_Currency_Market WHERE sold = 1 ORDER BY id DESC) ORDER BY id DESC'); 
			else{
                if(($acc != '' && $acc != '-') && $server == 'All')
                    $items = $this->website->db('web')->query('SELECT Top ' . $this->website->db('web')->sanitize_var($per_page) . ' id, server, price, price_type, reward, reward_type, seller_acc, buyer, purchase_date FROM DmN_Currency_Market WHERE sold = 1 AND (seller_acc like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' OR buyer like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\') AND id Not IN (SELECT Top ' . $this->website->db('web')->sanitize_var($per_page * ($page - 1)) . ' id FROM DmN_Currency_Market WHERE sold = 1 AND (seller_acc like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' OR buyer like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\') ORDER BY id DESC) ORDER BY id DESC'); else
				$items = $this->website->db('web')->query('SELECT Top ' . $this->website->db('web')->sanitize_var($per_page) . ' id, server, price, price_type, reward, reward_type, seller_acc, buyer, purchase_date FROM DmN_Currency_Market WHERE sold = 1  AND (seller_acc like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' OR buyer like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\') AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\' AND id Not IN (SELECT Top ' . $this->website->db('web')->sanitize_var($per_page * ($page - 1)) . ' id DmN_Currency_Market WHERE sold = 1 AND (seller_acc like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' OR buyer like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\') AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\' ORDER BY id DESC) ORDER BY id DESC');
            }
			$logs = [];
            foreach($items->fetch_all() as $value){
				$logs[] = [
					'price' => ($value['price_type'] == 3) ? $this->website->zen_format($value['price']) : $value['price'], 
					'price_type' => $this->website->translate_credits($value['price_type'], $value['server']), 
					'reward' => ($value['reward_type'] == 3) ? $this->website->zen_format($value['reward']) : $value['reward'], 
					'reward_type' => $this->website->translate_credits($value['reward_type'], $value['server']), 
					'seller' => htmlspecialchars($value['seller_acc']), 
					'buyer' => htmlspecialchars($value['buyer']), 
					'server' => htmlspecialchars($value['server']), 
					'purchase_date' => date(DATETIME_FORMAT, strtotime($value['purchase_date']))
				];
            }
            return $logs;
        }

        public function count_total_logs($acc = '', $server = 'All')
        {
            $sql = 'WHERE sold = 1';
            if($acc != '' && $acc != '-'){
                $sql .= ' AND(seller_acc like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' OR buyer like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\')';
                if($server != 'All'){
                    $sql .= ' AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\'';
                }
            }
            $count = $this->website->db('web')->snumrows('SELECT COUNT(id) AS count FROM DmN_Currency_Market ' . $sql . '');
            return $count;
        }
    }
