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
class StatusCodeController {
	// 返回json类型
	private $returnJson = array('type' => 'status_code');

	/**
	 * 检查登录状态
	 */
	public function __construct() {
		// 身份验证
		$server = new GuestModule;
		if (!$server -> checkLogin()) {
			$this -> returnJson['statusCode'] = '120005';
			exitOutput($this -> returnJson);
		}
	}

	/**
	 * 添加状态码
	 */
	public function addCode() {
		$codeLen = mb_strlen(quickInput('code'), 'utf8');
		$codeDescLen = mb_strlen(quickInput('codeDesc'), 'utf8');
		$groupID = securelyInput('groupID');
		$code = securelyInput('code');
		$codeDesc = securelyInput('codeDesc');

		if (!preg_match('/^[0-9]{1,11}$/', $groupID)) {
			//分组ID格式不合法
			$this -> returnJson['statusCode'] = '190002';
		} elseif (!($codeLen >= 1 && $codeLen <= 255)) {
			//状态码格式不合法
			$this -> returnJson['statusCode'] = '190008';
		} elseif (!($codeDescLen >= 1 && $codeDescLen <= 255)) {
			//状态码描述格式不合法
			$this -> returnJson['statusCode'] = '190003';
		} else {
			$service = new StatusCodeModule;
			$result = $service -> addCode($groupID, $codeDesc, $code);

			if ($result) {
				$this -> returnJson['statusCode'] = '000000';
				$this -> returnJson['codeID'] = $result;
			} else {
				$this -> returnJson['statusCode'] = '190004';
			}
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 删除状态码
	 */
	public function deleteCode() {
		$codeID = securelyInput('codeID');

		if (!preg_match('/^[0-9]{1,11}$/', $codeID)) {
			//状态码ID格式不合法
			$this -> returnJson['statusCode'] = '190005';
		} else {
			$service = new StatusCodeModule;
			$result = $service -> deleteCode($codeID);

			if ($result) {
				$this -> returnJson['statusCode'] = '000000';
			} else {
				$this -> returnJson['statusCode'] = '190006';
			}
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 获取状态码列表
	 */
	public function getCodeList() {
		$groupID = securelyInput('groupID');

		if (!preg_match('/^[0-9]{1,11}$/', $groupID)) {
			//分组ID格式不合法
			$this -> returnJson['statusCode'] = '190002';
		} else {
			$service = new StatusCodeModule;
			$result = $service -> getCodeList($groupID);

			if ($result) {
				$this -> returnJson['statusCode'] = '000000';
				$this -> returnJson['codeList'] = $result;
			} else {
				$this -> returnJson['statusCode'] = '190001';
			}
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 获取所有状态码列表
	 */
	public function getAllCodeList() {
		$projectID = securelyInput('projectID');

		if (!preg_match('/^[0-9]{1,11}$/', $projectID)) {
			//项目ID格式不合法
			$this -> returnJson['statusCode'] = '190007';
		} else {
			$service = new StatusCodeModule;
			$result = $service -> getAllCodeList($projectID);

			if ($result) {
				$this -> returnJson['statusCode'] = '000000';
				$this -> returnJson['codeList'] = $result;
			} else {
				$this -> returnJson['statusCode'] = '190001';
			}
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 修改状态码
	 */
	public function editCode() {
		$codeLen = mb_strlen(quickInput('code'), 'utf8');
		$codeDescLen = mb_strlen(quickInput('codeDesc'), 'utf8');
		$codeID = securelyInput('codeID');
		$groupID = securelyInput('groupID');
		$code = securelyInput('code');
		$codeDesc = securelyInput('codeDesc');

		if (!preg_match('/^[0-9]{1,11}$/', $codeID)) {
			//状态码ID格式非法
			$this -> returnJson['statusCode'] = '190005';
		} elseif (!preg_match('/^[0-9]{1,11}$/', $groupID)) {
			//分组ID格式非法
			$this -> returnJson['statusCode'] = '190002';
		} elseif (!($codeLen >= 1 && $codeLen <= 255)) {
			//状态码格式非法
			$this -> returnJson['statusCode'] = '190008';
		} elseif (!($codeDescLen >= 1 && $codeDescLen <= 255)) {
			//状态码描述格式非法
			$this -> returnJson['statusCode'] = '190003';
		} else {
			$service = new StatusCodeModule;
			$result = $service -> editCode($groupID, $codeID, $code, $codeDesc);

			if ($result) {
				$this -> returnJson['statusCode'] = '000000';
			} else {
				$this -> returnJson['statusCode'] = '190009';
			}
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 搜索状态码
	 */
	public function searchStatusCode() {
		$projectID = securelyInput('projectID');
		$tipsLen = mb_strlen(quickInput('tips'), 'utf8');
		$tips = securelyInput('tips');

		if (!preg_match('/^[0-9]{1,11}$/', $projectID)) {
			//项目ID格式不合法
			$this -> returnJson['statusCode'] = '190007';
		} elseif (!($tipsLen >= 1 && $tipsLen <= 255)) {
			$this -> returnJson['statusCode'] = '190008';
		} else {
			$service = new StatusCodeModule;
			$result = $service -> searchStatusCode($projectID, $tips);

			if ($result) {
				$this -> returnJson['statusCode'] = '000000';
				$this -> returnJson['codeList'] = $result;
			} else {
				$this -> returnJson['statusCode'] = '190001';
			}
		}
		exitOutput($this -> returnJson);
	}

	/*
	 * 获取状态码数量
	 */
	public function getStatusCodeNum() {
		$projectID = securelyInput('projectID');
		if (!preg_match('/^[0-9]{1,11}$/', $projectID)) {
			//项目ID格式不合法
			$this -> returnJson['statusCode'] = '190007';
		} else {
			$service = new StatusCodeModule;
			$result = $service -> getStatusCodeNum($projectID);

			if ($result) {
				$this -> returnJson['statusCode'] = '000000';
				$this -> returnJson['num'] = $result['num'];
			} else
				$this -> returnJson['statusCode'] = '190010';
		}
		exitOutput($this -> returnJson);
	}

}
?>