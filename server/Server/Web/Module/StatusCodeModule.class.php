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
class StatusCodeModule {
	public function __construct() {
		@session_start();
	}

	/**
	 * 添加状态码
	 * @param $groupID 分组ID
	 * @param $codeDesc 状态码描述，默认为NULL
	 * @param $code 状态码
	 */
	public function addCode(&$groupID, &$codeDesc, &$code) {
		$projectDao = new ProjectDao;
		$statucCodeGroupDao = new StatusCodeGroupDao;
		$statusCodeDao = new StatusCodeDao;
		if ($projectID = $statucCodeGroupDao -> checkStatusCodeGroupPermission($groupID, $_SESSION['userID'])) {
			$projectDao -> updateProjectUpdateTime($projectID);
			return $statusCodeDao -> addCode($groupID, $codeDesc, $code);
		} else
			return FALSE;
	}

	/**
	 * 删除状态码
	 * @param $codeID 状态码ID
	 */
	public function deleteCode(&$codeID) {
		$projectDao = new ProjectDao;
		$statusCodeDao = new StatusCodeDao;
		if ($projectID = $statusCodeDao -> checkStatusCodePermission($codeID, $_SESSION['userID'])) {
			$projectDao -> updateProjectUpdateTime($projectID);
			return $statusCodeDao -> deleteCode($codeID);
		} else
			return FALSE;
	}

	/**
	 * 获取状态码列表
	 * @param $groupID 分组ID
	 */
	public function getCodeList(&$groupID) {
		$statusCodeGroupDao = new StatusCodeGroupDao;
		$statusCodeDao = new StatusCodeDao;
		if ($statusCodeGroupDao -> checkStatusCodeGroupPermission($groupID, $_SESSION['userID'])) {
			return $statusCodeDao -> getCodeList($groupID);
		} else
			return FALSE;
	}

	/**
	 * 获取所有状态码列表
	 * @param $projectID 项目ID
	 */
	public function getAllCodeList(&$projectID) {
		$projectDao = new ProjectDao;
		$statusCodeDao = new StatusCodeDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'])) {
			return $statusCodeDao -> getAllCodeList($projectID);
		} else
			return FALSE;
	}

	/**
	 * 修改状态码
	 * @param $groupID 分组ID
	 * @param $codeID 状态码ID
	 * @param $code 状态码
	 * @param $codeDesc 状态码描述，默认为NULL
	 */
	public function editCode(&$groupID, &$codeID, &$code, &$codeDesc) {
		$projectDao = new ProjectDao;
		$statusCodeDao = new StatusCodeDao;
		if ($projectID = $statusCodeDao -> checkStatusCodePermission($codeID, $_SESSION['userID'])) {
			$projectDao -> updateProjectUpdateTime($projectID);
			return $statusCodeDao -> editCode($groupID, $codeID, $code, $codeDesc);
		} else
			return FALSE;
	}

	/**
	 * 搜索状态码
	 * @param $projectID 项目ID
	 * @param $tips 搜索关键字
	 */
	public function searchStatusCode(&$projectID, &$tips) {
		$projectDao = new ProjectDao;
		$statusCodeDao = new StatusCodeDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'])) {
			return $statusCodeDao -> searchStatusCode($projectID, $tips);
		} else
			return FALSE;
	}

	/**
	 * 获取状态码数量
	 * @param $projectID 项目ID
	 */
	public function getStatusCodeNum(&$projectID) {
		$projectDao = new ProjectDao;
		$statusCodeDao = new StatusCodeDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'])) {
			return $statusCodeDao -> getStatusCodeNum($projectID);
		} else
			return FALSE;
	}

}
?>