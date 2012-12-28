<?php
require_once(dirname(__FILE__)."/RequestVariables.php");
require_once(dirname(__FILE__)."/Post.php");
require_once(dirname(__FILE__)."/QueryString.php");
require_once(dirname(__FILE__)."/UrlParameter.php");

class Request
{
    // POSTパラメータ
    private $_post;
    // GETパラメータ
    private $_query;
    // URLパラメータ
    private $_param;
    
    // コンストラクタ@
    public function __construct()
    {
        $this->_post = new Post();
        $this->_query = new QueryString();
	$this->_param = new UrlParameter();
    }

    // POST変数取得
    public function getPost($key = null)
    {
        if (null == $key) {
            return $this->_post->get();
        }
        if (false == $this->_post->has($key)) {
            return null;
        }
        return $this->_post->get($key);
    }

    // GET変数取得
    public function getQuery($key = null)
    {
        if (null == $key) {
            return $this->_query->get();
        }
        if (false == $this->_query->has($key)) {
            return null;
        }
        return $this->_query->get($key);
    }

    // URLパラメーター取得
    public function getParam($key = null)
    {
        if (null == $key) {
            return $this->_param->get();
        }
        if (false == $this->_param->has($key)) {
            return null;
        }
        return $this->_param->get($key);
    }
}

