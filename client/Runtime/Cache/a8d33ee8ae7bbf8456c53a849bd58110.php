<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	
	 
    <link href="__PUBLIC__/metro/css/metro-bootstrap.css" rel="stylesheet">
    <link href="__PUBLIC__/metro/css/metro-bootstrap-responsive.css" rel="stylesheet">
    <link href="__PUBLIC__/metro/css/docs.css" rel="stylesheet">
    <link href="__PUBLIC__/metro/js/prettify/prettify.css" rel="stylesheet">

    <!-- Load JavaScript Libraries -->
    <script src="__PUBLIC__/metro/js/jquery/jquery.min.js"></script>
    <script src="__PUBLIC__/metro/js/jquery/jquery.widget.min.js"></script>
    <script src="__PUBLIC__/metro/js/jquery/jquery.mousewheel.js"></script>
    <script src="__PUBLIC__/metro/js/prettify/prettify.js"></script>

    <!-- Metro UI CSS JavaScript plugins -->
    <script src="__PUBLIC__/metro/js/load-metro.js"></script>

    <!-- Local JavaScript -->
    <script src="__PUBLIC__/metro/js/docs.js"></script>
    <title>快点订单系统</title>

<script src="__PUBLIC__/checkInput.js"></script>
 
</head>

<body class="metro">

    <div class="container">
        <h1>
       		<a href="<?php echo U("Index/index");?>"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
           	版本更新信息<small class="on-right">2014.9.5</small>
        </h1>
        <div class="tile-area no-padding clearfix">
            <div class="grid">
            	<div class="row">
            		<div class="accordion with-marker span12 place-left margin10" data-role="accordion" data-closeany="false">
                        <div class="accordion-frame">
                            <a class="heading bg-lightBlue fg-white" href="#">V3.0版本更新&nbsp;&nbsp;&nbsp;&nbsp;2014.9.5</a>
                            <div style="display: block;" class="content">
                                <ol>
                                	<li>
                                	<b>添加了财务管理模块中的收款计算、付款计算、往来管理、费用、费用查询、今日汇总、汇总查询功能</b><br>
                                	至此为止，一期所有功能全部完成。其中汇总功能可以下载excel表格到本地计算机，便于数据的保存。
                                	</li>
                                	<li>
                                	<b>添加了停车位置的快速选取列表</b><br>
                                	在下单时可以输入拼音首字母快速选择地址。
                                	</li>
                                	<li>
                                	<b>修改了历史记录排序bug；增加了销售汇总雏形</b><br>
                                	</li>
                                	<li>
                                	<b>给历史记录页面添加订单编号显示</b><br>
                                	用于在往来管理里查询订单。
                                	</li>
                                	<li>
                                	<b>将订单记录的信息增加物流信息；修改显示物流信息的网页；添加excel输出汇总</b><br>
                                	原来的订单表中是没有物流信息的（电话、地址、车号、停车地址），现在添加上了。相应的，也修改了查询订单的页面。
                                	</li>
                                	<li>
                                	<b>更改所有错误的函数说明注释</b><br>
                                	</li>
                                </ol>
                            </div>
                        </div>
                        <div class="accordion-frame">
                            <a class="heading ribbed-green fg-white collapsed" href="#">之前的更新</a>
                            <div class="content">
                            	<strong>V2.0版本更新&nbsp;&nbsp;&nbsp;&nbsp;2014.3.16</strong>
                            	<ol>
                                	<li>
                                	<b>程序的数据库操作实现了事务</b><br>
                                	完全确保数据一致。
                                	</li>
                                </ol>
                                <br>
                                
                            	<strong>V1.1.3版本更新&nbsp;&nbsp;&nbsp;&nbsp;2014.3.13</strong>
                            	<ol>
                                	<li>
                                	<b>验证码不区分大小写。（SAE平台下仍需要区分大小写）</b><br>
                                	直接用小写输入即可。
                                	</li>
                                	<li>
                                	<b>完成了修改商品信息的页面，现在可以在后台修改商品信息了</b><br>
                                	到后台管理界面登录，然后选择  浏览商品  ，然后选择一个具体的商品，进入后就能修改该商品的信息了。
                                	</li>
                                	<li>
                                	<b>在  结算信息页面  加入了历史欠付款情况</b><br>
                                	相应的添加了账户余额功能。
                                	</li>
                                	<li>
                                	<b>金额计算精确到小数点后两位</b><br>
                                	</li>
                                	<li>
                                	<b>对于历史记录页面添加了分页</b><br>
                                	对于大量数据可以翻页了。<br>
                                	默认每页显示20条记录。
                                	</li>
                                	<li>
                                	<b>在  结算信息页面  添加了新用户确认功能</b><br>
                                	</li>
                                	<li>
                                	<b>给后台管理系统登陆界面添加了验证码</b><br>
                                	</li>
                                	<li>
                                	<b>给后台管理系统加入了权限管理系统</b><br>
                                	</li>
                                </ol>
                                <br>
                                
                            	<strong>V1.1.2版本更新&nbsp;&nbsp;&nbsp;&nbsp;2014.3.8</strong>
                            	<ol>
                                	<li>
                                	<b>将订单时间拆分为  打印时间  与  创建时间</b><br>
                                	</li>
                                	<li>
                                	<b>添加了 高级查询 功能</b><br>
                                	    在 历史查询页面->右上角搜索图标  中<br>
                                	   支持 打印时间  与  订单创建时间  的   区间查询  与  多点查询
                                	</li>
                                	<li>
                                	<b>更改了 历史记录 页面，使得 历史记录 页面只显示当天的已完成订单</b><br>
                                	历史记录  页面中的  未完成订单  仍显示为所有日期的未完成订单。
                                	</li>
                                	<li>
                                	<b>删除处添加了询问提示</b><br>
                                	</li>
                                	<li>
                                	<b>屏蔽了修改购物车内商品的页面</b><br>
                                	</li>
                                	<li>
                                	<b>首页去掉了已拆分标签</b><br>
                                	</li>
                                	<li>
                                	<b>添加了更新信息页面</b><br>
                                	</li>
                                	<li>
                                	<b>添加了https强连接选项</b><br>
                                	</li>
                                	<li>
                                	<b>添加了AES加密算法</b><br>
                                	</li>
                                </ol>
                            </div>
                        </div>
                    </div>
            	</div>
            </div>
        </div>
    </div>

</body>
</html>