<?php
    in_file();

    class MemCachedCache
    {
        /**
         * The root cache directory.
         * @var string
         */
        private $memcached = null;
        private $lifetime;
        private $cache_time = [];
        private $isMemcache = false;

        /**
         * Creates a FileCache object
         *
         * @param array $options
         */
        // @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM   
        public function __construct($options = [])
        {
            $connected = false;
            $available_options = ['IP', 'PORT'];
            foreach($available_options as $name){
                if(isset($options[$name])){
                    $this->$name = $options[$name];
                }
            }
            if(class_exists('Memcache')){
                $this->memcached = new Memcache;  
                if($this->memcached->connect($this->IP, $this->PORT)){
                    $connected = true;
                    $this->isMemcache = true;
                }
            }
            if($connected == false && class_exists('Memcached')){
                $this->memcached = new Memcached;  
                if($this->memcached->addServer($this->IP, (int)$this->PORT)){
                    $connected = true;
                    $this->isMemcache = false;
                }
                else{
                    throw new Exception('Unable to connect to memcached');
                }
            }

			if($connected == false){
				throw new Exception('No memcache[d] class found.');
			}
        }

        /**
         * Fetches an entry from the cache.
         *
         * @param string $id
         * @param bool $delete_old_cache
         */
        // @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM   
        public function get($id, $delete_old_cache = true)
        {
            $data = $this->memcached->get($id);
            if($data == false){
                $this->cache_time[$id] = '';
                return false;
            }
            $this->cache_time[$id] = $data[0];
            if($this->cache_time[$id] !== 0 && $this->cache_time[$id] < time() && $delete_old_cache == true){
                $this->cache_time[$id] = '';
                $this->delete($id);
                return false;
            }
            
            return $data[1];
        }

        public function last_cached($id)
        {
            return $this->cache_time[$id];
        }

        /**
         * Deletes a cache entry.
         *
         * @param string $id
         *
         * @return bool
         */
        public function delete($id)
        {
            return $this->memcached->delete($id);
        }

        /**
         * Puts data into the cache.
         *
         * @param string $id
         * @param mixed $data
         * @param int $lifetime
         *
         * @return bool
         */
        // @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM   
        public function save($id, $data, $lifetime = 3600)
        {
            $this->lifetime = time() + $lifetime;
            $storeData = [$this->lifetime, $data];
            if($this->isMemcache){
                $result = $this->memcached->set($id, $storeData, false, $this->lifetime);
            }
            else{
                $result = $this->memcached->set($id, $storeData, $this->lifetime);
            }
            return $result;
        }
    }
