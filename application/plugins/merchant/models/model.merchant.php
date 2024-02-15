<?php

    class Mmerchant extends model
    {
        private $characters = [], $logs = [];

        public function __contruct()
        {
            parent::__construct();
        }

        /**
         * Load Merchant list
         *
         * @param bool $status
         *
         *
         * @return mixed
         */
        public function load_merchants()
        {
            return $this->website->db('web')->query('SELECT id, memb___id, name, contact, server, wallet, active FROM DmN_Merchant_List ORDER BY id ASC')->fetch_all();
        }

        /**
         * Check if account exists
         *
         * @param string $account
         * @param string $server
         *
         * @return mixed
         */
        public function check_account($account, $server)
        {
            $stmt = $this->website->db('account', $server)->prepare('SELECT TOP 1 memb_guid FROM MEMB_INFO WHERE memb___id = :account');
            $stmt->execute([':account' => $account]);
            return $stmt->fetch();
        }

        /**
         * Check if merchant already exists
         *
         * @param string $account
         * @param string $server
         * @param int $id
         *
         * @return mixed
         */
        public function check_merchant($account, $server, $id = -1)
        {
            $id_check = '';
            if($id != -1){
                $id_check = 'AND id != :id';
            }
            $stmt = $this->website->db('web')->prepare('SELECT TOP 1 id, active, wallet FROM DmN_Merchant_List WHERE memb___id = :account AND server = :server ' . $id_check . '');
            $data = [':account' => $account, ':server' => $server];
            if($id != -1){
                $data[':id'] = $id;
            }
            $stmt->execute($data);
            return $stmt->fetch();
        }

        /**
         * Check if merchant id exists
         *
         * @param int $id
         *
         * @return mixed
         */
        public function check_merchant_id($id = -1)
        {
            $stmt = $this->website->db('web')->prepare('SELECT TOP 1 id FROM DmN_Merchant_List WHERE id = :id');
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        }

        /**
         * Add new merchant
         *
         * @param string $account
         * @param string $name
         * @param string $contact
         * @param int $wallet
         * @param string $server
         *
         * @return mixed
         *
         */
        public function add_merchant($account, $name, $contact, $wallet, $server)
        {
            $stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Merchant_List (memb___id, name, contact, server, wallet) VALUES (:account, :name, :contact, :server, :wallet)');
            $stmt->execute([':account' => $account, ':name' => $name, ':contact' => $contact, ':server' => $server, ':wallet' => $wallet]);
            return $this->website->db('web')->last_insert_id();
        }

        /**
         * Edit existing merchant
         *
         * @param int $id
         * @param string $account
         * @param string $name
         * @param string $contact
         * @param int $wallet
         * @param string $server
         *
         *
         */
        public function edit_merchant($id, $account, $name, $contact, $wallet, $server)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Merchant_List SET memb___id = :account, name = :name, contact = :contact, server = :server, wallet = :wallet WHERE id = :id');
            $stmt->execute([':account' => $account, ':name' => $name, ':contact' => $contact, ':server' => $server, ':wallet' => $wallet, ':id' => $id]);
        }

        /**
         * Remove merchant
         *
         * @param int $id
         *
         *
         */
        public function delete_merchant($id)
        {
            $stmt = $this->website->db('web')->prepare('DELETE FROM DmN_Merchant_List WHERE id = :id');
            $stmt->execute([':id' => $id]);
        }

        /**
         * Enable / Disabled merchant
         *
         * @param int $id
         * @param int $status
         *
         *
         */
        public function change_status($id, $status)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Merchant_List SET active = :status WHERE id = :id');
            $stmt->execute([':status' => $status, ':id' => $id]);
        }

        /**
         * Load merchant logs
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
                $items = $this->website->db('web')->query('SELECT Top ' . $this->website->db('web')->sanitize_var($per_page) . ' id, merchant, amount, currency, account, server, date FROM DmN_Merchant_Logs WHERE id Not IN (SELECT Top ' . $this->website->db('web')->sanitize_var($per_page * ($page - 1)) . ' id FROM DmN_Merchant_Logs ORDER BY id DESC) ORDER BY id DESC'); else{
                if(($acc != '' && $acc != '-') && $server == 'All')
                    $items = $this->website->db('web')->query('SELECT Top ' . $this->website->db('web')->sanitize_var($per_page) . ' id, merchant, amount, currency, account, server, date FROM DmN_Merchant_Logs WHERE merchant like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' AND id Not IN (SELECT Top ' . $this->website->db('web')->sanitize_var($per_page * ($page - 1)) . ' id FROM DmN_Merchant_Logs WHERE merchant like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' ORDER BY id DESC) ORDER BY id DESC'); else
                    $items = $this->website->db('web')->query('SELECT Top ' . $this->website->db('web')->sanitize_var($per_page) . ' id, merchant, amount, currency, account, server, date FROM DmN_Merchant_Logs WHERE merchant like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\' AND id Not IN (SELECT Top ' . $this->website->db('web')->sanitize_var($per_page * ($page - 1)) . ' id DmN_Merchant_Logs WHERE merchant like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\' AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\' ORDER BY id DESC) ORDER BY id DESC');
            }
            foreach($items->fetch_all() as $value){
                $this->logs[] = ['id' => $value['id'], 'merchant' => $value['merchant'], 'amount' => $value['amount'], 'currency' => $value['currency'], 'account' => htmlspecialchars($value['account']), 'server' => htmlspecialchars($value['server']), 'date' => date(DATETIME_FORMAT, $value['date'])];
            }
            return $this->logs;
        }

        /**
         * Count total merchant logs for pagination
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
                $sql .= 'WHERE merchant like \'%' . $this->website->db('web')->sanitize_var($acc) . '%\'';
                if($server != 'All'){
                    $sql .= ' AND server = \'' . $this->website->db('web')->sanitize_var($server) . '\'';
                }
            }
            $count = $this->website->db('web')->snumrows('SELECT COUNT(merchant) AS count FROM DmN_Merchant_Logs ' . $sql . '');
            return $count;
        }

        public function add_wcoins($amount = 0, $id, $account, $server, $config = [])
        {
            $acc = (in_array($config['identifier_column'], ['MemberGuid', 'memb_guid'])) ? $id : $account;
            $stmt = $this->website->db($config['db'], $server)->prepare('UPDATE ' . $config['table'] . ' SET ' . $config['column'] . ' = ' . $config['column'] . ' + :wcoins WHERE ' . $config['identifier_column'] . ' = :account');
            $stmt->execute([':wcoins' => $amount, ':account' => $acc]);
            if($stmt->rows_affected() == 0){
                $stmt = $this->website->db($config['db'], $server)->prepare('INSERT INTO ' . $config['table'] . ' (' . $config['identifier_column'] . ', ' . $config['column'] . ') values (:user, :wcoins)');
                $stmt->execute([':user' => $acc, ':wcoins' => $amount]);
            }
        }

        public function add_account_log($log, $credits, $acc, $server)
        {
            $stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Account_Logs (text, amount, date, account, server, ip) VALUES (:text, :amount, GETDATE(), :acc, :server, :ip)');
            $stmt->execute([':text' => $log, ':amount' => $credits, ':acc' => $acc, ':server' => $server, ':ip' => $this->website->ip()]);
            $stmt->close_cursor();
        }

        public function deduct_merchant_money($merchant, $money, $server)
        {
            $stmt = $this->website->db('web')->prepare('UPDATE DmN_Merchant_List SET wallet = wallet - :money WHERE memb___id = :merchant AND server = :server');
            $stmt->execute([':money' => $money, ':merchant' => $merchant, ':server' => $server]);
            $stmt->close_cursor();
        }

        public function add_merchant_log($merchant, $amount, $currency, $account, $server)
        {
            $stmt = $this->website->db('web')->prepare('INSERT INTO DmN_Merchant_Logs (merchant, amount, currency, account, server, date) VALUES (:merchant, :amount, :currency, :acc, :server, :date)');
            $stmt->execute([':merchant' => $merchant, ':amount' => $amount, ':currency' => $currency, ':acc' => $account, ':server' => $server, ':date' => time()]);
            $stmt->close_cursor();
        }

        /**
         * Check if account is connected to game
         *
         * @param string $account
         * @param string $server
         *
         * @return bool
         */
        public function check_connect_stat($account, $server)
        {
            $stmt = $this->website->db('account', $server)->prepare('SELECT ConnectStat FROM MEMB_STAT WHERE memb___id = :user ' . $this->website->server_code($this->website->get_servercode($server)) . '');
            $stmt->execute([':user' => $account]);
            if($status = $stmt->fetch()){
                return ($status['ConnectStat'] == 0);
            }
            return true;
        }
    }
