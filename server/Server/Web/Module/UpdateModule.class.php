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
class UpdateModule {
	/**
	 * 自动更新项目
	 * @param $updateURI 更新地址
	 */
	public function autoUpdate($updateURI) {
		$ch = curl_init($updateURI);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		file_put_contents('../release.zip', curl_exec($ch));
		curl_close($ch);
		$zip = new ZipArchive;
		if ($zip -> open('../release.zip'))
			$zip -> extractTo('../');
		$zip -> close();

		//接下来开始获取旧数据库的全部结构
		$updateDao = new UpdateDao;
		$oldTablesCache = $updateDao -> getAllTable();
		$oldTables = array();
		
		$i = 0;
		defined('DB_TABLE_PREFIXION') or define('DB_TABLE_PREFIXION', 'eo');
		foreach ($oldTablesCache as $oldTable) {
			if (!(strpos($oldTable['Tables_in_' . DB_NAME], DB_TABLE_PREFIXION) === 0))
				continue;
			//获取表之后，遍历新建数组以存放表字段
			$oldTables[$i]['tableName'] = $oldTable['Tables_in_' . DB_NAME];

			//遍历获取所有表的字段名
			$columnFields = $updateDao -> getTableColumns($oldTables[$i]['tableName']);
			foreach ($columnFields as $field) {
				$oldTables[$i]['columns'][] = $field['Field'];
			}
			++$i;
		}
		unset($i);
		//开始重命名所有旧数据表
		foreach ($oldTables as &$oldTable) {
			$updateDao -> changeTableName($oldTable['tableName'], 'old_' . $oldTable['tableName']);
			$oldTable['tableName'] = 'old_' . $oldTable['tableName'];
		}

		//开始创建新的数据表
		//读取数据库文件
		$sql = file_get_contents(PATH_FW . DIRECTORY_SEPARATOR . 'db/eoapi_os_mysql.sql');
		$sqlArray = array_filter(explode(';', $sql));
		$installDao = new InstallDao;
		$installDao -> installDatabase($sqlArray);

		//获取新的数据表
		$newTablesCache = $updateDao -> getAllTable();
		$newTables = array();
		$i = 0;
		foreach ($newTablesCache as $newTable) {
			//获取表之后，遍历新建数组以存放表字段
			//先判断是否含有old关键字，有则跳过
			if (!strstr($newTable['Tables_in_' . DB_NAME], 'old_'))
				$newTables[$i]['tableName'] = $newTable['Tables_in_' . DB_NAME];
			else
				continue;

			//遍历获取所有表的字段名
			$columnFields = $updateDao -> getTableColumns($newTables[$i]['tableName']);
			foreach ($columnFields as $field) {
				$newTables[$i]['columns'][] = $field['Field'];
			}
			++$i;
		}
		unset($i);

		//开始转移数据
		$updateDao -> dumpData($oldTables, $newTables);

		//删除旧表格
		$updateDao -> dropTable($oldTables);

		//执行额外的更新操作，主要用于在版本过渡的过程中，数据以及文件发生变化等情况
		if (file_exists(PATH_FW . DIRECTORY_SEPARATOR . 'Common/UpdateFunction.php'))
			quickRequire(PATH_FW . DIRECTORY_SEPARATOR . 'Common/UpdateFunction.php');

		return TRUE;
	}

	/**
	 * 手动更新项目
	 */
	public function manualUpdate() {
		//接下来开始获取旧数据库的全部结构
		$updateDao = new UpdateDao;
		$oldTablesCache = $updateDao -> getAllTable();
		$oldTables = array();

		$i = 0;
		defined('DB_TABLE_PREFIXION') or define('DB_TABLE_PREFIXION', 'eo');
		foreach ($oldTablesCache as $oldTable) {
			if (!(strpos($oldTable['Tables_in_' . DB_NAME], DB_TABLE_PREFIXION) === 0))
				continue;
			//获取表之后，遍历新建数组以存放表字段
			$oldTables[$i]['tableName'] = $oldTable['Tables_in_' . DB_NAME];

			//遍历获取所有表的字段名
			$columnFields = $updateDao -> getTableColumns($oldTables[$i]['tableName']);
			foreach ($columnFields as $field) {
				$oldTables[$i]['columns'][] = $field['Field'];
			}
			++$i;
		}
		unset($i);

		//开始重命名所有旧数据表
		foreach ($oldTables as &$oldTable) {
			$updateDao -> changeTableName($oldTable['tableName'], 'old_' . $oldTable['tableName']);
			$oldTable['tableName'] = 'old_' . $oldTable['tableName'];
		}

		//开始创建新的数据表
		//读取数据库文件
		$sql = file_get_contents(PATH_FW . DIRECTORY_SEPARATOR . 'db/eoapi_os_mysql.sql');
		$sqlArray = array_filter(explode(';', $sql));
		$installDao = new InstallDao;
		$installDao -> installDatabase($sqlArray);

		//获取新的数据表
		$newTablesCache = $updateDao -> getAllTable();
		$newTables = array();
		$i = 0;
		foreach ($newTablesCache as $newTable) {
			//获取表之后，遍历新建数组以存放表字段
			//先判断是否含有old关键字，有则跳过
			if (!strstr($newTable['Tables_in_' . DB_NAME], 'old_'))
				$newTables[$i]['tableName'] = $newTable['Tables_in_' . DB_NAME];
			else
				continue;

			//遍历获取所有表的字段名
			$columnFields = $updateDao -> getTableColumns($newTables[$i]['tableName']);
			foreach ($columnFields as $field) {
				$newTables[$i]['columns'][] = $field['Field'];
			}
			++$i;
		}
		unset($i);

		//开始转移数据
		$updateDao -> dumpData($oldTables, $newTables);

		//删除旧表格
		$updateDao -> dropTable($oldTables);

		//执行额外的更新操作，主要用于在版本过渡的过程中，数据以及文件发生变化等情况
		if (file_exists(PATH_FW . DIRECTORY_SEPARATOR . 'Common/UpdateFunction.php'))
			quickRequire(PATH_FW . DIRECTORY_SEPARATOR . 'Common/UpdateFunction.php');

		return TRUE;
	}

}
?>