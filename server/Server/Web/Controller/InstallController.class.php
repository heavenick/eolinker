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
class InstallController {
	//返回Json类型
	private $returnJson = array('type' => 'install');

	/**
	 * 检测环境
	 */
	public function checkoutEnv() {
		//获取必要的信息
		$dbURL = quickInput('dbURL');
		$dbName = quickInput('dbName');
		$dbUser = quickInput('dbUser');
		$dbPassword = quickInput('dbPassword');
		$server = new InstallModule;
		$result = $server -> checkoutEnv($dbURL, $dbName, $dbUser, $dbPassword);
		if (isset($result['error'])) {
			$this -> returnJson['statusCode'] = '200004';
			$this -> returnJson['error'] = $result['error'];
		} else {
			$this -> returnJson['statusCode'] = '000000';
			$this -> returnJson['envStatus'] = $result;
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 检查配置
	 */
	public function checkConfig() {
		if (!file_exists(PATH_FW . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'eo_config.php')) {
			//强化判断，是否已经定义了数据库的地址信息
			if (@!defined(DB_URL))
				//不存在配置文件，需要跳转至引导页面进行安装
				$this -> returnJson['statusCode'] = '200003';
		} else {
			$this -> returnJson['statusCode'] = '000000';
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 安装eolinker
	 */
	public function start() {
		//检查是否已经存在配置文件或者是否可以获取到数据库地址
		if (file_exists(PATH_FW . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'eo_config.php') || defined(DB_URL)) {
			//直接返回成功
			$this -> returnJson['statusCode'] = '000000';
			exitOutput($this -> returnJson);
		} elseif (!file_exists(PATH_FW . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'eo_config.php')) {
			//获取必要的信息
			$dbURL = quickInput('dbURL');
			$dbName = quickInput('dbName');
			$dbUser = quickInput('dbUser');
			$dbPassword = quickInput('dbPassword');
			$websiteName = quickInput('websiteName');
			$server = new InstallModule;
			if ($server -> createConfigFile($dbURL, $dbName, $dbUser, $dbPassword, $websiteName)) {
				//写入成功
				quickRequire(PATH_FW . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'eo_config.php');
				if ($server -> installDatabase()) {
					$this -> returnJson['statusCode'] = '000000';
					@session_start();
					@session_destroy();
				} else {
					//创建数据库失败，确认是否拥有数据库操作权限
					$this -> returnJson['statusCode'] = '200002';
					unlink(realpath(PATH_FW . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'eo_config.php'));
				}
			} else {
				//写入失败，确认是否拥有文件操作权限
				$this -> returnJson['statusCode'] = '200001';
				unlink(realpath(PATH_FW . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'eo_config.php'));
			}
		}

		exitOutput($this -> returnJson);
	}

}
?>