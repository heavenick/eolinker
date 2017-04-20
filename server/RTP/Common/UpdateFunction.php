<?php
//本文件用于版本更新之后更新相关数据用途

//针对1.4之前的版本，在项目配置文件中增加更多的选项
$db_url = DB_URL;
$db_port = DB_PORT;
$db_user = DB_USER;
$db_password = DB_PASSWORD;
$db_name = DB_NAME;
$websiteName = defined('WEBSITE_NAME') ? WEBSITE_NAME : 'eolinker开源版';
$prefixion = defined('DB_TABLE_PREFIXION') ? DB_TABLE_PREFIXION : 'eo';

$config = "<?php
//主机地址
defined('DB_URL') or define('DB_URL', '{$db_url}');

//主机端口,默认mysql为3306
defined('DB_PORT') or define('DB_PORT', '{$db_port}');

//连接数据库的用户名
defined('DB_USER') or define('DB_USER', '{$db_user}');

//连接数据库的密码，推荐使用随机生成的字符串
defined('DB_PASSWORD') or define('DB_PASSWORD', '{$db_password}');

//数据库名
defined('DB_NAME') or define('DB_NAME', '{$db_name}');

//是否允许新用户注册
defined('ALLOW_REGISTER') or define('ALLOW_REGISTER', TRUE);

//是否允许更新项目，如果设置为FALSE，那么自动更新和手动更新都将失效
defined('ALLOW_UPDATE') or define('ALLOW_UPDATE', TRUE);

//网站名称
defined('WEBSITE_NAME') or define('WEBSITE_NAME', '{$websiteName}');

//数据表前缀
defined('DB_TABLE_PREFIXION') or define('DB_TABLE_PRIFIXION', '{$prefixion}');
?>";

$configFile = file_put_contents(PATH_FW . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'eo_config.php', $config);
?>