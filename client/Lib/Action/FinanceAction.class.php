<?php
require_once(LIB_PATH."commonAction.php");

class FinanceAction extends myAction
{
	/**
	 * 标签表的字母列表
	 */
	private $zimuTabList = array("A","B","C","D","E","F","G","H","I","J","K","M","L","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","其他");

    protected function _initialize()
    {
    	if (!$this->checkPower("financePower",session("userPower")))
    		$this->error("非法访问",U("Index/index"));
    	
    	//https开启了，且当前不是https访问，则强制跳转
    	if ( (_ISHTTPS === true) && ($this->_server["HTTPS"] <> "on") )
    		header("Location:https://".__SELF__);
    	
    	//初始化
    	for ($i = "A"; $i <= "Z"; $i++)
    		$zimuTabList	 = $i;
    }

    public function index()
    {
        $this->display();
    }
    
    /**
     * 获取用户信息用于显示
     * @return		返回整理好的用户队列$classInfo和快速选择的用户队列$accountList;
     * 						返回一个多维数组。其中a[0]是$classInfo,a[1]是$accountList
     */
    private function getUserInfoForDisplay()
    {
    	$userList	=		D("User")->order("userName")->select();
    	 
    	/*
    	 * $classInfo数组结构：
    	* 			$classInfo[拼音开头字母][account][i][name] = a; 代表某个字母后面的第i个名字值是a
    	* 			$classInfo[拼音开头字母][account][i][class] = "..."; 代表某个字母后面的第i个名字要显示的样式（css的class）是...
    	* 			$classInfo[拼音开头字母][count]	  = 1;代表该拼音字母开头下有count个名字
    	* 			$classInfo[拼音开头字母][tabName] = a;这个标签的标签名（类名），NOTE：是大写的
    	*/
    	for ($i = 0; $i < count($userList); $i++)
    	{
    	//准备格子的显示
    		$ch = null;
    		$ch = strtoupper($userList[$i]["userPinYin"][0]);
    		if (!isset($classInfo[$ch]["count"]))
    		{
    	    	$classInfo[$ch]["count"] =	0;
    	    	$classInfo[$ch]["tabName"] = $ch;
    		}
    	   $classInfo[$ch]["account"][$classInfo[$ch]["count"]]["name"] = $userList[$i]["userName"];
    	    if ($classInfo[$ch]["count"] % 2 == 0)
    	    	$classInfo[$ch]["account"][$classInfo[$ch]["count"]]["class"] = "shortcut primary";
    	    else
    	    	$classInfo[$ch]["account"][$classInfo[$ch]["count"]]["class"] = "shortcut success";
    	    $classInfo[$ch]["count"]++;
    	
    	 	//准备快速选择列表
    	    $accountList[$i]["pyname"] =  $userList[$i]["userPinYin"];
    	    $accountList[$i]["name"] =  $userList[$i]["userName"];
    	}
    	ksort($accountList);
    	ksort($classInfo);
    	//     	dump($classInfo);
    	 
    	 
    	//检查有没有超过$blockSize个商品的标签组，如果有超过的就划分为多个标签组
    	$i = 0;
    	foreach ($classInfo as $key=>$value)
    	{
    	if ($classInfo[$key]["count"] > BLOCKSIZE)
    	{
    			$tmpChunk = array_chunk($classInfo[$key]["account"],BLOCKSIZE);
    			for ($j = 0; $j < count($tmpChunk); $j++)
    			{
    			$tmp = null;
    			$tmp[$key.($j+1)]["tabName"] = $classInfo[$key]["tabName"]."-".($j+1);
    			$tmp[$key.($j+1)]["account"] = $tmpChunk[$j];
    			$classInfo = array_merge($classInfo,$tmp);//把新的拆分成2个后添加入原数组$classInfo
    			}
    			array_splice($classInfo,$i,1);//删除掉原来的那个分组（已经被拆分了）
    			}
    			$i++;
    	}
    	ksort($classInfo);
    	
    	return array($classInfo,$accountList);
    }
    
    /**
     * 应收款选账户页面
     */
    public function ar()
    {
    	$re = $this->getUserInfoForDisplay();
    	$this->assign("list",$re[0]);
    	$this->assign("accountList",$re[1]);
    	$this->display();
    }
    
    /**
     * 应收款填表页面
     */
    public function ardo()
    {
    	$id	=		$this->_get("id");
    	empty($id) && $this->error("非法操作",U("Finance/index"));
    	$this->assign("id",$id);
    	session("ardoID",$id);
    	$this->display();
    }
    
    /**
     * 处理应收款
     */
    public function toar()
    {
    	$id	=		session("ardoID");
    	$money	=	$this->_post("money");
    	$remark	=	$this->_post("remark");
    	empty($id) && $this->error("非法操作",U("Finance/index"));
    	empty($money) && $this->error("请填写金额",U("Finance/ardo",array("id"=>$id)));
    	
    	//TODO:检测id是否存在合法
    	//TODO:检查金额是否是数字
    	
    	D("Finance")->startTrans();
    	if ( D("Finance")->newFinance($id,$money,$remark,0,session("userName")) )
    	{
    		if (D("User")->updateMoney(1,$id,$money))
    			D("Finance")->commit();
	    	else
	    	{
	    		D("Finance")->rollback();
	    		$this->error("应收款创建失败，请重试",U("Index/goBack_2"));
	    	}
    	}
    	$this->success("应收款创建成功",U("Finance/index"));
    }
    
    /**
     * 应付款页面
     */
    public function ap()
    {
    	$re = $this->getUserInfoForDisplay();
    	$this->assign("list",$re[0]);
    	$this->assign("accountList",$re[1]);
    	$this->display();
    }
    
    /**
     * 应付款填表页面
     */
    public function apdo()
    {
    	$id	=		$this->_get("id");
    	empty($id) && $this->error("非法操作",U("Finance/index"));
    	$this->assign("id",$id);
    	session("apdoID",$id);
    	$this->display();
    }
    
    /**
     * 处理应付款
     */
    public function toap()
    {
    	$id	=		session("apdoID");
    	$money	=	$this->_post("money");
    	$remark	=	$this->_post("remark");
    	empty($id) && $this->error("非法操作",U("Finance/index"));
    	empty($money) && $this->error("请填写金额",U("Finance/apdo",array("id"=>$id)));
    	 
    	//TODO:检测id是否存在合法
    	//TODO:检查金额是否是数字
    	 
    	D("Finance")->startTrans();
    	if ( D("Finance")->newFinance($id,$money,$remark,1,session("userName")) )
    	{
    		if (D("User")->updateMoney(0,$id,$money))
    			D("Finance")->commit();
	    	else
	    	{
	    		D("Finance")->rollback();
	    		$this->error("应付款创建失败，请重试",U("Index/goBack_2"));
	    	}
    	}
    	$this->success("应付款创建成功",U("Finance/index"));
    }
    
    /**
     * 费用页面
     */
    public function cost()
    {
    	$this->assign("dateDisplay",date("Y-m-d"));
    	$this->display();
    }
    
    /**
     * 创建费用
     */
    public function toCost()
    {
    	$dateInfo	=	$this->_post("date");
    	$money	=	$this->_post("money");
    	$remark	=	$this->_post("remark");
    	empty($dateInfo) && $this->error("非法操作",U("Finance/index"));
    	empty($money) && $this->error("请填写金额",U("Finance/cost"));
    	
    	//TODO:检测id是否存在合法
    	//TODO:检查金额是否是数字
    	//TODO:检查日期是否合法
    	
    	if ( D("Finance")->newFinance(-1,$money,$remark,2,session("userName"),$dateInfo) )
    		$this->success("费用创建成功",U("Finance/index"));
    	else 
    		$this->error("费用创建失败，请重试",U("Index/goBack_2"));
    }
    
    /**
     * 汇总查询页面
     */
    public function total()
    {
    	
    	$this->display();
    }
    
    /**
     * 今日销售汇总页面
     */
    public function summary()
    {
    	$dbTmpOrder = D("TmpOrder");
    	$dbGoods = D("Goods");
    	
    	/*
    	 * 统计货物
    	 */
    	//取出今日销售订单；NOTE：只按printDate时间，不按createDate时间
    	$done = $dbTmpOrder->where("printState='7' and (printDate>='".date("Y-m-d")." 00:00:00' and printDate<='".date("Y-m-d")." 23:59:59')")
    				->order('createDate')->select();
    	
    	$total = null;
    	$maxID = -1;
    	$maxSize = -1;
    	for ($i = 0; $i < count($done); $i++)
    	{
    		//得到商品ID进行统计
    		$IDArray = null;
    		$numArray = null;
    		$moneyArray = null;
    		$sizeArray = null;
    		$IDArray = $dbTmpOrder->getArrayWithSelf($done[$i]["goodsIDArray"]);
    		$numArray = $dbTmpOrder->getArrayWithSelf($done[$i]["goodsNumArray"]);
    		$moneyArray = $dbTmpOrder->getArrayWithSelf($done[$i]["goodsMoneyArray"]);
    		$sizeArray = $dbTmpOrder->getArrayWithSelf($done[$i]["goodsSizeArray"]);
    		for ($j = 0; $j < count($IDArray); $j++)
    		{
    			$total[$IDArray[$j]][$sizeArray[$j]]["num"] += $numArray[$j];
    			$total[$IDArray[$j]][$sizeArray[$j]]["money"] += ($numArray[$j] * $moneyArray[$j]);
    			if ($IDArray[$j] > $maxID)
    				$maxID = $IDArray[$j];
    			if ($sizeArray[$j] > $maxSize)
    				$maxSize = $sizeArray[$j];
    		}
    	}
    	
    	$outputList = null;
    	$totalMoney = 0;
    	$totalNum = 0;
    	$k = 0;
    	for ($i = 0; $i <= $maxID; $i++)
    	{
    		for ($j = 0; $j <= $maxSize; $j++)
	    	{
	    		if ( !( ($total[$i][$j]["num"] == 0) || ($total[$i][$j]["num"] == null) ) )
	    		{
		    		$dbGoods->init($i);
		    		$outputList[$k]["name"] = $dbGoods->getGoodsName();
		    		$tmp = null;
		    		$tmp =  $dbGoods->getGoodsSize();
		    		$outputList[$k]["size"] = $tmp[$j];
		    		$outputList[$k]["id"] = $i;
		    		$outputList[$k]["num"] = $total[$i][$j]["num"];
		    		$outputList[$k]["totalPrice"] = $total[$i][$j]["money"];
		    		$outputList[$k]["price"] = round($outputList[$k]["totalPrice"] / $outputList[$k]["num"],2);
		    		
		    		$totalMoney += $outputList[$k]["totalPrice"];
		    		$totalNum += $outputList[$k]["num"];
		    		$k++;
	    		}
	    	}
    	}
	    
	    	
	    	
	   
	    	
	    /*
	     * 统计营业额
	     */
    	foreach($done as $key=>$value)
    	{
    		 $xianJinShiShou += $value["xianJinShiShou"];
    		 $yinHangShiShou += $value["yinHangShiShou"];
    	}
    	$this->assign("xianJinShiShou",$xianJinShiShou);
    	$this->assign("yinHangShiShou",$yinHangShiShou);
    	
	   	$this->assign("totalMoney",$totalMoney);
	   	$this->assign("totalNum",$totalNum);
	   	$this->assign("list",$outputList);
	   	$this->display();
    }
    
    /**
     * 往来管理页面
     */
    public function contacts()
    {
    	$re = $this->getUserInfoForDisplay();
    	$this->assign("list",$re[0]);
    	$this->assign("accountList",$re[1]);
    	$this->display();
    }
    
    /**
     * 查看某个账号的往来账目
     */
    public function watchUser()
    {
    	//TODO:检查用户名是否合法
    	$id	=		$this->_get("id");
    	empty($id) && $this->error("非法操作",U("Finance/contacts"));
    	
    	//往来凭证
    	$re	=		D("Finance")->where(array("userID"=>$id))->order("createDate desc")->select();
    	foreach($re as $key=>$value)
    	{
    		$list[$key]["id"] = $value["id"];
    		$list[$key]["createDate"] = $value["createDate"];
    		$list[$key]["money"] = $value["money"];
    		$list[$key]["remark"] = $value["remark"];
    		$list[$key]["createUser"] = $value["createUser"];
    		if ($value["mode"] == 0)
    		{
    			$list[$key]["mode"] = "应<font color='#FF0000'><b>收</b></font>款";
    		}
    		elseif ($value["mode"] == 1)
    		{
    			$list[$key]["mode"] = "应<font color='#0080FF'><b>付</b></font>款";
    		}
    		elseif ($value["mode"] == 2)
    		{
    			$list[$key]["mode"] = "费用";
    		}
    		else
    		{
    			$list[$key]["mode"] = "未知，如果看到这个请联系开发人员";
    		}
    	}
    	
    	//账户余额信息
    	$balanceTmp		=		D("User")->where(array("userName"=>$id))->select();
    	if ($balanceTmp[0]["money"] >= 0)
    		$balanceInfo = "<font color='#0080FF'>公司欠其".$balanceTmp[0]["money"]."</font>";
    	else
    		$balanceInfo = "<font color='#FF0000'>其欠公司".(0 - $balanceTmp[0]["money"])."</font>";
    	
    	$this->assign("id",$id);
    	$this->assign("balance",$balanceInfo);
    	$this->assign("list",$list);
    	$this->display();
    }
    
    /**
     * 费用查询页面
     */
    public function costQuery()
    {
    	//费用凭证
    	$re	=		D("Finance")->where(array("mode"=>"2"))->order("createDate desc")->select();
    	foreach($re as $key=>$value)
    	{
    		$list[$key]["id"] = $value["id"];
    		$list[$key]["createDate"] = $value["createDate"];
    		$list[$key]["money"] = $value["money"];
    		$list[$key]["remark"] = $value["remark"];
    		$list[$key]["createUser"] = $value["createUser"];
    		if ($value["mode"] == 0)
    		{
    			$list[$key]["mode"] = "应<font color='#FF0000'><b>收</b></font>款";
    		}
    		elseif ($value["mode"] == 1)
    		{
    			$list[$key]["mode"] = "应<font color='#0080FF'><b>付</b></font>款";
    		}
    		elseif ($value["mode"] == 2)
    		{
    			$list[$key]["mode"] = "费用";
    		}
    		else
    		{
    			$list[$key]["mode"] = "未知，如果看到这个请联系开发人员";
    		}
    	}
    	 
    	$this->assign("list",$list);
    	$this->display();
    }
    
    
    /**
     * 下载（生成）今日汇总报表
     */
    public function downloadReport()
    {
    	/** Error reporting */
    	error_reporting(E_ALL);
    	ini_set('display_errors', TRUE);
    	ini_set('display_startup_errors', TRUE);
    	date_default_timezone_set('Europe/London');
    	
    	if (PHP_SAPI == 'cli')
    		die('This example should only be run from a Web Browser');
    	
    	/** Include PHPExcel */
    	require_once(LIB_PATH."phpexcel/PHPExcel.php");
    	
    	
    	// Create new PHPExcel object
    	$objPHPExcel = new PHPExcel();
    	
    	// Set document properties
    	$objPHPExcel->getProperties()->setCreator(COMPANYNAME)
    	->setLastModifiedBy(COMPANYNAME)
    	->setTitle(date("Y-m-d")."汇总报表")
    	->setSubject(date("Y-m-d")."汇总报表")
    	->setDescription(COMPANYNAME.date("Y-m-d")."汇总报表")
    	->setKeywords("汇总报表")
    	->setCategory("汇总报表");
    	
    	$objPHPExcel->getActiveSheet()->setTitle(date("Y-m-d")."汇总报表");
    	$objPHPExcel->setActiveSheetIndex(0);
    	
    	
    	
    	/**
    	 * 添加数据
    	 */
    	//制作表头
    	$objActSheet = $objPHPExcel->setActiveSheetIndex(0);
    	
    	foreach(range('A', 'Z') as $value)
    	{
    		$objActSheet->getColumnDimension($value)->setWidth(10);
    	
    		$objActSheet->getStyle($value)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    		$objActSheet->getStyle($value)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    	}
    	 
    	$objActSheet->getColumnDimension("N")->setWidth(16);
    	$objActSheet->getColumnDimension("Q")->setWidth(16);
    	$objActSheet->getColumnDimension("R")->setWidth(16);
    	 
    	$objActSheet->getColumnDimension("D")->setWidth(18);
    	$objActSheet->getColumnDimension("E")->setWidth(18);
    	$objActSheet->getColumnDimension("G")->setWidth(20);
    	$objActSheet->getColumnDimension("H")->setWidth(20);
    	$objActSheet->getColumnDimension("S")->setWidth(12);
    	$objActSheet->getColumnDimension("T")->setWidth(25);
    	$objActSheet->getColumnDimension("U")->setWidth(25);
    	$objActSheet->getColumnDimension("V")->setWidth(10);
    	
    	$objActSheet
	    	->setCellValue('A1', '序号')
	    	->setCellValue('B1', '订单编号')
	    	->setCellValue('C1', '客户姓名')
	    	->setCellValue('D1', '创建时间')
	    	->setCellValue('E1', '打印时间')
	    	->setCellValue('F1', '商品详情')
			    	->setCellValue('F2', '序号')
			    	->setCellValue('G2', '商品名称')
			    	->setCellValue('H2', '规格')
			    	->setCellValue('I2', '数量')
			    	->setCellValue('J2', '单价')
			    	->setCellValue('K2', '金额')
			    	->setCellValue('L2', '货物总价')
	    	->setCellValue('M1', '付款信息')
			    	->setCellValue('M2', '优惠金额')
			    	->setCellValue('N2', '优惠后应收金额')
			    	->setCellValue('O2', '现金实收')
			    	->setCellValue('P2', '银行实收')
			    	->setCellValue('Q2', '本次总共实收')
			    	->setCellValue('R2', '客户本次欠付款')
	    	->setCellValue('S1', '物流信息')
			    	->setCellValue('S2', '电话')
			    	->setCellValue('T2', '客户地址')
			    	->setCellValue('U2', '停车位置')
			    	->setCellValue('V2', '车号');
    	
    	
    	
    	$objActSheet
	    	//合并左右
	    	->mergeCells('F1:L1')
	    	->mergeCells('M1:R1')
	    	->mergeCells('S1:V1');
// 	    	//合并上下
// 	    	->mergeCells('A1:A2')
// 	    	->mergeCells('B1:B2')
// 	    	->mergeCells('C1:C2')
// 	    	->mergeCells('D1:D2')
// 	    	->mergeCells('E1:E2');
    	
    	
    	$objActSheet->getStyle('A1')->getFont()->setSize(12)->setBold(true);
    	$objActSheet->getStyle('B1')->getFont()->setSize(12)->setBold(true);
    	$objActSheet->getStyle('C1')->getFont()->setSize(12)->setBold(true);
    	$objActSheet->getStyle('D1')->getFont()->setSize(12)->setBold(true);
    	$objActSheet->getStyle('E1')->getFont()->setSize(12)->setBold(true);
    	$objActSheet->getStyle('F1')->getFont()->setSize(12)->setBold(true);
    	$objActSheet->getStyle('M1')->getFont()->setSize(12)->setBold(true);
    	$objActSheet->getStyle('S1')->getFont()->setSize(12)->setBold(true);
    	
//     	$objActSheet->getStyle('F:L')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
//     	$objActSheet->getStyle('F:L')->getFill()->getStartColor()->setARGB('4A4AFF');
//     	$objActSheet->getStyle('M:R')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
//     	$objActSheet->getStyle('M:R')->getFill()->getStartColor()->setARGB('FF2D2D');
//     	$objActSheet->getStyle('S:V')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
//     	$objActSheet->getStyle('S:V')->getFill()->getStartColor()->setARGB('82D900');
		
//     	$objActSheet->getStyle('F')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//     	$objActSheet->getStyle('L')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    	
    	
    	//写数据
    	$dbTmpOrder = D("TmpOrder");
    	$dbGoods = D("Goods");
    	
    	$done = $dbTmpOrder->where("printState='7'
    				and (printDate>='".date("Y-m-d")." 00:00:00' and printDate<='".date("Y-m-d")." 23:59:59')")
    				->order('createDate')->select();
    	 /*
    	 * 已完成的订单信息
    	 */
    	$baseOffset = 3;//基准偏移量。内容第一行真正开始的位置
    	$offset = 0;//因商品多行造成的偏移量
    	 for ($i = 0; $i < count($done); $i++)//处理一个订单
    	 {
	    	 $tmpGoodsName = null;
	    	 $tmpGoodsSize = null;
	    	 $tmp = null;
	    	 
	    	 $nowRow = $baseOffset + $offset + $i;//当前订单开始的第一行的行编号（excel的）。当签订单有可能是多行，但是nowRow一定是开始的第一行
	    	 $objActSheet
	    	 ->setCellValue('A'.$nowRow, $i + 1)
	    	 ->setCellValue('B'.$nowRow,$done[$i]["id"])
	    	 ->setCellValue('C'.$nowRow,$done[$i]["customName"])
	    	 ->setCellValue('D'.$nowRow,$done[$i]["createDate"])
	    	 ->setCellValue('E'.$nowRow,$done[$i]["printDate"]);
    	
	    	 //得到商品信息
	    	 $numArray = null;
	    	 $moneyArray = null;
	    	 $sizeArray = null;
	    	 $numArray = $dbTmpOrder->getArrayWithSelf($done[$i]["goodsNumArray"]);
	    	 $moneyArray = $dbTmpOrder->getArrayWithSelf($done[$i]["goodsMoneyArray"]);
	    	 $sizeArray = $dbTmpOrder->getArrayWithSelf($done[$i]["goodsSizeArray"]);
	    	 $tmp = $dbTmpOrder->getArrayWithSelf($done[$i]["goodsIDArray"]);
	    	 for ($j = 0; $j < count($tmp); $j++)
	    	 {
	    	 		$dbGoods->init($tmp[$j]);
	    	 		$tmpGoodsName[$j] = $dbGoods->getGoodsName();
	    	 		
	    	 		//渲染规格
	    	 		$tmpSize = null;
	    	 		$tmpSize = $dbGoods->getGoodsSize();//商品规格信息
	    	 		$tmpGoodsSize[$j] = $tmpSize[$sizeArray[$j]];//选中的规格信息
	    	  }
	    	 
	    	 
	    	  //把货物信息写入
	    	  $offset = 0;
	    	  $totalMoney = 0;
	    	  foreach($tmpGoodsName as $key=>$value)
	    	  {
	    	  	
	    	  	
	    	  	$objActSheet
	    	  	->setCellValue('F'.($nowRow + $offset), $offset + 1)
	    	  	->setCellValue('G'.($nowRow + $offset), $value)
	    	  	->setCellValue('H'.($nowRow + $offset), $tmpGoodsSize[$offset])
	    	  	->setCellValue('I'.($nowRow + $offset), $numArray[$offset])
	    	  	->setCellValue('J'.($nowRow + $offset), $moneyArray[$offset])
	    	  	->setCellValue('K'.($nowRow + $offset), $numArray[$offset] * $moneyArray[$offset]);
	    	  	$totalMoney += $numArray[$offset] * $moneyArray[$offset];
	    	  	$offset++;
	    	  }
	    	  $offset--;
	    	  
	    	  //继续填一个订单第一行的数据（物流、付款信息）
	    	  $qianFuKuan = $totalMoney - $done[$i]["save"] - ($done[$i]["xianJinShiShou"] + $done[$i]["yinHangShiShou"]);
	    	  if ($qianFuKuan > 0)
	    	  	$qianFuKuanInfo = "欠我们".$qianFuKuan;
	    	  else
	    	  	$qianFuKuanInfo = "多余".(0 - $qianFuKuan);
	    	  $objActSheet
	    	  ->setCellValue('L'.$nowRow, $totalMoney)
	    	  ->setCellValue('M'.$nowRow, $done[$i]["save"])
	    	  ->setCellValue('N'.$nowRow, $totalMoney - $done[$i]["save"])
	    	  ->setCellValue('O'.$nowRow, $done[$i]["xianJinShiShou"])
	    	  ->setCellValue('P'.$nowRow, $done[$i]["yinHangShiShou"])
	    	  ->setCellValue('Q'.$nowRow, $done[$i]["xianJinShiShou"] + $done[$i]["yinHangShiShou"])
	    	  ->setCellValue('R'.$nowRow,  $qianFuKuanInfo)
	    	  ->setCellValue('S'.$nowRow, $done[$i]["tel"])
	    	  ->setCellValue('T'.$nowRow, $done[$i]["address"])
	    	  ->setCellValue('U'.$nowRow, $done[$i]["carAddress"])
	    	  ->setCellValue('V'.$nowRow, $done[$i]["carNo"]);
    	 }
    	
    	
    	
    	/**
    	 * 浏览器进行输出
    	 */
    	// Redirect output to a client’s web browser (Excel2007)
    	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    	header('Content-Disposition: attachment;filename="'.date("Y-m-d").'汇总报表.xlsx"');
    	header('Cache-Control: max-age=0');
    	// If you're serving to IE 9, then the following may be needed
    	header('Cache-Control: max-age=1');
    	
    	// If you're serving to IE over SSL, then the following may be needed
    	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    	header ('Pragma: public'); // HTTP/1.0
    	
    	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    	$objWriter->save('php://output');
    	exit;
    }
}

?>