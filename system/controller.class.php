<?php
    in_file();

    use Gettext\Translator;
    use Gettext\Translations;
    use Gettext\Generators;

    class initialize
    {
        private $translator;
        private $translations;
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function __construct(config $config)
        {
            static $translation_data = null;
            date_default_timezone_set($config->config_entry('main|timezone'));
            $this->setLocalization($config);
            $this->translator = new Translator();
            if($translation_data == null){
				$file = APP_PATH . DS . 'localization' . DS . $config->language() . '.json';
				if(file_exists($file)){
					$translation_data = Translations::fromJsonFile($file);
				}
				else{
					$translation_data = [];
				}
			}
			if(!empty($translation_data)){
				$this->translator->loadTranslations($translation_data);
			}
			else{
				$this->translator->loadTranslations(['messages' => '']);
			}
            $this->translator->register();
            if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'){
				if(!empty($translation_data)){
					$this->translations = Generators\Jed::toString($translation_data);
				}
				else{
					$this->translations = '{"messages":{"":{"domain":"messages","lang":"en","plural-forms":"nplurals=2; plural=(n != 1);"}}}';
				}
            }
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        private function setLocalization($config)
        {
            if(!isset($_COOKIE['dmn_language'])){
				if(defined('SWITCH_LANG_ON_LOCATION') && SWITCH_LANG_ON_LOCATION == true){
					$country = get_country_code(ip());
					if(isset(LANGS[$country])){
						setcookie("dmn_language", LANGS[$country], strtotime('+5 days', time()), "/");
						$_COOKIE['dmn_language'] = LANGS[$country];
					}
					else{
						$language = $config->language();
						setcookie("dmn_language", $language, strtotime('+5 days', time()), "/");
						$_COOKIE['dmn_language'] = $language;
					}
				}
				else{
					$language = $config->language();
					setcookie("dmn_language", $language, strtotime('+5 days', time()), "/");
					$_COOKIE['dmn_language'] = $language;
				}
            }
        }

        public function translations()
        {
            return $this->translations;
        }
    }

    class controller
    {
        private static $_instance;
        public $translations;
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        public function __construct()
        {
            self::$_instance = $this;
            foreach($this->is_loaded() as $key => $class){
                $this->$key = $this->load_class($class);
            }
            $this->config = $this->load_class('config');
            $this->load = $this->load_class('load');
            $this->translations = (new initialize($this->config))->translations();
            date_default_timezone_set($this->config->config_entry('main|timezone'));
        }

        public static function get_instance()
        {
            if(!self::$_instance instanceof self){
                self::$_instance = new controller;
            }
            return self::$_instance;
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        protected function load_class($class)
        {
            return load_class($class);
        }
		
		// @ioncube.dk cmsVersion('g8LU2sewjnwUpNnBTm9t85c3Xgf/0Y9V+rZWvw94O3A=', '009869451363953188238779430856374927754') -> "NewDmNIonCubeDynKeySecurityAlgo" RANDOM
        protected function is_loaded()
        {
            return is_loaded();
        }
    }

    interface pluginInterface
    {
        public function index();

        public function install();

        public function uninstall();

        public function enable();

        public function disable();

        public function admin();

        public function about();
    }