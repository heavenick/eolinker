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

namespace RTP\Module;

Class OutputStorageModule
{
	/**
	 * 注册表变量
	 */
	private static $registry = NULL;
	private static $instance;

	/**
	 * 获取实例
	 */
	public static function getInstance()
	{
		//如果已经含有一个实例则直接返回实例
		if (!is_null(self::$instance))
		{
			return self::$instance;
		}
		else
		{
			//如果没有实例则新建
			return self::getNewInstance();
		}
	}

	/**
	 * 获取一个新的实例
	 */
	public static function getNewInstance()
	{
		self::$instance = null;
		self::$instance = new self;
		return self::$instance;
	}

	/**
	 * 构造函数
	 */
	protected function __construct()
	{
		if (is_null(self::$registry))
			self::$registry = array();
	}

	/**
	 * 获取储存的数据
	 */
	public static function get($offset)
	{
		if (isset(self::$registry[$offset]))
			return self::$registry[$offset];
		return NULL;
	}

	/**
	 * 设置缓存数据值
	 */
	public static function set($value)
	{
		if (is_null(self::$registry))
			self::$registry = array();

		self::$registry[] = $value;
	}

	/**
	 * 获取所有缓存值
	 */
	public static function getAll()
	{
		return self::$registry;
	}

	/**
	 * 检查缓存值是否已经存在
	 */
	public static function isExist($value)
	{
		if (is_null(self::$registry))
		{
			return FALSE;
		}
		if (in_array($value, self::$registry))
		{
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * 清除缓存值
	 */
	public static function clean()
	{
		self::$registry = NULL;
	}

}
?>