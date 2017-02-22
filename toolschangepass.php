<?php

define( 'NV_SYSTEM', true );

define('NV_ROOTDIR', pathinfo(str_replace(DIRECTORY_SEPARATOR, '/', __file__), PATHINFO_DIRNAME));

require NV_ROOTDIR . '/includes/mainfile.php';

// change username and password if not know old username - Thay luôn username nếu không nhớ.
$newusername = '';

// change  password if know username -  chỉ thay pass nếu nhớ username
$username = '';

$newpassword = '';

$newpassword = $crypt->hash( trim( $newpassword ) );



if( !empty( $username ) )
{
	$md5username = nv_md5safe( $username );
	
	$db->sqlreset()->select('COUNT( * )')->from( NV_USERS_GLOBALTABLE )->where( 'md5username =' . $db->quote( $md5username ) );
	$countuser = $db->query( $db->sql() )->fetchColumn();
	if( $countuser > 0 )
	{
		$stmt = $db->prepare( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET password= :password WHERE md5username=' . $db->quote( $md5username ) );
		$stmt->bindParam( ':password', $newpassword, PDO::PARAM_STR );
		if( ! $stmt->execute() )
		{
			die( 'No Reset password' );
		}

	}else
	{
		die('Not found username in database');
	}
	
	
}elseif( !empty( $newusername ) && empty( $username ) )
{
	$md5newusername = nv_md5safe( $newusername );
	
	$db->sqlreset()->select('admin_id')->from( NV_AUTHORS_GLOBALTABLE )->where( 'lev = 1' )->order( 'admin_id ASC' )->limit( '1' );
	
	$admin_id = $db->query( $db->sql() )->fetchColumn();
	$stmt = $db->prepare( 'UPDATE ' . NV_USERS_GLOBALTABLE . ' SET password= :password, username= :username, md5username= :md5username WHERE userid=' . intval( $admin_id ) );
	$stmt->bindParam( ':password', $newpassword, PDO::PARAM_STR );
	$stmt->bindParam( ':username', $newusername, PDO::PARAM_STR );
	$stmt->bindParam( ':md5username', $md5newusername, PDO::PARAM_STR );
	if( ! $stmt->execute() )
	{
		die( 'No Reset password' );
	}
	
}else
{
	die('Please fill username one of $username or $newusername, note: not fill full two variable');
}
die('Change successful please delete this file');