<?php
$host = 'localhost:3306';
$database = 'ebike';
$username = 'root';
$password = '123456';
date_default_timezone_set("PRC");
function getPdo(){
	global $host,$database,$username,$password;
	$pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);//创建一个pdo对象
	$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//PDO::exec("SET NAMES 'utf8';"); 
	//$pdo->exec('set names gbk;');
	$pdo->exec('set names utf8mb4;');
	return $pdo;
}
$data="";
function test(){
	global $data;
  $pdo = getPdo();
  $stmt = $pdo->prepare("select * from ebikes"); 
  $stmt->execute(array());
  $count = $stmt->rowCount();
  //echo($count);
  $resjs=array();
  
  for($x=0;$x<$count;$x++){
  	$tdata = $stmt->fetch(PDO::FETCH_ASSOC);
		$resjs[] = $tdata;
		
		$data = $data."<tr><td>".$tdata["id"]."</td><td>".$tdata["hphm"]."</td><td>".$tdata["syrsfz"]."</td><td>".$tdata["syrsjhm"]."</td><td>".$tdata["syrzz"]."</td><td>".$tdata["clsbdh"]."</td><td>".$tdata["clgmsj"]."</td><td>".$tdata["clgmdd"]."</td></tr>";
		//var_dump($tdata);
  	//echo($tdata["id"].'-'.$tdata["name"].'<br />');
  }
  //print_r("<pre>");
  //print_r($data);
  //echo(json_encode($resjs));
  $pdo = null;
  
}
test();
?>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>test</title>
		<style>
			table{
				border:1px solid black;
				width:90%;
				margin:0 auto;
			}
			td{
				border:1px solid black;
				text-align:center;
			}
		</style>
	</head>
	<body>
		<table>
			<tr><td colspan="4"><h1>电动车查询</h1></td></tr>
			<tr><td>车牌号码：</td><td><input type="text" name="hphm" /></td><td>所有人姓名：</td><td><input type="text" name="syrxm" /></td></tr>
			<tr><td>登录手机号：</td><td><input type="text" name="sjh" /></td><td>登记时间：</td><td><input type="text" name="djsj" /></td></tr>
			<tr><td colspan="4"><input type="submit" value="查询" /></td></tr>
		</table>
		<table>
			<tr><td>id</td><td>hphm</td><td>syrsfz</td><td>syrsjhm</td><td>syrzz</td><td>clsbdh</td><td>clgmsj</td><td>clgmdd</td></tr>
			<?=$data?>
			
		</table>
	</body>
</html>