<?php
/**
 * @name eolinker open source，eolinker开源版本
 * @link https://www.eolinker.com
 * @package eolinker
 * @author www.eolinker.com 广州银云信息科技有限公司 ©2015-2016

 *  * eolinker，业内领先的Api接口管理及测试平台，为您提供最专业便捷的在线接口管理、测试、维护以及各类性能测试方案，帮助您高效开发、安全协作。
 * 如在使用的过程中有任何问题，欢迎加入用户讨论群进行反馈，我们将会以最快的速度，最好的服务态度为您解决问题。
 * 用户讨论QQ群：284421832
 *
 * 注意！eolinker开源版本仅供用户下载试用、学习和交流，禁止“一切公开使用于商业用途”或者“以eolinker开源版本为基础而开发的二次版本”在互联网上流通。
 * 注意！一经发现，我们将立刻启用法律程序进行维权。
 * 再次感谢您的使用，希望我们能够共同维护国内的互联网开源文明和正常商业秩序。
 *
 */
class GuestDao
{
	/**
	 * 用户名注册
	 */
	public function register(&$userName, &$hashPassword, &$userNickName)
	{
		$db = getDatabase();

		//判断是否已存在用户
		$result = $db -> prepareExecute('SELECT eo_user.userID FROM eo_user WHERE userName=?;', array($userName));

		//已存在则返回
		if (!empty($result))
			return FALSE;

		//若不存在则插入
		$result = $db -> prepareExecute('INSERT INTO eo_user (eo_user.userName,eo_user.userPassword,eo_user.userNickName) VALUES (?,?,?);', array(
			$userName,
			$hashPassword,
			$userNickName
		));

		//插入成功
		if ($db -> getAffectRow() > 0)
			return $db -> getLastInsertID();
		else
			return FALSE;
	}

	public function checkUserNameExist(&$userName)
	{
		$db = getDatabase();

		$result = $db -> prepareExecute('SELECT * FROM eo_user WHERE eo_user.userName = ?;', array($userName));

		if (empty($result))
			return TRUE;
		else
			return FALSE;
	}

	public function getLoginInfo(&$loginName)
	{
		$db = getDatabase();

		$result = $db -> prepareExecute('SELECT eo_user.userID,eo_user.userName,eo_user.userPassword,eo_user.userNickName FROM eo_user WHERE eo_user.userName = ?;', array($loginName));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

}
?>