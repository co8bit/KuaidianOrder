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

</head>

<body class="metro" onload="timedCount()">

    <div class="container">
        <h1>
            <a href="<?php echo U("Index/goBack");?>"><i class="icon-arrow-left-3 fg-darker smaller"></i></a>
           	结果
        </h1>
        
      		<div class="panel">
				<div class="panel-header bg-indigo fg-white">
					延迟发货
				</div>
				<div class="panel-content grid fluid">
					<div class="row">
						<h1 id="allText" class="text-center">
							<i class="icon-checkmark"></i>
							<span id="printState">延迟发货成功</span>
						</h1>
					</div>
					<div class="row">
						<h1 class="text-center"><a href="<?php echo U("Index/index");?>"><i class="icon-windows"></i></a></h1>
					</div>
				</div>
			</div>
       		
        	<div class="row">
	        	<div class="stepper rounded" data-role="stepper" data-steps="5" data-start="5"></div>
	        </div>
    </div>
</body>
</html>