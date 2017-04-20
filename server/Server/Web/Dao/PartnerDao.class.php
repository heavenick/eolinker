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
class PartnerDao
{
	/**
	 * 邀请协作人员
	 * @param $projectID 项目ID
	 * @param $inviteUserID 邀请人ID
	 */
	public function invitePartner(&$projectID, &$userID)
	{
		$db = getDatabase();
		$db -> prepareExecute('INSERT INTO eo_conn_project (eo_conn_project.projectID,eo_conn_project.userID,eo_conn_project.userType) VALUES (?,?,1);', array(
			$projectID,
			$userID,
		));

		if ($db -> getAffectRow() > 0)
			return $db -> getLastInsertID();
		else
			return FALSE;
	}

	/**
	 * 移除协作人员
	 * @param $projectID 项目ID
	 * @param $connID 用户与项目联系ID
	 */
	public function removePartner(&$projectID, &$connID)
	{
		$db = getDatabase();
		$db -> prepareExecute('DELETE FROM eo_conn_project WHERE eo_conn_project.projectID = ? AND eo_conn_project.connID = ? AND eo_conn_project.userType = 1;', array(
			$projectID,
			$connID
		));

		if ($db -> getAffectRow() > 0)
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * 获取协作人员列表
	 * @param $projectID 项目ID
	 */
	public function getPartnerList(&$projectID)
	{
		$db = getDatabase();
		$result = $db -> prepareExecuteAll('SELECT eo_conn_project.userID,eo_conn_project.connID,eo_conn_project.userType,eo_user.userName,eo_user.userNickName FROM eo_conn_project INNER JOIN eo_user ON eo_conn_project.userID = eo_user.userID WHERE eo_conn_project.projectID = ? ORDER BY eo_conn_project.userType ASC;', array($projectID));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 退出协作项目
	 * @param $projectID 项目ID
	 * @param $userID 用户ID
	 */
	public function quitPartner(&$projectID, &$userID)
	{
		$db = getDatabase();
		$db -> prepareExecute('DELETE FROM eo_conn_project WHERE eo_conn_project.projectID = ? AND eo_conn_project.userID = ? AND eo_conn_project.userType = 1;', array(
			$projectID,
			$userID
		));

		if ($db -> getAffectRow() > 0)
		{
			return TRUE;
		}
		else
			return FALSE;
	}

	/**
	 * 查询是否已经加入过项目
	 * @param $projectID 项目ID
	 * @param $userName 用户名
	 */
	public function checkIsInvited(&$projectID, &$userName)
	{
		$db = getDatabase();
		$result = $db -> prepareExecuteAll('SELECT eo_conn_project.connID FROM eo_conn_project INNER JOIN eo_user ON eo_user.userID = eo_conn_project.userID WHERE eo_conn_project.projectID = ? AND eo_user.userName = ?;', array(
			$projectID,
			$userName
		));
		if (empty($result))
			return FALSE;
		else
			return TRUE;
	}

	/**
	 * 获取用户ID
	 * @param $connID 用户与项目联系ID
	 */
	public function getUserID(&$connID)
	{
		$db = getDatabase();
		$result = $db -> prepareExecute('SELECT eo_conn_project.userID FROM eo_conn_project WHERE eo_conn_project.connID = ?;', array($connID));
		if (empty($result))
			return FALSE;
		else
			return $result['userID'];
	}

}
?>