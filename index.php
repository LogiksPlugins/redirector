<?php
if(!defined('ROOT')) exit('No direct script access allowed');
session_check(true);

if(!isset($_REQUEST["forsite"])) {
	dispErrMessage("HostSite Is Required But Not Found","CMS Error",400);
	exit();
}
loadModule("page");

$btns=array();
$btns[sizeOf($btns)]=array("title"=>"Reload","icon"=>"reloadicon","tips"=>"Load Meta List","onclick"=>"reloadMetaList()");
$btns[sizeOf($btns)]=array("title"=>"Create","icon"=>"addicon","tips"=>"Create New Meta","onclick"=>"createMeta()");
$btns[sizeOf($btns)]=array("title"=>"Clone","icon"=>"addicon","tips"=>"Clone Meta","onclick"=>"cloneMeta()");
$btns[sizeOf($btns)]=array("title"=>"Delete","icon"=>"deleteicon","tips"=>"Delete Selected Meta","onclick"=>"deleteMeta()");

$layout="apppage";
$params=array("toolbar"=>$btns,"contentarea"=>"printContent");

printPageContent($layout,$params);

function printContent() {
?>
<style>
#toolbar .right {
	width:180px !important;
}
#toolbar .right #loadingmsg {
	float:right;
	padding-right:20px;
}
#toolbar button {
	width:120px;
}
#toolbar select {
	margin-top:-13px;
	height:25px;
}
.tabPage {
	width:100%;height:500px;
	overflow:auto;
	padding:0px !important;margin:0px !important;
}
#pageArea .src,#pageArea .layout {
	display:none;
}
input[type=text],textarea {
	border:1px solid #aaa;
	width:100%;
}
select {
	width:100%;
	height:22px;
}
.metaTitle {
	text-align: left;
	background-color: #667F55;color: white;
	font-weight: bold;
	padding-left: 10px;
}
.editMeta {
	display:none;
}
table.datatable tbody tr.active td {
	background-color: #C9FFDE !important;
	padding-left: 20px;
	font-weight: bold;
}
table.datatable tbody tr td {
	padding-left:10px;
}
</style>
<div style='width:100%;height:100%;'>
	<div style='width:20%;height:100%;float:left;overflow:auto;'>
		<table class='datatable' width=100% border=0 cellpadding=3 cellspacing=0 style='margin:0px;'>
			<thead>
				<tr class='subheader clr_darkblue'><td colspan=10 style='padding-left:10px;'>All Metas</td></tr>
			</thead>
			<tbody id='allpages'>
			</tbody>
		</table>
	</div>
	<div id='metaEditor' class='metaEditor' style='width:80%;height:100%;float:right;overflow:hidden;'>
		<table class='editMeta nostyle' width=70% border=0 cellspacing=0 cellpadding=0 style='border:0px;margin-left:30px;'>
			<thead>
				<tr><th align=left width=150px>Editing Meta For </th><td class='metaTitle'></td></tr>
			</thead>
			<tbody>
				<tr><th align=left width=150px>Title</th><td><input name=title type=text /></td></tr>
				<tr><th align=left width=150px>Description</th><td><input name=description type=text /></td></tr>
				<tr><th align=left width=150px>Robots</th><td><input name=robots type=text /></td></tr>
				<tr><th align=left valign=top width=150px>Keywords</th><td><textarea name=keywords style='height:100px;resize:none;'></textarea></td></tr>
				<tr><th align=left valign=top width=150px>Xtra Metatags</th><td><textarea name=metatags style='height:70px;resize:none;'></textarea></td></tr>
				<tr><td colspan=10><hr/></td></tr>
				<tr><td colspan=10 align=center>
					<button onclick='clearMeta()'>Reset</button>
					<button onclick='saveMeta()'>Submit</button>
				</td></tr>
			</tbody>
		</table>
		<p class='editMeta' style='margin-left:50px;'>If left blank, corrosponding default(Global) values from AppSite's Configurations will be loaded.</p>
	</div>
</div>
<div style='display:none'>
	<select id=typeSelector class='ui-widget-header ui-corner-all' onchange="reloadTemplateList()">
		<option value=''>All</option>
	</select>
</div>
<script language=javascript>
$(function() {
	$(".tabPage").css("height",($("#pgworkspace").height()-35)+"px");
	$("#allpages").delegate("tr","click",function() {
			$("tr.active","#allpages").removeClass("active");
			$(this).addClass("active");
			
			loadMetaData($(this).attr("rel"));
		});
	$("#allpages").delegate("input[type=checkbox]","click",function(e) {
			//e.preventDefault();e.preventPropagation();

		});
	reloadMetaList();
});
function reloadMetaList() {
	$("#loadingmsg").show();
	lx=getServiceCMD("metaEditor")+"&action=list";//+"&type="+$("#typeSelector").val();
	$("#allpages").html("<tr><td colspan=20 class='ajaxloading6'><br/><br/><br/>Loading ...</td></tr>");
	$("#allpages").load(lx,function(txt) {
			$("#loadingmsg").hide();
			if($("#metaEditor .error").length<=0)
				$("#metaEditor").prepend("<h3 class='error'>Please click a meta to view and edit its properties.</h3>");
			$("#metaEditor .editMeta").hide();
		});
}
function loadMetaData(ref) {
	$("input[name],select[name],textarea[name]","#metaEditor").val("");
	$("#loadingmsg").show();
	lx=getServiceCMD("blocks.meta")+"&action=fetchmeta&forpage="+ref;
	processAJAXQuery(lx,function(txt) {
		json=$.parseJSON(txt);
		if(json!=null) {
			$.each(json,function(k,v) {
					$("input[name="+k+"],select[name="+k+"],textarea[name="+k+"]","#metaEditor").val(v);
				});
			$("#metaEditor").attr("rel",ref);
			$("#metaEditor .metaTitle").html(ref);
			$("#metaEditor .editMeta").show();
			$("#metaEditor .error").detach();
		}
		$("#loadingmsg").hide();
	});
}
function saveMeta() {
	if($("#metaEditor").attr("rel")==null || $("#metaEditor").attr("rel").length<=0) {
		return false;
	}
	page=$("#metaEditor").attr("rel");
	$("#loadingmsg").show();
	l1=getServiceCMD("blocks.meta")+"&action=savemeta&forpage="+page;
	q=[];
	$("input[name],select[name],textarea[name]","#metaEditor").each(function() {
			nm=$(this).attr("name");
			v=$(this).val();
			q.push(nm+"="+encodeURIComponent(v));
		});
	q=q.join("&");
	processAJAXPostQuery(l1,q,function(txt) {
			if(txt.trim().length>0) {
				lgksAlert(txt);
			}
			$("#loadingmsg").hide();
		});
}
function clearMeta() {
	$("input[name],select[name],textarea[name]","#metaEditor").val("");
}
function deleteMeta() {
	rel=$("#allpages tr.active").attr('rel');
	lgksConfirm("Are you sure about deleting meta for - <b>"+rel+"</b>","Delete Meta",function() {
		l1=getServiceCMD("metaEditor")+"&action=delete&ref="+rel;
		$("#loadingmsg").show();
		processAJAXQuery(l1,function(txt) {
			if(txt.length>0) lgksAlert(txt);
			else {
				$("#allpages tr[rel='"+rel+"']").detach();
				$("#metaEditor").prepend("<h3 class='error'>Please click a meta to view and edit its properties.</h3>");
				$("#metaEditor .editMeta").hide();
				$("#loadingmsg").hide();
			}
		});
	});
}
function createMeta() {
	lgksPrompt("Give a name or path for the new meta","New Meta").dialog({
		buttons:{
			"Create":function() {
				txt=$(this).find("input").val();
				if(txt!=null && txt.length>0) {
					l1=getServiceCMD("metaEditor")+"&action=create&ref="+txt;
					$("#loadingmsg").show();
					processAJAXQuery(l1,function(txt) {
						if(txt.length>0) lgksAlert(txt);
						else {
							reloadMetaList();
						}
						$("#loadingmsg").hide();
					});
				}
				$(this).dialog("close");
			},
			"Cancel":function() {
				$(this).dialog("close");
			}
		}
	});
}
function cloneMeta() {
	rel=$("#allpages tr.active").attr('rel');
	lgksPrompt("Give a name or path for the new meta <br/>Cloned from - <b>"+rel+"</b>","Clone Meta").dialog({
		buttons:{
			"Create":function() {
				txt=$(this).find("input").val();
				if(txt!=null && txt.length>0) {
					l1=getServiceCMD("metaEditor")+"&action=clone&ref="+txt+"&src="+rel;
					$("#loadingmsg").show();
					processAJAXQuery(l1,function(txt) {
						if(txt.length>0) lgksAlert(txt);
						else {
							reloadMetaList();
						}
						$("#loadingmsg").hide();
					});
				}
				$(this).dialog("close");
			},
			"Cancel":function() {
				$(this).dialog("close");
			}
		}
	});
}
</script>
<?php
}
?>