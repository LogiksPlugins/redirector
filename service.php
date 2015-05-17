<?php
if(!defined('ROOT')) exit('No direct script access allowed');
checkServiceSession();
checkUserSiteAccess($_REQUEST['forsite'],true);
user_admin_check(true);

if(isset($_REQUEST["action"])) {
	loadModule("dbcon");
	loadFolderConfig();
	
	$lf=$_SESSION["APP_FOLDER"]["APPROOT"].$_SESSION["APP_FOLDER"]["APPS_CONFIG_FOLDER"]."meta/";
	
	if($_REQUEST["action"]=="list") {
		$lfs=scandir($lf);
		unset($lfs[0]);unset($lfs[1]);
		$fx=array();
		foreach($lfs as $a) {
			if(!is_dir($lf.$a)) {
				$ext=end(explode(".",$a));
				$ext=strtolower($ext);
				if($ext=="json") {
					$t=substr($a,0,strlen($a)-5);
					$a=substr($a,0,strlen($a)-5);
					$tx=explode("_",$t);
					$tx=current($tx);
					if(in_array($tx, $fx)) {
						$t=str_replace("_","/",$t);
						$fx[$a]=$t;
					} else {
						$fx[$a]=$t;
					}
				}
			}
		}
		foreach($fx as $a=>$b) {
			echo "<tr rel='{$a}'><td>{$b}</td></tr>";
			//<td align=center width=35px><input type=checkbox name='select' /></td>
		}
	} elseif($_REQUEST["action"]=="delete" && isset($_REQUEST['ref']) && strlen($_REQUEST['ref'])>0) {
		$fx=$lf.$_REQUEST['ref'].".json";
		if(is_file($fx)) {
			unlink($fx);
			if(file_exists($fx)) {
				echo "Sorry, Meta Deletion Failed";
			}
		}
	} elseif($_REQUEST["action"]=="create" && isset($_REQUEST['ref']) && strlen($_REQUEST['ref'])>0) {
		$_REQUEST['ref']=str_replace("/", "_",$_REQUEST['ref']);
		$fx=$lf.$_REQUEST['ref'].".json";
		$data=array("title"=>getConfig("APPS_NAME"),
			"description"=>"",
			"robots"=>"INDEX, FOLLOW",
			"keywords"=>"",
			"metatags"=>"",);
		file_put_contents($fx, json_encode($data));
		if(!file_exists($fx)) {
			echo "Sorry, Meta Creation Failed";
		}
	} elseif($_REQUEST["action"]=="clone" && isset($_REQUEST['ref']) && strlen($_REQUEST['ref'])>0
			 && isset($_REQUEST['src']) && strlen($_REQUEST['src'])>0) {
		$_REQUEST['ref']=str_replace("/", "_",$_REQUEST['ref']);
		$fx=$lf.$_REQUEST['src'].".json";
		if(is_file($fx)) {
			$data=file_get_contents($fx);
			$fx=$lf.$_REQUEST['ref'].".json";
			file_put_contents($fx, $data);
			if(!file_exists($fx)) {
				echo "Sorry, Meta Clone Failed";
			}
		} else {
			echo "Sorry, Clone Source Not Found";
		}
	}
}
?>
