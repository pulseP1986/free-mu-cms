<?php

    class Mgift_code extends model
    {
        public $error = false, $vars = [], $characters = [], $char_info = [], $errors = [];

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

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function checkCode($code){
			return $this->website->db('web')->query('SELECT id, code, expires, max_uses_total, uses_left, max_uses_by_user, max_uses_by_char, min_lvl, min_mlvl, min_res, min_gres, code_type, code_reward_currency, code_reward_vip, code_reward_items, char_class, server FROM DmN_Gift_Codes WHERE code = \''.$this->website->db('web')->sanitize_var($code).'\'')->fetch();
		}

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function checkCodeWheel($code, $server){
			if($this->website->db('web')->check_if_table_exists('DmN_Wheel_Of_Fotune_Rewards')){
				return $this->website->db('web')->query('SELECT id, reward_id, account, is_claimed FROM DmN_Wheel_Of_Fotune_Rewards WHERE code = \''.$this->website->db('web')->sanitize_var($code).'\' AND server = \''.$this->website->db('web')->sanitize_var($server).'\'')->fetch();
			}
			return false;
		}

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function checkCodeById($id){
			return $this->website->db('web')->query('SELECT id, code, expires, max_uses_total, uses_left, max_uses_by_user, max_uses_by_char, min_lvl, min_mlvl, min_res, min_gres, code_type, code_reward_currency, code_reward_vip, code_reward_items, char_class, server, date_generated FROM DmN_Gift_Codes WHERE id = '.$this->website->db('web')->sanitize_var($id).'')->fetch();
		}

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function deleteCode($id){
			$this->website->db('web')->query('DELETE FROM DmN_Gift_Codes WHERE id = '.$this->website->db('web')->sanitize_var($id).'');
		}

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function listCodes(){
			return $this->website->db('web')->query('SELECT id, code, expires, max_uses_total, uses_left, char_class, server FROM DmN_Gift_Codes ORDER BY date_generated DESC')->fetch_all();
		}
		
		public function checkUsesByCharacter($coupon, $id, $user, $server){
			return $this->website->db('web')->snumrows('SELECT COUNT(id) AS count FROM DmN_Gift_Codes_Log WHERE code = \''.$this->website->db('web')->sanitize_var($coupon).'\' AND character = '.$this->website->db('web')->sanitize_var($id).' AND account = \''.$this->website->db('web')->sanitize_var($user).'\' AND server = \''.$this->website->db('web')->sanitize_var($server).'\'');
		}
		
		public function checkUsesByAccount($coupon, $user, $server){
			return $this->website->db('web')->snumrows('SELECT COUNT(id) AS count FROM DmN_Gift_Codes_Log WHERE code = \''.$this->website->db('web')->sanitize_var($coupon).'\' AND account = \''.$this->website->db('web')->sanitize_var($user).'\' AND server = \''.$this->website->db('web')->sanitize_var($server).'\'');
		}

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function createCode($coupon, $server, $valid, $max_uses, $max_uses_by_user, $max_uses_by_char, $min_lvl, $min_mlvl, $min_res, $min_gres, $coupon_type, $reward_amount, $vip_type, $item, $char_class){
			$stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Gift_Codes (code, expires, max_uses_total, uses_left, max_uses_by_user, max_uses_by_char, min_lvl, min_mlvl, min_res, min_gres, code_type, code_reward_currency, code_reward_vip, code_reward_items, server, date_generated, char_class) VALUES (:code, :expires, :max_uses_total, :uses_left, :max_uses_by_user, :max_uses_by_char, :min_lvl, :min_mlvl, :min_res, :min_gres, :code_type, :code_reward_currency, :code_reward_vip, :code_reward_items, :server, :date_generated, :char_class)');
			$stmt->execute([
				':code' => $coupon,
				':expires' => strtotime($valid),
				':max_uses_total' => $max_uses,
				':uses_left' => $max_uses,
				':max_uses_by_user' => $max_uses_by_user,
				':max_uses_by_char' => $max_uses_by_char,
				':min_lvl' => $min_lvl,
				':min_mlvl' => $min_mlvl,
				':min_res' => $min_res,
				':min_gres' => $min_gres,
				':code_type' => $coupon_type,
				':code_reward_currency' => $reward_amount,
				':code_reward_vip' => ($coupon_type == 8) ? $vip_type : 0,
				':code_reward_items' => json_encode($item),
				':server' => implode(',', $server),
				':date_generated' => time(),
				':char_class' => $char_class
			]);			
	   }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM	   
	   public function updateCode($server, $valid, $max_uses, $uses_left, $max_uses_by_user, $max_uses_by_char, $min_lvl, $min_mlvl, $min_res, $min_gres, $coupon_type, $reward_amount, $vip_type, $item, $char_class, $id){
		   $stmt = $this->website->db('web')->prepare('UPDATE DmN_Gift_Codes SET expires = :expires, max_uses_total = :max_uses_total, uses_left = :uses_left, max_uses_by_user = :max_uses_by_user, max_uses_by_char = :max_uses_by_char, min_lvl = :min_lvl, min_mlvl = :min_mlvl, min_res = :min_res, min_gres = :min_gres, code_type = :code_type, code_reward_currency = :code_reward_currency, code_reward_vip = :code_reward_vip, code_reward_items = :code_reward_items, char_class = :char_class, server = :server WHERE id = :id');
		   $stmt->execute([
				':expires' => strtotime($valid),
				':max_uses_total' => $max_uses,
				':uses_left' => $uses_left,
				':max_uses_by_user' => $max_uses_by_user,
				':max_uses_by_char' => $max_uses_by_char,
				':min_lvl' => $min_lvl,
				':min_mlvl' => $min_mlvl,
				':min_res' => $min_res,
				':min_gres' => $min_gres,
				':code_type' => $coupon_type,
				':code_reward_currency' => $reward_amount,
				':code_reward_vip' => ($coupon_type == 8) ? $vip_type : 0,
				':code_reward_items' => json_encode($item),
				':char_class' => $char_class,
				':server' => implode(',', $server),
				':id' => $id
			]);				
	   }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM	   
	   public function load_char_list($account, $server)
		{
			$stmt = $this->website->db('game', $server)->prepare('SELECT '.$this->website->get_char_id_col($server).' AS id, Name, ' . $this->reset_column($server) . $this->greset_column($server) . ' cLevel FROM Character WHERE AccountId = :account');
			$stmt->execute([':account' => $account]);
			$i = 0;
			while($row = $stmt->fetch()){
				$this->characters[] = [
					'id' => $row['id'], 
					'name' => $row['Name'], 
					'level' => $row['cLevel'], 
					'resets' => $row['resets'], 
					'gresets' => $row['grand_resets']
				];
				$i++;
			}
			if($i > 0){
				return $this->characters;
			} else{
				return false;
			}
		}
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function get_muun_content($char, $server = '') {
			$sql = (DRIVER == 'pdo_odbc') ? 'Items' : 'CONVERT(IMAGE, Items) AS Items';
			if($this->website->db('game', $server)->check_table('MuunInventory') > 0)
				$tbl = 'MuunInventory';
			else
				$tbl = 'IGC_Muun_Inventory';
			$stmt = $this->website->db('game', $server)->prepare('SELECT ' . $sql . ' FROM '.$tbl.' WHERE Name = :char');
			$stmt->execute([':char' => $this->website->c($char)]);
			$inv = $stmt->fetch();
			$stmt->close_cursor();
			if($inv != false){
				if(in_array(DRIVER, ['sqlsrv', 'pdo_sqlsrv', 'pdo_dblib'])){
					$unpack = unpack('H*', $inv['Items']);
					$this->vars['Items'] = $this->clean_hex($unpack[1]);
				}
				else{
					$this->vars['Items'] = $this->clean_hex($inv['Items']);
				}
			} 
			else{
				$this->vars['Items'] = false;
			}
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function find_free_muun_slot($char, $server) {
			$this->get_muun_content($char, $server);
            $items = str_split($this->vars['Items'], $this->website->get_value_from_server($server, 'item_size'));
			foreach($items AS $key => $val){
				if($key <= 3)
					continue;
				if(strtoupper($val) == str_pad("", $this->website->get_value_from_server($server, 'item_size'), "F")){
					return $key;
				}
			}
            return false;
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function generate_new_muun_by_slot($slot, $item = '', $server)
        {
            $hex = str_split($this->vars['Items'], $this->website->get_value_from_server($server, 'item_size'));
            if(isset($hex[$slot])){
                $hex[$slot] = ($item == '') ? str_pad("", $this->website->get_value_from_server($server, 'item_size'), "F") : $item;
            }
            $this->vars['new_hex'] = implode('', $hex);
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function update_muun_inventory($char , $server)
        {
			if($this->website->db('game', $server)->check_if_table_exists('MuunInventory'))
				$tbl = 'MuunInventory';
			else
				$tbl = 'IGC_Muun_Inventory';
            $stmt = $this->website->db('game', $server)->prepare('UPDATE '.$tbl.' SET Items = 0x' .  $this->vars['new_hex'] . ' WHERE Name = :char');
            $stmt->execute([':char' => $char]);
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function add_muun_period($char, $server, $serial, $time, $itemtype)
        {
			if($this->website->db('game', $server)->check_if_table_exists('MuunPeriodItem')){
				$stmt = $this->website->db('game', $server)->prepare('INSERT INTO MuunPeriodItem (ItemSerial, Time) VALUES (:ItemSerial, :Time)');
				$stmt->execute([':ItemSerial' => $serial, ':Time' => ($currTime + $time * 60)]);
			}
			else{
				if($this->website->db('game', $server)->check_if_table_exists('IGC_Muun_Period')){
					$stmt = $this->website->db('game', $server)->prepare('INSERT INTO IGC_Muun_Period (Name, ItemType, UsedInfo, Serial, GetItemDate, ExpireDate, ExpireDateConvert) VALUES (:char, :itemtype, 0, :serial, GETDATE(), DATEADD(minute, +'.$time.', GETDATE()), '.strtotime("+$time minutes").')');
					$stmt->execute([':char' => $char, ':itemtype' => $itemtype, ':serial' => $serial]);
				}
			}
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function inventory($char, $server){
			$sql = (DRIVER == 'pdo_odbc') ? 'Inventory' : 'CONVERT(IMAGE, Inventory) AS Inventory';
			$stmt = $this->website->db('game', $server)->prepare('SELECT Name, ' . $sql . ' FROM Character WHERE '.$this->website->get_char_id_col($server).' = :char');
			$stmt->execute([':char' => $char]);
			if($inv = $stmt->fetch()){
				if(in_array(DRIVER, ['sqlsrv', 'pdo_sqlsrv', 'pdo_dblib'])){
					$unpack = unpack('H*', $inv['Inventory']);

					$this->char_info['Inventory'] = $this->clean_hex($unpack[1]);
				}
				else{
					$this->char_info['Inventory'] = $this->clean_hex($inv['Inventory']);
				}
				$this->char_info['Name'] = $inv['Name'];
			}  
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function getInventoryContents($server){
			$items = [];
			$items_array = str_split($this->char_info['Inventory'], $this->website->get_value_from_server($server, 'item_size'));
			for($a = 12; $a < 76; $a++){
				$items[$a] = $items_array[$a];
			}
			return $items;	
		}	

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function check_inventory($server){
            return (strtoupper(substr($this->char_info['Inventory'], $this->website->get_value_from_server($server, 'item_size') * 12, $this->website->get_value_from_server($server, 'item_size') * 64)) === str_repeat('F', $this->website->get_value_from_server($this->session->userdata(['user' => 'server']), 'item_size') * 64));
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function updateInventorySlots($slots, $server){
			$items_array = str_split($this->char_info['Inventory'], $this->website->get_value_from_server($server, 'item_size'));
			for($a = 12; $a < 76; $a++){
				if(in_array($a, $slots)){
					$items_array[$a] = str_repeat('F', $this->website->get_value_from_server($server, 'item_size'));
				}
			}
			return $items_array;
		}

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function addItemsToInventory($items, $server){
			$items_array = str_split($this->char_info['Inventory'], $this->website->get_value_from_server($server, 'item_size'));
			for($a = 12; $a < 76; $a++){
				if(isset($items[$a-12]) && $items[$a-12] != str_repeat('F', $this->website->get_value_from_server($server, 'item_size'))){
					$items_array[$a] = $items[$a-12];
				}
			}
			return $items_array;
		}

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function updateInventory($char, $server, $items){
			$stmt = $this->website->db('game', $server)->prepare('UPDATE Character SET Inventory = 0x' . implode('', $items) . ' WHERE '.$this->website->get_char_id_col($server).' = :char');
            $stmt->execute([':char' => $char]);
		}
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
		public function addExpirableItem($guid, $server, $name, $index, $time, $serial, $currTime, $effectType = 0, $effect1 = 0, $effect2 = 0, $itemType = 2){
			if($this->website->db('game', $server)->check_if_table_exists('T_PeriodItemInfo')){
				$this->website->db('game', $server)->query('EXEC WZ_PeriodItemInsert '.$guid.', \''.$name.'\', '.$index.', '.$effectType.', '.$effect1.', '.$effect2.', '.$time.', \''.date('Y-m-d H:i:s', ($currTime + $time * 60)).'\'');
				return;
			}
			if($this->website->db('game', $server)->check_if_table_exists('CashShopPeriodicItem')){
				$stmt = $this->website->db('game', $server)->prepare('INSERT INTO CashShopPeriodicItem (ItemSerial, Time) VALUES (:ItemSerial, :Time)');
				$stmt->execute([':ItemSerial' => $serial, ':Time' => ($currTime + $time * 60)]);
				return;
			}
			if($this->website->db('game', $server)->check_if_table_exists('CashShopPeriodItem')){
				$stmt = $this->website->db('game', $server)->prepare('INSERT INTO CashShopPeriodItem (ItemSerial, Time) VALUES (:ItemSerial, :Time)');
				$stmt->execute([':ItemSerial' => $serial, ':Time' => ($currTime + $time * 60)]);
				return;
			}
			if($this->website->db('game', $server)->check_if_table_exists('IGC_PeriodItemInfo')){
				$this->website->db('game', $server)->query('EXEC IGC_PeriodItemInsertEx '.$guid.', \''.$name.'\', '.$itemType.', '.$index.', '.$effectType.', '.$effect1.', '.$effect2.', '.$serial.', '.($time * 60).', '.$currTime.', '.($currTime + $time * 60).'');
				return;
			}
		}

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function check_space_inventory($items, $item_x, $item_y, $multiplier = 64, $size = 32, $hor = 8, $ver = 8, $add_to_slot = false, $iteminfo, $takenSlots = []){
            $spots = str_repeat('0', $multiplier);
			
			if(!empty($takenSlots)){
				$spotsArr = str_split($spots, 1);
				foreach($takenSlots AS $key => $val){
					$spotsArr[$key] = 1;
				}
				$spots = implode('', $spotsArr);
			}
			
            $items_array = $items;
            for($i = 0; $i < $multiplier; ++$i){
                if($items_array[$i] != str_repeat('F', $size) && !empty($items_array[$i])){
                    $iteminfo->itemData($items_array[$i]);
                    if($iteminfo->getX() == false || $iteminfo->getY() == false){
                        $this->errors[] = sprintf(__('Found unknown item in inventory please remove it first. Slot: %d') . $i);
                        return null;
                    }
                    $y = 0;
                    while($y < $iteminfo->getY()){
                        $y++;
                        $x = 0;
                        while($x < $iteminfo->getX()){
                            $spots = substr_replace($spots, '1', ($i + $x) + (($y - 1) * $hor), 1);
                            $x++;
                        }
                    }
                }
            }
            for($y2 = 0; $y2 <= $ver - $item_y; $y2++){
                for($x2 = 0; $x2 <= $hor - $item_x; $x2++){
                    if($this->search($x2, $y2, $item_x, $item_y, $spots, $hor)){
                        if(!$add_to_slot){
                            return $x2 + ($y2 * $hor);
                        } else{
                            if($add_to_slot == ($x2 + ($y2 * $hor)))
                                return ($x2 + ($y2 * $hor));
                        }
                    }
                }
            }
            $this->errors[] = __('Please free up space in your inventory.');
            return null;
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function search($x, $y, $item_w, $item_h, &$spots, $vault_w){
            for($yy = 0; $yy < $item_h; $yy++){
                for($xx = 0; $xx < $item_w; $xx++){
                    if($spots[$x + $xx + (($y + $yy) * $vault_w)] != '0')
                        return false;
                }
            }
            return true;
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function logGiftCode($code, $character, $user, $server){
			$stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Gift_Codes_Log (code, account, server, character, date_used) VALUES (:code, :account, :server, :character, :date_used)');
			$stmt->execute([
				':code' => $code,
				':account' => $user,
				':server' => $server,
				':character' => $character,
				':date_used' => time()
			]);
		}

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function setUsesLeft($coupon){
			$this->website->db('web')->query('UPDATE DmN_Gift_Codes SET uses_left = uses_left - 1 WHERE  code = \''.$this->website->db('web')->sanitize_var($coupon).'\'');
		}
		
		public function load_logs($page = 1, $per_page = 25, $acc = '', $server = 'All')
        {
            if(($acc == '' || $acc == '-') && $server == 'All')
                $items = $this->website->db('web')->query('SELECT Top ' . $this->website->db('web')->sanitize_var($per_page) . '  id, code, account, server, character, date_used FROM DmN_Gift_Codes_Log WHERE id Not IN (SELECT Top ' . $this->website->db('web')->sanitize_var($per_page * ($page - 1)) . ' id FROM DmN_Gift_Codes_Log ORDER BY id DESC) ORDER BY id DESC'); 
			else{
                if(($acc != '' && $acc != '-') && $server == 'All')
                    $items = $this->website->db('web')->query('SELECT Top ' . $this->website->db('web')->sanitize_var($per_page) . ' id, code, account, server, character, date_used FROM DmN_Gift_Codes_Log WHERE account like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' AND id Not IN (SELECT Top ' . $this->website->db('web')->sanitize_var($per_page * ($page - 1)) . ' id FROM DmN_Gift_Codes_Log WHERE account like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' ORDER BY id DESC) ORDER BY id DESC'); else
				$items = $this->website->db('web')->query('SELECT Top ' . $this->website->db('web')->sanitize_var($per_page) . ' id, code, account, server, character, date_used FROM DmN_Gift_Codes_Log WHERE account like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\' AND id Not IN (SELECT Top ' . $this->website->db('web')->sanitize_var($per_page * ($page - 1)) . ' id DmN_Gift_Codes_Log WHERE account like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\' ORDER BY id DESC) ORDER BY id DESC');
            }
			$logs = [];
            foreach($items->fetch_all() as $value){
				$charData = $this->check_char($value['character'], $value['account'], $value['server'], true);
				$logs[] = [
					'code' => $value['code'],  
					'account' => htmlspecialchars($value['account']), 
					'char' => $charData['Name'],
					'server' => htmlspecialchars($value['server']), 
					'used' => date(DATETIME_FORMAT, $value['date_used'])
				];
            }
            return $logs;
        }

        public function count_total_logs($acc = '', $server = 'All')
        {
            $sql = '';
            if($acc != '' && $acc != '-'){
                $sql .= 'WHERE account like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\'';
                if($server != 'All'){
                    $sql .= ' AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\'';
                }
            }
            $count = $this->website->db('web')->snumrows('SELECT COUNT(id) AS count FROM DmN_Gift_Codes_Log ' . $sql . '');
            return $count;
        }
		
		private function reset_column($server = '')
		{
			$resets = $this->config->values('table_config', [$server, 'resets', 'column']);
			if($resets && $resets != ''){
				return $resets . ' AS resets,';
			}
			return '0 AS resets,';
		}

		private function greset_column($server = '')
		{
			$grand_resets = $this->config->values('table_config', [$server, 'grand_resets', 'column']);
			if($grand_resets && $grand_resets != ''){
				return $grand_resets . ' AS grand_resets,';
			}
			return '0 AS grand_resets,';
		}

		public function check_char($char, $account, $server, $byId = true)
        {
			$check = ($byId == true) ? $this->website->get_char_id_col($server) : 'Name';
            $stmt = $this->website->db('game', $server)->prepare('SELECT Name, '.$this->reset_column($server) . $this->greset_column($server) . ' cLevel, Class FROM Character WHERE AccountId = :user AND '.$check.' = :char');
            $stmt->execute([':user' => $account, ':char' => $char]);
            return $stmt->fetch();
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function load_master_level($char, $server)
        {
            if($this->config->values('table_config', [$server, 'master_level', 'column']) != false){
                $stmt = $this->website->db('game', $server)->prepare('SELECT ' . $this->config->values('table_config', [$server, 'master_level', 'column']) . ' AS mlevel FROM ' . $this->config->values('table_config', [$server, 'master_level', 'table']) . ' WHERE ' . $this->config->values('table_config', [$server, 'master_level', 'identifier_column']) . ' = :char');
                $stmt->execute([':char' => $char]);
                $mlevel = $stmt->fetch();
                //$stmt->close_cursor();
                if($mlevel){
                    return $mlevel['mlevel'];
                }
            }
            return 0;
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function add_zen($money, $char, $user, $server)
        {
            $stmt = $this->website->db('game', $server)->prepare('UPDATE Character SET Money = Money + :money WHERE AccountId = :account AND '.$this->website->get_char_id_col($server).' = :char');
			$stmt->execute([':money' => (int)$money, ':account' => $user, ':char' => $char]);
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function add_wcoins($amount = 0, $user, $server, $id, $config = [])
        {
            $acc = (in_array($config['identifier_column'], ['MemberGuid', 'memb_guid'])) ? $id : $user;
            $stmt = $this->website->db($config['db'], $server)->prepare('UPDATE ' . $config['table'] . ' SET ' . $config['column'] . ' = ' . $config['column'] . ' + :wcoins WHERE ' . $config['identifier_column'] . ' = :account');
            $stmt->execute([':wcoins' => $amount, ':account' => $acc]);
            if($stmt->rows_affected() == 0){
                $stmt = $this->website->db($config['db'], $server)->prepare('INSERT INTO ' . $config['table'] . ' (' . $config['identifier_column'] . ', ' . $config['column'] . ') values (:user, :wcoins)');
                $stmt->execute([':user' => $acc, ':wcoins' => $amount]);
            }
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function add_goblinpoints($amount = 0, $user, $server, $id, $config = [])
        {
            $acc = (in_array($config['identifier_column'], ['MemberGuid', 'memb_guid'])) ? $id : $user;
            $stmt = $this->website->db($config['db'], $server)->prepare('UPDATE ' . $config['table'] . ' SET ' . $config['column'] . ' = ' . $config['column'] . ' + :wcoins WHERE ' . $config['identifier_column'] . ' = :account');
            $stmt->execute([':wcoins' => $amount, ':account' => $acc]);
            if($stmt->rows_affected() == 0){
                $stmt = $this->website->db($config['db'], $server)->prepare('INSERT INTO ' . $config['table'] . ' (' . $config['identifier_column'] . ', ' . $config['column'] . ') values (:user, :wcoins)');
                $stmt->execute([':user' => $acc, ':wcoins' => $amount]);
            }
        }

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function checkRuud($char, $user, $server){
			if($this->website->db('game', $server)->check_if_column_exists('RuudMoney', 'Character') != false){
				$ruud = 'RuudMoney AS Ruud';
			}
			else{
				$ruud = 'Ruud';
			}
			$stmt = $this->website->db('game', $server)->prepare('SELECT '.$ruud.' FROM Character WHERE AccountId = :account AND '.$this->website->get_char_id_col($server).' = :char');
			$stmt->execute([':account' => $user, ':char' => $char]);
			return $stmt->fetch();
		}

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		public function add_ruud($money, $char, $user, $server)
        {
			if($this->website->db('game', $server)->check_if_column_exists('RuudMoney', 'Character') != false){
				$ruud = 'RuudMoney';
			}
			else{
				$ruud = 'Ruud';
			}
            $stmt = $this->website->db('game', $server)->prepare('UPDATE Character SET '.$ruud.' = '.$ruud.' + :money WHERE AccountId = :account AND '.$this->website->get_char_id_col($server).' = :char');
			$stmt->execute([':money' => (int)$money, ':account' => $user, ':char' => $char]);
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

		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM		
		private function clean_hex($data)
        {
            if(substr_count($data, "\0")){
                $data = str_replace("\0", '', $data);
            }
            return strtoupper($data);
        }

    }
