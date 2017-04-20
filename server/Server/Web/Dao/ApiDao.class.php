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
class ApiDao {
	/**
	 * 添加api
	 * @param $apiName 接口名称
	 * @param $apiURI 接口地址
	 * @param $apiProtocol 请求协议 [0/1]=>[HTTP/HTTPS]
	 * @param $apiSuccessMock 访问成功结果，默认为NULL
	 * @param $apiFailureMock 访问失败结果，默认为NULL
	 * @param $apiRequestType 请求类型 [0/1/2/3/4/5/6]=>[POST/GET/PUT/DELETE/HEAD/OPTIONS/PATCH]
	 * @param $apiStatus 接口状态 [0/1/2]=>[启用/维护/弃用]
	 * @param $groupID 接口分组ID
	 * @param $apiHeader 请求头(JSON格式) [{"headerName":"","headerValue":""]
	 * @param $apiRequestParam 请求参数(JSON格式) [{"paramName":"","paramKey":"","paramType":"","paramLimit":"","paramValue":"","paramNotNull":"","paramValueList":[]}]
	 * @param $apiResultParam 返回参数(JSON格式) ["paramKey":"","paramName":"","paramNotNull":"","paramValueList":[]]
	 * @param $starred 是否加星标 [0/1]=>[否/是]，默认为0
	 * @param $apiNoteType 备注类型 [0/1]=>[富文本/markdown]，默认为0
	 * @param $apiNoteRaw 备注(markdown)，默认为NULL
	 * @param $apiNote 备注(富文本)，默认为NULL
	 * @param $apiRequestParamType 请求参数类型 [0/1]=>[表单类型/源数据类型]，默认为0
	 * @param $apiRequestRaw 请求参数源数据，默认为NULL
	 * @param $cacheJson 接口缓存数据
	 * @param $updateTime 更新时间
	 */
	public function addApi(&$apiName, &$apiURI, &$apiProtocol, &$apiSuccessMock = '', &$apiFailureMock = '', &$apiRequestType, &$apiStatus, &$groupID, &$apiHeader, &$apiRequestParam, &$apiResultParam, &$starred, &$apiNoteType, &$apiNoteRaw, &$apiNote, &$projectID, &$apiRequestParamType, &$apiRequestRaw, &$cacheJson, &$updateTime) {
		$db = getDatabase();
		try {
			//开始事务
			$db -> beginTransaction();

			//插入api基本信息
			$db -> prepareExecute('INSERT INTO eo_api (eo_api.apiName,eo_api.apiURI,eo_api.apiProtocol,eo_api.apiSuccessMock,eo_api.apiFailureMock,eo_api.apiRequestType,eo_api.apiStatus,eo_api.groupID,eo_api.projectID,eo_api.starred,eo_api.apiNoteType,eo_api.apiNoteRaw,eo_api.apiNote,eo_api.apiRequestParamType,eo_api.apiRequestRaw,eo_api.apiUpdateTime) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);', array($apiName, $apiURI, $apiProtocol, $apiSuccessMock, $apiFailureMock, $apiRequestType, $apiStatus, $groupID, $projectID, $starred, $apiNoteType, $apiNoteRaw, $apiNote, $apiRequestParamType, $apiRequestRaw, $updateTime));

			if ($db -> getAffectRow() < 1)
				throw new \PDOException("addApi error");

			if ($db -> getAffectRow() > 0) {
				$apiID = $db -> getLastInsertID();

				//插入header信息
				foreach ($apiHeader as $param) {
					$db -> prepareExecute('INSERT INTO eo_api_header (eo_api_header.headerName,eo_api_header.headerValue,eo_api_header.apiID) VALUES (?,?,?);', array($param['headerName'], $param['headerValue'], $apiID));

					if ($db -> getAffectRow() < 1)
						throw new \PDOException("addHeader error");
				}

				//插入api请求值信息
				foreach ($apiRequestParam as $param) {
					$db -> prepareExecute('INSERT INTO eo_api_request_param (eo_api_request_param.apiID,eo_api_request_param.paramName,eo_api_request_param.paramKey,eo_api_request_param.paramValue,eo_api_request_param.paramLimit,eo_api_request_param.paramNotNull,eo_api_request_param.paramType) VALUES (?,?,?,?,?,?,?);', array($apiID, $param['paramName'], $param['paramKey'], $param['paramValue'], $param['paramLimit'], $param['paramNotNull'], $param['paramType']));

					if ($db -> getAffectRow() < 1)
						throw new \PDOException("addRequestParam error");

					$paramID = $db -> getLastInsertID();

					foreach ($param['paramValueList'] as $value) {
						$db -> prepareExecute('INSERT INTO eo_api_request_value (eo_api_request_value.paramID,eo_api_request_value.`value`,eo_api_request_value.valueDescription) VALUES (?,?,?);', array($paramID, $value['value'], $value['valueDescription']));

						if ($db -> getAffectRow() < 1)
							throw new \PDOException("addApi error");

					};
				};

				//插入api返回值信息
				foreach ($apiResultParam as $param) {
					$db -> prepareExecute('INSERT INTO eo_api_result_param (eo_api_result_param.apiID,eo_api_result_param.paramName,eo_api_result_param.paramKey,eo_api_result_param.paramNotNull) VALUES (?,?,?,?);', array($apiID, $param['paramName'], $param['paramKey'], $param['paramNotNull']));

					if ($db -> getAffectRow() < 1)
						throw new \PDOException("addResultParam error");

					$paramID = $db -> getLastInsertID();

					foreach ($param['paramValueList'] as $value) {
						$db -> prepareExecute('INSERT INTO eo_api_result_value (eo_api_result_value.paramID,eo_api_result_value.`value`,eo_api_result_value.valueDescription) VALUES (?,?,?);', array($paramID, $value['value'], $value['valueDescription']));

						if ($db -> getAffectRow() < 1)
							throw new \PDOException("addApi error");
					};
				};

				//生成mock测试码
				$mockCode = NULL;
				//尝试次数,超过5次则认为是服务器出错
				$count = 0;
				do {
					$count++;
					//获取随机32位字符串
					$mockCode = '';
					$strPool = 'NMqlzxcvdfghjQXCER67ty5HuasJKLZYTWmPASDFGk12iBpn34UIb9werV8';
					for ($i = 0; $i <= 31; $i++) {
						$mockCode .= $strPool[rand(0, 58)];
					}

					//查重
					$check = $db -> prepareExecute('SELECT eo_api_mock.apiID FROM eo_api_mock WHERE eo_api_mock.mockCode = ?;', array($mockCode));
				} while(!empty($check) &&$count < 5);

				if (!empty($check))
					throw new \PDOException("createMockCode error");

				//生成分享码
				$db -> prepareExecute('INSERT INTO eo_api_mock (eo_api_mock.mockCode,eo_api_mock.apiID) VALUES (?,?);', array($mockCode, $apiID));

				if ($db -> getAffectRow() < 1)
					throw new \PDOException("addMockCode error");

				//插入api缓存数据用于导出
				$db -> prepareExecute("INSERT INTO eo_api_cache (eo_api_cache.projectID,eo_api_cache.groupID,eo_api_cache.apiID,eo_api_cache.apiJson,eo_api_cache.starred) VALUES (?,?,?,?,?);", array($projectID, $groupID, $apiID, $cacheJson, $starred));

				if ($db -> getAffectRow() < 1) {
					throw new \PDOException("addApiCache error");
				}

				$db -> commit();
				$result['apiID'] = $apiID;
				$result['groupID'] = $groupID;
				return $result;
			} else {
				throw new \PDOException("addApi error");
				return FALSE;
			}
		} catch(\PDOException $e) {
			$db -> rollBack();
			return FALSE;
		}
	}

	/**
	 * 修改api
	 * @param $apiID 接口ID
	 * @param $apiName 接口名称
	 * @param $apiURI 接口地址
	 * @param $apiProtocol 请求协议 [0/1]=>[HTTP/HTTPS]
	 * @param $apiSuccessMock 访问成功结果，默认为NULL
	 * @param $apiFailureMock 访问失败结果，默认为NULL
	 * @param $apiRequestType 请求类型 [0/1/2/3/4/5/6]=>[POST/GET/PUT/DELETE/HEAD/OPTIONS/PATCH]
	 * @param $apiStatus 接口状态 [0/1/2]=>[启用/维护/弃用]
	 * @param $groupID 接口分组ID
	 * @param $apiHeader 请求头(JSON格式) [{"headerName":"","headerValue":""]
	 * @param $apiRequestParam 请求参数(JSON格式) [{"paramName":"","paramKey":"","paramType":"","paramLimit":"","paramValue":"","paramNotNull":"","paramValueList":[]}]
	 * @param $apiResultParam 返回参数(JSON格式) ["paramKey":"","paramName":"","paramNotNull":"","paramValueList":[]]
	 * @param $starred 是否加星标 [0/1]=>[否/是]，默认为0
	 * @param $apiNoteType 备注类型 [0/1]=>[富文本/markdown]，默认为0
	 * @param $apiNoteRaw 备注(markdown)，默认为NULL
	 * @param $apiNote 备注(富文本)，默认为NULL
	 * @param $apiRequestParamType 请求参数类型 [0/1]=>[表单类型/源数据类型]，默认为0
	 * @param $apiRequestRaw 请求参数源数据，默认为NULL
	 * @param $cacheJson 接口缓存数据
	 * @param $updateTime 更新时间
	 */
	public function editApi(&$apiID, &$apiName, &$apiURI, &$apiProtocol, &$apiSuccessMock, &$apiFailureMock, &$apiRequestType, &$apiStatus, &$groupID, &$apiHeader, &$apiRequestParam, &$apiResultParam, &$starred, &$apiNoteType, &$apiNoteRaw, &$apiNote, &$apiRequestParamType, &$apiRequestRaw, &$cacheJson, &$updateTime) {
		$db = getDatabase();
		try {
			$db -> beginTransaction();
			$db -> prepareExecute('UPDATE eo_api SET eo_api.apiName = ?,eo_api.apiURI = ?,eo_api.apiProtocol = ?,eo_api.apiSuccessMock = ?,eo_api.apiFailureMock = ?,eo_api.apiRequestType = ?,eo_api.apiStatus = ?,eo_api.starred = ?,eo_api.groupID = ?,eo_api.apiNoteType = ?,eo_api.apiNoteRaw = ?,eo_api.apiNote = ?,eo_api.apiUpdateTime = ?,eo_api.apiRequestParamType = ?,eo_api.apiRequestRaw = ? WHERE eo_api.apiID = ?;', array($apiName, $apiURI, $apiProtocol, $apiSuccessMock, $apiFailureMock, $apiRequestType, $apiStatus, $starred, $groupID, $apiNoteType, $apiNoteRaw, $apiNote, $updateTime, $apiRequestParamType, $apiRequestRaw, $apiID));

			if ($db -> getAffectRow() < 1)
				throw new \PDOException("edit Api error");

			$db -> prepareExecute('DELETE FROM eo_api_header WHERE eo_api_header.apiID = ?;', array($apiID));
			$db -> prepareExecute('DELETE FROM eo_api_request_param WHERE eo_api_request_param.apiID = ?;', array($apiID));
			$db -> prepareExecute('DELETE FROM eo_api_result_param WHERE eo_api_result_param.apiID = ?;', array($apiID));

			//插入header信息
			foreach ($apiHeader as $param) {
				$db -> prepareExecute('INSERT INTO eo_api_header (eo_api_header.headerName,eo_api_header.headerValue,eo_api_header.apiID) VALUES (?,?,?);', array($param['headerName'], $param['headerValue'], $apiID));

				if ($db -> getAffectRow() < 1)
					throw new \PDOException("addApi error");
			};

			//插入api请求值信息
			foreach ($apiRequestParam as $param) {
				$db -> prepareExecute('INSERT INTO eo_api_request_param (eo_api_request_param.apiID,eo_api_request_param.paramName,eo_api_request_param.paramKey,eo_api_request_param.paramValue,eo_api_request_param.paramLimit,eo_api_request_param.paramNotNull,eo_api_request_param.paramType) VALUES (?,?,?,?,?,?,?);', array($apiID, $param['paramName'], $param['paramKey'], $param['paramValue'], $param['paramLimit'], $param['paramNotNull'], $param['paramType']));

				if ($db -> getAffectRow() < 1)
					throw new \PDOException("addApi error");

				$paramID = $db -> getLastInsertID();

				foreach ($param['paramValueList'] as $value) {
					$db -> prepareExecute('INSERT INTO eo_api_request_value (eo_api_request_value.paramID,eo_api_request_value.`value`,eo_api_request_value.valueDescription) VALUES (?,?,?);', array($paramID, $value['value'], $value['valueDescription']));

					if ($db -> getAffectRow() < 1)
						throw new \PDOException("addApi error");
				};
			};

			//插入api返回值信息
			foreach ($apiResultParam as $param) {
				$db -> prepareExecute('INSERT INTO eo_api_result_param (eo_api_result_param.apiID,eo_api_result_param.paramName,eo_api_result_param.paramKey,eo_api_result_param.paramNotNull) VALUES (?,?,?,?);', array($apiID, $param['paramName'], $param['paramKey'], $param['paramNotNull']));

				if ($db -> getAffectRow() < 1)
					throw new \PDOException("addApi error");

				$paramID = $db -> getLastInsertID();

				foreach ($param['paramValueList'] as $value) {
					$db -> prepareExecute('INSERT INTO eo_api_result_value (eo_api_result_value.paramID,eo_api_result_value.`value`,eo_api_result_value.valueDescription) VALUES (?,?,?);', array($paramID, $value['value'], $value['valueDescription']));

					if ($db -> getAffectRow() < 1)
						throw new \PDOException("addApi error");
				};
			};

			//更新api缓存
			$db -> prepareExecute("UPDATE eo_api_cache SET eo_api_cache.apiJson = ?,eo_api_cache.groupID = ?,eo_api_cache.starred = ? WHERE eo_api_cache.apiID = ?;", array($cacheJson, $groupID, $starred, $apiID));

			if ($db -> getAffectRow() < 1) {
				throw new \PDOException("updateApiCache error");
			}

			$db -> commit();
			$result['apiID'] = $apiID;
			$result['groupID'] = $groupID;
			return $result;
		} catch(\PDOException $e) {
			$db -> rollBack();
			return FALSE;
		}
	}

	/**
	 * 删除api,将其移入回收站
	 * @param $apiID 接口ID
	 */
	public function removeApi(&$apiID) {
		$db = getDatabase();
		$db -> beginTransaction();

		$db -> prepareExecute('UPDATE eo_api SET eo_api.removed = 1 ,eo_api.removeTime = ? WHERE eo_api.apiID = ?;', array(date("Y-m-d H:i:s", time()), $apiID));

		if ($db -> getAffectRow() > 0) {
			$db -> commit();
			return TRUE;
		} else {
			$db -> rollback();
			return FALSE;
		}

	}

	/**
	 * 恢复api
	 * @param $apiID 接口ID
	 */
	public function recoverApi(&$apiID) {
		$db = getDatabase();
		$db -> beginTransaction();

		$db -> prepareExecute('UPDATE eo_api SET eo_api.removed = 0 WHERE eo_api.apiID = ?;', array($apiID));

		if ($db -> getAffectRow() > 0) {
			$db -> commit();
			return TRUE;
		} else {
			$db -> rollback();
			return FALSE;
		}

	}

	/**
	 * 彻底删除api
	 * @param $apiID 接口ID
	 */
	public function deleteApi(&$apiID) {
		$db = getDatabase();
		try {
			$db -> beginTransaction();

			$db -> prepareExecute('DELETE FROM eo_api WHERE eo_api.apiID = ? AND eo_api.removed = 1;', array($apiID));
			if ($db -> getAffectRow() < 1)
				throw new \PDOException("deleteApi error");

			$db -> prepareExecute('DELETE FROM eo_api_cache WHERE eo_api_cache.apiID = ?;', array($apiID));
			$db -> prepareExecute('DELETE FROM eo_api_header WHERE eo_api_header.apiID = ?;', array($apiID));
			$db -> prepareExecute('DELETE FROM eo_api_mock WHERE eo_api_mock.apiID = ?;', array($apiID));
			$db -> prepareExecute('DELETE FROM eo_api_request_param WHERE eo_api_request_param.apiID = ?;', array($apiID));
			$db -> prepareExecute('DELETE FROM eo_api_result_param WHERE eo_api_result_param.apiID = ?;', array($apiID));

			$db -> commit();
			return TRUE;
		} catch(\PDOException $e) {
			$db -> rollBack();
			return FALSE;
		}
	}

	/**
	 * 清空回收站
	 * @param $projectID 项目ID
	 */
	public function cleanRecyclingStation(&$projectID) {
		$db = getDatabase();
		$db -> prepareExecute('DELETE FROM eo_api WHERE eo_api.projectID= ? AND eo_api.removed = 1;', array($projectID));

		if ($db -> getAffectRow() > 0)
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * 获取api列表并按照名称排序
	 * @param $groupID 接口分组ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getApiListOrderByName(&$groupID, &$asc = 'ASC') {
		$db = getDatabase();
		$result = $db -> prepareExecuteAll("SELECT eo_api.apiID,eo_api.apiName,eo_api.apiURI,eo_api.apiStatus,eo_api.apiRequestType,eo_api.apiUpdateTime,eo_api.starred,eo_api_group.groupID,eo_api_group.parentGroupID,eo_api_group.groupName FROM eo_api INNER JOIN eo_api_group ON eo_api.groupID = eo_api_group.groupID WHERE eo_api.groupID = ? AND eo_api.removed = 0 ORDER BY eo_api.apiName $asc;", array($groupID));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 获取api列表并按照时间排序
	 * @param $groupID 接口分组ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getApiListOrderByTime(&$groupID, &$asc = 'ASC') {
		$db = getDatabase();
		$result = $db -> prepareExecuteAll("SELECT eo_api.apiID,eo_api.apiName,eo_api.apiURI,eo_api.apiStatus,eo_api.apiRequestType,eo_api.apiUpdateTime,eo_api.starred,eo_api_group.groupID,eo_api_group.parentGroupID,eo_api_group.groupName FROM eo_api INNER JOIN eo_api_group ON eo_api.groupID = eo_api_group.groupID WHERE eo_api.groupID = ? AND eo_api.removed = 0 ORDER BY eo_api.apiUpdateTime $asc;", array($groupID));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 获取api列表并按照星标排序
	 * @param $groupID 接口分组ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getApiListOrderByStarred(&$groupID, &$asc = 'ASC') {
		$db = getDatabase();
		$result = $db -> prepareExecuteAll("SELECT eo_api.apiID,eo_api.apiName,eo_api.apiURI,eo_api.apiStatus,eo_api.apiRequestType,eo_api.apiUpdateTime,eo_api.starred,eo_api_group.groupID,eo_api_group.parentGroupID,eo_api_group.groupName FROM eo_api INNER JOIN eo_api_group ON eo_api.groupID = eo_api_group.groupID WHERE eo_api.groupID = ? AND eo_api.removed = 0 ORDER BY eo_api.starred $asc;", array($groupID));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 获取api列表并按照星标排序
	 * @param $groupID 接口分组ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getApiListOrderByUri(&$groupID, &$asc = 'ASC') {
		$db = getDatabase();
		$result = $db -> prepareExecuteAll("SELECT eo_api.apiID,eo_api.apiName,eo_api.apiURI,eo_api.apiStatus,eo_api.apiRequestType,eo_api.apiUpdateTime,eo_api.starred,eo_api_group.groupID,eo_api_group.parentGroupID,eo_api_group.groupName FROM eo_api INNER JOIN eo_api_group ON eo_api.groupID = eo_api_group.groupID WHERE eo_api.groupID = ? AND eo_api.removed = 0 ORDER BY eo_api.apiURI $asc;", array($groupID));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 获取api详情
	 * @param $apiID 接口ID
	 */
	public function getApi(&$apiID) {
		$db = getDatabase();
		$apiInfo = $db -> prepareExecute('SELECT eo_api_cache.*,eo_api_mock.mockCode,eo_api_group.parentGroupID FROM eo_api_cache INNER JOIN eo_api_mock ON eo_api_cache.apiID = eo_api_mock.apiID INNER JOIN eo_api_group ON eo_api_cache.groupID = eo_api_group.groupID WHERE eo_api_cache.apiID = ?;', array($apiID));

		$apiJson = json_decode($apiInfo['apiJson'], TRUE);
		$apiJson['baseInfo']['successMockURL'] = $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '?g=Web&c=Mock&o=success&mockCode=' . $apiInfo['mockCode'];
		$apiJson['baseInfo']['failureMockURL'] = $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '?g=Web&c=Mock&o=failure&mockCode=' . $apiInfo['mockCode'];
		$apiJson['baseInfo']['starred'] = $apiInfo['starred'];
		$apiJson['baseInfo']['groupID'] = $apiInfo['groupID'];
		$apiJson['baseInfo']['parentGroupID'] = $apiInfo['parentGroupID'];
		$apiJson['baseInfo']['projectID'] = $apiInfo['projectID'];
		$apiJson['baseInfo']['apiID'] = $apiInfo['apiID'];

		$testResult = $db -> prepareExecuteAll('SELECT eo_api_test_history.testID,eo_api_test_history.requestInfo,eo_api_test_history.resultInfo,eo_api_test_history.testTime FROM eo_api_test_history WHERE eo_api_test_history.apiID = ? ORDER BY eo_api_test_history.testTime DESC;', array($apiID));

		$apiJson['testHistory'] = $testResult;

		return $apiJson;
	}

	/**
	 * 获取所有api列表
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getAllApiListOrderByName(&$projectID, &$asc = 'ASC') {
		$db = getDatabase();
		$result = $db -> prepareExecuteAll("SELECT eo_api.apiID,eo_api.apiName,eo_api.apiURI,eo_api_group.groupID,eo_api_group.parentGroupID,eo_api_group.groupName,eo_api.apiStatus,eo_api.apiRequestType,eo_api.apiUpdateTime,eo_api.starred FROM eo_api INNER JOIN eo_api_group ON eo_api.groupID = eo_api_group.groupID WHERE eo_api_group.projectID = ? AND eo_api.removed = 0 ORDER BY eo_api.apiName $asc;", array($projectID));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 获取所有api列表
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getAllApiListOrderByUri(&$projectID, &$asc = 'ASC') {
		$db = getDatabase();
		$result = $db -> prepareExecuteAll("SELECT eo_api.apiID,eo_api.apiName,eo_api.apiURI,eo_api_group.groupID,eo_api_group.parentGroupID,eo_api_group.groupName,eo_api.apiStatus,eo_api.apiRequestType,eo_api.apiUpdateTime,eo_api.starred FROM eo_api INNER JOIN eo_api_group ON eo_api.groupID = eo_api_group.groupID WHERE eo_api_group.projectID = ? AND eo_api.removed = 0 ORDER BY eo_api.apiURI $asc;", array($projectID));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 获取所有api列表
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getAllApiListOrderByTime(&$projectID, &$asc = 'ASC') {
		$db = getDatabase();
		$result = $db -> prepareExecuteAll("SELECT eo_api.apiID,eo_api.apiName,eo_api.apiURI,eo_api_group.groupID,eo_api_group.parentGroupID,eo_api_group.groupName,eo_api.apiStatus,eo_api.apiRequestType,eo_api.apiUpdateTime,eo_api.starred FROM eo_api INNER JOIN eo_api_group ON eo_api.groupID = eo_api_group.groupID WHERE eo_api_group.projectID = ? AND eo_api.removed = 0 ORDER BY eo_api.apiUpdateTime $asc;", array($projectID));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 获取所有api列表
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getAllApiListOrderByStarred(&$projectID, &$asc = 'ASC') {
		$db = getDatabase();
		$result = $db -> prepareExecuteAll("SELECT eo_api.apiID,eo_api.apiName,eo_api.apiURI,eo_api_group.groupID,eo_api_group.parentGroupID,eo_api_group.groupName,eo_api.apiStatus,eo_api.apiRequestType,eo_api.apiUpdateTime,eo_api.starred FROM eo_api INNER JOIN eo_api_group ON eo_api.groupID = eo_api_group.groupID WHERE eo_api_group.projectID = ? AND eo_api.removed = 0 ORDER BY eo_api.starred $asc;", array($projectID));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 获取回收站中所有api列表按名称排序
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getRecyclingStationApiListOrderByName(&$projectID, &$asc = 'ASC') {
		$db = getDatabase();
		$result = $db -> prepareExecuteAll("SELECT eo_api.apiID,eo_api.apiName,eo_api.apiURI,eo_api_group.groupID,eo_api_group.parentGroupID,eo_api_group.groupName,eo_api.apiStatus,eo_api.apiRequestType,eo_api.apiUpdateTime,eo_api.removeTime,eo_api.starred FROM eo_api INNER JOIN eo_api_group ON eo_api.groupID = eo_api_group.groupID WHERE eo_api_group.projectID = ? AND eo_api.removed = 1 ORDER BY eo_api.apiName $asc;", array($projectID));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 获取回收站中所有api列表按名称排序
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getRecyclingStationApiListOrderByUri(&$projectID, &$asc = 'ASC') {
		$db = getDatabase();
		$result = $db -> prepareExecuteAll("SELECT eo_api.apiID,eo_api.apiName,eo_api.apiURI,eo_api_group.groupID,eo_api_group.parentGroupID,eo_api_group.groupName,eo_api.apiStatus,eo_api.apiRequestType,eo_api.apiUpdateTime,eo_api.removeTime,eo_api.starred FROM eo_api INNER JOIN eo_api_group ON eo_api.groupID = eo_api_group.groupID WHERE eo_api_group.projectID = ? AND eo_api.removed = 1 ORDER BY eo_api.apiURI $asc;", array($projectID));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 获取回收站中所有api列表按移除时间排序
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getRecyclingStationApiListOrderByRemoveTime(&$projectID, &$asc = 'ASC') {
		$db = getDatabase();
		$result = $db -> prepareExecuteAll("SELECT eo_api.apiID,eo_api.apiName,eo_api.apiURI,eo_api_group.groupID,eo_api_group.parentGroupID,eo_api_group.groupName,eo_api.apiStatus,eo_api.apiRequestType,eo_api.apiUpdateTime,eo_api.removeTime,eo_api.starred FROM eo_api INNER JOIN eo_api_group ON eo_api.groupID = eo_api_group.groupID WHERE eo_api_group.projectID = ? AND eo_api.removed = 1 ORDER BY eo_api.removeTime $asc;", array($projectID));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 获取回收站中所有api列表按星标排序
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getRecyclingStationApiListOrderByStarred(&$projectID, &$asc = 'ASC') {
		$db = getDatabase();
		$result = $db -> prepareExecuteAll("SELECT eo_api.apiID,eo_api.apiName,eo_api.apiURI,eo_api_group.groupID,eo_api_group.parentGroupID,eo_api_group.groupName,eo_api.apiStatus,eo_api.apiRequestType,eo_api.apiUpdateTime,eo_api.removeTime,eo_api.starred FROM eo_api INNER JOIN eo_api_group ON eo_api.groupID = eo_api_group.groupID WHERE eo_api_group.projectID = ? AND eo_api.removed = 1 ORDER BY eo_api.starred $asc;", array($projectID));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 搜索api
	 * @param $tips 搜索关键字
	 * @param $projectID 项目ID
	 */
	public function searchApi(&$tips, &$projectID) {
		$db = getDatabase();
		$result = $db -> prepareExecuteAll('SELECT eo_api.apiID,eo_api.apiName,eo_api.apiURI,eo_api_group.groupID,eo_api_group.parentGroupID,eo_api_group.groupName,eo_api.apiStatus,eo_api.apiRequestType,eo_api.apiUpdateTime FROM eo_api INNER JOIN eo_api_group ON eo_api.groupID = eo_api_group.groupID WHERE eo_api_group.projectID = ? AND eo_api.removed = 0 AND (eo_api.apiName LIKE ? OR eo_api.apiURI LIKE ?)ORDER BY eo_api.apiName;', array($projectID, '%' . $tips . '%', '%' . $tips . '%'));

		if (empty($result))
			return FALSE;
		else
			return $result;
	}

	/**
	 * 判断api与用户是否匹配
	 * @param $apiID 接口ID
	 * @param $userID 用户ID
	 */
	public function checkApiPermission(&$apiID, &$userID) {
		$db = getDatabase();
		$result = $db -> prepareExecute('SELECT eo_conn_project.projectID FROM eo_api INNER JOIN eo_api_group INNER JOIN eo_conn_project ON eo_conn_project.projectID = eo_api_group.projectID AND eo_api.groupID = eo_api_group.groupID WHERE eo_conn_project.userID = ? AND eo_api.apiID = ?;', array($userID, $apiID));

		if (empty($result))
			return FALSE;
		else
			return $result['projectID'];
	}

	/**
	 * 添加星标
	 * @param $apiID 接口ID
	 */
	public function addStar(&$apiID) {
		$db = getDatabase();
		$db -> execute("UPDATE eo_api SET eo_api.starred = 1 WHERE eo_api.apiID = $apiID");
		$db -> execute("UPDATE eo_api_cache SET eo_api_cache.starred = 1 WHERE eo_api_cache.apiID = $apiID");

		if ($db -> getAffectRow() > 0)
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * 去除星标
	 * @param $apiID 接口ID
	 */
	public function removeStar(&$apiID) {
		$db = getDatabase();
		$db -> execute("UPDATE eo_api SET eo_api.starred = 0 WHERE eo_api.apiID = $apiID");
		$db -> execute("UPDATE eo_api_cache SET eo_api_cache.starred = 0 WHERE eo_api_cache.apiID = $apiID");

		if ($db -> getAffectRow() > 0)
			return TRUE;
		else
			return FALSE;
	}

}
?>