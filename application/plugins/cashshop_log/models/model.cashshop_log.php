<?php

    class Mcashshop_log extends model
    {
		private $cache_name = '';


        public function __contruct()
        {
            parent::__construct();
        }
		
		public function getLogs($user, $server){
			if($this->website->db('game', $server)->check_table('T_InGameShop_Log') > 0){
				$stmt = $this->website->db('game', $server)->prepare('SELECT ID1, ID2, ID3, Price, CoinType, BuyDate FROM T_InGameShop_Log WHERE AccountID = :user ORDER BY BuyDate DESC');
				$stmt->execute([':user' => $user]);
				$logs =  $stmt->fetch_all();
				$logsParsed = [];
				
				static $cashItemInfo = null;
				libxml_use_internal_errors(true);
				
				$this->load->lib('iteminfo');
				
				if(!empty($logs)){
					if($cashItemInfo == null)
						$cashItemInfo = simplexml_load_file(APP_PATH . DS . 'data' . DS . 'ServerData' . DS . 'CashItem_Info.xml');
					if($cashItemInfo === false){
						$err = 'Failed loading XML<br>';
						foreach(libxml_get_errors() as $error){
							$err .= $error->message . '<br>';
						}
						writelog('[Server File Parser] Unable to parse xml file: ' . $err, 'system_error');
					}
					
					foreach($logs AS $log){
						$itemData = $cashItemInfo->xpath("//CashItemInfo/Item[@GUID='" . $log['ID2'] . "'][@ID='" . $log['ID3'] . "']");
						if($itemData != false){
							if($this->iteminfo->setItemData((string)$itemData[0]->attributes()->Index, (string)$itemData[0]->attributes()->Cat, $this->website->get_value_from_server($server, 'item_size'))){
								$name  = $this->iteminfo->getNameStyle(true);
							}
							else{
								$name = __('Unknown');
							}
						}
						else{
							$name = __('Unknown guid: '.$log['ID2'].', id: '.$log['ID3'].'');
						}
						$logsParsed[] = [
							'Cointype' => $log['CoinType'],
							'Price' => $log['Price'],
							'BuyDate' => $log['BuyDate'],
							'name' => $name
						];
					}
				}
				return $logsParsed;
			}		
			return false;	
		}
		
		private function check_cache($name, $identifier, $server, $time = 360)
        {
            if($this->npc_filter == true){
                $this->cache_name = $name . '#' . $server . '#' . $this->c_npc . '#' . $this->top;
            } else{
                $this->cache_name = $name . '#' . $server . '#' . $this->top;
            }
            $this->website->check_cache($this->cache_name, $identifier, $time);
        }
    }
