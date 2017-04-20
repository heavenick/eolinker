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
class PartnerModule
{
	public function __construct()
	{
		@session_start();
	}
	
	/**
	 * 邀请协作人员
	 * @param $projectID 项目ID
	 * @param $inviteUserID 邀请人ID
	 */
	public function invitePartner(&$projectID, &$inviteUserID)
	{
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'], 0))
		{
			$projectInfo = $projectDao -> getProjectName($projectID);
			$summary = '您已被邀请加入项目：' . $projectInfo['projectName'] . '，开始您的高效协作之旅吧！';
			$msg = '<p>您好！亲爱的用户：</p><p>您已经被加入项目：<b style="color:#4caf50">' . $projectInfo['projectName'] . '</b>，现在你可以参与项目的开发协作工作。</p><p>如果您在使用的过程中遇到任何问题，欢迎前往<b style="color:#4caf50">交流社区</b>反馈意见，谢谢！。</p>';

			//邀请协作人员
			$partnerDao = new PartnerDao;
			if ($partnerDao -> invitePartner($projectID, $inviteUserID))
			{
				//给协作人员发送邀请信息
				$msgDao = new MessageDao;
				$msgDao -> sendMessage($_SESSION['userID'], $inviteUserID, 1, $summary, $msg);
				return TRUE;
			}
			else
				return FALSE;
		}
		else
			return FALSE;
	}

	/**
	 * 移除协作人员
	 * @param $projectID 项目ID
	 * @param $connID 用户与项目联系ID
	 */
	public function removePartner($projectID, $connID)
	{
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'], 0))
		{
			$projectInfo = $projectDao -> getProjectName($projectID);
			$summary = '您已被移除出项目：' . $projectInfo['projectName'];
			$msg = '<p>您好！亲爱的用户：</p><p>您已经被移除出项目：<b style="color:#4caf50">' . $projectInfo['projectName'] . '</b>。</p><p>如果您在使用的过程中遇到任何问题，欢迎前往<b style="color:#4caf50">交流社区</b>反馈意见，谢谢！。</p>';

			$partnerDao = new PartnerDao;
			$remotePartnerID = $partnerDao -> getUserID($connID);
			if ($partnerDao -> removePartner($projectID, $connID))
			{
				//给协作人员发送邀请信息
				$msgDao = new MessageDao;
				$msgDao -> sendMessage(0, $remotePartnerID, 1, $summary, $msg);
				return TRUE;
			}
			else
				return FALSE;
		}
		else
			return FALSE;

	}

	/**
	 * 获取协作人员列表
	 * @param $projectID 项目ID
	 */
	public function getPartnerList(&$projectID)
	{
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID']))
		{
			$partnerDao = new PartnerDao;
			$list = $partnerDao -> getPartnerList($projectID);
			foreach ($list as &$param)
			{
				if ($param['userID'] == $_SESSION['userID'])
					$param['isNow'] = 1;
				else
					$param['isNow'] = 0;
				unset($param['userID']);
			}
			return $list;
		}
		else
			return FALSE;
	}

	/**
	 * 退出协作项目
	 * @param $projectID 项目ID
	 */
	public function quitPartner(&$projectID)
	{
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'], 1))
		{
			$partnerDao = new PartnerDao;
			if ($partnerDao -> quitPartner($projectID, $_SESSION['userID']))
				return TRUE;
			else
				return FALSE;
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
		$dao = new PartnerDao;
		return $dao -> checkIsInvited($projectID, $userName);
	}

}
?>