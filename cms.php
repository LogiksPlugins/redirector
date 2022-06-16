<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!_db() || !in_array("do_redirector", _db()->get_tableList())) {
	echo "<h2 align=center><br><br>Plugin is not properly installed, try installing the plugin again.</h2>";
	return;
}

$slug = _slug("module/subtype/type/refid");
$type=$slug['subtype'];

if($type==null || strlen($type)<=0){
    $type="panels";
	header("Location:"._link("modules/redirector/panels"));
	return;
}

$basePath = __DIR__."/{$type}/";

if(!is_dir($basePath)) {
	print_error("Component Not Supported Yet");
	return;
}

$report=$basePath."report.json";
$form=$basePath."form.json";
//echo $report;
loadModule("datagrid");
?>
<style>
.formbox .formbox-content {
	border:0px !important;
	-webkit-box-shadow: none !important;
	-moz-box-shadow: none !important;
	box-shadow: none !important;
}
</style>
<div class='col-xs-12 col-md-12 col-lg-12'>
	<div class='row'>
		<?php
			printDataGrid($report,$form,$form,["slug"=>"module/subtype/type/refid","glink"=>_link("modules/redirector/{$type}")],"app");
		?>
	</div>
</div>