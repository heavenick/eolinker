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
class ConfigModule
{
	/**
	 * 修改配置
	 * @param $databaseURL 数据库主机
	 * @param $databaseUser 数据库用户名
	 * @param $databasePassword 数据库密码
	 * @param $databaseName 数据库名
	 */
	public function editConfig(&$databaseURL, &$databaseUser, &$databasePassword, &$databaseName)
	{
		$configArray = array(
			'FIRST_DEPLOYMENT' => FALSE,
			'AT' => 'POST',
			'DEBUG' => TRUE,
			'DB_TYPE' => 'mysql',
			'DB_PERSISTENT_CONNECTION' => FALSE,
			'PATH_FW' => './RTP',
			'PATH_APP' => './Server',
			'DB_URL' => $databaseURL,
			'DB_USER' => $databaseUser,
			'DB_PASSWORD' => $databasePassword,
			'DB_NAME' => $databaseName
		);
	}

}
?>