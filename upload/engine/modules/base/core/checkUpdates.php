<?php
/*
=============================================================================
Проверка обновлений
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
 * класс для управления допполями
 */
class checkUpdates {

	/**
	 * @var
	 */
	public $result;
	/**
	 * @var string
	 */
	public $cacheFile;
	/**
	 * @var mixed
	 */
	private $cfg;

	/**
	 *
	 */
	function __construct() {
		$this->cfg       = json_decode(file_get_contents(ENGINE_DIR . '/data/ymaps_config.json'));
		$this->cacheFile = ENGINE_DIR . '/cache/system/ymap.json';
	}

	/**
	 * @param $url
	 *
	 * @param $mail
	 *
	 * @internal param $date
	 * @return $this
	 */
	public function check($url, $mail) {

		$cacheArr  = $this->readCache();
		$$mail     = ($mail) ? $mail : false;
		$infoArray = $this->getInfo($url, $this->cfg->moduleName, $mail);

		if ($cacheArr['currentVersion'] != $infoArray['currentVersion']) {
			$this->result = $this->showUpdateInfo($infoArray);
		}
		else {
			$this->result = $this->showUpdateInfo(false);
		}

		return $this;

	}

	/**
	 * @return mixed
	 */
	private function readCache() {
		$file = file_get_contents($this->cacheFile);
		if (!$file || (json_decode($file)->lastCheck < time() + 36000 )) {
			$this->createCacheFile();
		}

		return json_decode($file, true);
	}

	/**
	 * @return string
	 */
	private function createCacheFile() {
		$arr = array('currentVersion' => $this->cfg->moduleVersion, 'lastCheck' => time());
		file_put_contents($this->cacheFile, json_encode($arr), LOCK_EX);

		return file_get_contents($this->cacheFile);
	}

	/**
	 * @param $url
	 * @param $n
	 * @param $mail
	 *
	 * @return mixed
	 */
	private function getInfo($url, $n, $mail) {
		$thisDomain = $_SERVER['HTTP_HOST'];
		$mail       = ($mail) ? '&m=' . $mail : false;

		return json_decode(@file_get_contents($url . '?n=' . $n . '&d=' . $thisDomain . $mail), true);
	}

	/**
	 * @param $info
	 *
	 * @return bool
	 */
	private function showUpdateInfo($info) {
		if ($info) {
			return $info;
		}
		else {
			return false;
		}
	}


	/**
	 * @return mixed
	 */
	public function getResult() {
		return $this->result;
	}

} //checkUpdates
?>