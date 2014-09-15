<?php
/*
=============================================================================
Проверка обновлений для DLE модулей by ПафНутиЙ
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
google+: http://gplus.to/pafnuty
email:   pafnuty10@gmail.com
=============================================================================
*/
if (!defined('DATALIFEENGINE')) die("Go fuck yourself!");

/**
 * Class checkUpdates
 * класс для проверки обновлений
 */
class checkUpdates {

	public $result;
	var $infoArray = array();

	function __construct($infoArray) {
		$this->infoArray = $infoArray;
	}


	/**
	 * @return $this
	 */
	private function check() {
		$getInfoArray = $this->getInfo($this->infoArray);

		if ($this->parseVersion($this->infoArray['currentVersion']) < $this->parseVersion($getInfoArray['currentVersion'])) {
			$this->result = $this->showUpdateInfo($getInfoArray);
		} else {
			$this->result = $this->showUpdateInfo(false);
		}

		return $this;

	}

	/**
	 * @param $arr
	 *
	 * @return mixed
	 */
	private function getInfo($arr) {
		$arr['d'] = $_SERVER['HTTP_HOST'];
		$query = http_build_query($arr);
		return json_decode(@file_get_contents('http://updates.pafnuty.name/' . '?' . $query), true);
	}

	/**
	 * @param $info
	 *
	 * @return bool
	 */
	private function showUpdateInfo($info) {
		if ($info) {
			return $info;
		} else {
			return false;
		}
	}

	/**
	 * @param $str
	 *
	 * @return int
	 */
	private function parseVersion($str) {
		$arr = explode('.', $str);
		$retNum = 0;
		foreach ($arr as $num) {
			$retNum += $num;
		}

		return $retNum;
	}


	/**
	 * @return mixed
	 */
	public function getResult() {
		return $this->check()->result;
	}

} //checkUpdates
?>