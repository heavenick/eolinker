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
class UpdateDao
{
	public function __destruct()
	{
		session_start();
		session_destroy();
	}

	/**
	 * 获取所有项目表
	 */
	public function getAllTable()
	{
		$db = getDatabase();
		$result = $db -> queryAll('SHOW TABLES');
		return $result;
	}

	/**
	 * 获取相应表的字段名
	 * @param $tableName 数据表名
	 */
	public function getTableColumns(&$tableName)
	{
		$db = getDatabase();
		$result = $db -> queryAll('SHOW COLUMNS FROM ' . $tableName);
		return $result;
	}

	/**
	 * 修改数据库表名称
	 * @param $oldTableName 旧数据表名
	 * @param $newTableName 新数据表名
	 */
	public function changeTableName(&$oldTableName, $newTableName)
	{
		$db = getDatabase();
		$db -> execute('RENAME TABLE ' . $oldTableName . ' TO ' . $newTableName);
		return TRUE;
	}

	/**
	 * 删除数据库表
	 * @param $oldTables 旧数据表
	 */
	public function dropTable(&$oldTables)
	{
		$db = getDatabase();
		$db -> beginTransaction();
		foreach ($oldTables as &$oldTable)
		{
			$db -> execute("DROP TABLE IF EXISTS {$oldTable['tableName']}");
		}
		$db -> commit();
		return TRUE;
	}

	/**
	 * 转移数据
	 * @param $oldTables 旧数据表
	 * @param $newTables 新数据表
	 */
	public function dumpData(&$oldTables, &$newTables)
	{
		$db = getDatabase();
		$db -> beginTransaction();
		$oldTablesCount = count($oldTables) - 1;
		$newTablesCount = count($newTables) - 1;
		for ($i = 0, $j = 0; $i <= $oldTablesCount; $i++, $j = 0)
		{
			//检查是否有相同的表
			for (; $j <= $newTablesCount; $j++)
			{
				//如果有相同的表
				if ($oldTables[$i]['tableName'] == 'old_' . $newTables[$j]['tableName'])
				{
					$columnSQL = '';
					foreach ($oldTables[$i]['columns'] as $column)
					{
						$columnSQL .= "`{$column}`,";
					}

					//过滤空参数
					if (empty($columnSQL))
						continue;
					$columnSQL = substr($columnSQL, 0, -1);
					$db -> execute("INSERT INTO {$newTables[$j]['tableName']} ($columnSQL) SELECT $columnSQL FROM {$oldTables[$i]['tableName']}");

					if ($db -> getAffectRow() < 1)
					{
						$db -> rollback();
						return FALSE;
					}
				}
				else
					continue;
			}
		}

		$db -> commit();
		return TRUE;
	}

}
?>