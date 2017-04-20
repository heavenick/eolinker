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
class ImportModule {
	function __construct() {
		@session_start();
	}

	/**
	 * 导入eoapi
	 * @param $data 从eoapi导出的Json格式数据
	 */
	public function eoapiImport(&$data) {
		$dao = new ImportDao;
		return $dao -> importEoapi($data, $_SESSION['userID']);
	}

	/**
	 * 导入DHC
	 * @param $data 从DHC导出的Json格式数据
	 */
	public function importDHC(&$data) {
		try {
			$projectInfo = array('projectName' => $data['nodes'][0]['name'], 'projectType' => 0, 'projectVersion' => 1.0);

			//生成分组信息
			$groupInfoList[] = array('groupName' => 'DHC导入', 'id' => $data['nodes'][0]['id']);
			if (is_array($data['nodes'])) {
				foreach ($data['nodes'] as $element) {
					if ($element['type'] == 'Service') {
						$groupInfoList[] = array('groupName' => $element['name'], 'id' => $element['id']);
					}
				}
			}

			if (is_array($groupInfoList)) {
				foreach ($groupInfoList as &$groupInfo) {
					$apiList = array();
					if (is_array($data['nodes'])) {
						foreach ($data['nodes'] as $element) {
							if ($element['type'] != 'Request' || $element['parentId'] != $groupInfo['id']) {
								continue;
							}

							$apiInfo['baseInfo']['apiName'] = $element['name'];
							$apiInfo['baseInfo']['apiURI'] = $element['uri']['path'];
							$apiInfo['baseInfo']['apiProtocol'] = ($element['uri']['scheme']['name'] == 'http') ? 0 : 1;
							$apiInfo['baseInfo']['apiStatus'] = 0;
							$apiInfo['baseInfo']['starred'] = 0;
							$apiInfo['baseInfo']['apiSuccessMock'] = '';
							$apiInfo['baseInfo']['apiFailureMock'] = '';
							$apiInfo['baseInfo']['apiRequestParamType'] = 0;
							$apiInfo['baseInfo']['apiRequestRaw'] = '';
							$apiInfo['baseInfo']['apiNoteType'] = 0;
							$apiInfo['baseInfo']['apiNote'] = '';
							$apiInfo['baseInfo']['apiNoteRaw'] = '';
							$apiInfo['baseInfo']['apiUpdateTime'] = date("Y-m-d H:i:s", time());
							switch($element['method']['name']) {
								case 'POST' :
									$apiInfo['baseInfo']['apiRequestType'] = 0;
									break;
								case 'GET' :
									$apiInfo['baseInfo']['apiRequestType'] = 1;
									break;
								case 'PUT' :
									$apiInfo['baseInfo']['apiRequestType'] = 2;
									break;
								case 'DELETE' :
									$apiInfo['baseInfo']['apiRequestType'] = 3;
									break;
								case 'HEAD' :
									$apiInfo['baseInfo']['apiRequestType'] = 4;
									break;
								case 'OPTIONS' :
									$apiInfo['baseInfo']['apiRequestType'] = 5;
									break;
								case 'PATCH' :
									$apiInfo['baseInfo']['apiRequestType'] = 6;
									break;
							}

							$headerInfo = array();

							if (is_array($element['headers'])) {
								foreach ($element['headers'] as $header) {
									$headerInfo[] = array('headerName' => $header['name'], 'headerValue' => $header['value']);
								}
							}
							$apiInfo['headerInfo'] = $headerInfo;
							unset($headerInfo);

							$apiRequestParam = array();
							if ($element['method']['requestBody']) {
								$items = $element['body']['formBody']['items'];
								if (is_array($items)) {
									foreach ($items as $item) {
										$param['paramKey'] = $item['name'];
										$param['paramValue'] = $item['value'];
										$param['paramType'] = ($item['type'] == 'Text') ? 0 : 1;
										$param['paramNotNull'] = $item['enabled'] ? 0 : 1;
										$param['paramName'] = '';
										$param['paramLimit'] = '';
										$param['paramValueList'] = array();
										$apiRequestParam[] = $param;
										unset($param);
									}
								}
							}
							$apiInfo['requestInfo'] = $apiRequestParam;
							unset($apiRequestParam);
							$apiInfo['resultInfo'] = array();

							$apiList[] = $apiInfo;
							unset($apiInfo);
						}
					}
					$groupInfo['apiList'] = $apiList;
					unset($apiList);
				}
			}
			$dao = new ImportDao;
			return $dao -> importOther($projectInfo, $groupInfoList, $_SESSION['userID']);
		} catch(\PDOException $e) {
			return FALSE;
		}
	}

	/**
	 * 导入V1版本postman
	 * @param $data 从Postman V1版本导出的Json格式数据
	 */
	public function importPostmanV1(&$data) {
		try {
			$projectInfo = array('projectName' => $data['name'], 'projectType' => 0, 'projectVersion' => 1.0);

			$groupInfoList[] = array('groupName' => 'PostMan导入');

			$apiList = array();
			if (is_array($groupInfoList)) {
				foreach ($groupInfoList as &$groupInfo) {
					if (is_array($data['requests'])) {
						foreach ($data['requests'] as $request) {
							$apiInfo['baseInfo']['apiName'] = $request['name'];
							$apiInfo['baseInfo']['apiURI'] = $request['url'];
							$apiInfo['baseInfo']['apiProtocol'] = (strpos($request['url'], 'https') !== 0) ? 0 : 1;
							$apiInfo['baseInfo']['apiStatus'] = 0;
							$apiInfo['baseInfo']['starred'] = 0;
							$apiInfo['baseInfo']['apiSuccessMock'] = '';
							$apiInfo['baseInfo']['apiFailureMock'] = '';
							$apiInfo['baseInfo']['apiRequestParamType'] = 0;
							$apiInfo['baseInfo']['apiRequestRaw'] = '';
							$apiInfo['baseInfo']['apiNoteType'] = 0;
							$apiInfo['baseInfo']['apiNote'] = '';
							$apiInfo['baseInfo']['apiNoteRaw'] = '';
							$apiInfo['baseInfo']['apiUpdateTime'] = date("Y-m-d H:i:s", time());
							switch($request['method']) {
								case 'POST' :
									$apiInfo['baseInfo']['apiRequestType'] = 0;
									break;
								case 'GET' :
									$apiInfo['baseInfo']['apiRequestType'] = 1;
									break;
								case 'PUT' :
									$apiInfo['baseInfo']['apiRequestType'] = 2;
									break;
								case 'DELETE' :
									$apiInfo['baseInfo']['apiRequestType'] = 3;
									break;
								case 'HEAD' :
									$apiInfo['baseInfo']['apiRequestType'] = 4;
									break;
								case 'OPTIONS' :
									$apiInfo['baseInfo']['apiRequestType'] = 5;
									break;
								case 'PATCH' :
									$apiInfo['baseInfo']['apiRequestType'] = 6;
									break;
							}

							$headerInfo = array();
							$header_rows = array_filter(explode(chr(10), $request['headers']), "trim");

							if (is_array($header_rows)) {
								foreach ($header_rows as $row) {
									$keylen = strpos($row, ':');
									if ($keylen) {
										$headerInfo[] = array('headerName' => substr($row, 0, $keylen), 'headerValue' => trim(substr($row, $keylen + 1)));
									}
								}
							}
							$apiInfo['headerInfo'] = $headerInfo;
							unset($headerInfo);

							$apiRequestParam = array();
							$items = $request['data'];
							if (is_array($items)) {
								foreach ($items as $item) {
									$param['paramKey'] = $item['key'];
									$param['paramValue'] = $item['value'];
									$param['paramType'] = ($item['type'] == 'text') ? 0 : 1;
									$param['paramNotNull'] = $item['enabled'] ? 0 : 1;
									$param['paramName'] = '';
									$param['paramLimit'] = '';
									$param['paramValueList'] = array();
									$apiRequestParam[] = $param;
									unset($param);
								}
							}
							$apiInfo['requestInfo'] = $apiRequestParam;
							unset($apiRequestParam);
							$apiInfo['resultInfo'] = array();

							$apiList[] = $apiInfo;
							unset($apiInfo);
						}
					}
					$groupInfo['apiList'] = $apiList;
					unset($apiList);
				}
			}
			$dao = new ImportDao;
			return $dao -> importOther($projectInfo, $groupInfoList, $_SESSION['userID']);
		} catch(\PDOException $e) {
			return FALSE;
		}
	}

	/**
	 * 导入V2版本postman
	 * @param $data 从Postman V2版本导出的Json格式数据
	 */
	public function importPostmanV2(&$data) {
		try {
			$projectInfo = array('projectName' => $data['info']['name'], 'projectType' => 0, 'projectVersion' => 1.0);

			$groupInfoList[] = array('groupName' => 'PostMan导入');

			$apiList = array();

			if (is_array($groupInfoList)) {
				foreach ($groupInfoList as &$groupInfo) {
					if (is_array($data['item'])) {
						foreach ($data['item'] as $item) {
							$apiInfo['baseInfo']['apiName'] = $item['name'];
							$apiInfo['baseInfo']['apiURI'] = $item['request']['url'];
							$apiInfo['baseInfo']['apiProtocol'] = (strpos($item['request']['url'], 'https') !== 0) ? 0 : 1;
							$apiInfo['baseInfo']['apiStatus'] = 0;
							$apiInfo['baseInfo']['starred'] = 0;
							$apiInfo['baseInfo']['apiSuccessMock'] = '';
							$apiInfo['baseInfo']['apiFailureMock'] = '';
							$apiInfo['baseInfo']['apiRequestParamType'] = 0;
							$apiInfo['baseInfo']['apiRequestRaw'] = '';
							$apiInfo['baseInfo']['apiNoteType'] = 0;
							$apiInfo['baseInfo']['apiNote'] = '';
							$apiInfo['baseInfo']['apiNoteRaw'] = '';
							$apiInfo['baseInfo']['apiUpdateTime'] = date("Y-m-d H:i:s", time());
							switch($item['request']['method']) {
								case 'POST' :
									$apiInfo['baseInfo']['apiRequestType'] = 0;
									break;
								case 'GET' :
									$apiInfo['baseInfo']['apiRequestType'] = 1;
									break;
								case 'PUT' :
									$apiInfo['baseInfo']['apiRequestType'] = 2;
									break;
								case 'DELETE' :
									$apiInfo['baseInfo']['apiRequestType'] = 3;
									break;
								case 'HEAD' :
									$apiInfo['baseInfo']['apiRequestType'] = 4;
									break;
								case 'OPTIONS' :
									$apiInfo['baseInfo']['apiRequestType'] = 5;
									break;
								case 'PATCH' :
									$apiInfo['baseInfo']['apiRequestType'] = 6;
									break;
							}

							$headerInfo = array();
							if (is_array($item['request']['header'])) {
								foreach ($item['request']['header'] as $header) {
									$headerInfo[] = array('headerName' => $header['key'], 'headerValue' => $header['value']);
								}
							}
							$apiInfo['headerInfo'] = $headerInfo;
							unset($headerInfo);

							$apiRequestParam = array();
							if ($item['request']['body']['mode'] == 'formdata') {
								$parameters = $item['request']['body']['formdata'];
								if (is_array($parameters)) {
									foreach ($parameters as $parameter) {
										$param['paramKey'] = $parameter['key'];
										$param['paramValue'] = $parameter['value'];
										$param['paramType'] = ($parameter['type'] == 'text') ? 0 : 1;
										$param['paramNotNull'] = $parameter['enabled'] ? 0 : 1;
										$param['paramName'] = '';
										$param['paramLimit'] = '';
										$param['paramValueList'] = array();
										$apiRequestParam[] = $param;
										unset($param);
									}
								}
							}
							$apiInfo['requestInfo'] = $apiRequestParam;
							unset($apiRequestParam);

							$apiInfo['resultInfo'] = array();

							$apiList[] = $apiInfo;
							unset($apiInfo);
						}
					}
					$groupInfo['apiList'] = $apiList;
					unset($apiList);
				}
			}
			$dao = new ImportDao;
			return $dao -> importOther($projectInfo, $groupInfoList, $_SESSION['userID']);
		} catch(\PDOException $e) {
			return FALSE;
		}
	}

}
?>