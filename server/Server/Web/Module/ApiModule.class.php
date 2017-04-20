<?php
/**
 * @name eoapi open source，eoapi开源版本
 * @link https://www.eoapi.cn
 * @package eoapi
 * @author www.eoapi.cn 广州银云信息科技有限公司 ©2015-2016

 * eoapi，业内领先的Api接口管理及测试平台，为您提供最专业便捷的在线接口管理、测试、维护以及各类性能测试方案，帮助您高效开发、安全协作。
 * 如在使用的过程中有任何问题，欢迎加入用户讨论群进行反馈，我们将会以最快的速度，最好的服务态度为您解决问题。
 * 用户讨论QQ群：284421832
 *
 * 注意！eoapi开源版本仅供用户下载试用、学习和交流，禁止“一切公开使用于商业用途”或者“以eoapi开源版本为基础而开发的二次版本”在互联网上流通。
 * 注意！一经发现，我们将立刻启用法律程序进行维权。
 * 再次感谢您的使用，希望我们能够共同维护国内的互联网开源文明和正常商业秩序。
 *
 */
class ApiModule {
	public function __construct() {
		@session_start();
	}

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
	 */
	public function addApi(&$apiName, &$apiURI, &$apiProtocol, &$apiSuccessMock, &$apiFailureMock, &$apiRequestType, &$apiStatus, &$groupID, &$apiHeader, &$apiRequestParam, &$apiResultParam, &$starred, &$apiNoteType, &$apiNoteRaw, &$apiNote, &$apiRequestParamType, &$apiRequestRaw) {
		//判断部分请求参数是否为空，如果为空值则赋值成为空字符串
		if (empty($apiSuccessMock)) {
			$apiSuccessMock = '';
		}
		if (empty($apiFailureMock)) {
			$apiFailureMock = '';
		}
		if (empty($apiRequestRaw)) {
			$apiRequestRaw = '';
		}
		if (empty($apiNote) || $apiNote == '&lt;p&gt;&lt;br&gt;&lt;/p&gt;') {
			$apiNote = '';
		}
		if (empty($apiNoteRaw)) {
			$apiNoteRaw = '';
		}

		$apiDao = new ApiDao;
		$groupDao = new GroupDao;
		$projectDao = new ProjectDao;
		if ($projectID = $groupDao -> checkGroupPermission($groupID, $_SESSION['userID'])) {
			$projectDao -> updateProjectUpdateTime($projectID);

			//生成缓存数据
			$cacheJson['baseInfo']['apiName'] = $apiName;
			$cacheJson['baseInfo']['apiURI'] = $apiURI;
			$cacheJson['baseInfo']['apiProtocol'] = intval($apiProtocol);
			$cacheJson['baseInfo']['apiSuccessMock'] = $apiSuccessMock;
			$cacheJson['baseInfo']['apiFailureMock'] = $apiFailureMock;
			$cacheJson['baseInfo']['apiRequestType'] = intval($apiRequestType);
			$cacheJson['baseInfo']['apiStatus'] = intval($apiStatus);
			$cacheJson['baseInfo']['starred'] = intval($starred);
			$cacheJson['baseInfo']['apiNoteType'] = intval($apiNoteType);
			$cacheJson['baseInfo']['apiNoteRaw'] = $apiNoteRaw;
			$cacheJson['baseInfo']['apiNote'] = $apiNote;
			$cacheJson['baseInfo']['apiRequestParamType'] = intval($apiRequestParamType);
			$cacheJson['baseInfo']['apiRequestRaw'] = $apiRequestRaw;
			$updateTime = date("Y-m-d H:i:s", time());
			$cacheJson['baseInfo']['apiUpdateTime'] = $updateTime;
			$cacheJson['headerInfo'] = $apiHeader;
			//将数组中的数字字符串转换为数字并且进行排序
			//			if (is_array($apiRequestParam))
			//			{
			//				$sortKey = array();
			//				foreach ($apiRequestParam as &$param)
			//				{
			//					$sortKey[] = $param['paramKey'];
			//					$param['paramNotNull'] = intval($param['paramNotNull']);
			//					$param['paramType'] = intval($param['paramType']);
			//				}
			//				array_multisort($sortKey, SORT_ASC, $apiRequestParam);
			//			}
			$cacheJson['requestInfo'] = $apiRequestParam;
			//			if (is_array($apiResultParam))
			//			{
			//				$sortKey = array();
			//				foreach ($apiResultParam as &$param)
			//				{
			//					$sortKey[] = $param['paramKey'];
			//					$param['paramNotNull'] = intval($param['paramNotNull']);
			//				}
			//				array_multisort($sortKey, SORT_ASC, $apiResultParam);
			//			}
			$cacheJson['resultInfo'] = $apiResultParam;
			$cacheJson = json_encode($cacheJson);

			return $apiDao -> addApi($apiName, $apiURI, $apiProtocol, $apiSuccessMock, $apiFailureMock, $apiRequestType, $apiStatus, $groupID, $apiHeader, $apiRequestParam, $apiResultParam, $starred, $apiNoteType, $apiNoteRaw, $apiNote, $projectID, $apiRequestParamType, $apiRequestRaw, $cacheJson, $updateTime);
		} else
			return FALSE;
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
	 */
	public function editApi(&$apiID, &$apiName, &$apiURI, &$apiProtocol, &$apiSuccessMock, &$apiFailureMock, &$apiRequestType, &$apiStatus, &$groupID, &$apiHeader, &$apiRequestParam, &$apiResultParam, &$starred, &$apiNoteType, &$apiNoteRaw, &$apiNote, &$apiRequestParamType, &$apiRequestRaw) {
		//判断部分请求参数是否为空，如果为空值则赋值成为空字符串
		if (empty($apiSuccessMock)) {
			$apiSuccessMock = '';
		}
		if (empty($apiFailureMock)) {
			$apiFailureMock = '';
		}
		if (empty($apiRequestRaw)) {
			$apiRequestRaw = '';
		}
		if (empty($apiNote) || $apiNote == '&lt;p&gt;&lt;br&gt;&lt;/p&gt;') {
			$apiNote = '';
		}
		if (empty($apiNoteRaw)) {
			$apiNoteRaw = '';
		}

		$apiDao = new ApiDao;
		$groupDao = new GroupDao;
		$projectDao = new ProjectDao;
		if ($apiDao -> checkApiPermission($apiID, $_SESSION['userID'])) {
			if ($projectID = $groupDao -> checkGroupPermission($groupID, $_SESSION['userID'])) {
				$projectDao -> updateProjectUpdateTime($projectID);

				//生成缓存数据
				$cacheJson['baseInfo']['apiName'] = $apiName;
				$cacheJson['baseInfo']['apiURI'] = $apiURI;
				$cacheJson['baseInfo']['apiProtocol'] = intval($apiProtocol);
				$cacheJson['baseInfo']['apiSuccessMock'] = $apiSuccessMock;
				$cacheJson['baseInfo']['apiFailureMock'] = $apiFailureMock;
				$cacheJson['baseInfo']['apiRequestType'] = intval($apiRequestType);
				$cacheJson['baseInfo']['apiStatus'] = intval($apiStatus);
				$cacheJson['baseInfo']['starred'] = intval($starred);
				$cacheJson['baseInfo']['apiNoteType'] = intval($apiNoteType);
				$cacheJson['baseInfo']['apiNoteRaw'] = $apiNoteRaw;
				$cacheJson['baseInfo']['apiNote'] = $apiNote;
				$cacheJson['baseInfo']['apiRequestParamType'] = intval($apiRequestParamType);
				$cacheJson['baseInfo']['apiRequestRaw'] = $apiRequestRaw;
				$updateTime = date("Y-m-d H:i:s", time());
				$cacheJson['baseInfo']['apiUpdateTime'] = $updateTime;
				$cacheJson['headerInfo'] = $apiHeader;
				//将数组中的数字字符串转换为数字并且进行排序
				//				if (is_array($apiRequestParam))
				//				{
				//					$sortKey = array();
				//					foreach ($apiRequestParam as &$param)
				//					{
				//						$sortKey[] = $param['paramKey'];
				//						$param['paramNotNull'] = intval($param['paramNotNull']);
				//						$param['paramType'] = intval($param['paramType']);
				//					}
				//					array_multisort($sortKey, SORT_ASC, $apiRequestParam);
				//				}
				$cacheJson['requestInfo'] = $apiRequestParam;
				//				if (is_array($apiResultParam))
				//				{
				//					$sortKey = array();
				//					foreach ($apiResultParam as &$param)
				//					{
				//						$sortKey[] = $param['paramKey'];
				//						$param['paramNotNull'] = intval($param['paramNotNull']);
				//					}
				//					array_multisort($sortKey, SORT_ASC, $apiResultParam);
				//				}
				$cacheJson['resultInfo'] = $apiResultParam;
				$cacheJson = json_encode($cacheJson);

				return $apiDao -> editApi($apiID, $apiName, $apiURI, $apiProtocol, $apiSuccessMock, $apiFailureMock, $apiRequestType, $apiStatus, $groupID, $apiHeader, $apiRequestParam, $apiResultParam, $starred, $apiNoteType, $apiNoteRaw, $apiNote, $apiRequestParamType, $apiRequestRaw, $cacheJson, $updateTime);
			} else
				return FALSE;
		} else
			return FALSE;
	}

	/**
	 * 删除api,将其移入回收站
	 * @param $apiID 接口ID
	 */
	public function removeApi(&$apiID) {
		$apiDao = new ApiDao;
		$projectDao = new ProjectDao;
		if ($projectID = $apiDao -> checkApiPermission($apiID, $_SESSION['userID'])) {
			$projectDao -> updateProjectUpdateTime($projectID);
			return $apiDao -> removeApi($apiID);
		} else
			return FALSE;
	}

	/**
	 * 恢复api
	 * @param $apiID 接口ID
	 */
	public function recoverApi(&$apiID) {
		$apiDao = new ApiDao;
		$projectDao = new ProjectDao;
		if ($projectID = $apiDao -> checkApiPermission($apiID, $_SESSION['userID'])) {
			$projectDao -> updateProjectUpdateTime($projectID);
			return $apiDao -> recoverApi($apiID);
		} else
			return FALSE;
	}

	/**
	 * 彻底删除api
	 * @param $apiID 接口ID
	 */
	public function deleteApi(&$apiID) {
		$apiDao = new ApiDao;
		$projectDao = new ProjectDao;
		if ($projectID = $apiDao -> checkApiPermission($apiID, $_SESSION['userID'])) {
			$projectDao -> updateProjectUpdateTime($projectID);
			return $apiDao -> deleteApi($apiID);
		} else
			return FALSE;
	}

	/**
	 * 清空回收站
	 * @param $projectID 项目ID
	 */
	public function cleanRecyclingStation(&$projectID) {
		$apiDao = new ApiDao;
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'])) {
			return $apiDao -> cleanRecyclingStation($projectID);
		} else
			return FALSE;
	}

	/**
	 * 获取api列表并按照名称排序
	 * @param $groupID 接口分组ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getApiListOrderByName(&$groupID, &$asc = 0) {
		$apiDao = new ApiDao;
		$groupDao = new GroupDao;
		if ($groupDao -> checkGroupPermission($groupID, $_SESSION['userID'])) {
			$asc = $asc == 0 ? 'ASC' : 'DESC';
			return $apiDao -> getApiListOrderByName($groupID, $asc);
		} else
			return FALSE;
	}

	/**
	 * 获取api列表并按照时间排序
	 * @param $groupID 接口分组ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getApiListOrderByTime(&$groupID, &$asc = 0) {
		$apiDao = new ApiDao;
		$groupDao = new GroupDao;
		if ($groupDao -> checkGroupPermission($groupID, $_SESSION['userID'])) {
			$asc = $asc == 0 ? 'ASC' : 'DESC';
			return $apiDao -> getApiListOrderByTime($groupID, $asc);
		} else
			return FALSE;
	}

	/**
	 * 获取api列表并按照星标排序
	 * @param $groupID 接口分组ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getApiListOrderByStarred(&$groupID, &$asc = 0) {
		$apiDao = new ApiDao;
		$groupDao = new GroupDao;
		if ($groupDao -> checkGroupPermission($groupID, $_SESSION['userID'])) {
			$asc = $asc == 0 ? 'ASC' : 'DESC';
			return $apiDao -> getApiListOrderByStarred($groupID, $asc);
		} else
			return FALSE;
	}

	/**
	 * 获取api列表并按照名称排序
	 * @param $groupID 接口分组ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getApiListOrderByUri(&$groupID, &$asc = 0) {
		$apiDao = new ApiDao;
		$groupDao = new GroupDao;
		if ($groupDao -> checkGroupPermission($groupID, $_SESSION['userID'])) {
			$asc = $asc == 0 ? 'ASC' : 'DESC';
			return $apiDao -> getApiListOrderByUri($groupID, $asc);
		} else
			return FALSE;
	}

	/**
	 * 获取api列表并按照名称排序
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getRecyclingStationApiListOrderByName(&$projectID, &$asc = 0) {
		$apiDao = new ApiDao;
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'])) {
			$asc = $asc == 0 ? 'ASC' : 'DESC';
			return $apiDao -> getRecyclingStationApiListOrderByName($projectID, $asc);
		} else
			return FALSE;
	}

	/**
	 * 获取api列表并按照时间排序
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getRecyclingStationApiListOrderByRemoveTime(&$projectID, &$asc = 0) {
		$apiDao = new ApiDao;
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'])) {
			$asc = $asc == 0 ? 'ASC' : 'DESC';
			return $apiDao -> getRecyclingStationApiListOrderByRemoveTime($projectID, $asc);
		} else
			return FALSE;
	}

	/**
	 * 获取api列表并按照星标排序
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getRecyclingStationApiListOrderByStarred(&$projectID, &$asc = 0) {
		$apiDao = new ApiDao;
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'])) {
			$asc = $asc == 0 ? 'ASC' : 'DESC';
			return $apiDao -> getRecyclingStationApiListOrderByStarred($projectID, $asc);
		} else
			return FALSE;
	}

	/**
	 * 获取api列表并按照星标排序
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getRecyclingStationApiListOrderByUri(&$projectID, &$asc = 0) {
		$apiDao = new ApiDao;
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'])) {
			$asc = $asc == 0 ? 'ASC' : 'DESC';
			return $apiDao -> getRecyclingStationApiListOrderByUri($projectID, $asc);
		} else
			return FALSE;
	}

	/**
	 * 获取api详情
	 * @param $apiID 接口ID
	 */
	public function getApi(&$apiID) {
		$apiDao = new ApiDao;
		$groupDao = new GroupDao;
		if ($apiDao -> checkApiPermission($apiID, $_SESSION['userID'])) {
			$result = $apiDao -> getApi($apiID);
			//将mock数据转码以适应前端直接显示html代码
			$result['baseInfo']['apiSuccessMock'] = htmlspecialchars($result['baseInfo']['apiSuccessMock']);
			$result['baseInfo']['apiFailureMock'] = htmlspecialchars($result['baseInfo']['apiFailureMock']);
			return $result;
		} else
			return FALSE;
	}

	/**
	 * 获取所有分组的api并按照名称排序
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getAllApiListOrderByName(&$projectID, &$asc = 0) {
		$apiDao = new ApiDao;
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'])) {
			$asc = $asc == 0 ? 'ASC' : 'DESC';
			return $apiDao -> getAllApiListOrderByName($projectID, $asc);
		} else
			return FALSE;
	}

	/**
	 * 获取所有分组的api并按照时间排序
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getAllApiListOrderByTime(&$projectID, &$asc = 0) {
		$apiDao = new ApiDao;
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'])) {
			$asc = $asc == 0 ? 'ASC' : 'DESC';
			return $apiDao -> getAllApiListOrderByTime($projectID, $asc);
		} else
			return FALSE;
	}

	/**
	 * 获取所有分组的api并按照时间排序
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getAllApiListOrderByUri(&$projectID, &$asc = 0) {
		$apiDao = new ApiDao;
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'])) {
			$asc = $asc == 0 ? 'ASC' : 'DESC';
			return $apiDao -> getAllApiListOrderByUri($projectID, $asc);
		} else
			return FALSE;
	}

	/**
	 * 获取所有分组的api并按照星标排序
	 * @param $projectID 项目ID
	 * @param $asc 排序 [0/1]=>[升序/降序]
	 */
	public function getAllApiListOrderByStarred(&$projectID, &$asc = 0) {
		$apiDao = new ApiDao;
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'])) {
			$asc = $asc == 0 ? 'ASC' : 'DESC';
			return $apiDao -> getAllApiListOrderByStarred($projectID, $asc);
		} else
			return FALSE;
	}

	/**
	 * 搜索api
	 * @param $tips 搜索关键字
	 * @param $projectID 项目ID
	 */
	public function searchApi(&$tips, &$projectID) {
		$apiDao = new ApiDao;
		$projectDao = new ProjectDao;
		if ($projectDao -> checkProjectPermission($projectID, $_SESSION['userID'])) {
			return $apiDao -> searchApi($tips, $projectID);
		} else
			return FALSE;
	}

	/**
	 * 添加星标
	 * @param $apiID 接口ID
	 */
	public function addStar(&$apiID) {
		$apiDao = new ApiDao;
		if ($apiDao -> checkApiPermission($apiID, $_SESSION['userID'])) {
			return $apiDao -> addStar($apiID);
		} else
			return FALSE;
	}

	/**
	 * 去除星标
	 * @param $apiID 接口ID
	 */
	public function removeStar(&$apiID) {
		$apiDao = new ApiDao;
		if ($apiDao -> checkApiPermission($apiID, $_SESSION['userID'])) {
			return $apiDao -> removeStar($apiID);
		} else
			return FALSE;
	}

}
?>