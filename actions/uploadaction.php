<?php
if (isset($_POST['uploadsubmit'])){
session_start();
require 'dbfile.php';
$userid = $_SESSION['sesuserId'];
$timest = $lat = $long = $acc = $actimest = $type = $conf = null;
$strlong1 = array();
$strlong2 = array();

require_once (__DIR__.'../../json-machine/vendor/autoload.php');

$jsondata = \JsonMachine\JsonMachine::fromFile($_FILES['jsonfile']['tmp_name']);

foreach($jsondata as $property => $valueA){
	foreach ($valueA as $keyA => $val) {
		foreach($val as $key => $value){
			//elements of each object in locations
      switch ($key) {
        case "timestampMs":
          $timest = $value;
          break;
        case "latitudeE7":
          $lat = $value;
          break;
        case "longitudeE7":
          $long = $value;
          break;
        case "accuracy":
          $acc = $value;
          break;
        case "activity":
            for($i = 0; $i<count($value); $i++){
            $actimest = $value[$i]["timestampMs"];
            $type = $value[$i]["activity"][0]["type"];
            $conf = $value[$i]["activity"][0]["confidence"];
            }
        break;
      }
		}
		$str1 = "('".$userid."', '".$timest."', ".$lat.", ".$long.", ".$acc.")";
		array_push($strlong1, $str1);
		if (!$actimest == null){
		$str2 = "('".$userid."', '".$actimest."', '".$type."', ".$conf.")";
		array_push($strlong2, $str2);
		}
  $timest = $lat = $long = $acc = $actimest = $type = $conf = null;
 }
}
$insertvalues = implode(", ", $strlong1);
$sql = "INSERT INTO userdata (userid, timestampms, latitude, longtitude, accuracy) VALUES $insertvalues";
if (!mysqli_query($conn, $sql)){
	echo "Error: " . $sql . "<br>" . mysqli_error($conn), "<br>";
}
	$insertvalues = implode(", ", $strlong2);
	$sql = "INSERT INTO activity (userid, timestampms, type, confidence) VALUES $insertvalues";
	if (!mysqli_query($conn, $sql)){
		echo "Error: " . $sql . "<br>" . mysqli_error($conn), "<br>";
}
header("Location: ../user.php?upload=succes");
}else{
	header("Location: ../user.php");
}
