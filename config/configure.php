<?php
/*
 * Configuration Script Pogram.
 * See usage -> php configure.php
 *
 */

if( $argc != 1 ){
	print("===checking config files...\n");
	$mvcConf = parse_ini_file('mvc.conf', true);
	$rootDir = $mvcConf['DIR']['root'];
	$routes  = parse_ini_file('routes.conf', true);
	$appConf = parse_ini_file('application.conf', true);
	print("--done\n");
}

switch($argc){
	case 1:
		help();
		break;
	default:
		dispatch($argv);
		break;
}

function dispatch($params){
	switch($params[1]){
		case '--init':
			init();
			break;
		case '-add':
			add($params);
			break;
		case '-del':
			del($params);
			break;
		default:
			help();
			break;
	}
}

function init(){
	print("===initialize mvc framework...\n");
	global $mvcConf;
	global $rootDir;
	$context = '
# アクセス制限
order deny,allow
#deny from all
#allow from localhost
#allow from all

# アクセスコントロール
RewriteEngine on
RewriteRule ^images/(.*)$ public/images/$1 [L]
RewriteRule ^javascripts/(.*)$ public/javascripts/$1 [L]
RewriteRule ^stylesheets/(.*)$ public/stylesheets/$1 [L]
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule (.*)$ '.$mvcConf['DIR']['url'].'/public/index.php?params=$1 [P,L]
		';
	file_put_contents('../.htaccess', $context);
	print("--htaccess wrote\n");
	print("--done\n");
}

function add($params){
	global $rootDir;
	print("===Create new controllers...\n");
	if( count($params) < 3 ){
		print("### ERROR : too few arguments.\n Please read help.\n");
		help();
		return;
	}
	if( !checkControllerName($params[2]) ){
		print("### ERROR : we cannot create controller '".$params[2]."'\n");
		return;
	}
	$lowName = strtolower($params[2]);
	$ClassName = ucfirst($lowName);
	print("===Generating Class : ".$ClassName."\n");
	$ModelFile = $rootDir."/app/models/".$ClassName."Model.php";
	$ControllerFile = $rootDir."/app/controllers/".$ClassName."Controller.php";
	$viewDir    = $rootDir."/app/views/".$lowName;
	if( file_exists($ModelFile) || file_exists($ControllerFile) || file_exists($viewDir) ){
		print("### ERROR : such file is already exists : ".$ModeFile.",".$ControllerFile.",".$viewDir."\n");
		return;
	}
	$ModelContext = "<?php

class ".$ClassName."Model extends ModelBase{

	// TABLE NAME
	protected \$name = null;

}
	";

	$ControllerContext = "<?php

class ".$ClassName."Controller extends ControllerBase{

	// 共通の前処理を記述
	protected function preAction(){

	}

}
	";
	file_put_contents($ModelFile, $ModelContext);
	print("   create Model      : ".$ModelFile."\n");
	file_put_contents($ControllerFile, $ControllerContext);
	print("   create Controller : ".$ControllerFile."\n");
	system("mkdir ".$viewDir);
	print("   create ViewDir    : ".$viewDir."\n");
	print("--done\n");
}

function del($params){
	global $rootDir;
	print("===Deleting controllers...\n");
	if( count($params) < 3 ){
		print("### ERROR : too few arguments.\n Please read help.\n");
		help();
		return;
	}
	if( !checkControllerName($params[2]) ){
		print("### ERROR : invalid controller name '".$params[2]."'\n");
		return;
	}
	$lowName = strtolower($params[2]);
	$ClassName = ucfirst($lowName);
	print("===Deleting Class : ".$ClassName."\n");
	$ModelFile = $rootDir."/app/models/".$ClassName."Model.php";
	$ControllerFile = $rootDir."/app/controllers/".$ClassName."Controller.php";
	$viewDir    = $rootDir."/app/views/".$lowName;
	if( !file_exists($ModelFile) || !file_exists($ControllerFile) || !file_exists($viewDir) ){
		print("### ERROR : no such file or directory : ".$ModeFile.",".$ControllerFile.",".$viewDir."\n");
		return;
	}
	print("   delete Model      : ".$ModelFile."\n");
	system("rm ".$ModelFile);
	print("   delete Controller : ".$ControllerFile."\n");
	system("rm ".$ControllerFile);
	print("   delete ViewDir    : ".$viewDir."\n");
	system("rm -r ".$viewDir);
	print("--done\n");	
}

function help(){
	print('Usage: php configure.php [options] [args...]
   php configure.php --init 
   php configure.php -add <controller>
   php configure.php -del <controller>

  --init           Check configuration files and initialize routes
  -add             Add controller and generate files
  -del             Delete controller and files

');
}

function checkControllerName($name){
	if( !preg_match('/^[a-zA-Z]$/', substr($name, 0, 1) )){
		return false;
	}
	return !!!preg_match('/[^a-zA-Z0-9_-]/', $name);
}
