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
class ImportDao {
	/**
	 * 导入eoapi
	 */
	public function importEoapi(&$data, &$userID) {
		$db = getDatabase();
		try {
			//开始事务
			$db -> beginTransaction();

			//插入项目
			$db -> prepareExecute('INSERT INTO eo_project(eo_project.projectName,eo_project.projectType,eo_project.projectVersion,eo_project.projectUpdateTime) VALUES (?,?,?,?);', array($data['projectInfo']['projectName'], $data['projectInfo']['projectType'], $data['projectInfo']['projectVersion'], $data['projectInfo']['projectUpdateTime']));
			if ($db -> getAffectRow() < 1)
				throw new \PDOException("addProject error");

			//获取projectID
			$projectID = $db -> getLastInsertID();

			//生成项目与用户的联系
			$db -> prepareExecute('INSERT INTO eo_conn_project (eo_conn_project.projectID,eo_conn_project.userID,eo_conn_project.userType) VALUES (?,?,0);', array($projectID, $userID));

			if ($db -> getAffectRow() < 1)
				throw new \PDOException("addConnProject error");

			//插入接口分组信息
			if (is_array($data['apiGroupList'])) {
				foreach ($data['apiGroupList'] as $apiGroup) {
					$db -> prepareExecute('INSERT INTO eo_api_group (eo_api_group.groupName,eo_api_group.projectID) VALUES (?,?);', array($apiGroup['groupName'], $projectID));

					if ($db -> getAffectRow() < 1)
						throw new \PDOException("addGroup error");

					$groupID = $db -> getLastInsertID();

					//如果当前分组没有接口，则跳过到下一分组
					if (empty($apiGroup['apiList']))
						continue;

					if (is_array($apiGroup['apiList'])) {
						foreach ($apiGroup['apiList'] as $api) {
							//插入api基本信息
							$db -> prepareExecute('INSERT INTO eo_api (eo_api.apiName,eo_api.apiURI,eo_api.apiProtocol,eo_api.apiSuccessMock,eo_api.apiFailureMock,eo_api.apiRequestType,eo_api.apiStatus,eo_api.groupID,eo_api.projectID,eo_api.starred,eo_api.apiNoteType,eo_api.apiNoteRaw,eo_api.apiNote,eo_api.apiRequestParamType,eo_api.apiRequestRaw,eo_api.apiUpdateTime) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);', array($api['baseInfo']['apiName'], $api['baseInfo']['apiURI'], $api['baseInfo']['apiProtocol'], $api['baseInfo']['apiSuccessMock'], $api['baseInfo']['apiFailureMock'], $api['baseInfo']['apiRequestType'], $api['baseInfo']['apiStatus'], $groupID, $projectID, $api['baseInfo']['starred'], $api['baseInfo']['apiNoteType'], $api['baseInfo']['apiNoteRaw'], $api['baseInfo']['apiNote'], $api['baseInfo']['apiRequestParamType'], $api['baseInfo']['apiRequestRaw'], $api['baseInfo']['apiUpdateTime']));

							if ($db -> getAffectRow() < 1)
								throw new \PDOException("addApi error");

							$apiID = $db -> getLastInsertID();

							//插入header信息
							if (is_array($api['headerInfo'])) {
								foreach ($api['headerInfo'] as $header) {
									$db -> prepareExecute('INSERT INTO eo_api_header (eo_api_header.headerName,eo_api_header.headerValue,eo_api_header.apiID) VALUES (?,?,?);', array($header['headerName'], $header['headerValue'], $apiID));

									if ($db -> getAffectRow() < 1)
										throw new \PDOException("addHeader error");
								}
							}

							//插入api请求值信息
							if (is_array($api['requestInfo'])) {
								foreach ($api['requestInfo'] as $request) {
									$db -> prepareExecute('INSERT INTO eo_api_request_param (eo_api_request_param.apiID,eo_api_request_param.paramName,eo_api_request_param.paramKey,eo_api_request_param.paramValue,eo_api_request_param.paramLimit,eo_api_request_param.paramNotNull,eo_api_request_param.paramType) VALUES (?,?,?,?,?,?,?);', array($apiID, $request['paramName'], $request['paramKey'], $request['paramValue'], $request['paramLimit'], $request['paramNotNull'], $request['paramType']));

									if ($db -> getAffectRow() < 1)
										throw new \PDOException("addRequestParam error");

									$paramID = $db -> getLastInsertID();

									foreach ($request['paramValueList'] as $value) {
										$db -> prepareExecute('INSERT INTO eo_api_request_value (eo_api_request_value.paramID,eo_api_request_value.`value`,eo_api_request_value.valueDescription) VALUES (?,?,?);', array($paramID, $value['value'], $value['valueDescription']));

										if ($db -> getAffectRow() < 1)
											throw new \PDOException("addApi error");

									};
								};
							}

							//插入api返回值信息
							if (is_array($api['resultInfo'])) {
								foreach ($api['resultInfo'] as $result) {
									$db -> prepareExecute('INSERT INTO eo_api_result_param (eo_api_result_param.apiID,eo_api_result_param.paramName,eo_api_result_param.paramKey,eo_api_result_param.paramNotNull) VALUES (?,?,?,?);', array($apiID, $result['paramName'], $result['paramKey'], $result['paramNotNull']));

									if ($db -> getAffectRow() < 1)
										throw new \PDOException("addResultParam error");

									$paramID = $db -> getLastInsertID();

									if (is_array($result['paramValueList'])) {
										foreach ($result['paramValueList'] as $value) {
											$db -> prepareExecute('INSERT INTO eo_api_result_value (eo_api_result_value.paramID,eo_api_result_value.`value`,eo_api_result_value.valueDescription) VALUES (?,?,?);;', array($paramID, $value['value'], $value['valueDescription']));

											if ($db -> getAffectRow() < 1)
												throw new \PDOException("addApi error");
										};
									}
								};
							}

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
							$db -> prepareExecute("INSERT INTO eo_api_cache (eo_api_cache.projectID,eo_api_cache.groupID,eo_api_cache.apiID,eo_api_cache.apiJson,eo_api_cache.starred) VALUES (?,?,?,?,?);", array($projectID, $groupID, $apiID, json_encode($api), $api['baseInfo']['starred']));

							if ($db -> getAffectRow() < 1) {
								throw new \PDOException("addApiCache error");
							}
						}
					}
				}
			}

			if (!empty($data['statusCodeGroupList'])) {
				//导入状态码
				if (is_array($data['statusCodeGroupList'])) {
					foreach ($data['statusCodeGroupList'] as $statusCodeGroup) {
						//插入分组
						$db -> prepareExecute('INSERT INTO eo_project_status_code_group (eo_project_status_code_group.projectID,eo_project_status_code_group.groupName) VALUES (?,?);', array($projectID, $statusCodeGroup['groupName']));

						if ($db -> getAffectRow() < 1) {
							throw new \PDOException("add statusCodeGroup error");
						}

						$groupID = $db -> getLastInsertID();

						if (empty($statusCodeGroup['statusCodeList']))
							continue;

						//插入状态码
						foreach ($statusCodeGroup['statusCodeList'] as $statusCode) {
							$db -> prepareExecute('INSERT INTO eo_project_status_code (eo_project_status_code.groupID,eo_project_status_code.code,eo_project_status_code.codeDescription) VALUES (?,?,?);', array($groupID, $statusCode['code'], $statusCode['codeDescription']));

							if ($db -> getAffectRow() < 1) {
								throw new \PDOException("add statusCode error");
							}
						}
					}
				}
			}
		} catch(\PDOException $e) {
			$db -> rollBack();
			return FALSE;
		}
		$db -> commit();
		return TRUE;
	}

	/**
	 * 导入其他
	 */
	public function importOther(&$projectInfo, &$groupInfoList, &$userID) {
		$db = getDatabase();
		try {
			//开始事务
			$db -> beginTransaction();

			//插入项目
			$db -> prepareExecute('INSERT INTO eo_project(eo_project.projectName,eo_project.projectType,eo_project.projectVersion,eo_project.projectUpdateTime) VALUES (?,?,?,?);', array($projectInfo['projectName'], $projectInfo['projectType'], $projectInfo['projectVersion'], date('Y-m-d H:i:s', time())));
			if ($db -> getAffectRow() < 1)
				throw new \PDOException("addProject error");

			$projectID = $db -> getLastInsertID();

			//生成项目与用户的联系
			$db -> prepareExecute('INSERT INTO eo_conn_project (eo_conn_project.projectID,eo_conn_project.userID,eo_conn_project.userType) VALUES (?,?,0);', array($projectID, $userID));

			if ($db -> getAffectRow() < 1)
				throw new \PDOException("addConnProject error");

			if (is_array($groupInfoList)) {
				foreach ($groupInfoList as $groupInfo) {
					if (!$groupInfo['apiList'])
						continue;

					$db -> prepareExecute('INSERT INTO eo_api_group (eo_api_group.groupName,eo_api_group.projectID) VALUES (?,?);', array($groupInfo['groupName'], $projectID));

					if ($db -> getAffectRow() < 1)
						throw new \PDOException("addGroup error");

					$groupID = $db -> getLastInsertID();

					if (is_array($groupInfo['apiList'])) {
						foreach ($groupInfo['apiList'] as $api) {
							//插入api基本信息
							$db -> prepareExecute('INSERT INTO eo_api (eo_api.apiName,eo_api.apiURI,eo_api.apiProtocol,eo_api.apiSuccessMock,eo_api.apiFailureMock,eo_api.apiRequestType,eo_api.apiStatus,eo_api.groupID,eo_api.projectID,eo_api.starred,eo_api.apiNoteType,eo_api.apiNoteRaw,eo_api.apiNote,eo_api.apiRequestParamType,eo_api.apiRequestRaw,eo_api.apiUpdateTime) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);', array($api['baseInfo']['apiName'], $api['baseInfo']['apiURI'], $api['baseInfo']['apiProtocol'], $api['baseInfo']['apiSuccessMock'], $api['baseInfo']['apiFailureMock'], $api['baseInfo']['apiRequestType'], $api['baseInfo']['apiStatus'], $groupID, $projectID, $api['baseInfo']['starred'], $api['baseInfo']['apiNoteType'], $api['baseInfo']['apiNoteRaw'], $api['baseInfo']['apiNote'], $api['baseInfo']['apiRequestParamType'], $api['baseInfo']['apiRequestRaw'], $api['baseInfo']['apiUpdateTime']));

							if ($db -> getAffectRow() < 1)
								throw new \PDOException("addApi error");

							$apiID = $db -> getLastInsertID();

							//插入header信息
							if (is_array($api['headerInfo'])) {
								foreach ($api['headerInfo'] as $param) {
									$db -> prepareExecute('INSERT INTO eo_api_header (eo_api_header.headerName,eo_api_header.headerValue,eo_api_header.apiID) VALUES (?,?,?);', array($param['headerName'], $param['headerValue'], $apiID));

									if ($db -> getAffectRow() < 1)
										throw new \PDOException("addHeader error");
								}
							}

							//插入api请求值信息
							if (is_array($api['requestInfo'])) {
								foreach ($api['requestInfo'] as $param) {
									$db -> prepareExecute('INSERT INTO eo_api_request_param (eo_api_request_param.apiID,eo_api_request_param.paramName,eo_api_request_param.paramKey,eo_api_request_param.paramValue,eo_api_request_param.paramLimit,eo_api_request_param.paramNotNull,eo_api_request_param.paramType) VALUES (?,?,?,?,?,?,?);', array($apiID, $param['paramName'], $param['paramKey'], ($param['paramValue']) ? $param['paramValue'] : "", $param['paramLimit'], $param['paramNotNull'], $param['paramType']));

									if ($db -> getAffectRow() < 1)
										throw new \PDOException("addRequestParam error");

									$paramID = $db -> getLastInsertID();

									foreach ($param['paramValueList'] as $value) {
										$db -> prepareExecute('INSERT INTO eo_api_request_value (eo_api_request_value.paramID,eo_api_request_value.`value`,eo_api_request_value.valueDescription) VALUES (?,?,?);;', array($paramID, $value['value'], $value['valueDescription']));

										if ($db -> getAffectRow() < 1)
											throw new \PDOException("addApi error");

									};
								};
							}

							//插入api返回值信息
							if (is_array($api['resultInfo'])) {
								foreach ($api['resultInfo'] as $param) {
									$db -> prepareExecute('INSERT INTO eo_api_result_param (eo_api_result_param.apiID,eo_api_result_param.paramName,eo_api_result_param.paramKey,eo_api_result_param.paramNotNull) VALUES (?,?,?,?);', array($apiID, $param['paramName'], $param['paramKey'], $param['paramNotNull']));

									if ($db -> getAffectRow() < 1)
										throw new \PDOException("addResultParam error");

									$paramID = $db -> getLastInsertID();

									if (is_array($param['paramValueList'])) {
										foreach ($param['paramValueList'] as $value) {
											$db -> prepareExecute('INSERT INTO eo_api_result_value (eo_api_result_value.paramID,eo_api_result_value.`value`,eo_api_result_value.valueDescription) VALUES (?,?,?);;', array($paramID, $value['value'], $value['valueDescription']));

											if ($db -> getAffectRow() < 1)
												throw new \PDOException("addApi error");
										};
									}
								};
							}

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

							$db -> prepareExecute('INSERT INTO eo_api_mock (eo_api_mock.mockCode,eo_api_mock.apiID) VALUES (?,?);', array($mockCode, $apiID));

							if ($db -> getAffectRow() < 1)
								throw new \PDOException("addMockCode error");

							//插入api缓存数据用于导出
							$db -> prepareExecute("INSERT INTO eo_api_cache (eo_api_cache.projectID,eo_api_cache.groupID,eo_api_cache.apiID,eo_api_cache.apiJson,eo_api_cache.starred) VALUES (?,?,?,?,?);", array($projectID, $groupID, $apiID, json_encode($api), 0));

							if ($db -> getAffectRow() < 1) {
								throw new \PDOException("addApiCache error");
							}
						}
					}
				}
			}
		} catch(\PDOException $e) {
			$db -> rollBack();
			return FALSE;
		}
		$db -> commit();
		return TRUE;
	}

}
?>