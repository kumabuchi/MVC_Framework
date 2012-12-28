<?php

class ModelBase{

	protected $db;
	protected $name;

	// コンストラクタ
	public function __construct(){
		// 継承先で$nameが設定されていない場合はクラス名からテーブル名を生成
		if ($this->name == null) {
			$this->setDefaultTableName();
		}
	}

	// データベースに接続
	public function initDb($iniConfig){
		if( $iniConfig["use"] != true ){
			return;
		}
		$dsn = sprintf(
				'mysql:host=%s;dbname=%s;port=3306;',
				$iniConfig['host'],
				$iniConfig['dbname']
			      );
		$this->db = new PDO($dsn, $iniConfig['dbuser'], $iniConfig['password']);
	}

	// クラス名からデータベースのテーブル名を求める
	public function setDefaultTableName(){
		$className = get_class($this);
		$len = strlen($className);
		$tableName = '';
		for ($i = 0; $i < $len; $i++) {
			$char = substr($className, $i, 1);
			$lower = strtolower($char);
			if ($i > 0 && $char != $lower) {
				$tableName .= '_';
			}
			$tableName .= $lower;
		}
		$this->name  = $tableName;
	}

	// クエリ結果を取得
	public function query($sql, array $params = array()){
		$stmt = $this->db->prepare($sql);
		if ($params != null) {
			foreach ($params as $key => $val) {
				$stmt->bindValue(':' . $key, $val);
			}
		}
		$stmt->execute();
		$rows = $stmt->fetchAll();
		return $rows;
	}

	// 全データ取得
	public function getAll()
	{
		$sql = sprintf('SELECT * FROM %s', $this->name);
		$rows = $this->query($sql);
		return $rows;
	}

	// INSERTを実行
	public function insert($data){
		$fields = array();
		$values = array();
		foreach ($data as $key => $val) {
			$fields[] = $key;
			$values[] = ':' . $key;
		}
		$sql = sprintf(
				"INSERT INTO %s (%s) VALUES (%s)", 
				$this->name,
				implode(',', $fields),
				implode(',', $values)
			      );
		$stmt = $this->db->prepare($sql);
		foreach ($data as $key => $val) {
			$stmt->bindValue(':' . $key, $val);
		}
		$res  = $stmt->execute();
		return $res;        
	}

	// DELETEを実行
	public function delete($where, $params = null){
		$sql = sprintf("DELETE FROM %s", $this->name);
		if ($where != "") {
			$sql .= " WHERE " . $where;
		}
		$stmt = $this->db->prepare($sql);
		if ($params != null) {
			foreach ($params as $key => $val) {
				$stmt->bindValue(':' . $key, $val);
			}
		}
		$res = $stmt->execute();
		return $res;
	}

}
