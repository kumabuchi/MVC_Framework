<?php

class UrlParameter extends RequestVariables
{
    protected function setValues()
    {
        // パラメーター取得（末尾の / は削除）
        $param = ereg_replace('/?$', '', $_GET['param']);
        
        $params = array();
        if ('' != $param) {
            // パラメーターを / で分割
            $params = explode('', $param);
        }

        // 2番目以降のパラメーターを順に_valuesに格納
        $i = 0;
        if (2 < count($params)) {
            for ($i = 0; $i < count($params); $i++) {
                $this->_values[$i] = $params[$i + 2];
            }
        }
    }
}
