<?php
class Zeai_dbbak {    
		var $_mysql_link_id;    
		var $_dataDir;    
		var $_tableList;    
		var $_TableBak;    
		function __construct($_mysql_link_id,$dataDir){
			( (!is_string($dataDir)) || strlen($dataDir)==0) && die('error:$datadir is not a string');
			!is_dir($dataDir) && mkdir($dataDir);
			$this->_dataDir = $dataDir;    
			$this->_mysql_link_id = $_mysql_link_id;    
		}    
		function backupDb($dbName,$tableName=null){    
			( (!is_string($dbName)) || strlen($dbName)==0 ) && die('$dbName must be a string value');    
			//step1:选择数据库：    
			//mysql_select_db($dbName);    
			//step2:创建数据库备份目录    
			$dbDir = $this->_dataDir.DIRECTORY_SEPARATOR.$dbName;
			!is_dir($dbDir) && mkdir($dbDir);    
		 	//step3:得到数据库所有表名 并开始备份表   
			$this->_TableBak = new TableBak($this->_mysql_link_id,$dbDir);    
			if(is_null($tableName)){//backup all table in the db    
				$this->_backupAllTable($dbName);    
				return;    
			}    
			if(is_string($tableName)){    
				(strlen($tableName)==0) && die('....');    
				$this->_backupOneTable($dbName,$tableName);    
				return;    
			}    
			if (is_array($tableName)){    
				foreach ($tableName as $table){    
				( (!is_string($table)) || strlen($table)==0 ) && die('....');    
			}    
			$this->_backupSomeTalbe($dbName,$tableName);    
				return;    
			}    
		}   
		//数据恢复
		function restoreDb($dbName,$tableName=null){    
			( (!is_string($dbName)) || strlen($dbName)==0 ) && die('$dbName must be a string value');    
			//step1:检查是否存在数据库 并连接：    
			//@mysql_select_db($dbName) || die("the database <b>$dbName</b> dose not exists");    
			
			//step2:检查是否存在数据库备份目录    
			$dbDir = $this->_dataDir.DIRECTORY_SEPARATOR.$dbName;    
			!is_dir($dbDir) && die("$dbDir not exists");    
			//step3:start restore    
			$this->_TableBak = new TableBak($this->_mysql_link_id,$dbDir);    
			if(is_null($tableName)){//backup all table in the db    
				$this->_restoreAllTable($dbName);    
				return;    
			}    
			if(is_string($tableName)){    
				(strlen($tableName)==0) && die('....');    
				$this->_restoreOneTable($dbName,$tableName);    
				return;    
			}    
			if (is_array($tableName)){    
				foreach ($tableName as $table){    
					( (!is_string($table)) || strlen($table)==0 ) && die('....');    
				}    
				$this->_restoreSomeTalbe($dbName,$tableName);    
				return;    
			}
			return true;//gyl后加的
		}    
		function _getTableList($dbName){    
			/*
			$tableList = array();    
			$result=mysql_list_tables($dbName,$this->_mysql_link_id);    
			for ($i = 0; $i < mysql_num_rows($result); $i++){    
				array_push($tableList,mysql_tablename($result, $i));  
			}    
			mysql_free_result($result);   
			return $tableList;  
			*/
			$tableList = array();   
			$tables = mysqli_query($this->_mysql_link_id,"select table_name from information_schema.tables where table_schema='$dbName'"); 
			while (list($table) = mysqli_fetch_array($tables)) {
				array_push($tableList,$table);  
				 //echo $table." <br />";
			}
			mysqli_free_result($tables);
			return $tableList;  
		}    
		function _backupAllTable($dbName){    
			foreach ($this->_getTableList($dbName) as $tableName){    
				$this->_TableBak->backupTable($tableName);    
			}    
		}    
		function _backupOneTable($dbName,$tableName){    
			!in_array($tableName,$this->_getTableList($dbName)) && die("指定的表名<b>$tableName</b>在数据库中不存在");    
			$this->_TableBak->backupTable($tableName);    
		}    
		function _backupSomeTalbe($dbName,$TableNameList){    
			foreach ($TableNameList as $tableName){    
				!in_array($tableName,$this->_getTableList($dbName)) && die("指定的表名<b>$tableName</b>在数据库中不存在");    
			}    
			foreach ($TableNameList as $tableName){    
				$this->_TableBak->backupTable($tableName);    
			}    
		}    
		function _restoreAllTable($dbName){    
			//step1:检查是否存在所有数据表的备份文件 以及是否可写：    
			foreach ($this->_getTableList($dbName) as $tableName){    
			$tableBakFile = $this->_dataDir.DIRECTORY_SEPARATOR    
						. $dbName.DIRECTORY_SEPARATOR    
						   . $tableName.DIRECTORY_SEPARATOR    
						. $tableName.'.sql';    
			!is_writeable ($tableBakFile) && die("$tableBakFile not exists or unwirteable");    
			}    
			//step2:start restore    
			foreach ($this->_getTableList($dbName) as $tableName){    
			$tableBakFile = $this->_dataDir.DIRECTORY_SEPARATOR    
						   . $dbName.DIRECTORY_SEPARATOR    
						   . $tableName.DIRECTORY_SEPARATOR    
						   . $tableName.'.sql';    
			$this->_TableBak->restoreTable($tableName,$tableBakFile);    
			}    
		}    
		function _restoreOneTable($dbName,$tableName){    
		//step1:检查是否存在数据表:    
		!in_array($tableName,$this->_getTableList($dbName)) && die("指定的表名<b>$tableName</b>在数据库中不存在");    
		//step2:检查是否存在数据表备份文件 以及是否可写：    
		$tableBakFile = $this->_dataDir.DIRECTORY_SEPARATOR    
				 . $dbName.DIRECTORY_SEPARATOR    
			  . $tableName.DIRECTORY_SEPARATOR    
				 . $tableName.'.sql';    
		!is_writeable ($tableBakFile) && die("$tableBakFile not exists or unwirteable");    
		//step3:start restore    
		$this->_TableBak->restoreTable($tableName,$tableBakFile);    
		}    
		function _restoreSomeTalbe($dbName,$TableNameList)    
		{    
		//step1:检查是否存在数据表:    
		foreach ($TableNameList as $tableName){    
		!in_array($tableName,$this->_getTableList($dbName)) && die("指定的表名<b>$tableName</b>在数据库中不存在");    
		}    
		//step2:检查是否存在数据表备份文件 以及是否可写：    
		foreach ($TableNameList as $tableName){    
		$tableBakFile = $this->_dataDir.DIRECTORY_SEPARATOR    
					   . $dbName.DIRECTORY_SEPARATOR    
					   . $tableName.DIRECTORY_SEPARATOR    
					   . $tableName.'.sql';    
		!is_writeable ($tableBakFile) && die("$tableBakFile not exists or unwirteable");    
		}    
		//step3:start restore:    
		foreach ($TableNameList as $tableName){    
		$tableBakFile = $this->_dataDir.DIRECTORY_SEPARATOR    
					   . $dbName.DIRECTORY_SEPARATOR    
					   . $tableName.DIRECTORY_SEPARATOR    
					   . $tableName.'.sql';    
		$this->_TableBak->restoreTable($tableName,$tableBakFile);    
		}    
		}    
}





//只有DbBak才能调用这个类     
class TableBak{     
		var $_mysql_link_id;     
		var $_dbDir;     
		//private $_DbManager;     
		function __construct($mysql_link_id,$dbDir){     
			$this->_mysql_link_id = $mysql_link_id;     
			$this->_dbDir = $dbDir;     
		}     
		function backupTable($tableName){     
			//step1:创建表的备份目录名： 
			$tableDir = $this->_dbDir.DIRECTORY_SEPARATOR.$tableName;    
			!is_dir($tableDir) && mkdir($tableDir);     
			//step2:开始备份：     
			$this->_backupTable($tableName,$tableDir);     
		}     
		function restoreTable($tableName,$tableBakFile){  
			@set_time_limit(0);
			$fileArray = @file($tableBakFile) or die("can open file $tableBakFile");     
			$num = count($fileArray);  
			//mysql_unbuffered_query("DELETE FROM $tableName");     
						mysqli_query($this->_mysql_link_id,"DELETE FROM $tableName"); 
			
			    
			$sql = $fileArray[0];
			mysqli_query($this->_mysql_link_id,"set names 'utf8'");//gyl
			for ($i=1;$i<$num;$i++){   
				//if ($i % 2 != 0)mysql_unbuffered_query($sql.$fileArray[$i]) or (die (mysql_error()));//gyl
							if ($i % 2 != 0)mysqli_query($this->_mysql_link_id,$sql.$fileArray[$i]);//gyl
				//mysql_unbuffered_query($sql.$fileArray[$i]) or (die (mysql_error()));     
			}
			return true;     
		}     
		function _getFieldInfo($tableName){    
			/* 
			$fieldInfo = array();     
			$sql="SELECT * FROM $tableName LIMIT 1";
			//mysqli_query("set names 'utf8'");//gyl
			$result = mysqli_query($this->_mysql_link_id,$sql);     
			$num_field=mysqli_num_fields($result);  
			for($i=0;$i<$num_field;$i++){     
				$field_name=mysql_field_name($result,$i);     
				$field_type=mysql_field_type($result,$i);     
				$fieldInfo[$field_name] = $field_type;     
			}     
			mysqli_free_result($result);     
			return $fieldInfo;     
			*/
			$fieldInfo = array();     
			$sql   = "SELECT * FROM $tableName LIMIT 1";
			$rs    = mysqli_query($this->_mysql_link_id,$sql);     
			while ($fieldinfo = mysqli_fetch_field($rs)) {
				$field_name = $fieldinfo->name;
				$field_type  = $fieldinfo->type;
				$fieldInfo[$field_name] = $field_type;
			}
			
			
			//if ($tableName == 'zeai_trend'){var_dump($fieldInfo);exit;}
			
			mysqli_free_result($rs);     
			return $fieldInfo;     
		}
		function _quoteRow($fieldInfo,$row){     
			foreach ($row as $field_name=>$field_value){     
				$field_value = strval($field_value);     
				switch($fieldInfo[$field_name]){     
					/*  
					case "blob":     $row[$field_name] = "'".mysqli_escape_string($field_value)."'";break;         
					case "string":   $row[$field_name] = "'".mysqli_escape_string($field_value)."'";break;       
					case "253":      $row[$field_name] = "'".mysqli_escape_string($field_value)."'";break;       
					case "date":     $row[$field_name] = "'".mysqli_escape_string($field_value)."'";break;       
					case "datetime": $row[$field_name] = "'".mysqli_escape_string($field_value)."'";break;       
					case "time":     $row[$field_name] = "'".mysqli_escape_string($field_value)."'";break;       
					case "unknown":  $row[$field_name] = "'".mysqli_escape_string($field_value)."'";break;         
					case "int":      $row[$field_name] = intval($field_value); break;     
					case "real":     $row[$field_name] = intval($field_value); break;     
					case "timestamp":$row[$field_name] = intval($field_value); break;     
					default:         $row[$field_name] = intval($field_value); break;   
					*/  
					//case "blob":     $row[$field_name] = "'".mysqli_escape_string($this->_mysql_link_id,$field_value)."'";break;         
					case "254":      $row[$field_name] = "'".mysqli_escape_string($this->_mysql_link_id,$field_value)."'";break;//char      
					case "253":      $row[$field_name] = "'".mysqli_escape_string($this->_mysql_link_id,$field_value)."'";break;//varchar   
					case "252":      $row[$field_name] = "'".mysqli_escape_string($this->_mysql_link_id,$field_value)."'";break;//text  
					case "10":       $row[$field_name] = "'".mysqli_escape_string($this->_mysql_link_id,$field_value)."'";break;//data    
					//case "datetime": $row[$field_name] = "'".mysqli_escape_string($this->_mysql_link_id,$field_value)."'";break;
					case "unknown":  $row[$field_name] = "'".mysqli_escape_string($this->_mysql_link_id,$field_value)."'";break;         
					case "3":        $row[$field_name] = intval($field_value); break;//int
					case "1":        $row[$field_name] = intval($field_value); break;//tinyint   
					case "2":        $row[$field_name] = intval($field_value); break;//smallint   
					//case "timestamp":$row[$field_name] = intval($field_value); break;     
					default:         $row[$field_name] = intval($field_value); break;     
				}     
			}     
			return $row;     
		}    
		function _backupTable($tableName,$tableDir){     
			//取得表的字段类型：     
			$fieldInfo = $this->_getFieldInfo($tableName);     
			//step1:构造INSERT语句前半部分 并写入文件：     
			$fields = array_keys($fieldInfo);     
			$fields = implode(',',$fields);     
			$sqltext="INSERT INTO $tableName($fields)VALUES \r\n";     
			$datafile = $tableDir.DIRECTORY_SEPARATOR.$tableName.'.sql';     
			(!$handle = fopen($datafile,'w')) && die("can not open file <b>$datafile</b>"); 
			
			@set_time_limit(0);     
			$sql = "select * from $tableName";    
			
			mysqli_query($this->_mysql_link_id,"set names 'utf8'");//gyl
			 
			$result = mysqli_query($this->_mysql_link_id,$sql);     
			//打开数据备份文件:$tableName.xml     
			$datafile = $tableDir.DIRECTORY_SEPARATOR.$tableName.'.sql';     
			(!$handle = fopen($datafile,'a')) && die("can not open file <b>$datafile</b>"); 
				
			//逐条取得表记录并写入文件： 
			
			   
			while ($row = mysqli_fetch_assoc($result)) {     
			$row = $this->_quoteRow($fieldInfo,$row);      
			$record='(' . implode(',',$row) . ");\r\n"; 
			
			$record=$sqltext.$record;
			   
			(!fwrite($handle, $record))   && die("can not write data to file <b>$datafile</b>");     
			}    
			mysqli_free_result($result);     
			//关闭文件：     
			fclose($handle);     
			return true;     
		}     
}     
?> 