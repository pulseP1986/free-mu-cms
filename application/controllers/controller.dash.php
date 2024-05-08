<?php
    in_file();

    class dash extends controller
    {
        protected $vars = [], $errors = [];

        public function __construct(){
            parent::__construct();
            $this->load->helper('website');
            $this->load->lib('session', ['DmNCMS']);
			$this->session->checkSession();
            $this->load->helper('meta');
            $this->load->helper('breadcrumbs', [$this->request]);
        }

        public function index(){
            $this->load->model('home');
            $this->vars['news'] = $this->Mhome->load_news(1, 20);
            $this->load->view($this->config->config_entry('main|template') . DS . 'dashboard' . DS . 'view.index', $this->vars);
        }
    }