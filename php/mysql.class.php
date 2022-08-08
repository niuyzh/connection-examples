<?php
//部分设置在此PHP中，有问题Q：529189858
class mysql{
	
	private $db_host;  //数据库主机
	private $db_user;  //数据库用户名
	private $db_pwd;   //数据库用户名密码
	private $db_database;    //数据库名
	private $conn;           //数据库连接标识;
	private $result;         //执行query命令的结果资源标识
	private $sql;	  //sql执行语句
	private $row;     //返回的条目数
	private $coding;  //数据库编码，gbk,utf8,gb2312
	private $bulletin = true;    //是否开启错误记录
	private $show_error = true;	 //测试阶段，显示所有错误,具有安全隐患,默认关闭
	private $is_error = false;   //发现错误是否立即终止,默认true,建议不启用，因为当有问题时用户什么也看不到是很苦恼的
		

	/*构造函数*/ 
	public function construct($db_host,$db_user,$db_pwd,$db_database,$conn,$coding){
     	$this->db_host=$db_host;
     	$this->db_user=$db_user;
     	$this->db_pwd = $db_pwd;
     	$this->db_database=$db_database;
     	$this->conn=$conn;
     	$this->coding=$coding;
     	$this->connect();
    }
	
	public function _construct($db_host,$db_user,$db_pwd,$conn,$coding){
     	$this->db_host=$db_host;
     	$this->db_user=$db_user;
     	$this->db_pwd = $db_pwd;
     	$this->conn=$conn;
     	$this->coding=$coding;
     	$this->connect();
    }

	/*数据库连接*/                 
	public function connect() 
	{ 
		if($this->conn=="pconn"){
			//永久链接
    		$this->conn=mysql_pconnect($this->db_host,$this->db_user,$this->db_pwd);
		}else{
			//即时链接
			$this->conn=mysql_connect($this->db_host,$this->db_user,$this->db_pwd);
		}

		if(!mysql_select_db($this->db_database,$this->conn)){
			if($this->show_error){
				$this->show_error("Error");
				exit();
			}
		}
		mysql_query("SET NAMES $this->coding");
		//mysql_query("SET NAMES utf8");
	}
	
	public function _connect() 
	{ 
		if($this->conn=="pconn"){
			//永久链接
    		$this->conn=mysql_pconnect($this->db_host,$this->db_user,$this->db_pwd);
		}else{
			//即时链接
			$this->conn=mysql_connect($this->db_host,$this->db_user,$this->db_pwd);
		}
	}
	
	/*数据库执行语句，可执行查询添加修改删除等任何sql语句*/
	public function query($sql)
	{		
		if($sql == ""){
		$this->show_error("Error");
		exit();
		} 
    	$this->sql = $sql; 
    	
    	$result = mysql_query($this->sql,$this->conn); 
    
		if(!$result){
			//调试中使用，sql语句出错时会自动打印出来
			if($this->show_error){
				$this->show_error("Error");
				exit();
			}
		}else{
			$this->result = $result; 
		}
    	return $this->result; 	  
	}
	
	/*取错误信息*/
    public function geterror()
    {
        return mysql_error();
    }	
    
	/*创建添加新的数据库*/
	public function create_database($database_name){
		$database=$database_name;
		$sqlDatabase = 'create database '.$database;
		$this->query($sqlDatabase);
	}
	
	/*查询服务器所有数据库*/
	//将系统数据库与用户数据库分开，更直观的显示？
	public function show_databases(){
		$this->query("show databases");
		echo "现有数据库：".$amount =$this->db_num_rows($rs);
		echo "<br />";
		$i=1;
		while($row = $this->fetch_array($rs)){			
			echo "$i $row[Database]";			
			echo "<br />";
			$i++;
		}
	}
	
	//以数组形式返回主机中所有数据库名 
	public function databases() 
	{ 
		$rsPtr=mysql_list_dbs($this->conn); 
		$i=0; 
		$cnt=mysql_num_rows($rsPtr); 
		while($i<$cnt) 
		{ 
		  $rs[]=mysql_db_name($rsPtr,$i); 
		  $i++; 
		} 
		return $rs; 
	}
	
	
	/*查询数据库下所有的表*/
	function show_tables($database_name){
		$this->query("show tables");
		while($row = $this->fetch_array($rs)){
			$columnName="Tables_in_".$database_name;
			$res.=$row[$columnName];
			$res.=",";
		}
		return $res;
	}
	
	/*
	mysql_fetch_row()    array  $row[0],$row[1],$row[2]
	mysql_fetch_array()  array  $row[0] 或 $row[id]
	mysql_fetch_assoc()  array  用$row->content 字段大小写敏感
	mysql_fetch_object() object 用$row[id],$row[content] 字段大小写敏感
	*/
	
	/*取得结果数据*/
	public function mysql_result_li()  
	{ 
		return mysql_result($str); 
	} 
	 
	/*取得记录集,获取数组-索引和关联,使用$row['content'] */
	public function fetch_array()  
	{		
		return mysql_fetch_array($this->result); 
	}
	
	
	//获取关联数组,使用$row['字段名']
	public function fetch_assoc() 
	{ 
		return mysql_fetch_assoc($this->result); 
	}    
	
	//获取数字索引数组,使用$row[0],$row[1],$row[2]
	public function fetch_row() 
	{ 
		return mysql_fetch_row($this->result); 
	} 
	
	//获取对象数组,使用$row->content 
	public function fetch_Object() 
	{ 
		return mysql_fetch_object($this->result); 
	}  
	
	
	
	//简化查询select 
	public function findall($table)
	{
		$this->query("SELECT * FROM $table");
	}
	
	
	//简化查询select 
	public function select($table,$columnName,$condition)
	{
		if($columnName==""){
			$columnName="*";
		}

		$this->query("SELECT $columnName FROM $table $condition");

	}
	
	function insert($table, $values) {
		$table = $this->fulltablename($table);
		$sql = "INSERT INTO {$table}";
		$keysql = '';
		$valuesql = '';
		foreach($values as $key => $value) {
			$keysql .= "`$key`,";
			$valuesql .= "'".ak_addslashes($value)."',";
		}
		$sql = $sql.'('.substr($keysql, 0, -1).')VALUES('.substr($valuesql, 0, -1).')';
		return $this->query($sql);
	}

	function update($table, $values, $where) {
		$table = $this->fulltablename($table);
		$sql = "UPDATE {$table} SET ";
		$keysql = '';
		$valuesql = '';
		foreach($values as $k => $v) {
			if(substr($v, 0, 1) == '+' && a_is_int(substr($v, 1))) {
				$sql .= "`$k`={$k}{$v},";
			} else {
				$sql .= "`$k`='".ak_addslashes($v)."',";
			}
		}
		$sql = substr($sql, 0, -1);
		$sql .= " WHERE {$where}";
		return $this->query($sql);
	}

	function delete($table, $where = '') {
		$table = $this->fulltablename($table);
		$sql = "DELETE FROM {$table}";
		if($where != '') $sql .= " WHERE {$where}";
		return $this->query($sql);
	}
		
	
	/*取得上一步 INSERT 操作产生的 ID*/
	public function insert_id(){
		return mysql_insert_id();
    }
	

	
	//指向确定的一条数据记录
	public function db_data_seek($id){
		if($id>0){
			$id=$id-1;
		}
		if(!@mysql_data_seek($this->result,$id)){
			$this->show_error("sql语句有误：", "指定的数据为空");		
		}
		return $this->result; 
	}
	
	
	// 根据select查询结果计算结果集条数 
	public function db_num_rows(){ 
		 if($this->result==null){
		 	if($this->show_error){
		 		$this->show_error("sql语句错误","暂时为空，没有任何内容！");
			}			
		 }else{
		 	return  mysql_num_rows($this->result); 
		 }
	}
	
	// 根据insert,update,delete执行结果取得影响行数 
	public function db_affected_rows(){ 
		 return mysql_affected_rows(); 
	}
	
	
	//释放结果集 
	public function free(){ 
		@mysql_free_result($this->result); 
	}
	
	//数据库选择
	public function select_db($db_database){ 
		return mysql_select_db($db_database);
	}
	
	//查询字段数量
	public function num_fields($table_name){ 
		//return mysql_num_fields($this->result);
		$this->query("select * from $table_name");
		echo "<br />";
		echo "字段数：".$total = mysql_num_fields($this->result);
		echo "<pre>";
		for ($i=0; $i<$total; $i++){
			print_r(mysql_fetch_field($this->result,$i) );
		}
		echo "</pre>";
		echo "<br />";
	}
	
	//取得 MySQL 服务器信息
	public function mysql_server($num=''){
		switch ($num){
			case 1 :
			return mysql_get_server_info(); //MySQL 服务器信息	
			break;
			
			case 2 :
			return mysql_get_host_info();   //取得 MySQL 主机信息
			break;
			
			case 3 :
			return mysql_get_client_info(); //取得 MySQL 客户端信息
			break;
			
			case 4 :
			return mysql_get_proto_info();  //取得 MySQL 协议信息
			break;
			
			default:
			return mysql_get_client_info(); //默认取得mysql版本信息
		}
	}
	
	//析构函数，自动关闭数据库,垃圾回收机制
	public function destruct()
	{
		if(!empty($this->result)){ 
			$this->free();
		}
		mysql_close($this->conn);
	}//function __destruct();

	function show_error($str,$str1){
		
		echo $str."<br>".$str1;
	
	}
	
	/*获得客户端真实的IP地址*/
	function getip(){ 
		if(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		{
			$ip = getenv("HTTP_CLIENT_IP"); 
		}
		else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
			$ip = getenv("HTTP_X_FORWARDED_FOR"); 
		}
		else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
		{
			$ip = getenv("REMOTE_ADDR"); 
		}
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){
		$ip = $_SERVER['REMOTE_ADDR']; 
		}
		else{
			$ip = "unknown"; 		
		}
		return($ip);
	}
	
	
}
?>