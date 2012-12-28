<?php

abstract class ControllerBase {

	protected $config;
	protected $controller;
	protected $action;
	protected $model;
	protected $view;
	protected $template;
	protected $request;

	// コンストラクタ
	public function __construct(){
		$this->request = new Request();
	}

	// 各設定情報をセット
	public function setConfig($config){
		$this->config = $config;
	}
		
	// コントローラーとアクションの文字列設定
	public function setControllerAction($controller, $action){
		$this->controller = $controller;
		$this->action = $action;
	}

	// Model, Viewの初期化
	public function initialize(){
		$this->initializeModel();
		$this->initializeView();	
	}

	// モデルインスタンス生成処理
	protected function initializeModel(){
		$className = ucfirst($this->controller);
		$classFile = sprintf('%s/app/models/%sModel.php', $this->config["DIR"]["root"], $className);
		if (false == file_exists($classFile)) {
			return;
		}
		require_once $classFile;
		if (false == class_exists($className)) {
			return;
		}        
		$this->model = new $className();
		$this->model->initDb($this->config["DATABASE"]);
	}

	// ビューの初期化
	protected function initializeView(){
		$this->view = new ViewBase();
		$this->template = sprintf('%s/app/views/%s/%sView.php', $this->config["DIR"]["root"], $this->controller, ucfirst($this->action));
	}


	// 処理実行
	public function run(){
		try {

			// 共通前処理
			$this->preAction();

			// アクションメソッド
			$method = $this->action;
			$this->$method();            

			// 表示
			include_once $this->template; 

		} catch (Exception $e) {
			// ログ出力等の処理を記述
			print("error : ".$e);
		}
	}

	// 共通前処理（オーバーライド前提）
	protected function preAction(){
	}

	// view変数取得ラッパー関数
	protected function get($key){
		return $this->view->getVariable($key);
	}

	// view変数設置ラッパー関数
	protected function set($key, $val){
		$this->view->setVariable($key, $val);
	}

}
