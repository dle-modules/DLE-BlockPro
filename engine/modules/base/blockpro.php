<?php
/*
=============================================================================
BLockPro - основной модуль
=============================================================================
Автор:   ПафНутиЙ
URL:     http://pafnuty.name/
twitter: https://twitter.com/pafnuty_name
google+: http://gplus.to/pafnuty
email:   pafnuty10@gmail.com
=============================================================================
 */

if (!defined('DATALIFEENGINE')) {
	die("Go fuck yourself!");
}

if ($showstat) {
	$start = microtime(true);
	$dbStat = '';
}
// Конфиг модуля
if ($isAjaxConfig) {
	$cfg = $ajaxConfigArr;
} else {
	$cfg = array(
		'moderate' => !empty($moderate) ? $moderate : false, // Показывать только новости на модерации

		'template' => !empty($template) ? $template : 'blockpro/blockpro', // Название шаблона (без расширения)

		'cachePrefix' => !empty($cachePrefix) ? $cachePrefix : 'news', // Префикс кеша
		'cacheSuffixOff' => !empty($cacheSuffixOff) ? true : false, // Отключить суффикс кеша (будет создаваться один кеш-файл для всех пользователей). По умолчанию включен, т.е. для каждой группы пользователей будет создаваться свой кеш (на случай разного отображения контента разным юзерам).

		'cacheNameAddon' => '', // Назначаем дополнение к имени кеша, если имеются переменные со значениями this, они будут дописаны в имя кеша, иначе для разных мест будет создаваться один и тот же файл кеша

		'nocache' => !empty($nocache) ? $nocache : false, // Не использовать кеш
		'cacheLive' => (!empty($cacheLive) && !$mcache) ? $cacheLive : false, // Время жизни кеша в минутах

		'startFrom' => !empty($startFrom) ? $startFrom : '0', // C какой новости начать вывод
		'limit' => !empty($limit) ? $limit : '10', // Количество новостей в блоке
		'fixed' => !empty($fixed) ? $fixed : 'yes', // Обработка фиксированных новостей (yes/only/without показ всех/только фиксированных/только обычных новостей)
		'allowMain' => !empty($allowMain) ? $allowMain : 'yes', // Обработка новостей, опубликованных на главной (yes/only/without показ всех/только на главной/только не на главной)
		'postId' => !empty($postId) ? $postId : '', // ID новостей для вывода в блоке (через запятую, или черточку)
		'notPostId' => !empty($notPostId) ? $notPostId : '', // ID игнорируемых новостей (через запятую, или черточку)

		'author' => !empty($author) ? $author : '', // Логины авторов, для показа их новостей в блоке (через запятую)
		'notAuthor' => !empty($notAuthor) ? $notAuthor : '', // Логины игнорируемых авторов (через запятую)

		'xfilter' => !empty($xfilter) ? $xfilter : '', // Имена дополнительных полей для фильтрации новостей по ним (через запятую)
		'notXfilter' => !empty($notXfilter) ? $notXfilter : '', // Имена дополнительных полей для игнорирования показа новостей (через запятую)

		'xfSearch' => !empty($xfSearch) ? $xfSearch : false, // синтаксис передачи данных: &xfSearch=имя_поля|значение||имя_поля|значение
		'notXfSearch' => !empty($notXfSearch) ? $notXfSearch : false, // синтаксис передачи данных: &notXfSearch=имя_поля|значение||имя_поля|значение
		'xfSearchLogic' => !empty($xfSearchLogic) ? $xfSearchLogic : 'OR', // Принимает OR или AND (по умолчанию OR)

		'catId' => !empty($catId) ? $catId : '', // Категории для показа	(через запятую, или черточку)
		'subcats' => !empty($subcats) ? $subcats : false, // Выводить подкатегории указанных категорий (&subcats=y), работает и с диапазонами.
		'notCatId' => !empty($notCatId) ? $notCatId : '', // Игнорируемые категории (через запятую, или черточку)
		'notSubcats' => !empty($notSubcats) ? $notSubcats : false, // Игнорировать подкатегории игнорируемых категорий (&notSubcats=y), работает и с диапазонами.
		'thisCatOnly' => !empty($thisCatOnly) ? $thisCatOnly : false, // Показывать новости ТОЛЬКО из текущей категории (имеет смысл при выводе похожих новостей и использоваии мультикатегорий).

		'tags' => !empty($tags) ? $tags : '', // Теги из облака тегов для показа новостей, содержащих их (через запятую)
		'notTags' => !empty($notTags) ? $notTags : '', // Игнорируемые теги (через запятую)

		'day' => !empty($day) ? $day : false, // Временной период для отбора новостей
		'dayCount' => !empty($dayCount) ? $dayCount : false, // Интервал для отбора (т.е. к примеру выбираем новости за прошлую недею так: &day=14&dayCount=7 )
		'sort' => !empty($sort) ? $sort : 'top', // Сортировка (top, date, comms, rating, views, title, hit, random, randomLight, download, symbol, editdate или xf|xfieldname где xfieldname - имя дополнительного поля)
		'xfSortType' => !empty($xfSortType) ? $xfSortType : 'int', // Тип сортировки по допполю (string, int) - для корректной сортировки по строки используем `string`, по умолчанию сортируется как число (для цен полезно).
		'order' => !empty($order) ? $order : 'new', // Направление сортировки (new, old, asis)

		'avatar' => !empty($avatar) ? $avatar : false, // Вывод аватарки пользователя (немного усложнит запрос).

		'showstat' => !empty($showstat) ? $showstat : false, // Показывать время и статистику по блоку

		'related' => !empty($related) ? $related : false, // Включить режим вывода похожих новостей (по умолчанию нет)
		'saveRelated' => !empty($saveRelated) ? $saveRelated : false, // Включить запись похожих новостей в БД
		'showNav' => !empty($showNav) ? $showNav : false, // Включить постраничную навигацию
		'navDefaultGet' => !empty($navDefaultGet) ? $navDefaultGet : false, // Слушать дефолтный get-параметр постраничной навигации
		'pageNum' => !empty($pageNum) ? $pageNum : '1', // Текущая страница при постраничной конфигурации
		'navStyle' => !empty($navStyle) ? $navStyle : 'classic', // Стиль навигации. Возможны следующие стили:
		/*
		classic:	<< Первая  < 1 [2] 3 >  Последняя >>
		digg:		<< Назад  1 2 ... 5 6 7 8 9 [10] 11 12 13 14 ... 25 26  Вперёд >>
		extended:	<< Назад | Страница 2 из 11 | Показаны новости 6-10 из 52 | Вперёд >>
		punbb:		1 ... 4 5 [6] 7 8 ... 15
		arrows:	    << Назад Вперёд >>
		 */
		/**
		 * @todo  реализовать функционал с options и notOptions
		 */
		'options' => !empty($options) ? $options : false, // Опции, публикации новости для показа (Публиковать на главной, Разрешить рейтинг статьи, Разрешить комментарии, Запретить индексацию страницы для поисковиков, Зафиксировать новость) main|rating|comments|noindex
		'notOptions' => !empty($notOptions) ? $notOptions : false, // Опции, публикации новости для исключения (Публиковать на главной, Разрешить рейтинг статьи, Разрешить комментарии, Запретить индексацию страницы для поисковиков, Зафиксировать новость) main|rating|comments|noindex

		'future' => !empty($future) ? $future : false, // Выводить только новости, дата публикации которых не наступила (Полезно для афиш) &future=y
		'cacheVars' => !empty($cacheVars) ? $cacheVars : false, // Значимые переменные в формировании кеша блока на случай разного вывода в зависимости от условий расположения модуля. Сюда можно передавать ключи, доступные через $_REQUEST или значения переменной $dle_module
		'symbols' => !empty($symbols) ? $symbols : false, // Символьные коды для фильтрации по символьному каталогу. Перечисляем через запятую.
		'notSymbols' => !empty($notSymbols) ? $notSymbols : false, // Символьные коды для исключающей фильтрации по символьному каталогу. Перечисляем через запятую или пишем this для текущего символьного кода
		'fields' => !empty($fields) ? $fields : false, // Дополнение к выборке полей из БД (p.field,e.field)
	);
}

/**
 * var array $bpConfig
 */
include ENGINE_DIR . '/data/blockpro.php';

// Объединяем массивы конфигов
$cfg = array_merge($cfg, $bpConfig);

// Если имеются переменные со значениями this, изменяем значение переменной cacheNameAddon
if ($cfg['catId'] == 'this') {
	$cfg['cacheNameAddon'] .= $category_id . 'cId_';
}
if ($cfg['notCatId'] == 'this') {
	$cfg['cacheNameAddon'] .= $category_id . 'nCId_';
}
if ($cfg['postId'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST['newsid'] . 'pId_';
}
if ($cfg['notPostId'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST['newsid'] . 'nPId_';
}
if ($cfg['author'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST['user'] . 'a_';
}
if ($cfg['notAuthor'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST['user'] . 'nA_';
}
if ($cfg['tags'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST['tag'] . 't_';
}
if ($cfg['notTags'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST['tag'] . 'nT_';
}
if ($cfg['symbols'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST['catalog'] . 's_';
}
if ($cfg['notSymbols'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST['catalog'] . 'nS_';
}
if ($cfg['related'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST['newsid'] . 'r_';
}

if ($cfg['xfilter'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST['xf'] . 'xf_';
}
if ($cfg['notXfilter'] == 'this') {
	$cfg['cacheNameAddon'] .= $_REQUEST['xf'] . 'nXf_';
}

if ($cfg['navDefaultGet']) {
	$cfg['cacheNameAddon'] .= $_REQUEST['cstart'] . 'cs_';
}

if ($cfg['cacheVars']) {
	// Если установлена переменная, добавим в имя кеша требуемые дополнения
	// Убираем пробелы, на всякий пожарный
	$cfg['cacheVars'] = str_replace(' ', '', $cfg['cacheVars']);
	// Разбиваем строку на массив
	$arCacheVars = explode(',', $cfg['cacheVars']);
	foreach ($arCacheVars as $cacheVar) {
		// Сверяем данные из массива с данными, доступными на странице
		if (isset($_REQUEST[$cacheVar])) {
			$cfg['cacheNameAddon'] .= $_REQUEST[$cacheVar] . $cacheVar . '_';
		}
		if ($dle_module == $cacheVar) {
			$cfg['cacheNameAddon'] .= $dle_module . '_';
		}
	}

}

if ($cfg['cacheLive']) {
	// Меняем префикс кеша для того, чтобы он не чистился автоматически, если указано время жизни кеша.
	$cfg['cachePrefix'] = 'base';
}

// Формируем имя кеша
$cacheName = implode('_', $cfg) . $config['skin'];

// Определяем необходимость создания кеша для разных групп
$cacheSuffix = ($cfg['cacheSuffixOff']) ? false : true;

// Если установлено время жизни кеша
if ($cfg['cacheLive']) {
	// Формируем имя кеш-файла в соответствии с правилами формирования тагового стандартными средствами DLE, для последующей проверки на существование этого файла.
	$_end_file = (!$cfg['cacheSuffixOff']) ? ($is_logged) ? '_' . $member_id['user_group'] : '_0':false;
	$filedate = ENGINE_DIR . '/cache/' . $cfg['cachePrefix'] . '_' . md5($cacheName) . $_end_file . '.tmp';

	if (@file_exists($filedate)) {
		$cache_time = time()-@filemtime($filedate);
	} else {
		$cache_time = $cfg['cacheLive'] * 60;
	}
	if ($cache_time >= $cfg['cacheLive'] * 60) {
		$clear_time_cache = true;
	}
}

$output = false;

// Если nocache не установлен - пытаемся вывести данные из кеша.
if (!$cfg['nocache']) {
	$output = dle_cache($cfg['cachePrefix'], $cacheName, $cacheSuffix);
}
// Сбрасываем данные, если истекло время жизни кеша
if ($clear_time_cache) {
	$output = false;
}

if (!$output) {

	// Проверяем лицензию.	
	if(!class_exists('Protect')) {
		// Класс проверки лицензии -->

			class Protect{ public $status=false; public $errors=false; public $activation_key=''; public $activation_key_expires; public $secret_key='fdfbLhlLgnJDKJklblngkk6krtkghm565678kl78klkUUHtvdfdoghphj'; public $server=''; public $remote_port=80; public $remote_timeout=20; public $local_ua='PHP code protect'; public $use_localhost=false; public $use_expires=true; public $local_key_storage='filesystem'; public $local_key_path='./'; public $local_key_name='license.lic'; public $local_key_transport_order='scf'; public $local_key_delay_period=7; public $local_key_last; public $release_date='2014-10-24'; public $user_name=''; public $status_messages=array('status_1'=>'This activation key is active.','status_2'=>'Error: This activation key has expired.','status_3'=>'Activation key republished. Awaiting reactivation.','status_4'=>'Error: This activation key has been suspended.','localhost'=>'This activation key is active (localhost).','pending'=>'Error: This activation key is pending review.','download_access_expired'=>'Error: This version of the software was released after your download access expired. Please downgrade software or contact support for more information.','missing_activation_key'=>'Error: The activation key variable is empty.','could_not_obtain_local_key'=>'Error: I could not obtain a new local key.','maximum_delay_period_expired'=>'Error: The maximum local key delay period has expired.','local_key_tampering'=>'Error: The local key has been tampered with or is invalid.','local_key_invalid_for_location'=>'Error: The local key is invalid for this location.','missing_license_file'=>'Error: Please create the following file (and directories if they dont exist already): ','license_file_not_writable'=>'Error: Please make the following path writable: ','invalid_local_key_storage'=>'Error: I could not determine the local key storage on clear.','could_not_save_local_key'=>'Error: I could not save the local key.','activation_key_string_mismatch'=>'Error: The local key is invalid for this activation key.'); private $trigger_delay_period; public function __construct(){} public function validate(){if($this->use_localhost&&$this->getIpLocal()&&$this->isWindows()&&!file_exists("{$this->local_key_path}{$this->local_key_name}")){$this->status=true;return $this->errors=$this->status_messages['localhost'];}if(!$this->activation_key){return $this->errors=$this->status_messages['missing_activation_key'];}switch($this->local_key_storage){case 'filesystem':$local_key=$this->readLocalKey();break;default:return $this->errors=$this->status_messages['missing_activation_key'];}$this->trigger_delay_period=$this->status_messages['could_not_obtain_local_key'];if($this->errors==$this->trigger_delay_period&&$this->local_key_delay_period){$delay=$this->processDelayPeriod($this->local_key_last);if($delay['write']){if($this->local_key_storage=='filesystem'){$this->writeLocalKey($delay['local_key'],"{$this->local_key_path}{$this->local_key_name}");}}if($delay['errors']){return $this->errors=$delay['errors'];}$this->errors=false;return $this;}if($this->errors){return $this->errors;}return $this->validateLocalKey($local_key);} private function calcMaxDelay($local_key_expires,$delay){return ((integer)$local_key_expires+((integer)$delay*86400));} private function processDelayPeriod($local_key){$local_key_src=$this->decodeLocalKey($local_key);$parts=$this->splitLocalKey($local_key_src);$key_data=unserialize($parts[0]);$local_key_expires=(integer)$key_data['local_key_expires'];unset($parts,$key_data);$write_new_key=false;$parts=explode("\n\n",$local_key);$local_key=$parts[0];foreach($local_key_delay_period=explode(',',$this->local_key_delay_period) as $key=>$delay){if(!$key){$local_key.="\n";}if($this->calcMaxDelay($local_key_expires,$delay)>time()){continue;}$local_key.="\n{$delay}";$write_new_key=true;}if(time()>$this->calcMaxDelay($local_key_expires,array_pop($local_key_delay_period))){return array('write'=>false,'local_key'=>'','errors'=>$this->status_messages['maximum_delay_period_expired']);}return array('write'=>$write_new_key,'local_key'=>$local_key,'errors'=>false);} private function inDelayPeriod($local_key,$local_key_expires){$delay=$this->splitLocalKey($local_key,"\n\n");if(!isset($delay[1])){return -1;}return (integer)($this->calcMaxDelay($local_key_expires,array_pop(explode("\n",$delay[1])))-time());} private function decodeLocalKey($local_key){return base64_decode(str_replace("\n",'',urldecode($local_key)));} private function splitLocalKey($local_key,$token='{protect}'){return explode($token,$local_key);} private function validateAccess($key,$valid_accesses){return in_array($key,(array)$valid_accesses);} private function wildcardIp($key){$octets=explode('.',$key);array_pop($octets);$ip_range[]=implode('.',$octets).'.*';array_pop($octets);$ip_range[]=implode('.',$octets).'.*';array_pop($octets);$ip_range[]=implode('.',$octets).'.*';return $ip_range;} private function wildcardServerHostname($key){$hostname=explode('.',$key);unset($hostname[0]);$hostname=(!isset($hostname[1]))?array($key):$hostname;return '*.'.implode('.',$hostname);} private function extractAccessSet($instances,$enforce){foreach($instances as $key=>$instance){if($key!=$enforce){continue;}return $instance;}return array();} private function validateLocalKey($local_key){$local_key_src=$this->decodeLocalKey($local_key);$parts=$this->splitLocalKey($local_key_src);if(!isset($parts[1])){return $this->errors=$this->status_messages['local_key_tampering'];}if(md5((string)$this->secret_key.(string)$parts[0])!=$parts[1]){return $this->errors=$this->status_messages['local_key_tampering'];}unset($this->secret_key);$key_data=unserialize($parts[0]);$instance=$key_data['instance'];unset($key_data['instance']);$enforce=$key_data['enforce'];unset($key_data['enforce']);$this->user_name=$key_data['user_name'];if((string)$key_data['activation_key_expires']=='never'){$this->activation_key_expires=0;}else {$this->activation_key_expires=(integer)$key_data['activation_key_expires'];}if((string)$key_data['activation_key']!=(string)$this->activation_key){return $this->errors=$this->status_messages['activation_key_string_mismatch'];}if((integer)$key_data['status']!=1&&(integer)$key_data['status']!=2){return $this->errors=$this->status_messages['status_'.$key_data['status']];}if($this->use_expires==false&&(string)$key_data['activation_key_expires']!='never'&&(integer)$key_data['activation_key_expires']<time()){return $this->errors=$this->status_messages['status_2'];}if($this->use_expires==false&&(string)$key_data['local_key_expires']!='never'&&(integer)$key_data['local_key_expires']<time()){if($this->inDelayPeriod($local_key,$key_data['local_key_expires'])<0){$this->clearLocalKey();return $this->validate();}}if($this->use_expires==true&&(string)$key_data['activation_key_expires']!='never'&&(integer)$key_data['activation_key_expires']<strtotime($this->release_date)){return $this->errors=$this->status_messages['download_access_expired'];}if($this->use_expires==true&&(string)$key_data['local_key_expires']!='never'&&(integer)$key_data['local_key_expires']<time()&&(integer)$key_data['activation_key_expires']>(integer)$key_data['local_key_expires']+604800){if($this->inDelayPeriod($local_key,$key_data['local_key_expires'])<0){$this->clearLocalKey();return $this->validate();}}$conflicts=array();$access_details=$this->accessDetails();foreach((array)$enforce as $key){$valid_accesses=$this->extractAccessSet($instance,$key);if(!$this->validateAccess($access_details[$key],$valid_accesses)){$conflicts[$key]=true;if(in_array($key,array('ip','server_ip'))){foreach($this->wildcardIp($access_details[$key]) as $ip){if($this->validateAccess($ip,$valid_accesses)){unset($conflicts[$key]);break;}}}elseif(in_array($key,array('domain'))){if(isset($key_data['domain_wildcard'])){if($key_data['domain_wildcard']==1&&preg_match("/".$valid_accesses[0]."\z/i",$access_details[$key])){$access_details[$key]='*.'.$valid_accesses[0];}if($key_data['domain_wildcard']==2){$exp_domain=explode('.',$valid_accesses[0]);$exp_domain=$exp_domain[0];if(preg_match("/".$exp_domain."/i",$access_details[$key])){$access_details[$key]='*.'.$valid_accesses[0].'.*';}}if($key_data['domain_wildcard']==3){$exp_domain=explode('.',$valid_accesses[0]);$exp_domain=$exp_domain[0];if(preg_match("/\A".$exp_domain."/i",$access_details[$key])){$access_details[$key]=$valid_accesses[0].'.*';}}}if($this->validateAccess($access_details[$key],$valid_accesses)){unset($conflicts[$key]);}}elseif(in_array($key,array('server_hostname'))){if($this->validateAccess($this->wildcardServerHostname($access_details[$key]),$valid_accesses)){unset($conflicts[$key]);}}}}if(!empty($conflicts)){return $this->errors=$this->status_messages['local_key_invalid_for_location'];}$this->errors=$this->status_messages['status_1'];return $this->status=true;} public function readLocalKey(){if(!is_dir($this->local_key_path)){mkdir($this->local_key_path,0755,true);}if(!file_exists($path="{$this->local_key_path}{$this->local_key_name}")){$f=@fopen($path,'w');if(!$f){return $this->errors=$this->status_messages['missing_license_file'].$path;}else {fwrite($f,'');fclose($f);}}if(!is_writable($path)){@chmod($path,0777);if(!is_writable($path)){@chmod("$path",0755);if(!is_writable($path)){return $this->errors=$this->status_messages['license_file_not_writable'].$path;}}}if(!$local_key=@file_get_contents($path)){$local_key=$this->getServerLocalKey();if($this->errors){return $this->errors;}$this->writeLocalKey(urldecode($local_key),$path);}return $this->local_key_last=$local_key;} public function clearLocalKey(){if($this->local_key_storage=='filesystem'){$this->writeLocalKey('',"{$this->local_key_path}{$this->local_key_name}");}else {$this->errors=$this->status_messages['invalid_local_key_storage'];}} public function writeLocalKey($local_key,$path){$fp=@fopen($path,'w');if(!$fp){return $this->errors=$this->status_messages['could_not_save_local_key'];}@fwrite($fp,$local_key);@fclose($fp);return true;} private function getServerLocalKey(){$query_string='activation_key='.urlencode($this->activation_key).'&';$query_string.=http_build_query($this->accessDetails());if($this->errors){return false;}$priority=$this->local_key_transport_order;$result=false;while(strlen($priority)){$use=substr($priority,0,1);if($use=='s'){if($result=$this->useFsockopen($this->server,$query_string)){break;}}if($use=='c'){if($result=$this->useCurl($this->server,$query_string)){break;}}if($use=='f'){if($result=$this->useFopen($this->server,$query_string)){break;}}$priority=substr($priority,1);}if(!$result){$this->errors=$this->status_messages['could_not_obtain_local_key'];return false;}if(substr($result,0,7)=='Invalid'){$this->errors=str_replace('Invalid','Error',$result);return false;}if(substr($result,0,5)=='Error'){$this->errors=$result;return false;}return $result;} private function accessDetails(){$access_details=array();if(function_exists('phpinfo')){ob_start();phpinfo();$phpinfo=ob_get_contents();ob_end_clean();$list=strip_tags($phpinfo);$access_details['domain']=$this->scrapePhpInfo($list,'HTTP_HOST');$access_details['ip']=$this->scrapePhpInfo($list,'SERVER_ADDR');$access_details['directory']=$this->scrapePhpInfo($list,'SCRIPT_FILENAME');$access_details['server_hostname']=$this->scrapePhpInfo($list,'System');$access_details['server_ip']=@gethostbyname($access_details['server_hostname']);}$access_details['domain']=($access_details['domain'])?$access_details['domain']:$_SERVER['HTTP_HOST'];$access_details['ip']=($access_details['ip'])?$access_details['ip']:$this->serverAddr();$access_details['directory']=($access_details['directory'])?$access_details['directory']:$this->pathTranslated();$access_details['server_hostname']=($access_details['server_hostname'])?$access_details['server_hostname']:@gethostbyaddr($access_details['ip']);$access_details['server_hostname']=($access_details['server_hostname'])?$access_details['server_hostname']:'Unknown';$access_details['server_ip']=($access_details['server_ip'])?$access_details['server_ip']:@gethostbyaddr($access_details['ip']);$access_details['server_ip']=($access_details['server_ip'])?$access_details['server_ip']:'Unknown';foreach($access_details as $key=>$value){$access_details[$key]=($access_details[$key])?$access_details[$key]:'Unknown';}return $access_details;} private function pathTranslated(){$option=array('PATH_TRANSLATED','ORIG_PATH_TRANSLATED','SCRIPT_FILENAME','DOCUMENT_ROOT','APPL_PHYSICAL_PATH');foreach($option as $key){if(!isset($_SERVER[$key])||strlen(trim($_SERVER[$key]))<=0){continue;}if($this->isWindows()&&strpos($_SERVER[$key],'\\')){return @substr($_SERVER[$key],0,@strrpos($_SERVER[$key],'\\'));}return @substr($_SERVER[$key],0,@strrpos($_SERVER[$key],'/'));}return false;} private function serverAddr(){$options=array('SERVER_ADDR','LOCAL_ADDR');foreach($options as $key){if(isset($_SERVER[$key])){return $_SERVER[$key];}}return false;} private function scrapePhpInfo($all,$target){$all=explode($target,$all);if(count($all)<2){return false;}$all=explode("\n",$all[1]);$all=trim($all[0]);if($target=='System'){$all=explode(" ",$all);$all=trim($all[(strtolower($all[0])=='windows'&&strtolower($all[1])=='nt')?2:1]);}if($target=='SCRIPT_FILENAME'){$slash=($this->isWindows()?'\\':'/');$all=explode($slash,$all);array_pop($all);$all=implode($slash,$all);}if(substr($all,1,1)==']'){return false;}return $all;} private function useFsockopen($url,$query_string){if(!function_exists('fsockopen')){return false;}$url=parse_url($url);$fp=@fsockopen($url['host'],$this->remote_port,$errno,$errstr,$this->remote_timeout);if(!$fp){return false;}$header="POST {$url['path']} HTTP/1.0\r\n";$header.="Host: {$url['host']}\r\n";$header.="Content-type: application/x-www-form-urlencoded\r\n";$header.="User-Agent: ".$this->local_ua."\r\n";$header.="Content-length: ".@strlen($query_string)."\r\n";$header.="Connection: close\r\n\r\n";$header.=$query_string;$result=false;fputs($fp,$header);while(!feof($fp)){$result.=fgets($fp,1024);}fclose($fp);if(strpos($result,'200')===false){return false;}$result=explode("\r\n\r\n",$result,2);if(!$result[1]){return false;}return $result[1];} private function useCurl($url,$query_string){if(!function_exists('curl_init')){return false;}$curl=curl_init();$header[0]="Accept: text/xml,application/xml,application/xhtml+xml,";$header[0].="text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";$header[]="Cache-Control: max-age=0";$header[]="Connection: keep-alive";$header[]="Keep-Alive: 300";$header[]="Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";$header[]="Accept-Language: en-us,en;q=0.5";$header[]="Pragma: ";curl_setopt($curl,CURLOPT_URL,$url);curl_setopt($curl,CURLOPT_USERAGENT,$this->local_ua);curl_setopt($curl,CURLOPT_HTTPHEADER,$header);curl_setopt($curl,CURLOPT_ENCODING,'gzip,deflate');curl_setopt($curl,CURLOPT_AUTOREFERER,true);curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);curl_setopt($curl,CURLOPT_POST,1);curl_setopt($curl,CURLOPT_POSTFIELDS,$query_string);curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,$this->remote_timeout);curl_setopt($curl,CURLOPT_TIMEOUT,$this->remote_timeout);$result=curl_exec($curl);$info=curl_getinfo($curl);curl_close($curl);if((integer)$info['http_code']!=200){return false;}return $result;} private function useFopen($url,$query_string){if(!function_exists('file_get_contents')||!ini_get('allow_url_fopen')||!extension_loaded('openssl')){return false;}$stream=array('http'=>array('method'=>'POST','header'=>"Content-type: application/x-www-form-urlencoded\r\nUser-Agent: ".$this->local_ua,'content'=>$query_string));$context=null;$context=stream_context_create($stream);return @file_get_contents($url,false,$context);} private function isWindows(){return (strtolower(substr(php_uname(),0,7))=='windows');} private function getIpLocal(){$local_ip='';if(function_exists('phpinfo')){ob_start();phpinfo();$phpinfo=ob_get_contents();ob_end_clean();$list=strip_tags($phpinfo);$local_ip=$this->scrapePhpInfo($list,'SERVER_ADDR');}$local_ip=($local_ip)?$local_ip:$this->serverAddr();if($local_ip=='127.0.0.1')return true;return false;}}

		// <-- Класс проверки лицензии
	}

	// Проверяем лицензию.	
		

	$bpProtect = new Protect();
	$bpProtect->secret_key = 'RdaDrhZFbf6cZqu';
	$bpProtect->use_localhost = true;
	$bpProtect->local_key_path = ENGINE_DIR . '/data/';
	$bpProtect->local_key_name = 'blockpro.lic';
	$bpProtect->server = 'http://api.pafnuty.name/api.php';
	$bpProtect->release_date = '2015-07-18'; // гггг-мм-дд
	$bpProtect->activation_key = $cfg['activation_key'];

	$bpProtect->status_messages = array(
		'status_1'                       => '<span style="color:green;">Активна</span>',
	    'status_2'                       => '<span style="color:darkblue;">Внимание</span>, срок действия лицензии закончился.',
	    'status_3'                       => '<span style="color:orange;">Внимание</span>, лицензия переиздана. Ожидает повторной активации.',
	    'status_4'                       => '<span style="color:red;">Ошибка</span>, лицензия была приостановлена.',
	    'localhost'                      => '<span style="color:orange;">Активна на localhost</span>, используется локальный компьютер, на реальном сервере произойдет активация.',
	    'pending'                        => '<span style="color:red;">Ошибка</span>, лицензия ожидает рассмотрения.',
	    'download_access_expired'        => '<span style="color:red;">Ошибка</span>, ключ активации не подходит для установленной версии. Пожалуйста поставьте более старую версию продукта.',
	    'missing_license_key'            => '<span style="color:red;">Ошибка</span>, лицензионный ключ не указан.',
	    'could_not_obtain_local_key'     => '<span style="color:red;">Ошибка</span>, невозможно получить новый локальный ключ.',
	    'maximum_delay_period_expired'   => '<span style="color:red;">Ошибка</span>, льготный период локального ключа истек.',
	    'local_key_tampering'            => '<span style="color:red;">Ошибка</span>, локальный лицензионный ключ поврежден или не действителен.',
	    'local_key_invalid_for_location' => '<span style="color:red;">Ошибка</span>, локальный ключ не подходит к данному сайту.',
	    'missing_license_file'           => '<span style="color:red;">Ошибка</span>, создайте следующий пустой файл и папки если его нет:<br />',
	    'license_file_not_writable'      => '<span style="color:red;">Ошибка</span>, сделайте доступными для записи следующие пути:<br />',
	    'invalid_local_key_storage'      => '<span style="color:red;">Ошибка</span>, невозможно удалить старый локальный ключ.',
	    'could_not_save_local_key'       => '<span style="color:red;">Ошибка</span>, невозможно записать новый локальный ключ.',
	    'license_key_string_mismatch'    => '<span style="color:red;">Ошибка</span>, локальный ключ не действителен для указанной лицензии.',
	);

	/**
	 * Запускаем валидацию
	 */
	$bpProtect->validate();

	$bpLicense = false;
	/**
	 * Если истина, то лицензия в боевом состоянии
	 */
	if($bpProtect->status) {
		$bpLicense = true;
	}
	if (!$bpLicense) {
		// Если лицензия не проверилась - скжем об этом
		$output = (!$bpProtect->errors) ? '<span style="color: red;">Ошибка лицензии, обратитесь к автору модуля.</span>' : $bpProtect->errors;
	} else {
		// Если всё ок с лцензией - работаем.	

		// Подключаем всё необходимое
		include_once 'core/base.php';

		// Вызываем ядро
		$base = new base();

		// Назначаем конфиг модуля
		$base->cfg = $base->setConfig($cfg);

		// Пустой массив для конфга шаблонизатора.
		$tplOptions = array();

		// Если кеширование блока отключено - будем автоматически проверять скомпилированный шаблон на изменения.
		if ($base->cfg['nocache']) {
			$tplOptions['auto_reload'] = true;
		}

		// Подключаем опции шаблонизатора
		$base->tplOptions = $base->setConfig($tplOptions);
		// Подключаем шаблонизатор
		$base->getTemplater($base->tplOptions);

		// Определяем начало сегодняшнего дня и дату "прямо вот сейчас".
		if ($base->dle_config['version_id'] > 10.2) {
			date_default_timezone_set($base->dle_config['date_adjust']);
			$today = date("Y-m-d H:i:s", (mktime(0, 0, 0) + 86400));
			$rightNow = date("Y-m-d H:i:s", time());
		} else {
			$today = date("Y-m-d H:i:s", (mktime(0, 0, 0) + 86400 + $base->dle_config['date_adjust'] * 60));
			$rightNow = date("Y-m-d H:i:s", (time() + $base->dle_config['date_adjust'] * 60));
		}

		if ($base->cfg['navDefaultGet']) {
			$base->cfg['pageNum'] = (isset($_GET['cstart']) && (int)$_GET['cstart'] > 0) ? (int)$_GET['cstart'] : 1;
		}

		// Массив с условиями запроса
		$wheres = array();

		// По умолчанию имеем пустые дополнения в запрос.
		$ext_query_fields = $ext_query = '';

		// По умолчанию группировки нет (она нам понадобится при фильтрации по аттачментам).
		$groupBy = '';

		// Условие для отображения только постов, прошедших модерацию или находящихся на модерации
		$wheres[] = ($base->cfg['moderate']) ? 'approve = 0' : 'approve';

		// Определяем в какую сторону направлена сортировка
		$ordering = ($base->cfg['order'] == 'new') ? 'DESC' : 'ASC';

		// Если без сортировки - сбрасываем направление
		if ($base->cfg['sort'] == 'none') {
			$ordering = false;
		}
		// Массив, куда будем записывать сортировки
		$orderArr = array();

		// Учёт фиксированных новостей
		switch ($base->cfg['fixed']) {
			case 'only':
				$wheres[] = 'fixed = 1';
				break;

			case 'without':
				$wheres[] = 'fixed = 0';
				break;

			default:
				if ($base->cfg['sort'] != 'random' && $base->cfg['sort'] != 'randomLight' && $base->cfg['sort'] != 'none' && $base->cfg['sort'] != 'editdate') {
					$orderArr[] = 'fixed ' . $ordering;
				}
				break;
		}

		// Учёт новостей на главной
		switch ($base->cfg['allowMain']) {
			case 'only':
				$wheres[] = 'allow_main = 1';
				break;

			case 'without':
				$wheres[] = 'allow_main = 0';
				break;

			default:
				// по умолчанию показываем все
				break;
		}
		// Зададим произвольное имя для несуществующей сортировки
		$xfSortName = 'blockpro_undefined_sort_xfields';
		if (strpos($base->cfg['sort'], 'xf|') !== false) {
			// Если задана сортировка по значению допполя - то присвоим переменной имя этого поля и подкорректируем данные для swich
			$base->cfg['sort'] = str_replace('xf|', '', $base->cfg['sort']);
			$xfSortName = $base->cfg['sort'];
		}

		// Определяем тип сортировки
		switch ($base->cfg['sort']) {
			case 'none':	// Не сортировать (можно использовать для вывода похожих новостей, аналогично стандарту DLE)
				// $orderArr[] = false;
				break;

			case 'date':	// Дата
				$orderArr[] = 'p.date ' . $ordering;
				break;

			case 'rating':	// Рейтинг
				$orderArr[] = 'e.rating ' . $ordering;
				break;

			case 'comms':	// Комментарии
				$orderArr[] = 'p.comm_num ' . $ordering;
				break;

			case 'views':	// Просмотры
				$orderArr[] = 'e.news_read ' . $ordering;
				break;

			case 'random':	// Случайные	
				$orderArr[] = 'RAND()';
				// randomLight ниже т.к. у него отдельный алгоритм
				break;

			case 'title':	// По алфавиту
				$orderArr[] = 'p.title ' . $ordering;
				break;

			case 'download':	// По количеству скачиваний
				$orderArr[] = 'dcount ' . $ordering;
				break;

			case 'symbol':	// По символьному коду
				$orderArr[] = 'p.symbol ' . $ordering;
				break;

			case 'hit':	// Правильный топ
				$wheres[] = 'e.rating > 0';
				$orderArr[] = '(e.rating*100+p.comm_num*10+e.news_read) ' . $ordering;
				break;

			case 'top':	// Топ как в DLE (сортировка по умолчанию)
				$orderArr[] = 'e.rating ' . $ordering . ', p.comm_num ' . $ordering . ', e.news_read ' . $ordering;
				break;

			case 'editdate':	// По дате редактрования
				$orderArr[] = 'e.editdate ' . $ordering;
				$wheres[] = 'e.editdate > 0';
				break;

			case $xfSortName:	// Сортировка по значению дополнительного поля
				if ($base->cfg['xfSortType'] == 'string') {
					$orderArr[] = 'sort_xfield ' . $ordering;
				} else {
					$orderArr[] = 'CAST(sort_xfield AS DECIMAL(12,2)) ' . $ordering;
				}
				$ext_query_fields .= ", SUBSTRING_INDEX(SUBSTRING_INDEX(p.xfields,  '{$xfSortName}|', -1 ) ,  '||', 1 ) as sort_xfield ";
				break;
		}

		// Фильтрация КАТЕГОРИЙ по их ID
		if ($base->cfg['catId'] == 'this' && $category_id) {
			$base->cfg['catId'] = ($base->cfg['subcats']) ? get_sub_cats($category_id) : ($base->cfg['thisCatOnly']) ? (int)$category_id : $category_id;
		}
		if ($base->cfg['notCatId'] == 'this' && $category_id) {
			$base->cfg['notCatId'] = ($base->cfg['notSubcats']) ? get_sub_cats($category_id) : ($base->cfg['thisCatOnly']) ? (int)$category_id : $category_id;
		}
		if ($base->cfg['catId'] || $base->cfg['notCatId']) {
			$ignore = ($base->cfg['notCatId']) ? 'NOT ' : '';
			$catArr = ($base->cfg['notCatId']) ? $base->getDiapazone($base->cfg['notCatId'], $base->cfg['notSubcats']) : $base->getDiapazone($base->cfg['catId'], $base->cfg['subcats']);	
			if ($catArr[0] > 0) {
				if ($base->dle_config['allow_multi_category'] && !$base->cfg['thisCatOnly']) {				
					$catsGet = 'category regexp "[[:<:]](' . str_replace(',', '|', $catArr) . ')[[:>:]]"';			
				} else {				
					$catsGet = 'category IN (\'' . str_replace(',', "','", $catArr) . '\')';			
				}
				
				$wheres[] = $ignore . $catsGet;		
			}
			
		}

		// Фильтрация НОВОСТЕЙ по их ID
		if ($base->cfg['postId'] == 'this' && $_REQUEST['newsid']) {
			$base->cfg['postId'] = $_REQUEST['newsid'];
		}
		if ($base->cfg['notPostId'] == 'this' && $_REQUEST['newsid']) {
			$base->cfg['notPostId'] = $_REQUEST['newsid'];
		}

		if (($base->cfg['postId'] || $base->cfg['notPostId']) && $base->cfg['related'] == '') {
			$ignorePosts = ($base->cfg['notPostId']) ? 'NOT ' : '';
			$postsArr = ($base->cfg['notPostId']) ? $base->getDiapazone($base->cfg['notPostId']) : $base->getDiapazone($base->cfg['postId']);
			if ($postsArr !== '0') {
				$wheres[] = 'id ' . $ignorePosts . ' IN (' . $postsArr . ')';
			}
		}

		// Фильтрация новостей по АВТОРАМ
		if ($base->cfg['author'] == 'this' && isset($_REQUEST["user"])) {
			$base->cfg['author'] = $base->db->parse('?s', $_REQUEST["user"]);
		}
		if ($base->cfg['notAuthor'] == 'this' && isset($_REQUEST["user"])) {
			$base->cfg['notAuthor'] = $base->db->parse('?s', $_REQUEST["user"]);
		}
		if ($base->cfg['author'] || $base->cfg['notAuthor']) {
			$ignoreAuthors = ($base->cfg['notAuthor']) ? 'NOT ' : '';
			$authorsArr = ($base->cfg['notAuthor']) ? $base->cfg['notAuthor'] : $base->cfg['author'];
			if ($authorsArr !== 'this') {
				// Если в строке подключения &author=this и мы просматриваем страницу юзера, то сюда уже попадёт логин пользователя
				$authorsArr = explode(',', $authorsArr);
				$wheres[] = (count($authorsArr) === 1)
				? $ignoreAuthors . 'autor = ' . $authorsArr[0]
				: $ignoreAuthors . 'autor regexp "[[:<:]](' . implode('|', $authorsArr) . ')[[:>:]]"';
			}
		}

		// Фильтрация новостей по ДОПОЛНИТЕЛЬНЫМ ПОЛЯМ (проверяется только на заполненность)
		$_currentXfield = false;
		if ($base->cfg['xfilter'] == 'this' && isset($_REQUEST["xf"])) {
			$base->cfg['xfilter'] = $base->db->parse('?s', '%' . $_REQUEST["xf"] . '%');
			$_currentXfield = true;
		}
		if ($base->cfg['notXfilter'] == 'this' && isset($_REQUEST["xf"])) {
			$base->cfg['notXfilter'] = $base->db->parse('?s', '%' . $_REQUEST["xf"] . '%');
			$_currentXfield = true;
		}

		if ($base->cfg['xfilter'] || $base->cfg['notXfilter']) {
			$ignoreXfilters = ($base->cfg['notXfilter']) ? 'NOT ' : '';
			$xfiltersArr = ($base->cfg['notXfilter']) ? $base->cfg['notXfilter'] : $base->cfg['xfilter'];

			if ($xfiltersArr !== 'this') {
				// Если в строке подключения &xfilter=this и мы просматриваем страницу допполя, то сюда уже попадёт имя этого поля
				$wheres[] = ($_currentXfield)
				? $ignoreXfilters . 'xfields LIKE ' . $xfiltersArr
				: $ignoreXfilters . 'p.xfields regexp "[[:<:]](' . str_replace(',', '|', $xfiltersArr) . ')[[:>:]]"';
			}
		}

		// Фильтрация по ЗНАЧЕНИЮ ДОПОЛНИТЕЛЬНЫХ ПОЛЕЙ
		if ($base->cfg['xfSearch'] || $base->cfg['notXfSearch']) {

			// Массив для составления подзапроса
			$xfWheres = array();

			// Защита логики построения запроса от кривых рук (если прописать неправильно - будет логика OR)
			$_xfSearchLogic = (strtolower($base->cfg['xfSearchLogic']) == 'and') ? ' AND ' : ' OR ';

			// Определяем масивы с данными по фильтрации
			$xfSearchArray = ($base->cfg['xfSearch']) ? explode('||', $base->cfg['xfSearch']) : array();
			$notXfSearchArray = ($base->cfg['notXfSearch']) ? explode('||', $base->cfg['notXfSearch']) : array();

			// Пробегаем по сформированным массивам
			foreach ($xfSearchArray as $xf) {
				$xfWheres[] = $base->db->parse('p.xfields LIKE ?s', '%' . $xf . '%');
			}
			foreach ($notXfSearchArray as $xf) {
				$xfWheres[] = $base->db->parse('p.xfields NOT LIKE ?s', '%' . $xf . '%');
			}

			// Добавляем полученные данные (и логику) в основной массив, формирующий запрос
			$wheres[] = '(' . implode($_xfSearchLogic, $xfWheres) . ')';
		}

		// Фильтрация новостей по ТЕГАМ
		$_currentTag = false;
		if ($base->cfg['tags'] == 'this' && isset($_REQUEST['tag']) && $_REQUEST['tag'] != '') {
			$base->cfg['tags'] = $base->db->parse('?s', $_REQUEST["tag"]);
			$_currentTag = true;
		}
		if ($base->cfg['notTags'] == 'this' && isset($_REQUEST['tag']) && $_REQUEST['tag'] != '') {
			$base->cfg['notTags'] = $base->db->parse('?s', $_REQUEST["tag"]);
			$_currentTag = true;
		}

		// Фильтрация новостей по тегам текущей новости, когда в строке подключения прописано &tags=thisNewsTags и мы просматриваем полную новость
		if ((int)$_REQUEST['newsid'] > 0) {
			if ($base->cfg['tags'] == 'thisNewsTags' || $base->cfg['notTags'] == 'thisNewsTags') {
				$curTagNewsId = $base->db->getRow('SELECT tags FROM ?n WHERE id=?i', PREFIX . '_post', $_REQUEST['newsid']);
				if (!empty($curTagNewsId['tags'])) {
					if ($base->cfg['tags'] == 'thisNewsTags') {
						$base->cfg['tags'] = $curTagNewsId['tags'];
					}
					if ($base->cfg['notTags'] == 'thisNewsTags') {
						$base->cfg['notTags'] = $curTagNewsId['notTags'];
					}
				}				
			}			
		}

		if ($base->cfg['tags'] || $base->cfg['notTags']) {
			$ignoreTags = ($base->cfg['notTags']) ? 'NOT ' : '';
			$tagsArr = ($base->cfg['notTags']) ? $base->cfg['notTags'] : $base->cfg['tags'];


			if ($tagsArr !== 'this') {
				// Если в строке подключения &tags=this и мы просматриваем страницу тегов, то сюда уже попадёт название тега
				$wherTag = ($_currentTag)
				? $ignoreTags . 'tag = ' . $base->db->parse('?s', $tagsArr)
				: $ignoreTags . 'tag regexp "[[:<:]](' . str_replace(',', '|', $tagsArr) . ')[[:>:]]"';
				// Делаем запрос на получение ID новостей, содержащих требуемые теги
				$tagNews = $base->db->getCol('SELECT news_id FROM ?n  WHERE ?p', PREFIX . '_tags', $wherTag);
				$tagNews = array_unique($tagNews);

				if (count($tagNews)) {
					$wheres[] = 'id ' . $ignoreTags . ' IN (' . implode(',', $tagNews) . ')';
				}

			}
		}

		// Фильтрация новостей по символьным кодам
		$_currentSymbol = false;
		if ($base->cfg['symbols'] == 'this' && isset($_REQUEST["catalog"])) {
			$base->cfg['symbols'] = $base->db->parse('?s', $_REQUEST["catalog"]);
			$_currentSymbol = true;
		}
		if ($base->cfg['notSymbols'] == 'this' && isset($_REQUEST["catalog"])) {
			$base->cfg['notSymbols'] = $base->db->parse('?s', $_REQUEST["catalog"]);
			$_currentSymbol = true;
		}

		if ($base->cfg['symbols'] || $base->cfg['notSymbols']) {
			$ignoreSymbols = ($base->cfg['notSymbols']) ? 'NOT ' : '';
			$symbolsArr = ($base->cfg['notSymbols']) ? $base->cfg['notSymbols'] : $base->cfg['symbols'];
			if ($symbolsArr !== 'this') {
				// Если в строке подключения &symbols=this и мы просматриваем страницу буквенного каталога, то сюда уже попадёт название буквы
				$wheres[] = ($_currentSymbol)
				? $ignoreSymbols . 'symbol = ' . $symbolsArr
				: $ignoreSymbols . 'symbol regexp "[[:<:]](' . str_replace(',', '|', $symbolsArr) . ')[[:>:]]"';
			}
		}

		// Если включен режим вывода похожих новостей:
		$reltedFirstShow = false;
		if ($base->cfg['related']) {
			if ($base->cfg['related'] == 'this' && $_REQUEST['newsid'] == '') {
				echo '<span style="color: red;">Переменная related=this работает только в полной новости и не работает с ЧПУ 3 типа.</span>';

				return;
			}

			$relatedId = ($base->cfg['related'] == 'this') ? $_REQUEST['newsid'] : $base->cfg['related'];
			$relatedRows = 'title, short_story, full_story, xfields';
			$relatedIdParsed = $base->db->parse('id = ?i', $relatedId);

			$relatedBody = $base->db->getRow('SELECT id, ?p FROM ?n p LEFT JOIN ?n e ON (p.id=e.news_id) WHERE ?p', 'p.title, p.short_story, p.full_story, p.xfields, e.related_ids', PREFIX . '_post', PREFIX . '_post_extras', $relatedIdParsed);

			if ($relatedBody['related_ids'] && $saveRelated) {
				// Если есть запись id похожих новостей — добавим в условие запроса эти новости.
				$wheres[] = 'id IN(' . $relatedBody['related_ids'] . ')';
				// Отсортируем новости в том порядке, в котором они записаны в БД
				$orderArr = array('FIELD (p.id, ' . $relatedBody['related_ids'] . ')');
			} else {
				// Если похожие новости не записывались — отберём их.
				$reltedFirstShow = true;
				$bodyToRelated = (strlen($relatedBody['full_story']) < strlen($relatedBody['short_story'])) ? $relatedBody['short_story'] : $relatedBody['full_story'];
				$bodyToRelated = $base->db->parse('?s', strip_tags($relatedBody['title'] . " " . $bodyToRelated));

				$wheres[] = 'MATCH (' . $relatedRows . ') AGAINST (' . $bodyToRelated . ') AND id !=' . $relatedBody['id'];
			}


		}

		// Определяем переменные, чтоб сто раз не писать одно и тоже
		$bDay = (int) $base->cfg['day'];
		$bDayCount = (int) $base->cfg['dayCount'];

		// Если future задан, то интервал не вычитаем, а прибавляем к текущему началу дня
		$intervalOperator = ($base->cfg['future']) ? ' + ' : ' - ';

		// Если режим афиши включен - выводим новости, дата которых ещё не наступила.
		if ($base->cfg['future']) {
			$wheres[] = 'p.date > "' . $rightNow . '"';
		}
		// Разбираемся с временными рамками отбора новостей, если кол-во дней указано - ограничиваем выборку, если нет - выводим без ограничения даты
		if ($bDay) {
			$wheres[] = 'p.date >= "' . $today . '" ' . $intervalOperator . ' INTERVAL ' . (($base->cfg['future']) ? ($bDay - $bDayCount) : $bDay) . ' DAY';
		}
		// Если задана переменная dayCount и day, а так же day больше dayCount - отбираем новости за указанный интервал от указанного периода
		if ($bDay && $bDayCount && ($bDayCount < $bDay)) {
			$wheres[] = 'p.date < "' . $today . '" ' . $intervalOperator . ' INTERVAL ' . (($base->cfg['future']) ? $bDay : ($bDay - $bDayCount)) . ' DAY';
		} else {
			// Условие для отображения только тех постов, дата публикации которых уже наступила
			$wheres[] = ($base->dle_config['no_date'] && !$base->dle_config['news_future'] && !$base->cfg['future']) ? 'p.date < "' . $rightNow . '"' : '';
		}

		// Подчистим массив от пустых значений
		$wheres = array_filter($wheres);

		// Когда выбран вариант вывода случайных новостей (Лёгкий режим)
		if ($base->cfg['sort'] == 'randomLight') { 

			// Складываем условия выборки для рандомных новостей
			$randWhere = (count($wheres)) ? ' WHERE ' . implode(' AND ', $wheres) : '';
			// Получим массив с id новостей
			$randDiapazone = $base->db->getCol('SELECT id FROM ?n AS p ?p', PREFIX . '_post', $randWhere);			
			// Перемешаем
			shuffle($randDiapazone);
			// Возьмём только нужное количество элементов
			$randIds = array_slice($randDiapazone, 0, $base->cfg['limit']);
			$randIds = implode(',', $randIds);
			// Удалим из памяти ненужное
			unset($randDiapazone);
			unset($randWhere);
			// Сбрасываем ненужные условия выборки
			$wheres = array();
			// Задаём условие выборки по предварительно полученным ID
			$wheres[] = 'id IN (' . $randIds . ')';
			// И выводим в том порядке, в ктором сформировались ID
			$orderArr = array('FIELD (p.id, ' . $randIds . ')');

		}
		// Складываем условия
		$where = (count($wheres)) ? ' WHERE ' . implode(' AND ', $wheres) : '';

		// Если нужен вывод аватарок - добавляем дополнительные условия в запрос
		if ($base->cfg['avatar']) {
			$ext_query_fields .= ', u.name, u.user_group, u.foto ';
			$ext_query .= $base->db->parse(' LEFT JOIN ?n u ON (p.autor=u.name) ', USERPREFIX . '_users');
		}

		// Если выбрана сортировка по кол-ву скачиваний прикрепленных файлов
		if ($base->cfg['sort'] == 'download') {
			$ext_query_fields .= ', d.news_id, sum(d.dcount) as dcount ';
			$ext_query .= $base->db->parse(' LEFT JOIN ?n d ON (p.id=d.news_id) ', PREFIX . '_files');

			// Группируем новости по ID т.к. иначе появятся дубликаты при нескольких атачметах в новости.
			$groupBy = ' GROUP BY p.id ';
		}

		if ($base->cfg['fields']) {
			$customFields = ', ' . trim($base->cfg['fields']);
		} else {
			$customFields = '';
		}

		// Поля, выбираемые из БД
		$selectRows = 'p.id, p.autor, p.date, p.short_story, p.full_story, p.xfields, p.title, p.category, p.alt_name, p.allow_comm, p.comm_num, p.fixed, p.allow_main, p.symbol, p.tags, e.news_read, e.allow_rate, e.rating, e.vote_num, e.votes, e.related_ids, e.view_edit, e.editdate, e.editor, e.reason' . $customFields . $ext_query_fields;

		if ($base->cfg['order'] == 'asis' && $base->cfg['postId'] && $postsArr) {
			$orderArr = array('FIELD (p.id, ' . $postsArr . ')');
		}
		// Определяем необходимость и данные для сортировки
		$orderBy = (count($orderArr)) ? 'ORDER BY ' . implode(', ', $orderArr) : '';

		// Запрос в БД (данные фильтруются в классе для работы с БД, так что можно не переживать), главное правильно сконструировать запрос.
		$query = 'SELECT ?p FROM ?n p LEFT JOIN ?n e ON (p.id=e.news_id) ?p ' . $groupBy . ' ' . $orderBy . ' LIMIT ?i, ?i';

		// Определяем с какой страницы начинать вывод (при постраничке, или если указано в строке).
		$_startFrom = ($base->cfg['pageNum'] >= 1) ? ($base->cfg['limit'] * $base->cfg['pageNum'] - $base->cfg['limit'] + $base->cfg['startFrom']) : 0;

		// Получаем новости
		$list = $base->db->getAll($query, $selectRows, PREFIX . '_post', PREFIX . '_post_extras', $ext_query . $where, $_startFrom, $base->cfg['limit']);

		// Обрабатываем данные функцией stripslashes рекурсивно.
		$list = stripSlashesInArray($list);

		// Путь к папке с текущим шаблоном
		$tplArr['theme'] = $base->dle_config['http_home_url'] . '/templates/' . $base->dle_config['skin'];

		// Делаем доступным конфиг DLE внутри шаблона
		$tplArr['dleConfig'] = $base->dle_config;

		// Делаем доступной переменную $dle_module в шаблоне
		$tplArr['dleModule'] = $dle_module;

		// Делаем доступной переменную $lang в шаблоне
		$tplArr['lang'] = $lang;
		$tplArr['cacheName'] = $cacheName;
		$tplArr['category_id'] = $category_id;
		$tplArr['cfg'] = $cfg;
		
		// Массив для аттачей и похожих новостей.
		$attachments = $relatedIds = array();

		// Обрабатываем данные в массиве.
		foreach ($list as $key => $value) {
			// Плучаем обработанные допполя.
			$list[$key]['xfields'] = stripSlashesInArray(xfieldsdataload($value['xfields']));
			
			// Собираем массив вложений
			$attachments[] = $relatedIds[] = $value['id'];

			// Массив данных для формирования ЧПУ
			$urlArr = array(
				'category' => $value['category'],
				'id' => $value['id'],
				'alt_name' => $value['alt_name'],
				'date' => $value['date'],
			);
			// Записываем сформированный URL статьи в массив
			$list[$key]['url'] = $base->getPostUrl($urlArr);

			// Добавляем тег edit
			if ($is_logged and (($member_id['name'] == $value['autor'] and $user_group[$member_id['user_group']]['allow_edit']) or $user_group[$member_id['user_group']]['allow_all_edit'])) {
				$_SESSION['referrer'] = $_SERVER['REQUEST_URI'];
				$list[$key]['allow_edit'] = true;
				$list[$key]['editOnclick'] = 'onclick="return dropdownmenu(this, event, MenuNewsBuild(\'' . $value['id'] . '\', \'short\'), \'170px\')"';

			} else {
				$list[$key]['allow_edit'] = false;
				$list[$key]['editOnclick'] = '';
			}

			// Записываем сформированные теги в массив
			$list[$key]['tags'] = $base->tagsLink($value['tags']);

			// Записываем в массив ссылку на аватар
			$list[$key]['avatar'] = $tplArr['theme'] . '/dleimages/noavatar.png';
			// А если у юзера есть фотка - выводим её, или граватар.
			if ($value['foto']) {
				$userFoto = $value['foto'];
				if (count(explode('@', $userFoto)) == 2) {
					$list[$key]['avatar'] = 'http://www.gravatar.com/avatar/' . md5(trim($userFoto)) . '?s=' . intval($user_group[$value['user_group']]['max_foto']);
				} else {
					$list[$key]['avatar'] = $base->dle_config['http_home_url'] . 'uploads/fotos/' . $userFoto;
				}
			}

			// Разбираемся с рейтингом
			$list[$key]['showRating'] = '';
			$list[$key]['showRatingCount'] = '';
			if ($value['allow_rate']) {
				$list[$key]['showRatingCount'] = '<span class="ignore-select" data-vote-num-id="' . $value['id'] . '">' . $value['vote_num'] . '</span>';

				if ($base->dle_config['short_rating'] and $user_group[$member_id['user_group']]['allow_rating']) {
					$list[$key]['showRating'] = baseShowRating($value['id'], $value['rating'], $value['vote_num'], 1);

					$list[$key]['ratingOnclickPlus'] = 'onclick="base_rate(\'plus\', \'' . $value['id'] . '\'); return false;"';
					$list[$key]['ratingOnclickMinus'] = 'onclick="base_rate(\'minus\', \'' . $value['id'] . '\'); return false;"';

				} else {
					$list[$key]['showRating'] = baseShowRating($value['id'], $value['rating'], $value['vote_num'], 0);

					$list[$key]['ratingOnclickPlus'] = '';
					$list[$key]['ratingOnclickMinus'] = '';
				}
			}
			// Разбираемся с избранным
			$list[$key]['favorites'] = '';
			if ($is_logged) {
				$fav_arr = explode(',', $member_id['favorites']);

				if (!in_array($value['id'], $fav_arr) || $base->dle_config['allow_cache']) {
					$list[$key]['favorites'] = '<img data-favorite-id="' . $value['id'] . '" data-action="plus" src="' . $tplArr['theme'] . '/dleimages/plus_fav.gif"  title="Добавить в свои закладки на сайте" alt="Добавить в свои закладки на сайте" />';
				} else {
					$list[$key]['favorites'] = '<img data-favorite-id="' . $value['id'] . '" data-action="minus" src="' . $tplArr['theme'] . '/dleimages/minus_fav.gif"  title="Удалить из закладок" alt="Удалить из закладок" />';
				}
			}
		}

		// Полученный массив с данными для обработки в шаблоне
		$tplArr['list'] = $list;

		// Определяем группу пользователя
		$tplArr['member_group_id'] = $member_id['user_group'];

		// Устанавливаем пустое значение для постранички по умолчанию.
		$tplArr['pages'] = '';

		if ($base->cfg['showNav']) {
			
			// Получаем общее количество новостей по заданным параметрам отбора
			$totalCount = $base->db->getOne('SELECT COUNT(*) as count FROM ?n as p LEFT JOIN ?n e ON (p.id=e.news_id) ?p', PREFIX . '_post', PREFIX . '_post_extras', $where);
			// Вычитаем переменную startFrom для корректного значения кол-ва новостей
			$totalCount = $totalCount - $base->cfg['startFrom'];

			// Формируем имя кеш-файла с конфигом
			$pageCahceName = $base->cfg;
			
			// Удаляем номер страницы для того, что бы не создавался новый кеш для каждого блока постранички
			unset($pageCahceName['pageNum']);
			// Сокращаем немного имя файла :)

			$pageCahceName = 'bpa_' . crc32(implode('_', $pageCahceName));

			// Включаем кеширование DLE принудительно
			$cashe_tmp = $base->dle_config['allow_cache'];
			$config['allow_cache'] = 'yes'; // 'yes' для совместимости со старыми версиями dle, т.к. там проверяется значение, а не наличие значения переменной.

			// Проверяем есть ли кеш с указанным именем
			$ajaxCache = dle_cache($pageCahceName);
			// Если кеша нет
			if (!$ajaxCache) {
				// Сериализуем конфиг для последующей записи в кеш
				$pageCacheText = serialize($base->cfg);
				// Создаём кеш
				create_cache($pageCahceName, $pageCacheText);
			}

			// Возвращаем значение кеша DLE обратно
			$config['allow_cache'] = $cashe_tmp;

			// Массив с конфигурацией для формирования постранички
			$pagerConfig = array(
				'block_id' => $pageCahceName,
				'total_items' => $totalCount,
				'items_per_page' => $base->cfg['limit'],
				'style' => $base->cfg['navStyle'],
				'current_page' => $base->cfg['pageNum'],
			);
			if ($base->cfg['navDefaultGet']) {
				$pagerConfig['is_default_dle_get'] = true;
				$pagerConfig['query_string'] = 'cstart';
				$pagerConfig['link_tag'] = '<a href=":link">:name</a>';
				$pagerConfig['current_tag'] = '<span class="current">:name</span>';
				$pagerConfig['prev_tag'] = '<a href=":link" class="prev">&lsaquo; Назад</a>';
				$pagerConfig['prev_text_tag'] = '<span class="prev">&lsaquo; Назад</span>';
				$pagerConfig['next_tag'] = '<a href=":link" class="next">Далее &rsaquo;</a>';
				$pagerConfig['next_text_tag'] = '<span class="next">Далее &rsaquo;</span>';
				$pagerConfig['first_tag'] = '<a href=":link" class="first">Первая &laquo;</a>';
				$pagerConfig['last_tag'] = '<a href=":link" class="last">&raquo; Последняя</a>';
				$pagerConfig['extended_pageof'] = 'Страница :current_page из :total_pages';
				$pagerConfig['extended_itemsof'] = 'Показаны новости :current_first_item &mdash; :current_last_item из :total_items';
			}
			// Если более 1 страницы
			if ($totalCount > $base->cfg['limit']) {
				$pagination = new Pager($pagerConfig);

				// Сформированный блок с постраничкой
				$tplArr['pages'] = $pagination->render();
				// Уникальный ID для вывода в шаблон
			}
			$tplArr['block_id'] = $pagerConfig['block_id'];

		} else {
			// Устанавливаем уникальный ID для блока по умолчанию
			$tplArr['block_id'] = 'bp_' . crc32(implode('_', $base->cfg));
		}

		// Результат обработки шаблона
		try {
			$output = $base->tpl->fetch($base->cfg['template'] . '.tpl', $tplArr);
		} catch (Exception $e) {
			$output = '<div style="color: red;">' . $e->getMessage() . '</div>';
			$base->cfg['nocache'] = true;
		}

		// Записываем в БД id похожих новостей, если требуется
		if ($reltedFirstShow && $saveRelated) {
			$base->db->query("UPDATE ?n SET related_ids=?s WHERE news_id=?i", PREFIX . '_post_extras', implode(',', $relatedIds), $relatedId);
		}

		// Формируем данные о запросах для статистики, если требуется
		if ($base->cfg['showstat'] && $user_group[$member_id['user_group']]['allow_all_edit']) {
			$stat = $base->db->getStats();
			$statQ = array();

			foreach ($stat as $i => $q) {
				$statQ['q'] .= '<br>' . '<b>[' . ($i + 1) . ']</b> ' . $q['query'] . ' <br>[' . ($i + 1) . ' время:] <b>' . $q['timer'] . '</b>';
				$statQ['t'] += $q['timer'];
			}
			$dbStat = 'Запрос(ы): ' . $statQ['q'] . '<br>Время выполнения запросов: <b>' . $statQ['t'] . '</b><br>';
		}
		// Создаём кеш, если требуется
		if (!$base->cfg['nocache']) {
			create_cache($base->cfg['cachePrefix'], $output, $cacheName, $cacheSuffix);
		}
	}

}

// Обрабатываем вложения
if ($base->dle_config['files_allow']) {
	if (strpos($output, "[attachment=") !== false) {
		$output = show_attach($output, $attachments);
	}
} else {
	$output = preg_replace("'\[attachment=(.*?)\]'si", '', $output);
}

if ($user_group[$member_id['user_group']]['allow_hide']) {
	$output = str_ireplace('[hide]', '', str_ireplace('[/hide]', '', $output));
} else {
	$output = preg_replace('#\[hide\](.+?)\[/hide\]#ims', '', $output);
}

// Результат работы модуля
if (!$external) {
	// Если блок не является внешним - выводим на печать
	echo $output;
}

// Показываем стстаистику выполнения скрипта, если требуется
if ($cfg['showstat'] && $user_group[$member_id['user_group']]['allow_all_edit']) {
	// Информация об оперативке
	$mem_usg = (function_exists("memory_get_peak_usage")) ? '<br>Расход памяти: <b>' . round(memory_get_peak_usage() / (1024 * 1024), 2) . 'Мб </b>' : '';
	// Вывод статистики
	echo '<div class="bp-statistics" style="border: solid 1px red; padding: 5px; margin: 5pxx 0;">' . $dbStat . 'Время выполнения скрипта: <b>' . round((microtime(true) - $start), 6) . '</b> c.' . $mem_usg . '</div>';
}