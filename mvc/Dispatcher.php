<?php
require_once(dirname(__FILE__).'/Config.php');
require_once(dirname(__FILE__).'/ControllerBase.php');
require_once(dirname(__FILE__).'/ModelBase.php');
require_once(dirname(__FILE__).'/ViewBase.php');
require_once(dirname(__FILE__).'/Request.php');

new Dispatcher();

class Dispatcher {

	private $config;
	private $routes;
	private $root;

	/*
	 * コンストラクタ
	 */
	public function __construct(){
		$conf = new Config();
		$this->config = $conf->getConfig();
		$this->routes = $conf->getRoutes();
		$this->root   = $conf->getConfig('DIR', 'root');
		if( $this->root == null ){
			header("HTTP/1.0 404 Not Found");
			exit;
		}
		$this->initializeSession();
	}

	/*
	 * セッション初期化
	 */
	private function initializeSession(){
		if( $this->config['SESSION']['use'] != true ){
			return;
		}
		$sessConf = $this->config['SESSION'];
		if( isset($sessConf['name']) ){
			session_name($sessConf['name']);
		}
		if( isset($sessConf['sessdir']) ){
			session_save_path($sessConf['sessdir']);
		}
		$lifetime = 0;
		if( isset($sessConf['lifetime']) ){
			session_cache_expire($sessConf['lifetime']);
			$lifetime = $sessConf['lifetime'];
		}
		if( isset($sessConf['path']) ){
			session_set_cookie_params($lifetime*60, $sessConf['path']);
		}
		session_start();
		session_regenerate_id();		
	}

	/*
	 * リクエスト振り分け
	 */		
	public function dispatch(){

		// パラメーター取得（末尾の / は削除）
		$param = ereg_replace('/?$', '', $_GET['params']);
		$param = trim($param, '/');

		$params = array();
		if ('' != $param) {
			// パラメーターを"/"で分割
			$params = explode('/', $param);
		}

		// １番目のパラメーターをコントローラーとして取得
		$controller = 'index';
		if (0 < count($params) && !preg_match('/.(\.php|\.html)/', $params[0]) ) {
			$controller = $params[0];
		}

		// 2番目のパラメーターをアクションとして取得
		$action= "index";
		if (1 < count($params)) {
			$action = $params[1];
		}

		// ユーザのルーティングテーブルを参照
		$route = $this->transferController($controller, $action);
		if( $route != null ){
			$routes = explode("#", $route);
			if( 1 < count($routes) ){
				$controller = $routes[0];
				$action = $routes[1];
			}
		}
		
		// １番目のパラメーターをもとにコントローラークラスインスタンス取得
		$controllerInstance = $this->getControllerInstance($controller);
		if (null == $controllerInstance) {
			header("HTTP/1.0 404 Not Found");
			exit;
		}

		// アクションメソッドの存在確認
		if (false == method_exists($controllerInstance, $action)) {
			header("HTTP/1.0 404 Not Found");
			exit;
		}

		// コントローラー初期設定
		$controllerInstance->setControllerAction($controller, $action);
		$controllerInstance->setConfig($this->config);
		$controllerInstance->initialize();

		// 処理実行
		$controllerInstance->run();
	}


	// ユーザルーティング
	private function transferController($controller, $action)
	{
		$routeKey = $controller."/".$action;
		if (true == array_key_exists($routeKey, $this->routes)) {
			$newRoute = $this->routes[$routeKey];
		}
		return $newRoute;
	}

	// コントローラークラスのインスタンスを取得
	private function getControllerInstance($controller)
	{
		// 一文字目のみ大文字に変換＋"Controller"
		$className = ucfirst(strtolower($controller)) . 'Controller';
		// コントローラーファイル名
		$controllerFileName = sprintf(
				'%s/app/controllers/%s.php',
				$this->config["DIR"]["root"],
				$className
				);
		// ファイル存在チェック
		if (false == file_exists($controllerFileName)) {
			return null;
		}
		// クラスファイルを読込
		require_once $controllerFileName;
		// クラスが定義されているかチェック
		if (false == class_exists($className)) {
			return null;
		}
		// クラスインスタンス生成
		$controllerInstarnce = new $className($this->config);

		return $controllerInstarnce;
	}

}	
