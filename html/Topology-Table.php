<?php
# Program: Topology-Table.php
# Programmer: Remo Rickli

error_reporting(E_ALL ^ E_NOTICE);

ini_set('default_socket_timeout',3);    								# Tweak this, if you don't want to wait long for osm or weather info

$printable = 1;

include_once ("inc/header.php");
include_once ("inc/libdev.php");
include_once ("inc/libmon.php");

$_GET = sanitize($_GET);
$reg = isset($_GET['reg']) ? $_GET['reg'] : "";
$cty = isset($_GET['cty']) ? $_GET['cty'] : "";
$bld = isset($_GET['bld']) ? $_GET['bld'] : "";
$flr = isset($_GET['fl']) ? $_GET['fl'] : "";
$rom = isset($_GET['rm']) ? $_GET['rm'] : "";
$nsd = isset($_GET['nsd']) ? $_GET['nsd'] : "";
$img = isset($_GET['img']) ? $_GET['img'] : "";
$pop = isset($_GET['pop']) ? $_GET['pop'] : "";

$sub    = 0;
$alarr  = array();
$monarr = array();

if( isset($_GET['map']) ){
	$map = $_GET['map'];
	if($_SESSION['opt']) $_SESSION['tmap'] = $map;
}elseif( isset($_SESSION['tmap']) ){
	$map = $_SESSION['tmap'];
}else{
	$map = '';
}
?>
<h1>Topology Table</h1>

<?php
$link  = DbConnect($dbhost,$dbuser,$dbpass,$dbname);
TopoTable($reg,$cty,$bld,$flr,$rom,$nsd);

if(!$reg) $leok = 1;
if( count($dreg) == 1 ){
	$reg = array_pop ( array_keys($dreg) );
	if( count($dcity[$reg]) == 1 ){
		$cty = array_pop ( array_keys($dcity[$reg]) );
	}
}

if( !isset($_GET['print']) ) { ?>
<table class="content">
<tr class="bgmain">
<td class="ctr s">
	<a href="<?= $self ?>.php"><img src="img/32/<?= $selfi ?>.png" title="<?= $self ?>"></a>
</td>
<td>
</td>
<td class="ctr m">
<?php
echo "\t<a href=\"?map=$map\"><img src=\"img/16/bbup.png\" title=\"$toplbl\"></a>\n";
if($cty) echo "\t<a href=\"?reg=".urlencode($reg)."&map=$map\"><img src=\"img/16/glob.png\" title=\"$place[r] $reg\"></a>\n";
if($bld) echo "\t<a href=\"?reg=".urlencode($reg)."&cty=".urlencode($cty)."&map=$map\"><img src=\"img/16/fort.png\" title=\"$place[c] $cty\"></a>\n";
if($flr) echo "\t<a href=\"?reg=".urlencode($reg)."&cty=".urlencode($cty)."&bld=".urlencode($bld)."&map=$map\"><img src=\"img/16/home.png\" title=\"$place[b] $bld\"></a>\n";
echo "\n</td>\n<td class=\"ctr m\">\n";
if($pop){
	echo "\t<img src=\"img/16/bcls.png\" onclick=\"document.location.href='?".str_replace("&pop=1","",$_SERVER[QUERY_STRING])."';\"  title=\"$nonlbl $poplbl\">\n";
}else{
	echo "\t<img src=\"img/16/nods.png\" onclick=\"document.location.href='?$_SERVER[QUERY_STRING]&pop=1';\"  title=\"$poplbl\">\n";
}
if($bld){
	if($nsd){
		echo "\t<img src=\"img/16/bcls.png\" onclick=\"document.location.href='?".str_replace("&nsd=1","",$_SERVER[QUERY_STRING])."';\"  title=\"$nonlbl SNMP: hide\">\n";
	}else{
		echo "\t<img src=\"img/16/wlan.png\" onclick=\"document.location.href='?$_SERVER[QUERY_STRING]&nsd=1';\"  title=\"$nonlbl SNMP: $sholbl\">\n";
	}

	if(!$rom){
		if($img){
			echo "\t<img src=\"img/16/icon.png\" onclick=\"document.location.href='?".str_replace("&img=1","",$_SERVER[QUERY_STRING])."';\"  title=\"Device Icons\">\n";
		}else{
			echo "\t<img src=\"img/16/foto.png\" onclick=\"document.location.href='?$_SERVER[QUERY_STRING]&img=1';\"  title=\"Device $imglbl\">\n";
		}
	}
}else{
	$extmap = ($_SESSION['map'])?'Googlemap':'Openstreetmap';
	if($map == 1){
		echo "<img src=\"img/16/map.png\" onclick=\"document.location.href='?reg=".urlencode($reg)."&cty=".urlencode($cty)."&map=2';\"  title=\"$extmap\">\n";
	}elseif($map == 2){
		echo "<img src=\"img/16/wthr.png\" onclick=\"document.location.href='?reg=".urlencode($reg)."&cty=".urlencode($cty)."&map=3';\"  title=\"$extmap & $igrp[16]\">\n";
	}elseif($map == 3){
		echo "<img src=\"img/16/icon.png\" onclick=\"document.location.href='?reg=".urlencode($reg)."&cty=".urlencode($cty)."&map=0';\"  title=\"Icons\">\n";
	}else{
		echo "<img src=\"img/16/paint.png\" onclick=\"document.location.href='?$_SERVER[QUERY_STRING]&map=1';\"  title=\"NeDimaps\">\n";
	}
}
echo "</td>\n</tr>\n</table>\n<p>\n\n";
}

if(!$reg){
	TopoRegs();
}elseif (!$cty){
	TopoCities($reg);
}elseif (!$bld){
	TopoBuilds($reg,$cty);
}elseif (!$rom){
	TopoFloors($reg,$cty,$bld);
}else{
	TopoRoom($reg,$cty,$bld,$flr,$rom);
}
if($leok) TopoLocErr();

include_once ("inc/footer.php");

?>
