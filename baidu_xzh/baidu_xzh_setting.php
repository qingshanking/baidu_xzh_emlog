<?php
!defined('EMLOG_ROOT') && exit('access deined!');
function  plugin_setting_view(){
	include(EMLOG_ROOT.'/content/plugins/baidu_xzh/baidu_xzh_config.php');
	?>
	<?php if(isset($_GET['setting'])):?>
<div class="actived alert alert-success alert-dismissable">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
设置成功
</div>
<?php endif;?>
<div class="row">
  <div class="col-md-12">
      <div class="panel panel-default card-view">
          <div class="panel-body"> 
	          <h2>百度熊掌号自动推送提交设置</h2>
              <p>appid和token与站点链接一一对应，如果appid和token错误会显示提交失败   </p>
		      <P>token请在百度平台：https://ziyuan.baidu.com/xzh/home/index 手动获取 </P>
		      <p>提交结果可以在 https://ziyuan.baidu.com/xzh/analysis/data  查看 结果有一定延迟 </p>
		      <p>清除提交记录将导致保存文章的时候会再次提交该文章！请慎重！慎重！慎重！重要的事情说三遍</p>
		      <p>百度一下萧瑟云资讯  网址：www.xsyrz.cn</p>	
          </div>
      </div>
  </div>
</div>   
<form action="./plugin.php?plugin=baidu_xzh&action=setting" method="POST">
     <div class="row">
         <div class="col-md-12">
             <div class="panel panel-default card-view">
                 <div class="form-group">
                     <label class="control-label mb-10">appid：</label>
                     <input class="form-control"  name="appid" type="text" value="<?php echo $config['appid'];?>" />
                 </div>
                 <div class="form-group">
                     <label class="control-label mb-10">token:</label>
                     <input class="form-control"  type="text" name="token" value="<?php echo $config['token'];?>" />
                 </div>
                 <div class="form-group">
                     <label class="control-label mb-10">type:</label>
                     <input class="form-control"  type="text" name="type" value="<?php echo $config['type'];?>" />
                 </div>
                 <div class="form-group">
                     <label class="control-label mb-10">显示记录前N条(0为全部显示)</label>
                     <input class="form-control" type="text" name="xzh_display" value="<?php echo $config['xzh_display'];?>">
                 </div>
                 <div class="form-group">
                     <input type="checkbox" name="clean_log">
                     <label class="control-label mb-10">清除提交记录(勾选后修改设置会清理所有记录)</label>
                 </div>
                 <div class="clearfix"></div>
                 <div class="form-group text-center">
                     <input class="btn  btn-success" type="submit" value="修改设置">
                 </div>
             </div>
         </div>
     </div>  	
</form>
	<?php 
	$bdxzh_file = dirname(__FILE__).'/xzhbmit_log.txt';	
	$bdxzh_logs = file_get_contents($bdxzh_file);
	$bdxzh_logs_info = explode("\r\n",$bdxzh_logs);
	array_pop($bdxzh_logs_info);
	?>
	<style type="text/css">
		td{
			padding-right: 5px;
		}
	</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default card-view">	
            <div class="table-wrap ">
                <div class="table-responsive">	
                    <table id="adm_link_list"  class="table table-striped table-bordered mb-0">
                          <tbody>
		                  <tr><th>提交网址</th><th>提交状态</th><th>提交时间</th><th>错误原因</th></tr>
		<?php
		$i = ($config['xzh_display'] == 0) ? 0 : ($config['xzh_display'] + 1);

		foreach (array_reverse($bdxzh_logs_info) as $xzhbmit_log) {
			$i--;
			if($i == 0) break;
			$xzhbmit_log_info = explode("||",$xzhbmit_log);
			if($xzhbmit_log_info[0] == 0)
				echo "<tr><td>".$xzhbmit_log_info[3]."</td><td>提交失败</td><td>".$xzhbmit_log_info[2]."</td><td>".$xzhbmit_log_info[1]."</td></tr>";
			else
				echo "<tr><td>".$xzhbmit_log_info[2]."</td><td>提交成功</td><td>".$xzhbmit_log_info[1]."</td><td>-</td></tr>";

		}
		?>
                          </tbody>
                    </table>	
                </div>
            </div>
        </div>
    </div>  	
</div>		
	<?php
}

function plugin_setting(){
	if($_POST['clean_log'] == true){
		@file_put_contents(EMLOG_ROOT.'/content/plugins/baidu_xzh/xzhbmit_log.txt', "");
		@file_put_contents(EMLOG_ROOT.'/content/plugins/baidu_xzh/logid_log.txt', "");
	}
	$newconfig = '<?php
					$config =  array(
						"appid" => "'.$_POST['appid'].'",
						"token" => "'.$_POST['token'].'",
						"type" => "'.$_POST['type'].'",
						"xzh_display" => "'.$_POST['xzh_display'].'" 
					);';
	@file_put_contents(EMLOG_ROOT.'/content/plugins/baidu_xzh/baidu_xzh_config.php', $newconfig);
}
?>