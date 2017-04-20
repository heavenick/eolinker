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
class UserModule
{
	public function __construct()
	{
		@session_start();
	}

	/**
	 * 获取用户信息
	 */
	public function getUserInfo()
	{
		$dao = new MessageDao;
		$userInfo['userID'] = $_SESSION['userID'];
		$userInfo['userNickName'] = $_SESSION['userNickName'];
		$userInfo['userName'] = $_SESSION['userName'];
		$userInfo['unreadMsgNum'] = $dao -> getUnreadMessageNum($_SESSION['userID']);
		return $userInfo;
	}

	/**
	 * 修改密码
	 * @param $oldPassword 旧密码
	 * @param $newPassword 新密码
	 */
	public function changePassword(&$oldPassword, &$newPassword)
	{
		$guestDao = new GuestDao;
		$userDao = new UserDao;
		$userInfo = $guestDao -> getLoginInfo($_SESSION['userName']);

		if (md5($oldPassword) == $userInfo['userPassword'])
		{
			return $userDao -> changePassword(md5($newPassword), $_SESSION['userID']);
		}
		else
			return FALSE;
	}

	/**
	 * 修改昵称
	 * @param $nickName 昵称
	 */
	public function changeNickName(&$nickName)
	{
		$dao = new UserDao;
		if ($dao -> changeNickName($_SESSION['userID'], $nickName))
		{
			$_SESSION['userNickName'] = $nickName;
			return TRUE;
		}
		else
			return FALSE;
	}

	/**
	 * 检查用户是否存在
	 * @param $userName 用户名
	 */
	public function checkUserExist(&$userName)
	{
		$dao = new UserDao;
		return $dao -> checkUserExist($userName);
	}

}
?>