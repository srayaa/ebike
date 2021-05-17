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
$searchparm = array();
$page=1;
$pagesize=10;
$pgli="";
$ct = 0;
function test(){
	global $data,$searchparm,$page,$pagesize,$pgli,$ct;
  $pdo = getPdo();
  
  $searchparm = $_GET;
  $parmarr = array();
  $hphm = array_key_exists("hphm",$_GET)?$_GET["hphm"]:"";
  $page = intval(array_key_exists("page",$_GET)?$_GET["page"]:"1");
  if(!$page){
  	$page=1;
  }
  //echo($page);
  $sql = "select * from ebikes where 1=1 ";
  $sqlct = "select count(*) from ebikes where 1=1 ";
  if($hphm){
  	//echo($hphm);
  	$sql = $sql." and hphm = ?";
  	$sqlct = $sqlct." and hphm = ?";
  	$parmarr[] = $hphm;
  }
  //limit 前先取总数
  $stmt = $pdo->prepare($sqlct); 
  $stmt->execute($parmarr);
  $ctall = $stmt->fetchAll();
  $ct = $ctall[0][0];
  $pages = ceil($ct/10);
  //处理分页
  if($page==1){
  	$pgli = '<li class="disabled"><span>«</span></li>';
  }else{
  	$pgli = '<li><span><a href="?page='.intval($page-1).'">«</a></span></li>';
  }
  //显示前一页，1，当前页-1，当前页，当前页+1，最后页，后一页。
  if($page>3){
  	//5  1,2,3,4,5,6,7,8
  	//《 1 ... 4,5,6...999 》
  	$pgli = $pgli.'<li><span><a href="?page=1">1</a></span></li><li class="disabled"><span>...</span></li>';
  	$pgli = $pgli.'<li><span><a href="?page='.intval($page-1).'">'.intval($page-1).'</a></span></li>';
  	$pgli = $pgli.'<li class="active"><span><a style="color:white!important" href="?page='.$page.'">'.$page.'</a></span></li>';
  	if($page+1<$pages){
  		$pgli = $pgli.'<li><span><a href="?page='.intval($page+1).'">'.intval($page+1).'</a></span></li>';
  	}
  	$pgli = $pgli.'<li class="disabled"><span>...</span></li>';
  	$pgli = $pgli.'<li><span><a href="?page='.$pages.'">'.$pages.'</a></span></li>';
  	
  }else{
  	//显示1-8
  	//《 1,2,3 ... 999 》
  	
  	$pgli = $pgli.'<li class="'.($page==1?"active":"").'"><a href="?page=1"><span>1</span></a></li>';
  	$pgli = $pgli.($pages>1?'<li class="'.($page==2?"active":"").'"><a href="?page=2"><span>2</span></a></li>':'');
  	$pgli = $pgli.($pages>2?'<li class="'.($page==3?"active":"").'"><a href="?page=3"><span>3</span></a></li>':'');
  	
  		$pgli = $pgli.($pages>3?'<li><a href="?page=4"><span>4</span></a></li>':'');
  	
  	$pgli = $pgli.'<li class="disabled"><span>...</span></li>';
  	$pgli = $pgli.'<li><a href="?page='.$pages.'"><span>'.$pages.'</span></a></li>';
  }
  
  /*$pgli = '<li class="disabled"><span>«</span></li>
                  <li class="active"><span>1</span></li>
                  <li><a href="#1">2</a></li>
                  <li><a href="#1">3</a></li>
                  <li><a href="#1">4</a></li>
                  <li><a href="#1">5</a></li>
                  <li><a href="#1">6</a></li>
                  <li><a href="#1">7</a></li>
                  <li><a href="#1">8</a></li>
                  <li class="disabled"><span>...</span></li>
                  <li><a href="#!">14452</a></li>
                  <li><a href="#!">14453</a></li>
                  <li><a href="#!">»</a></li>';*/
  /*if($pages>8&&$page<$pages-2){
  	$pgli = $pgli.'<li class="disabled"><span>...</span></li>'; 
  	$pgli = $pgli.'<li><span><a href="?page="'.$pages.'>«</a></span></li>';               
  	$pgli = $pgli.'<li><span><a href="?page="'.$pages.'>«</a></span></li>';
  }*/
  if($page<=$pages-1){
  	$pgli = $pgli.'<li><span><a href="?page='.intval($page+1).'">»</a></span></li>';
  }
 	
  
  $sql = $sql." limit ?,?";
  $parmarr[] = ($page-1)*$pagesize;
  $parmarr[] = $pagesize;
  /*echo($sql);
  var_dump($parmarr);*/
  $stmt = $pdo->prepare($sql); 
  $stmt->execute($parmarr);
  $count = $stmt->rowCount();
  //echo($count);
  $resjs=array();
  
  for($x=0;$x<$count;$x++){
  	$tdata = $stmt->fetch(PDO::FETCH_ASSOC);
		$resjs[] = $tdata;
		
		$data = $data."<tr><td>".$tdata["id"]."</td><td>".$tdata["hphm"]."</td><td>".$tdata["clpp"]."</td><td>".$tdata["clys"]."</td><td>".$tdata["djxm"]."</td><td>".$tdata["djsfz"]."</td><td>".$tdata["djsjh"]."</td><td>".$tdata["gzdw"]."</td><td>".$tdata["bz1"]."</td><td>".$tdata["lrr"]."</td><td>".$tdata["djsj"].'</td><td><!--<a href="javascript:void(0);" id="updatebike" bid="'.$tdata["id"].'" class="text-muted" data-toggle="tooltip" title="" data-original-title="编辑"><i class="mdi mdi-pencil"></i></a>--><a href="?action=del&id='.$tdata["id"].'" class="text-danger m-l-5" data-toggle="tooltip" title="" data-original-title="删除"><i class="mdi mdi-delete"></i></a></td></tr>';
		//$data = $data."<tr><td>".$tdata["id"]."</td><td>".$tdata["hphm"]."</td><td>".$tdata["clpp"]."</td><td>".$tdata["clys"]."</td><td>".$tdata["djxm"]."</td><td>".$tdata["djsfz"]."</td><td>".$tdata["djsjh"]."</td><td>".$tdata["gzdw"]."</td><td>".$tdata["bz1"]."</td><td>".$tdata["lrr"]."</td><td>".$tdata["djsj"].'</td><td><a href="?action=edit&id='.$tdata["id"].'" id="updatebike" bid="'.$tdata["id"].'" class="text-muted" data-toggle="tooltip" title="" data-original-title="编辑"><i class="mdi mdi-pencil"></i></a><a href="?action=del&id='.$tdata["id"].'" class="text-danger m-l-5" data-toggle="tooltip" title="" data-original-title="删除"><i class="mdi mdi-delete"></i></a></td></tr>';
		//$data = $data."<tr><td>".$tdata["id"]."</td><td>".$tdata["hphm"]."</td><td>".$tdata["csh"]."</td><td>".$tdata["cjh"]."</td><td>".$tdata["clpp"]."</td><td>".$tdata["djsfz"]."</td><td>".$tdata["djsjh"]."</td><td>".$tdata["djdz"]."</td><td>".$tdata["gzdw"]."</td><td>".$tdata["gmsj"]."</td><td>".$tdata["lrr"]."</td><td>".$tdata["djsj"].'</td><td><a href="?edit=1&id='.$tdata["id"].'" class="text-muted" data-toggle="tooltip" title="" data-original-title="编辑"><i class="mdi mdi-pencil"></i></a><a href="?del=1&id='.$tdata["id"].'" class="text-danger m-l-5" data-toggle="tooltip" title="" data-original-title="删除"><i class="mdi mdi-delete"></i></a></td></tr>';
		//var_dump($tdata);
  	//echo($tdata["id"].'-'.$tdata["name"].'<br />');
  }
  //print_r("<pre>");
  //print_r($data);
  //echo(json_encode($resjs));
  $pdo = null;
  
}
function save(){
	//$sql = "insert into ebikes('hphm','clpp','clys','djxm','djsfz','djsjh','gzdw','bz1','lrr','djsj') values(:hphm,:clpp,:clys,:djxm,:djsfz,:djsjh,:gzdw,:bz1,:lrr,:djsj)";
	$sql = "insert into ebikes(hphm,clpp,clys,djxm,djsfz,djsjh,gzdw,bz1,lrr,djsj) values(?,?,?,?,?,?,?,?,?,?)";
	
	$pdo = getPdo();
	/*$parmarr=array(':hphm'=>$_POST["hphm"],
								':clpp'=>$_POST["clpp"],
								':clys'=>$_POST["clys"],
								':djxm'=>$_POST["djxm"],
								':djsfz'=>$_POST["djsfz"],
								':djsjh'=>$_POST["djsjh"],
								':gzdw'=>$_POST["gzdw"],
								':bz1'=>$_POST["bz1"],
								':lrr'=>$_POST["lrr"],
								':djsj'=>time()
								);*/
	$parmarr=array($_POST["hphm"],$_POST["clpp"],$_POST["clys"],$_POST["djxm"],$_POST["djsfz"],
								$_POST["djsjh"],
								$_POST["gzdw"],
								$_POST["bz1"],
								$_POST["lrr"],
								date("Y-m-d H:i:s")
								);
	$stmt = $pdo->prepare($sql); 
	
	/*$stmt->bindParam(':hphm',$_POST["hphm"]);
	$stmt->bindParam(':clpp',$_POST["clpp"]);
	$stmt->bindParam(':clys',$_POST["clys"]);
	$stmt->bindParam(':djxm',$_POST["djxm"]);
	$stmt->bindParam(':djsfz',$_POST["djsfz"]);
	$stmt->bindParam(':djsjh',$_POST["djsjh"]);
	$stmt->bindParam(':gzdw',$_POST["gzdw"]);
	$stmt->bindParam(':bz1',$_POST["bz1"]);
	$stmt->bindParam(':lrr',$_POST["lrr"]);
	$stmt->bindParam(':djsj',time());*/
  $stmt->execute($parmarr);
  $pdo = null;
}
function del(){
	$id=$_GET["id"];
	if($id){
		$pdo = getPdo();
		$stmt = $pdo->prepare("delete from ebikes where id=?"); 
		$stmt->execute(array($id));
		$pdo = null;
	}
	header('Location: ebike.php');
	die;
}
$action = array_key_exists("action",$_POST)?$_POST["action"]:"";
if(!$action){
	$action = array_key_exists("action",$_GET)?$_GET["action"]:"";
}
//echo($action);
//echo("12");
if($action&&$action=="add"){
	save();
	
}else{
	if($action&&$action=="edit"){
		update();
	}else{
		if($action&&$action=="del"){
			del();
		}
	}
}
test();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>鹤壁市电动自行车登记系统</title>
<meta name="keywords" content="鹤壁市电动自行车登记系统">
<meta name="description" content="鹤壁市电动自行车登记系统">
<meta name="author" content="王捷航">
<link href="ebikestatics/bootstrap.min.css" rel="stylesheet">
<link href="ebikestatics/materialdesignicons.min.css" rel="stylesheet">
<!--日期选择插件-->
<link rel="stylesheet" href="ebikestatics/bootstrap-datepicker3.min.css">
<link href="ebikestatics/style.min.css" rel="stylesheet">
<style>
	#add{
		display:none;	
	}
</style>
</head>
  
<body>
<div class="container-fluid">
  
  <div class="row">
    
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header"><h4>电动车查询</h4></div>
        <form>
        <!--更多内容-->
        <div class="card-toolbar clearfix">
          <div class="form-inline">
            <div class="form-group m-b-5">
              <input class="form-control" type="text" name="hphm" placeholder="备案标牌号码" value="<?=@$searchparm['hphm']?>">
            </div>
            <div class="form-group m-b-5">
              <input class="form-control" type="text" name="syrzjhm" placeholder="车主证件号">
            </div>
            <div class="form-group m-b-5">
              <input class="form-control" type="phone" name="usernbame" placeholder="车主手机号">
            </div>
						<div class="form-group m-b-5">
			              <div class="input-group" data-provide = 'datepicker'>
			                <input class="form-control" type="text" name="start_time" value="" placeholder="登记开始时间">
			                <span class="input-group-addon no-border-lr">~</span>
			                <input class="form-control" type="text" name="end_time" value="" placeholder="登记结束时间">
			              </div>
						</div>
            <button  class="btn btn-default m-b-5" type="submit">
            	<i class="mdi mdi-magnify"></i>
							搜索
						</button>
            
            <a id="addbike" class="btn btn-primary m-b-5" href="#!"><i class="mdi mdi-plus"></i> 新增</a>
            </form>
          </div>
        </div>
        <div class="card-body">
          <p>输入筛选条件，点击搜索按钮</p>
        </div>
      </div>
    </div>
	
  </div>
  <div class="row">
    
    <div class="col-md-12">
      <div class="card">
        <div class="card-header"><h4>车辆列表</h4></div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead>
                <tr role="row">
                  <th>ID</th>
                  <th>备案标牌号码</th>
                  <!--<th>车身号</th>
                  <th>车架号</th>-->
                  <th>车辆品牌</th>
                  <th>车辆颜色</th>
                  <th>车主姓名</th>
                  <th>车主身份证</th>
                  <th>联系电话</th>
                  <!--<th>登记地址</th>-->
                  <th>单位或所属辖区</th>
                  <th>备注</th>
                  <!--<th>购买时间</th>-->
                  <th>录入人</th>
                  <th>录入时间</th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody>
              	
                <?=$data?>
                <!--<tr>
                  <td>3013</td>
                  <td>
                    <img src="http://lyear.itshubao.com/iframe/images/gallery/1.jpg" class="img-rounded" width="40">
                    <span>佳能 4000D 18-55 MM</span>
                  </td>
                  <td>马小云</td>
                  <td>128 元</td>
                  <td><span class="badge bg-info bg-gray bg-success bg-warning bg-primary">等待付款</span></td>
                  <td>2018/08/28 21:24:36</td>
                  <td>
                    <a href="#!" class="text-muted" data-toggle="tooltip" title="" data-original-title="编辑"><i class="mdi mdi-pencil"></i></a>
                    <a href="#!" class="text-danger m-l-5" data-toggle="tooltip" title="" data-original-title="删除"><i class="mdi mdi-delete"></i></a>
                  </td>
                </tr>-->
                
              </tbody>
            </table>
          </div>
          <ul class="pagination">
          	<?=$pgli?>
                  <!--<li class="disabled"><span>«</span></li>
                  <li class="active"><span>1</span></li>
                  <li><a href="#1">2</a></li>
                  <li><a href="#1">3</a></li>
                  <li><a href="#1">4</a></li>
                  <li><a href="#1">5</a></li>
                  <li><a href="#1">6</a></li>
                  <li><a href="#1">7</a></li>
                  <li><a href="#1">8</a></li>
                  <li class="disabled"><span>...</span></li>
                  <li><a href="#!">14452</a></li>
                  <li><a href="#!">14453</a></li>
                  <li><a href="#!">»</a></li>-->
          </ul>
          <div>总计：<?=$ct?></div>
        </div>
      </div>
    </div>
     
  </div>
</div>

<div class="col-md-12" id="add">
      <div class="card">
        <div class="card-header"><h4>电动车登记</h4></div>
        <div class="card-body">
          
          <p>请填写下方的登记信息</p>
          <form action="" method="post" id="example-from-1" class="form-horizontal">
          	<input type="hidden" name="action" value="add" id="formaction"/>
            <div class="form-group">
              <label class="col-md-2 control-label" for="hphm">备案标牌号码</label>
              <div class="col-md-10">
                <input type="text" class="form-control" id="hphm" name="hphm" value="鹤临" placeholder="" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label" for="clpp">车辆品牌</label>
              <div class="col-md-10">
                <input type="text" class="form-control" id="clpp" name="clpp" value="" placeholder="" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label" for="clys">车辆颜色</label>
              <div class="col-md-10">
                <input type="text" class="form-control" id="clys" name="clys" value="" placeholder="" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label" for="djxm">车主姓名</label>
              <div class="col-md-10">
                <input type="text" class="form-control" id="djxm" name="djxm" value="" placeholder="" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label" for="djsfz">车主身份证</label>
              <div class="col-md-10">
                <input type="text" class="form-control" id="djsfz" name="djsfz" value="" placeholder="" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label" for="djsjh">联系电话</label>
              <div class="col-md-10">
                <input type="text" class="form-control" id="djsjh" name="djsjh" value="" placeholder="" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label" for="gzdw">单位或辖区</label>
              <div class="col-md-10">
                <input type="text" class="form-control" id="gzdw" name="gzdw" value="" placeholder="" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label" for="bz1">备注</label>
              <div class="col-md-10">
                <input type="text" class="form-control" id="bz1" name="bz1" value="" placeholder="" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label" for="lrr">录入人</label>
              <div class="col-md-10">
                <input type="text" class="form-control" id="lrr" name="lrr" value="" placeholder="" />
              </div>
            </div>
            <!--<div class="form-group">
              <label class="col-md-2 control-label" for="password">密码框</label>
              <div class="col-md-8">
                <input type="password" class="form-control" id="password" name="password" value="" placeholder="密码框" />
              </div>
              <div class="col-md-2"><small class="help-block p-t-5">密码是用5-20位字符</small></div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label" for="type">(默认)选择框</label>
              <div class="col-md-10">
                <select name="type" class="form-control" id="type">
                  <option value="1">小说</option>
                  <option value="2">古籍</option>
                  <option value="3">专辑</option>
                  <option value="4">自传</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label" for="example">(使用插件)选择框</label>
              <div class="col-md-10">
                <select class="form-control selectpicker" title="小伙子选择一个吧..." name="example-6" id="example">
                  <option>吊打海内外的编程技术</option>
                  <option>迷倒万千少女的颜值</option>
                  <option>马云大大的财富</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label" for="idname">开关</label>
              <div class="col-md-10 p-t-8 h-28">
                <label class="lyear-switch switch-solid switch-primary">
                  <input type="checkbox" checked="" value="1" id="idname"><span></span>
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label">复选框</label>
              <div class="col-md-10 h-28">
                <label class="lyear-checkbox checkbox-inline checkbox-primary">
                  <input type="checkbox"><span>篮球</span>
                </label>
                <label class="lyear-checkbox checkbox-inline checkbox-primary">
                  <input type="checkbox"><span>足球</span>
                </label>
                <label class="lyear-checkbox checkbox-inline checkbox-primary">
                  <input type="checkbox"><span>排球</span>
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label">单选框</label>
              <div class="col-md-10 h-28">
                <label class="lyear-radio radio-inline radio-primary">
                  <input type="radio" name="e"><span>羽毛球</span>
                </label>
                <label class="lyear-radio radio-inline radio-primary">
                  <input type="radio" name="e"><span>冰球</span>
                </label>
                <label class="lyear-radio radio-inline radio-primary">
                  <input type="radio" name="e"><span>网球</span>
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label" for="desc">文本域</label>
              <div class="col-md-10">
                <textarea name="desc" id="desc" placeholder="请输入内容" class="form-control"></textarea>
              </div>
            </div>-->
            <div class="form-group">
              <div class="col-md-10 col-md-offset-2">
                <button type="submit" class="btn btn-primary">立即提交</button>
                <button type="reset" class="btn btn-default">重 置</button>
              </div>
            </div>

          </form>
          
        </div>
      </div>
    </div>
<script type="text/javascript" src="ebikestatics/jquery.min.js"></script>
<!--日期选择插件-->
<script type="text/javascript" src="ebikestatics/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="ebikestatics/bootstrap-datepicker.zh-CN.min.js"></script>

<script type="text/javascript" src="ebikestatics/popper.min.js"></script>
<script type="text/javascript" src="ebikestatics/bootstrap.min.js"></script>
<script type="text/javascript" src="ebikestatics/main.min.js"></script>
<script type="text/javascript" src="ebikestatics/layer.min.js"></script>

<script>
	jQuery( function() {
		/*$('#addbike').click(function() {
		    layer.open({
		        type: 1,
		        skin: 'layui-layer-rim',  // 加上边框
		        area: ['600px', '400px'], // 宽高
		        content: '<div class="p-3">魏临川于中取利　花文芳将计就计</div>'
		    });
		});*/
		$('#del').click(function(){
			
		});
		$('#updatebike').click(function() {
				$("#formaction").val('update');
		    layer.open({
        type: 1,
        shade: false,
        title: false, //不显示标题
        area: ['800px', '400px'], // 宽高
        content: $('#add'), //捕获的元素，注意：最好该指定的元素要存放在body最外层，否则可能被其它的相对元素所影响
        cancel: function(){
            //layer.msg('捕获就是从页面已经存在的元素上，包裹layer的结构', {time: 5000, icon: 'success'});
        }
    });
		});
		$('#addbike').click(function() {
		    layer.open({
        type: 1,
        shade: false,
        title: false, //不显示标题
        area: ['800px', '500px'], // 宽高
        content: $('#add'), //捕获的元素，注意：最好该指定的元素要存放在body最外层，否则可能被其它的相对元素所影响
        cancel: function(){
            //layer.msg('捕获就是从页面已经存在的元素上，包裹layer的结构', {time: 5000, icon: 'success'});
        }
    });
		});
    jQuery("[data-provide = 'datepicker']").each(function() {
        var options = {
            language: 'zh-CN',  // 默认简体中文
            multidateSeparator: ', ', // 默认多个日期用,分隔
        }
  
        if ( $(this).prop("tagName") != 'INPUT' ) {
            options.inputs = [$(this).find('input:first'), $(this).find('input:last')];
        }
  
        $(this).datepicker(options);
    });
});
</script>
</body>
</html>