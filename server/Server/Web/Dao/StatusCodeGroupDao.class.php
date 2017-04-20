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
class StatusCodeGroupDao
{
	/**
	 * 添加状态码分组
	 * @param $projectID 项目ID
	 * @param $groupName 分组名
	 */
	public function addGroup(&$projectID, &$groupName)
	{
		$db = getDatabase();

		$db -> prepareExecute('INSERT INTO eo_project_status_code_group (eo_project_status_code_group.projectID,eo_project_status_code_group.groupName) VALUES (?,?);', array(
			$projectID,
			$groupName
		));

		$groupID = $db -> getLastInsertID();

		if ($db -> getAffectRow() < 1)
			return FALSE;
		else
			return $groupID;

	}

	/**
	 * 添加子分组
	 * @param $projectID 项目ID
	 * @param $groupName 分组名
	 * @param $parentGroupID 父分组ID
	 */
	public function addChildGroup(&$projectID, &$groupName, &$parentGroupID)
	{
		$db = getDatabase();

		$db -> prepareExecute('INSERT INTO eo_project_status_code_group (eo_project_status_code_group.projectID,eo_project_status_code_group.groupName,eo_project_status_code_group.parentGroupID,eo_project_status_code_group.isChild) VALUES (?,?,?,1);', array(
			$projectID,
			$groupName,
			$parentGroupID
		));

		$groupID = $db -> getLastInsertID();

		if ($db -> getAffectRow() < 1)
			return FALSE;
		else
			return $groupID;
	}

	/**
	 * 判断用户和分组是否匹配
	 * @param $groupID 分组ID
	 * @param $userID 用户ID
	 */
	public function checkStatusCodeGroupPermission(&$groupID, &$userID)
	{
		$db = getDatabase();

		$result = $db -> prepareExecute('SELECT eo_conn_project.projectID FROM eo_conn_project INNER JOIN eo_project_status_code_group ON eo_conn_project.projectID = eo_project_status_code_group.projectID WHERE groupID = ? AND userID = ?;', array(
			$groupID,
			$userID
		));

		if (empty($result))
			return FALSE;
		else
			return $result['projectID'];
	}

	/**
	 * 删除分组
	 * @param $groupID 分组ID
	 */
	public function deleteGroup(&$groupID)
	{
		$db = getDatabase();

		$db -> prepareExecute('DELETE FROM eo_project_status_code_group WHERE eo_project_status_code_group.groupID = ?;', array($groupID));

		if ($db -> getAffectRow() < 1)
			return FALSE;
		else
			return TRUE;
	}

	/**
	 * 获取分组列表
	 * @param $projectID 项目ID
	 */
	public function getGroupList(&$projectID)
	{
		$db = getDatabase();

		$groupList = $db -> prepareExecuteAll('SELECT eo_project_status_code_group.groupID,eo_project_status_code_group.groupName FROM eo_project_status_code_group WHERE projectID = ? AND isChild = 0 ORDER BY eo_project_status_code_group.groupID DESC;', array($projectID));

		if (is_array($groupList))
			foreach ($groupList as &$parentGroup)
			{
				$parentGroup['childGroupList'] = array();
				$childGroup = $db -> prepareExecuteAll('SELECT eo_project_status_code_group.groupID,eo_project_status_code_group.groupName,eo_project_status_code_group.parentGroupID FROM eo_project_status_code_group WHERE projectID = ? AND isChild = 1 AND parentGroupID = ? ORDER BY eo_project_status_code_group.groupID DESC;', array(
					$projectID,
					$parentGroup['groupID']
				));

				//判断是否有子分组
				if (!empty($childGroup))
					$parentGroup['childGroupList'] = $childGroup;
			}

		if (empty($groupList))
			return FALSE;
		else
			return $groupList;
	}

	/**
	 * 修改分组
	 * @param $groupID 分组ID
	 * @param $groupName 分组名
	 */
	public function editGroup(&$groupID, &$groupName)
	{
		$db = getDatabase();

		$db -> prepareExecute('UPDATE eo_project_status_code_group SET eo_project_status_code_group.groupName = ? WHERE groupID = ?;', array(
			$groupName,
			$groupID
		));

		if ($db -> getAffectRow() < 1)
			return FALSE;
		else
			return TRUE;
	}

}
?>