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
class GroupModule
{
	public function __construct()
	{
		@session_start();
	}

	/**
	 * 添加项目分组
	 * @param $projectID 项目ID
	 * @param $groupName 分组名
	 * @param $parentGroupID 父分组ID，默认为0
	 */
	public function addGroup(&$projectID, &$groupName, &$parentGroupID)
	{
		$groupDao = new GroupDao;
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID']))
		{
			$projectDao -> updateProjectUpdateTime($projectID);
			if (is_null($parentGroupID))
				return $groupDao -> addGroup($projectID, $groupName);
			else
				return $groupDao -> addChildGroup($projectID, $groupName, $parentGroupID);
		}
		else
			return FALSE;
	}

	/**
	 * 删除项目分组
	 * @param $groupID 分组ID
	 */
	public function deleteGroup(&$groupID)
	{
		$groupDao = new GroupDao;
		$projectDao = new ProjectDao;
		if ($projectID = $groupDao -> checkGroupPermission($groupID, $_SESSION['userID']))
		{
			$projectDao -> updateProjectUpdateTime($projectID);
			return $groupDao -> deleteGroup($groupID);
		}
		else
			return FALSE;
	}

	/**
	 * 获取项目分组
	 * @param $projectID 项目ID
	 */
	public function getGroupList(&$projectID)
	{
		$groupDao = new GroupDao;
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID']))
			return $groupDao -> getGroupList($projectID);
		else
			return FALSE;
	}

	/**
	 * 修改项目分组
	 * @param $groupID 分组ID
	 * @param $groupName 分组名
	 */
	public function editGroup(&$groupID, &$groupName)
	{
		$groupDao = new GroupDao;
		$projectDao = new ProjectDao;
		if ($projectID = $groupDao -> checkGroupPermission($groupID, $_SESSION['userID']))
		{
			$projectDao -> updateProjectUpdateTime($projectID);
			return $groupDao -> editGroup($groupID, $groupName);
		}
		else
			return FALSE;
	}

}
?>