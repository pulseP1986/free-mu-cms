<?php

    class Mslots extends model
    {
        private $logs = [], $mechanism;

        public function __contruct()
        {
            parent::__construct();
        }

        public function get_slots_on_server($server)
        {
            $count = $this->website->db('web')->snumrows('SELECT COUNT(id) AS count FROM DmN_Slots_Prizes WHERE server = \'' . $this->website->db('web')->sanitize_var($server) . '\' AND STATUS = 1');
            if($count > 0){
                return true;
            }
            return false;
        }

        public function get_prizes($server)
        {
            return $this->website->db('web')->query('SELECT id, reel1, reel2, reel3, payout_credits, payout_winnings  FROM DmN_Slots_Prizes WHERE server = \'' . $this->website->db('web')->sanitize_var($server) . '\' AND STATUS = 1 ORDER BY payout_winnings DESC, payout_credits DESC')->fetch_all();
        }

        public function increment_slot_machine_spins($userID, $server)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Slots_Users SET spins = spins + 1 WHERE id = :id AND server = :server');
            return $stmt->execute([':id' => $userID, ':server' => $server]);
        }

        private function check_user($userID, $server)
        {
            $stmt = $this->website->db('web')->prepare('SELECT TOP 1 id, spins, free_spins FROM DmN_Slots_Users WHERE id = :id AND server = :server');
            $stmt->execute([':id' => $userID, ':server' => $server]);
            return $stmt->fetch();
        }

        public function add_free_spins($userID, $server, $spins)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Slots_Users SET free_spins = free_spins + :free_spins WHERE id = :id AND server = :server');
            return $stmt->execute([':free_spins' => $spins, ':id' => $userID, ':server' => $server]);
        }

        public function decrease_free_spins($userID, $server, $spins)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Slots_Users SET free_spins = free_spins - :free_spins WHERE id = :id AND server = :server');
            return $stmt->execute([':free_spins' => $spins, ':id' => $userID, ':server' => $server]);
        }

        public function get_free_spins($userID, $account, $server, $spins = 0)
        {
            if(($data = $this->check_user($userID, $server)) != false){
                return $data['free_spins'];
            } else{
                $this->insert_user($userID, $account, $server, $spins);
                return false;
            }
        }

        private function insert_user($userID, $account, $server, $spins)
        {
            $stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Slots_Users (id, memb___id, spins, server, free_spins) VALUES (:id, :account, 1, :server, :free_spins)');
            return $stmt->execute([':id' => $userID, ':account' => $account, ':server' => $server, ':free_spins' => $spins]);
        }

        private function update_user($userID, $server)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Slots_Users SET spins = spins + 1 WHERE id = :id AND server = :server');
            return $stmt->execute([':id' => $userID, ':server' => $server]);
        }

        public function increase_winnings($userID, $payout, $server)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Slots_Users SET lifetime_winnings = lifetime_winnings + :payout WHERE id = :id AND server = :server');
            return $stmt->execute([':payout' => $payout, ':id' => $userID, ':server' => $server]);
        }

        public function lifetime_winnings($userID, $server)
        {
            $stmt = $this->website->db('web')->prepare('SELECT lifetime_winnings FROM DmN_Slots_Users WHERE id = :id AND server = :server');
            $stmt->execute([':id' => $userID, ':server' => $server]);
            return $stmt->fetch()['lifetime_winnings'];
        }

        public function random_mechanism($mechanism)
        {
            $this->mechanism = $mechanism;
        }

        public function spin($userID, $account, $server, $bet, $windowID)
        {
            if($this->mechanism == 2){
                $result = ['reels' => [$this->random_reel_spin($server, 1), $this->random_reel_spin($server, 2), $this->random_reel_spin($server, 3)]];
            } else if($this->mechanism == 1){
                $prizeID = $this->random_prize_spin($server);
                $result = $this->get_forced_spin($server, $prizeID);
            } else{
                throw new Exception('Invalid Random Mechanism');
            }
            $result['prize'] = null;
            $prizeID = $this->get_prize_for_reels($server, $result['reels']);
            if($prizeID != null){
                $prizeData = $this->prize_data($prizeID, $server);
                $result['prize'] = ['id' => $prizeID, 'payoutCredits' => $prizeData['payout_credits'] * $bet, 'payoutWinnings' => $prizeData['payout_winnings'] * $bet,];
                $this->prize_won($userID, $server, $prizeID);
            }
            $this->log_spin($userID, $account, $server, $windowID, "Spin", $bet, $result['reels'][0], $result['reels'][1], $result['reels'][2], ($result['prize'] != null) ? $result['prize']['id'] : null, ($result['prize'] != null) ? $result['prize']['payoutCredits'] : null, ($result['prize'] != null) ? $result['prize']['payoutWinnings'] : null);
            return $result;
        }

        private function random_reel_spin($server, $reel)
        {
            $outcomes = $this->reel_odds_table($server, $reel);
            $totalWeight = $outcomes[count($outcomes) - 1]['accWeight'];
            $r = rand() * $totalWeight / getrandmax();
            for($i = 0; $i < count($outcomes); $i++){
                if($outcomes[$i]['accWeight'] >= $r){
                    return $outcomes[$i]['outcome'];
                }
            }
        }

        private function random_prize_spin($server)
        {
            $prizes = $this->prize_odds_table($server);
            $r = rand() / getrandmax();
            for($i = 0; $i < count($prizes); $i++){
                if($prizes[$i]['accWeight'] >= $r){
                    return $prizes[$i]['id'];
                }
            }
            return null;
        }

        static $_PrizesCacheByServer = [];
        static $_PrizesCacheByID = [];
        static $_PrizeOddsCache = [];
        static $_ReelsCache = [];

        private function reel_odds_table($server, $reel)
        {
            $key = $server . "_" . $reel;
            if(!isset(self::$_ReelsCache[$key])){
                $reelData = [];
                $totalWeight = 0;
                $result = $this->website->db('web')->query('SELECT outcome, probability FROM DmN_Slots_Reels WHERE server = \'' . $this->website->db('web')->sanitize_var($server) . '\' AND STATUS = 1');
                while($row = $result->fetch()){
                    $totalWeight += $row["probability"];
                    $row["accWeight"] = $totalWeight;
                    $reelData[] = $row;
                }
                self::$_ReelsCache[$key] = $reelData;
            }
            return self::$_ReelsCache[$key];
        }

        private function prize_odds_table($server)
        {
            if(!isset(self::$_PrizeOddsCache[$server])){
                $prizeData = [];
                $totalWeight = 0;
                $result = $this->website->db('web')->query('SELECT id, probability FROM DmN_Slots_Prizes WHERE server = \'' . $this->website->db('web')->sanitize_var($server) . '\' AND STATUS = 1');
                while($row = $result->fetch()){
                    $totalWeight += $row["probability"];
                    $row["accWeight"] = $totalWeight;
                    $prizeData[] = $row;
                }
                self::$_PrizeOddsCache[$server] = $prizeData;
            }
            return self::$_PrizeOddsCache[$server];
        }

        private function get_forced_spin($server, $forcedPrizeID)
        {
            $matchedPrizeID = -1;
            $count = 0;
            if($forcedPrizeID != null){
                $rowForcedPrize = $this->prize_data($forcedPrizeID, $server);
            } else{
                $rowForcedPrize = ['reel1' => '*', 'reel2' => '*', 'reel3' => '*'];
            }
            while($matchedPrizeID != $forcedPrizeID){
                $reels = ['reels' => [$this->forced_reel_spin($server, $rowForcedPrize['reel1']), $this->forced_reel_spin($server, $rowForcedPrize['reel2']), $this->forced_reel_spin($server, $rowForcedPrize['reel3'])]];
                $matchedPrizeID = $this->get_prize_for_reels($server, $reels['reels']);
                $count++;
                if($count > 100){
                    break;
                }
            }
            return $reels;
        }

        private function prize_data($prizeID, $server)
        {
            if(!isset(self::$_PrizesCacheByID[$prizeID])){
                $this->load_prizes_cache($server);
            }
            return self::$_PrizesCacheByID[$prizeID];
        }

        private function load_prizes_cache($server)
        {
            $prizes = $this->website->db('web')->query('SELECT * FROM DmN_Slots_Prizes WHERE server = \'' . $this->website->db('web')->sanitize_var($server) . '\' AND STATUS = 1 ORDER BY payout_winnings DESC, payout_credits DESC')->fetch_all();
            foreach($prizes as $row){
                $row['reel1_unprocessed'] = $row['reel1'];
                $row['reel2_unprocessed'] = $row['reel2'];
                $row['reel3_unprocessed'] = $row['reel3'];
                $row['reel1'] = $this->pre_process_reel_rule($row['reel1']);
                $row['reel2'] = $this->pre_process_reel_rule($row['reel2']);
                $row['reel3'] = $this->pre_process_reel_rule($row['reel3']);
                if(!isset(self::$_PrizesCacheByServer[$row['server']])){
                    self::$_PrizesCacheByServer[$row['server']] = [];
                }
                self::$_PrizesCacheByServer[$row['server']][] = $row;
                self::$_PrizesCacheByID[$row['id']] = $row;
            }
        }

        private function pre_process_reel_rule($rule)
        {
            $rules = explode("/", $rule);
            $rules = array_map('trim', $rules);
            if(count($rules) == 1){
                $rules = $rules[0];
            }
            return $rules;
        }

        private function forced_reel_spin($server, $rule)
        {
            $randMax = $this->icons_per_reel($server) * 2 + 1;
            $reel = rand(2, $randMax) / 2;
            $count = 0;
            while(!$this->compare_reel($reel, $rule)){
                $reel = rand(2, $randMax) / 2;
                $count++;
                if($count > 100){
                    break;
                }
            }
            return $reel;
        }

        public function icons_per_reel($server)
        {
            return 6;
        }

        private function compare_reel($outcome, $rule)
        {
            if($rule == "*"){
                return true;
            }
            if($rule == "*.0"){
                return ($outcome == ((int)$outcome));
            }
            if($rule == "*.5"){
                return ($outcome != ((int)$outcome));
            }
            if(is_array($rule)){
                foreach($rule as $v){
                    if($this->compare_reel($outcome, $v) == true){
                        return true;
                    }
                }
                return false;
            }
            return ($outcome == $rule);
        }

        private function get_prize_for_reels($server, $reels)
        {
            $prizes = $this->prizes_for_server($server);
            foreach($prizes as $row){
                if($this->compare_reel($reels[0], $row['reel1']) && $this->compare_reel($reels[1], $row['reel2']) && $this->compare_reel($reels[2], $row['reel3'])){
                    return $row['id'];
                }
            }
            return null;
        }

        private function prizes_for_server($server)
        {
            if(!isset(self::$_PrizesCacheByServer[$server])){
                $this->load_prizes_cache($server);
            }
            return self::$_PrizesCacheByServer[$server];
        }

        public function prize_won($userID, $server, $prizeID)
        {
        }

        public function log_spin($userID, $account, $server, $windowID, $action, $bet = null, $reel1 = null, $reel2 = null, $reel3 = null, $prizeID = null, $payoutCredits = null, $payoutWinnings = null)
        {
            $fields = "date, user_id, memb___id, server, window_id, action";
            $values = "GETDATE(), " . $userID . ", '" . $this->website->db('web')->sanitize_var($account) . "', '" . $this->website->db('web')->sanitize_var($server) . "', '" . $this->website->db('web')->sanitize_var($windowID) . "', '" . $this->website->db('web')->sanitize_var($action) . "'";
            if($bet != null){
                $fields .= ", bet, reel1, reel2, reel3";
                $values .= ", " . $bet . ", " . $reel1 . ", " . $reel2 . ", " . $reel3;
            }
            if($prizeID != null){
                $fields .= ", prize_id, payout_credits, payout_winnings";
                $values .= ", " . $prizeID . ", " . $payoutCredits . ", " . $payoutWinnings;
            }
            $sql = "INSERT INTO DmN_Slots_Spins (" . $fields . ") VALUES (" . $values . ");";
            $this->website->db('web')->query($sql);
        }

        /**
         * Load logs
         *
         * @param int $page
         * @param int $per_page
         * @param string $acc
         * @param string $server
         *
         *
         */
        public function load_logs($page = 1, $per_page = 25, $acc = '', $server = 'All')
        {
            if(($acc == '' || $acc == '-') && $server == 'All')
                $items = $this->website->db('web')->query('SELECT Top ' . $this->website->db('web')->sanitize_var($per_page) . ' id, date, memb___id, server, bet, prize_id, payout_credits FROM DmN_Slots_Spins WHERE id Not IN (SELECT Top ' . $this->website->db('web')->sanitize_var($per_page * ($page - 1)) . ' id FROM DmN_Slots_Spins ORDER BY id DESC) ORDER BY id DESC'); else{
                if(($acc != '' && $acc != '-') && $server == 'All')
                    $items = $this->website->db('web')->query('SELECT Top ' . $this->website->db('web')->sanitize_var($per_page) . ' id, date, memb___id, server, bet, prize_id, payout_credits FROM DmN_Slots_Spins WHERE memb___id like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' AND id Not IN (SELECT Top ' . $this->website->db('web')->sanitize_var($per_page * ($page - 1)) . ' id FROM DmN_Slots_Spins WHERE memb___id like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' ORDER BY id DESC) ORDER BY id DESC'); else
                    $items = $this->website->db('web')->query('SELECT Top ' . $this->website->db('web')->sanitize_var($per_page) . ' id, date, memb___id, server, bet, prize_id, payout_credits FROM DmN_Slots_Spins WHERE memb___id like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\' AND id Not IN (SELECT Top ' . $this->website->db('web')->sanitize_var($per_page * ($page - 1)) . ' id DmN_Slots_Spins WHERE memb___id like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\' ORDER BY id DESC) ORDER BY id DESC');
            }
            foreach($items->fetch_all() as $value){
                $this->logs[] = [
					'id' => $value['id'], 
					'acc' => htmlspecialchars($value['memb___id']), 
					'server' => htmlspecialchars($value['server']), 
					'bet' => $value['bet'], 
					'prize_id' => $value['prize_id'], 
					'payout_credits' => $value['payout_credits'], 
					'date' => date(DATETIME_FORMAT, strtotime($value['date']))
				];
            }
            return $this->logs;
        }

        /**
         * Count total logs
         *
         * @param string $acc
         * @param string $server
         *
         *
         * @return int
         */
        public function count_total_logs($acc = '', $server = 'All')
        {
            $sql = '';
            if($acc != '' && $acc != '-'){
                $sql .= 'WHERE memb___id like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\'';
                if($server != 'All'){
                    $sql .= ' AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\'';
                }
            }
            $count = $this->website->db('web')->snumrows('SELECT COUNT(memb___id) AS count FROM DmN_Slots_Spins ' . $sql . '');
            return $count;
        }

        /**
         * find out account memb_guid
         *
         * @param string $account
         * @param string $server
         *
         *
         * @return bool
         */
        public function get_guid($account, $server)
        {
            $stmt = $this->website->db('account', $server)->prepare('SELECT memb_guid FROM MEMB_INFO WHERE memb___id = :account');
            $stmt->execute([':account' => $account]);
            $guid = $stmt->fetch();
            if($guid){
                return $guid['memb_guid'];
            }
            return false;
        }
		
		public function checkPrizes($server){
			$count = $this->website->db('web')->snumrows('SELECT COUNT(id) AS count FROM DmN_Slots_Prizes WHERE server = \'' . $this->website->db('web')->sanitize_var($server) . '\'');
			return $count;
		}
		
		public function checkReels($server){
			$count = $this->website->db('web')->snumrows('SELECT COUNT(id) AS count FROM DmN_Slots_Reels WHERE server = \'' . $this->website->db('web')->sanitize_var($server) . '\'');
			return $count;
		}
		
		public function insertPrizes($server){
			$this->website->db('web')->query("INSERT DmN_Slots_Prizes ([reel1], [reel2], [reel3], [payout_credits], [payout_winnings], [probability], [server], [status]) 
											VALUES
											(N'6', N'6', N'6', CAST(50.00 AS Decimal(10, 2)), CAST(50.00 AS Decimal(10, 2)), 0.0005, N'".$server."', 1),
											(N'4', N'4', N'4', CAST(30.00 AS Decimal(10, 2)), CAST(30.00 AS Decimal(10, 2)), 0.0025, N'".$server."', 1),
											(N'2', N'2', N'2', CAST(25.00 AS Decimal(10, 2)), CAST(25.00 AS Decimal(10, 2)), 0.009, N'".$server."', 1),
											(N'1/3', N'5/2', N'4/6', CAST(20.00 AS Decimal(10, 2)), CAST(20.00 AS Decimal(10, 2)), 0.01125, N'".$server."', 1),
											(N'5', N'5', N'5', CAST(15.00 AS Decimal(10, 2)), CAST(15.00 AS Decimal(10, 2)), 0.0233333, N'".$server."', 1),
											(N'1', N'1', N'1', CAST(12.00 AS Decimal(10, 2)), CAST(12.00 AS Decimal(10, 2)), 0.0257143, N'".$server."', 1),
											(N'3', N'3', N'3', CAST(8.00 AS Decimal(10, 2)), CAST(8.00 AS Decimal(10, 2)), 0.0316667, N'".$server."', 1),
											(N'1/3/5', N'1/3/5', N'1/3/5', CAST(3.00 AS Decimal(10, 2)), CAST(3.00 AS Decimal(10, 2)), 0.0514286, N'".$server."', 1),
											(N'*.5', N'*.5', N'*.5', CAST(1.00 AS Decimal(10, 2)), CAST(1.00 AS Decimal(10, 2)), 0.1, N'".$server."', 1)");
		}
		
		public function insertReels($server){
			$this->website->db('web')->query("INSERT DmN_Slots_Reels ([server], [reel], [outcome], [probability], [status])
											VALUES
											(N'".$server."', 1, CAST(1.0 AS Decimal(3, 1)), 0.16, 1),
											(N'".$server."', 1, CAST(1.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 1, CAST(2.0 AS Decimal(3, 1)), 0.14, 1),
											(N'".$server."', 1, CAST(2.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 1, CAST(3.0 AS Decimal(3, 1)), 0.17, 1),
											(N'".$server."', 1, CAST(3.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 1, CAST(4.0 AS Decimal(3, 1)), 0.11, 1),
											(N'".$server."', 1, CAST(4.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 1, CAST(5.0 AS Decimal(3, 1)), 0.15, 1),
											(N'".$server."', 1, CAST(5.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 1, CAST(6.0 AS Decimal(3, 1)), 0.06, 1),
											(N'".$server."', 1, CAST(6.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 2, CAST(1.0 AS Decimal(3, 1)), 0.16, 1),
											(N'".$server."', 2, CAST(1.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 2, CAST(2.0 AS Decimal(3, 1)), 0.14, 1),
											(N'".$server."', 2, CAST(2.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 2, CAST(3.0 AS Decimal(3, 1)), 0.17, 1),
											(N'".$server."', 2, CAST(3.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 2, CAST(4.0 AS Decimal(3, 1)), 0.11, 1),
											(N'".$server."', 2, CAST(4.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 2, CAST(5.0 AS Decimal(3, 1)), 0.15, 1),
											(N'".$server."', 2, CAST(5.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 2, CAST(6.0 AS Decimal(3, 1)), 0.06, 1),
											(N'".$server."', 2, CAST(6.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 3, CAST(1.0 AS Decimal(3, 1)), 0.16, 1),
											(N'".$server."', 3, CAST(1.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 3, CAST(2.0 AS Decimal(3, 1)), 0.14, 1),
											(N'".$server."', 3, CAST(2.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 3, CAST(3.0 AS Decimal(3, 1)), 0.17, 1),
											(N'".$server."', 3, CAST(3.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 3, CAST(4.0 AS Decimal(3, 1)), 0.11, 1),
											(N'".$server."', 3, CAST(4.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 3, CAST(5.0 AS Decimal(3, 1)), 0.15, 1),
											(N'".$server."', 3, CAST(5.5 AS Decimal(3, 1)), 0.035, 1),
											(N'".$server."', 3, CAST(6.0 AS Decimal(3, 1)), 0.06, 1),
											(N'".$server."', 3, CAST(6.5 AS Decimal(3, 1)), 0.035, 1)");
		}		
    }
