<?php
define('NV_SYSTEM', true);

// Tested on NukeViet 4.1 Beta
// Xac dinh thu muc goc cua site

define('NV_ROOTDIR', pathinfo(str_replace(DIRECTORY_SEPARATOR, '/', __file__), PATHINFO_DIRNAME));

require NV_ROOTDIR . '/includes/mainfile.php';

// Dien ten module goc ( news, shops, ...)
// $module = 'news';
$module = 'shops';
$module = str_replace("-", "_", change_alias($module));

// Dien ten module Ao cua module muon copy
// Can phai luu y chon chinh xac, neu khong se hong cau truc module
// $clone = 'articles';
$clone = 'product';
$clone = str_replace("-", "_", change_alias($clone));

// Khai bao ngon ngu data muon copy. De trong neu muon copy toan bo
// $lang = 'en';
$lang = 'vi';

// Neu la module shops thi copy toan bo vi module nay hoi tao lao chut
if($module == 'shops'){
	$source_module = (!empty($lang)) ? $db_config['prefix'] . '_' . $module : $module;
	$target_module = (!empty($lang)) ? $db_config['prefix'] . '_' . $clone : $clone;
}
else{
	$source_module = (!empty($lang)) ? $db_config['prefix'] . '_' . $lang . '_' . $module : $module;
	$target_module = (!empty($lang)) ? $db_config['prefix'] . '_' . $lang . '_' . $clone : $clone;
}

// Kiem tra Module nguon
$check_module = $db->query("SELECT module_data FROM " . $db_config['prefix'] . "_" . $lang . "_modules WHERE module_file=" . $db->quote($module))->fetchColumn();
if( empty($check_module)){
	die('Base module "' . $module . '" is not exist/not installed !');
}

// Kiem tra Module dich
$check_clone = $db->query("SELECT module_data FROM " . $db_config['prefix'] . "_" . $lang . "_modules WHERE module_file=" . $db->quote($module) . " AND module_data=" . $db->quote($clone))->fetchColumn();
if(empty($check_clone)){
	die('Clone module "' . $clone . '" is not exist/not installed !');
}

// Lay danh sach table cua module goc
try {
	$query = $db->query("SHOW TABLES LIKE '%" . $source_module . "%'");
	$tables = $query->fetchAll(3);

	// Kiem tra xem module nguon co cac table khong
	if(!empty($tables)){
		foreach( $tables as $table){
			$target_table = str_replace($module, $clone, $table[0]);
			
			// Drop toan bo data cua module ao
			try{
				$query = $db->query("DROP TABLE " . $target_table);
			} catch (PDOException $e) {
				trigger_error($e->getMessage());
			}
			
			// Copy data tu module goc
			try{
				$query = $db->query("CREATE TABLE " . $target_table . " AS SELECT * FROM " . $table[0]);
			} catch (PDOException $e) {
				trigger_error($e->getMessage());
			}
		}
	}else{
		// Module goc khong ton tai hoac chua duoc cai
		die('No table(s) of Base module or not installed !');
	}

} catch (PDOException $e) {
	trigger_error($e->getMessage());
}
die('Clone successful ! Please check all error(s) ouput and delete this file !');