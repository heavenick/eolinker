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
class UpdateController
{
	//返回Json类型
	private $returnJson = array('type' => 'update');

	/**
	 * 检查是否有更新
	 */
	public function checkUpdate()
	{
		if (ALLOW_UPDATE)
		{
			$server = new ProxyModule;
			$updateInfo = $server -> proxyToDesURL('GET', 'https://api.eolinker.com/OS/Update/checkout');
			$result = json_decode($updateInfo['testResult']['body'], TRUE);
			if ($result['statusCode'] == '000000')
			{
				if (OS_VERSION_CODE < $result['versionInfo']['versionCode'])
				{
					$this -> returnJson['statusCode'] = '000000';
				}
				else
				{
					$this -> returnJson['statusCode'] = '320002';
				}
			}
			else
			{
				$this -> returnJson['statusCode'] = '320001';
			}
			exitOutput($this -> returnJson);
		}
		else
		{
			//更新已被禁用
			$this -> returnJson['statusCode'] = '320004';
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 自动更新项目
	 */
	public function autoUpdate()
	{
		if (ALLOW_UPDATE)
		{
			$proxyServer = new ProxyModule;
			$updateInfo = $proxyServer -> proxyToDesURL('GET', 'https://api.eolinker.com/OS/Update/checkout');
			$result = json_decode($updateInfo['testResult']['body'], TRUE);
			if ($result['statusCode'] == '000000')
			{
				if (OS_VERSION_CODE < $result['versionInfo']['versionCode'])
				{
					$updateServer = new UpdateModule;
					if ($updateServer -> autoUpdate($result['versionInfo']['updateURI']))
					{
						$this -> returnJson['statusCode'] = '000000';
						//清除session退出登录
						@session_start();
						@session_destroy();
					}
					else
					{
						//更新失败
						$this -> returnJson['statusCode'] = '320003';
					}
				}
				else
				{
					//已是最新版本，无需更新
					$this -> returnJson['statusCode'] = '320002';
				}
			}
			else
			{
				//无法获取更新信息(可能断网等)
				$this -> returnJson['statusCode'] = '320001';
			}
		}
		else
		{
			//更新已被禁用
			$this -> returnJson['statusCode'] = '320004';
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 手动更新项目
	 */
	public function manualUpdate()
	{
		if (ALLOW_UPDATE)
		{
			$updateServer = new UpdateModule;
			if ($updateServer -> manualUpdate())
			{
				$this -> returnJson['statusCode'] = '000000';
				//清除session退出登录
				@session_start();
				@session_destroy();
			}
			else
			{
				//更新失败
				$this -> returnJson['statusCode'] = '320003';
			}
		}
		else
		{
			//更新已被禁用
			$this -> returnJson['statusCode'] = '320004';
		}
		exitOutput($this -> returnJson);
	}

}
?>