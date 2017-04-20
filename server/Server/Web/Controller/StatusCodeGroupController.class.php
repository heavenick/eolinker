<?php
/**
 * @name eolinker open source，eolinker开源版本
 * @link https://www.eolinker.com
 * @package eolinker
 * @author www.eolinker.com 广州银云信息科技有限公司 ©2015-2016

 * eolinker，业内领先的Api接口管理及测试平台，为您提供最专业便捷的在线接口管理、测试、维护以及各类性能测试方案，帮助您高效开发、安全协作。
 * 如在使用的过程中有任何问题，欢迎加入用户讨论群进行反馈，我们将会以最快的速度，最好的服务态度为您解决问题。
 * 用户讨论QQ群：284421832
 *
 * 注意！eolinker开源版本仅供用户下载试用、学习和交流，禁止“一切公开使用于商业用途”或者“以eolinker开源版本为基础而开发的二次版本”在互联网上流通。
 * 注意！一经发现，我们将立刻启用法律程序进行维权。
 * 再次感谢您的使用，希望我们能够共同维护国内的互联网开源文明和正常商业秩序。
 *
 */
class StatusCodeGroupController
{

	// 返回json类型
	private $returnJson = array('type' => 'status_code_group');
	
	/**
	 * 检查登录状态
	 */
	public function __construct()
	{
		// 身份验证
		$server = new GuestModule;
		if (!$server -> checkLogin())
		{
			$this -> returnJson['statusCode'] = '120005';
			exitOutput($this -> returnJson);
		}
	}

	/**
	 * 添加分组
	 */
	public function addGroup()
	{
		$nameLen = mb_strlen(quickInput('groupName'), 'utf8');
		$projectID = securelyInput('projectID');
		$groupName = securelyInput('groupName');
		$parentGroupID = securelyInput('parentGroupID', NULL);

		if (!preg_match('/^[0-9]{1,11}$/', $projectID))
		{
			//项目ID格式不合法
			$this -> returnJson['statusCode'] = '180005';
		}
		elseif (!($nameLen >= 1 && $nameLen <= 32))
		{
			//分组名称不合法
			$this -> returnJson['statusCode'] = '180004';
		}
		else
		{
			$service = new StatusCodeGroupModule;
			$result = $service -> addGroup($projectID, $groupName, $parentGroupID);

			if ($result)
			{
				//添加分组成功
				$this -> returnJson['statusCode'] = '000000';
				$this -> returnJson['statusGroupID'] = $result;
			}
			else
			{
				//添加分组失败
				$this -> returnJson['statusCode'] = '180002';
			}
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 删除分组
	 */
	public function deleteGroup()
	{
		$groupID = securelyInput('groupID');

		if (!preg_match('/^[0-9]{1,11}$/', $groupID))
		{
			//分组ID格式不合法
			$this -> returnJson['statusCode'] = '180003';
		}
		else
		{
			$service = new StatusCodeGroupModule;
			$result = $service -> deleteGroup($groupID);

			if ($result)
			{
				$this -> returnJson['statusCode'] = '000000';
			}
			else
			{
				$this -> returnJson['statusCode'] = '180006';
			}
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 获取分组列表
	 */
	public function getGroupList()
	{
		$projectID = securelyInput('projectID');

		if (!preg_match('/^[0-9]{1,11}$/', $projectID))
		{
			//项目ID格式不合法
			$this -> returnJson['statusCode'] = '180005';
		}
		else
		{
			$service = new StatusCodeGroupModule;
			$result = $service -> getGroupList($projectID);

			if ($result)
			{
				$this -> returnJson['statusCode'] = '000000';
				$this -> returnJson['groupList'] = $result;
			}
			else
			{
				$this -> returnJson['statusCode'] = '180001';
			}
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 修改分组
	 */
	public function editGroup()
	{
		$nameLen = mb_strlen(quickInput('groupName'), 'utf8');
		$groupID = securelyInput('groupID');
		$groupName = securelyInput('groupName');

		if (!preg_match('/^[0-9]{1,11}$/', $groupID))
		{
			//项目ID格式不合法
			$this -> returnJson['statusCode'] = '180003';
		}
		elseif (!($nameLen >= 1 && $nameLen <= 32))
		{
			//分组名称不合法
			$this -> returnJson['statusCode'] = '180004';
		}
		else
		{
			$service = new StatusCodeGroupModule;
			$result = $service -> editGroup($groupID, $groupName);
			if ($result)
			{
				$this -> returnJson['statusCode'] = '000000';
			}
			else
			{
				$this -> returnJson['statusCode'] = '180007';
			}
		}
		exitOutput($this -> returnJson);
	}

}
?>