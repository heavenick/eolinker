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
class TestHistoryDao
{
	/**
	 * 添加测试记录
	 * @param $apiID 接口ID
	 * @param $requestInfo 测试请求信息
	 * @param $resultInfo 测试结果信息
	 * @param $testTime 测试时间
	 */
	public function addTestHistory(&$projectID, &$apiID, &$requestInfo, &$resultInfo, &$testTime)
	{
		$db = getDatabase();
		$db -> prepareExecute('INSERT INTO eo_api_test_history (eo_api_test_history.projectID,eo_api_test_history.apiID,eo_api_test_history.requestInfo,eo_api_test_history.resultInfo,eo_api_test_history.testTime) VALUES (?,?,?,?,?);', array(
			$projectID,
			$apiID,
			$requestInfo,
			$resultInfo,
			$testTime
		));

		if ($db -> getAffectRow() < 1)
			return FALSE;
		else
		{
			return $db -> getLastInsertID();
		}
	}

	/**
	 * 删除测试记录
	 * @param $testID 测试记录ID
	 */
	public function deleteTestHistory(&$testID)
	{
		$db = getDatabase();

		$db -> prepareExecute('DELETE FROM eo_api_test_history WHERE eo_api_test_history.testID =?;', array($testID));

		if ($db -> getAffectRow() < 1)
			return FALSE;
		else
			return TRUE;
	}

	/**
	 * 获取测试记录信息
	 * @param $testID 测试记录ID
	 */
	public function getTestHistory(&$testID)
	{
		$db = getDatabase();

		$result = $db -> prepareExecute('SELECT eo_api_test_history.projectID,eo_api_test_history.apiID,eo_api_test_history.testID,eo_api_test_history.requestInfo,eo_api_test_history.resultInfo,eo_api_test_history.testTime FROM eo_api_test_history WHERE testID =?;', array($testID));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 检查测试记录与用户的联系
	 * @param $testID 测试记录ID
	 * @param $userId 用户ID
	 */
	public function checkTestHistoryPermission(&$testID, &$userID)
	{
		$db = getDatabase();
		
		$result = $db -> prepareExecute('SELECT eo_conn_project.projectID FROM eo_api_test_history INNER JOIN eo_api INNER JOIN eo_conn_project ON eo_api.projectID = eo_conn_project.projectID AND eo_api.apiID = eo_api_test_history.apiID WHERE eo_api_test_history.testID = ? AND eo_conn_project.userID = ?;', array(
			$testID,
			$userID
		));

		if (empty($result))
			return FALSE;
		else
			return $result['projectID'];
	}

}
?>