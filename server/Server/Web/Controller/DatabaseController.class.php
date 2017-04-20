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
class DatabaseController
{
	//返回Json类型
	private $returnJson = array('type' => 'database');
	
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
	 * 添加数据库
	 */
	public function addDatabase()
	{
		$nameLen = mb_strlen(quickInput('dbName'), 'utf8');
		$dbName = securelyInput('dbName');
		$dbVersion = securelyInput('dbVersion');

		if (!($nameLen >= 1 && $nameLen <= 32))
		{
			// 数据库名长度不合法
			$this -> returnJson['statusCode'] = '220001';
		}
		elseif (!(is_float(floatval($dbVersion)) && intval($dbVersion)))
		{
			// 数据库版本不合法
			$this -> returnJson['statusCode'] = '220002';
		}
		else
		{
			$service = new DatabaseModule;
			$result = $service -> addDatabase($dbName, $dbVersion);
			if ($result)
			{
				$this -> returnJson['statusCode'] = '000000';
				$this -> returnJson['dbID'] = $result;
			}
			else
			{
				$this -> returnJson['statusCode'] = '220003';
			}
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 删除数据库
	 */
	public function deleteDatabase()
	{
		$dbID = securelyInput('dbID');

		//数据库ID格式非法
		if (!preg_match('/^[0-9]{1,11}$/', $dbID))
		{
			$this -> returnJson['statusCode'] = '220004';
		}
		else
		{
			$service = new DatabaseModule;
			$result = $service -> deleteDatabase($dbID);
			if ($result)
			{
				$this -> returnJson['statusCode'] = '000000';
			}
			else
			{
				$this -> returnJson['statusCode'] = '220005';
			}
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 获取数据库列表
	 */
	public function getDatabase()
	{
		$service = new DatabaseModule;
		$result = $service -> getDatabase();

		if ($result)
		{
			$this -> returnJson['statusCode'] = '000000';
			$this -> returnJson['databaseList'] = $result;
		}
		else
		{
			$this -> returnJson['statusCode'] = '220006';
		}
		exitOutput($this -> returnJson);
	}

	/**
	 * 修改数据库
	 */
	public function editDatabase()
	{
		$dbID = securelyInput('dbID');
		$nameLen = mb_strlen(quickInput('dbName'), 'utf8');
		$dbName = securelyInput('dbName');
		$dbVersion = securelyInput('dbVersion');

		//数据库ID格式非法
		if (!preg_match('/^[0-9]{1,11}$/', $dbID))
		{
			$this -> returnJson['statusCode'] = '220004';
		}
		elseif (!($nameLen >= 1 && $nameLen <= 32))
		{
			// 数据库名长度不合法
			$this -> returnJson['statusCode'] = '220001';
		}
		elseif (!(is_float(floatval($dbVersion)) && intval($dbVersion)))
		{
			// 数据库版本不合法
			$this -> returnJson['statusCode'] = '220002';
		}
		else
		{
			$service = new DatabaseModule;
			$result = $service -> editDatabase($dbID, $dbName, $dbVersion);
			if ($result)
			{
				$this -> returnJson['statusCode'] = '000000';
			}
			else
			{
				$this -> returnJson['statusCode'] = '220007';
			}
		}
		exitOutput($this -> returnJson);
	}

}
?>