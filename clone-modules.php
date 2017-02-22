<?php
define('NV_SYSTEM', true);

// Tested on NukeViet 4.1 Beta
// Xac dinh thu muc goc cua site

define('NV_ROOTDIR', pathinfo(str_replace(DIRECTORY_SEPARATOR, '/', __file__), PATHINFO_DIRNAME));

require NV_ROOTDIR . '/includes/mainfile.php';

// Dien ten module goc ( news, shops, ...)
$module = 'shops';

//Dien ten module Ao cua module muon copy
// Can phai luu y chon chinh xac, neu khong se hong cau truc module
$clone = 'product';

$clone = str_replace("-", "_", change_alias($clone));

// Lay danh sach table cua module goc
try {
	$query = $db->query("SHOW TABLES LIKE '%_" . $module . "_%'");
	$tables = $query->fetchAll(3);
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

} catch (PDOException $e) {
	trigger_error($e->getMessage());
}
