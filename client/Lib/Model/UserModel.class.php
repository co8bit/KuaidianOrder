<?php
class UserModel extends Model {

	private $userName = "";
	private $tmpOrderID = "";
	
	/*
	 * 错误信息
	* NOTE：	用getErrorMsg得到
	*/
	private $errorMsg = "";
	
	public function init($userName)//传入userName
	{
		$this->userName = $userName;
		$condition['userName'] = $this->userName;
		$result = $this->where($condition)->select();
		$this->tmpOrderID = $result[0]["tmpOrderID"];
	}
	
	// 自动验证设置
	protected $_validate = array(
			array('userName', 'require', '用户名不能为空！'),
			array('userName','','用户名已经存在！',0,'unique',Model::MODEL_BOTH), //验证name字段是否唯一
			array('userPassword', 'require', '密码不能为空！', 0),
			array('userPassword2', 'require', '请输入第二遍密码', 0),
			array('userPassword','userPassword2','两次输入的密码不一样',0,'confirm',Model::MODEL_BOTH), // 验证确认密码是否和密码一致
	);
	
	public function getTmpOrderID()
	{
		return $this->tmpOrderID;
	}
	
	public function getUserMoney()
	{
		$condition['userName'] = $this->userName;
		$result = $this->where($condition)->select();
		return $result[0]["money"];
	}
	
	public function getPreTmpOrderID()
	{
		$condition['userName'] = $this->userName;
		$result = $this->where($condition)->select();
		$this->preTmpOrderID = $result[0]["preTmpOrderID"];
		return $this->preTmpOrderID;
	}
	
	public function login($userPassword)
	/*
	 * 判断用户名和密码是否能登录
	 * @param string $userPassword 用户密码
	 * @return 数据库返回的结果集，数组大小应为1。外面调用形如$re[0][]
	 */
	{
		$condition['userName'] = $this->userName;
		$condition['userPassword'] = $userPassword;
		$tmp = $this->where($condition)->select();
		if ( ($tmp[0]["userPower"] == "root")
				|| ($tmp[0]["userPower"] == "admin")
				|| ($tmp[0]["userPower"] == "yyy")
			)
			return $tmp[0];
		else
			return false;
	}
	
	/*
	 * 得到所有用户的用户信息
	* @return	array;所有用户所有字段的数组
	*/
	public function getAllUserInfo()
	{
		return $this->select();
	}
	
	/*
	 * 得到指定用户的用户信息
	 * @param	string $name;用户名
	* @return	array;
	* 				查询成功返回用户所有字段的数组
	* 				没查到返回null
	* 				查询错误返回false
	*/
	public function getUserInfo($name)
	{
		$tmp = $this->where("userName=\"".$name."\"")->select();
		if ( ($tmp === false) || ($tmp === null) )
			return $tmp;
		else
			return $tmp[0];
	}
	
	/*
	 * 检查用户信息是否符合规定，并自动填充拼音
	 * @param	array $data;要检查的信息
	 * @return	
	 * 			false;信息有误，用getErrorMsg获得
	 * 			array $data;添加完拼音后的数组
	 */
	private function checkUserInfo($data)
	{
		/*
		 * 验证、预处理数据
		*/
		if ( ($data["userName"] == "") || ($data["userName"] == null) )
			return false;
		
		//拼音
		$data["userPinYin"] = getPinYinFirstChar($data["userName"]);
			
		//用户电话
		if (!isNum($data["tel"]))
		{
			$this->signErrorMsg = "用户电话不是数字";
			return false;
		}
		
		//TODO:	用户电话长度验证
		
		return $data;
	}
	
	/*
	 * 订单完成后User新建一条TmpOrder中的记录，并更新User内的tmpOrderID
	* @param	string $userName;//当前账户用户名
	* @return	bool;是否成功
	* @note:	sign中也有创建tmpOrderID的操作
	*/
	public function newTmpOrderID()
	{
		$dbTmpOrder = D("TmpOrder");
		$dbTmpOrder->init($this->getTmpOrderID());
		/*
		 * 更新将当前订单（未新建）的信息
		 * 	1.加上订单创建时间
		 */
		$tmpAdd["id"] = $this->getTmpOrderID();
		$tmpAdd["createDate"] = date("Y-m-d H:i:s");
		$tmpAddRe = $dbTmpOrder->save($tmpAdd);
		if ( ($tmpAddRe === false) || ($tmpAddRe === null) )
			return false;
		
		/*
		 * 	更新客户账户余额（money）
		 */
		//货物信息
		$tmp["id"] = $dbTmpOrder->getArray("goodsIDArray");
		$tmp["num"] = $dbTmpOrder->getArray("goodsNumArray");
		$tmp["money"] = $dbTmpOrder->getArray("goodsMoneyArray");
		$tmp["size"] = $dbTmpOrder->getArray("goodsSizeArray");
		$totalJinE = 0;
		for ($i = 0; $i < count($tmp["id"]); $i++)
		{
			$orderInfo[$i]["num"] = $tmp["num"]["$i"];
			$orderInfo[$i]["money"] = $tmp["money"]["$i"];
   
			$orderInfo[$i]["jinE"] = $orderInfo[$i]["num"] * $orderInfo[$i]["money"];//金额
			$totalJinE += $orderInfo[$i]["jinE"];
		}
		 //付款信息
		$tmpOrderInfo = $dbTmpOrder->getTmpOrderInfo();
		$yingShouJinE = $totalJinE - $tmpOrderInfo["save"];
		$benCiShiShou = $tmpOrderInfo["xianJinShiShou"] + $tmpOrderInfo["yinHangShiShou"];
		$benCiQianFuKuan = round($benCiShiShou - $yingShouJinE,2);
		
		//更新
		$tmpCustom = null;
		$tmpCustom["userName"] = $tmpOrderInfo["customName"];
		$preUserName = $this->userName;
		$this->init($tmpOrderInfo["customName"]);
		$tmpCustom["money"] = round($this->getUserMoney() + $benCiQianFuKuan,2);
		$tmpCustomRe = $this->save($tmpCustom);
		if ( ($tmpCustomRe === false) || ($tmpCustomRe === null) )
			return false;
		
		
		/*
		 *  着手创建新的TmpOrder记录
		 *  更新操作员表单数据 
		 */
		$this->init($preUserName);
		$tmpData["printState"] = 8;
		$tmp = null;
		$tmp["tmpOrderID"] = $dbTmpOrder->add($tmpData);
		if ( ($tmp["tmpOrderID"] === null) || ($tmp["tmpOrderID"] === false) )
		{
			return false;
		}
		else
		{
			$tmp["preTmpOrderID"] = $this->getTmpOrderID();
			$tmp["userName"] = $this->userName;
			$tmpRe = $this->save($tmp);
			if ($tmpRe === false)
				return false;
			else
				return true;
		}
	}
	
	/*
	 * 新用户注册
	* @param	$data;用户相关信息
	* @return	int；注册是否成功
	* 				如果数据非法或者查询错误则返回false;
	* 				如果是自增主键 则返回主键值，否则返回1
	*/
	public function sign($originData)
	{
		$data = $this->checkUserInfo($originData);
		if ($data === false)
		{
			return false;
		}
		
		$data["userPower"] = "pt";
		$data["userPassword"] = $data["tel"];
		$dbTmpOrder = D("TmpOrder");
		$tmpNewID = null;
		$tmpNewID = $dbTmpOrder->add($dbTmpOrder->prepareNewTmpOrderInfo());
		if ( ($tmpNewID === null) || ($tmpNewID === false) )
		{
			$this->errorMsg = "数据库通信失败，请重试";
			return false;
		}
		$data["tmpOrderID"] = $tmpNewID;
		$data["preTmpOrderID"] = $tmpNewID;
		
		/*
		 * 添加用户
		*/
		$re = $this->add($data);//TODO:这里如果user->add失败，则会产生一个空的多余的tmpOrder记录
		if ($re === false)
		{
			$this->errorMsg = "用户添加失败，请重试";
		}
		return $re;
	}
	
	/*
	 * 返回错误信息
	*/
	public function getErrorMsg()
	{
		return $this->errorMsg;
	}
	
	/*
	 * 更新用户信息
	 * @param	更新
	 * @return	
	 * 			int;更新成功返回更改的数量，会返回0
	 * 			false；更新失败
	 */
	public function updateInfo($data)
	{
		$tmp = $this->checkUserInfo($data);
		if ($tmp === false)
		{
			return false;
		}
		return falseOrNULL($this->save($tmp));
	}
	
}
?>