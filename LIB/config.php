<?php
class DBConnector{
	function __construct($db="school_report_db",$server="127.0.0.1",$user="root",$password=""){
		$con = mysql_connect($server,$user,$password);
		return mysql_select_db($db,$con);
	}
	public function select1cell($tbl,$field,$condition=null,$return_data=true){
		$sql = "SELECT `".$field."` FROM `".$tbl."`";
		if($condition !=null && count($condition) >0){
			$sql .= " WHERE";
			foreach($condition as $key=>$value){
				if(is_array($value)){
					$sql .= " && `".$key."`".$value['sign']."'".$value['value']."'";
				} else{
					$sql .= " && `".$key."` = '".$value."'";
				}
			}
		}
		$sql = preg_replace("/WHERE &&/","WHERE ",$sql);
		#echo $sql."<br>";
		$result = mysql_query($sql)or die(mysql_error());
		if($result){
			$res = mysql_fetch_array($result,MYSQL_ASSOC);
			if($return_data === true)return $res[$field];
			else return $res;
		}
		return null;
	}
	public function selectFields($tbl,$field,$condition=null,$limit=null,$order="",$indexed=true,$sign='=',$multiplereference=false,$distinct=false){
		if(count($field) < 1) return null;
 		$sql = "SELECT ";
		if($distinct) $sql .= "DISTINCT "; $count=0;
		foreach($field as $value){
			if($count != 0) $sql .= ", ";
			if($value == '*') $sql .= " * ";
			else $sql .= " `".$value."`";
			$count++;
		}
		$sql .= " FROM `".$tbl."`";
		if($condition != null){
			$sql .= " WHERE";# var_dump($condition);
			foreach($condition as $key=>$value){
				#var_dump($multiplereference);
				if($multiplereference){
					#echo "OK";
					foreach($value as $value2){
						if(!preg_match("/^LIKE/",$value2) && !preg_match("/^NOT LIKE/",$value2)) $sql .= " && `".$key."` ".$sign." \"".$value2."\"";
						else $sql .= " && `".$key."` ".$value2;
					}
				} else{
					#echo "OK";
					if(is_array($value)){
						$sql .= "&& `".$key."`".$value['sign']."\"".$value['value']."\"";
					} else{
						if(!preg_match("/^LIKE/",$value) && !preg_match("/^NOT LIKE/",$value))$sql .= " && `".$key."` ".$sign." \"".$value."\"";
						else $sql .= " && `".$key."` ".$value;
					}
				}
			}
		}
		$sql = preg_replace(array("/WHERE &&/","/SELECT ,/"),array("WHERE ","SELECT "),$sql);
		if($order !="") $sql .= " ".$order." ";
		if($limit != null && count($limit) == 2) $sql .= " LIMIT ".$limit[0].", ".$limit[1];
		#echo $sql."<br>";
		$result = mysql_query($sql)or die(mysql_error());
		if($result){
			$res; $rtn=array(); $count=0;
			if($indexed == true){
				while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
					$rtn[$count] = $row;
					$count++;
				}
			}
			if($indexed == false){
				while($row = mysql_fetch_array($result,MYSQL_NUM)){
					$rtn[$count] = $row;
					$count++;
				}
			}
			return $rtn;
		}
		return null;
	}
	public function selectInMoreTable($lbl,$multirows=false,$indexed=false, $order=""){
		$sql = "SELECT "; $tbl = $lbl['tbl']; $condition = $lbl['condition']; $field = $lbl['fld'];
		#try to extract all fields from first table
		for($i=0;$i<count($tbl);$i++){
			$fld = $field[$tbl[$i]];
			foreach($fld as $value) $sql .= "`".$tbl[$i]."`.`".$value."`, ";
		}
		$sql .= "FROM ";
		foreach($tbl as $value) $sql .= "`".$value."`, ";
		$sql .= "WHERE ";
		#var_dump($condition); return;
		foreach($condition as $key=>$value){
			if(is_array($value)){
				#$sql .= "&& `".$key."`".$value['sign']."'".$value['value']."'";
				if(!preg_match('/`/',$value)) $sql .= "&& `".$key."`".$value['sign']."'".$value."' ";
				else $sql .= "&& `".$key."`".$value['sign']."`".$value."` ";
			} else{
				if(!preg_match('/`/',$value)) $sql .= "&& `".$key."`='".$value."' ";
				else $sql .= "&& `".$key."`=`".$value."` ";
			}
		}
		$look_for = array("/, FROM/","/, WHERE/","/WHERE &&/");
		$replace_with = array(" FROM"," WHERE","WHERE ");
		$sql .= $order;
		$sql = preg_replace($look_for,$replace_with,$sql);
		#echo $sql."<br><br>";
		$result = mysql_query($sql)or die("Invalid data provided ".mysql_error());
		#start out put
		if($multirows == false && $indexed == false) return mysql_fetch_array($result,MYSQL_NUM);
		if($multirows == false && $indexed == true) return mysql_fetch_array($result,MYSQL_ASSOC);
		if($multirows == true){
			$dt = array(); $count=0;
			if($indexed == false){
				while($row = mysql_fetch_array($result,MYSQL_NUM)){
					$dt[$count] = $row;
					$count++;
				}
			}
			if($indexed == true){
				while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
					$dt[$count] = $row;
					$count++;
				}
			}
			return $dt;
		}
	}
	public function InsertIfNotExist($tbl,$data,$condition,$auto_increment=true){
		/*check if to inset or not*/
		$insert = true;
		if($condition != NULL && count($condition)>0){
			$check = "SELECT * FROM `".$tbl."` WHERE ";
			foreach($condition as $key=>$value){
				if($value == 'NOW()')$check .= "&& `".$key."`=".$value." ";
				else $check .= "&& `".$key."`='".$value."' ";
			}
			$check = preg_replace("/WHERE &&/","WHERE",$check);
			#echo $check;
			$res = mysql_query($check)or die(mysql_error());
			#var_dump(mysql_num_rows($res)); echo "<br><br>";
			if(mysql_num_rows($res)>0) $insert = false;
		}
		if($insert == true){
			$sql = "INSERT INTO `".$tbl."` SET ";
			if($auto_increment) $sql .= " ID=NULL";
			foreach($data as $key=>$value){
				if($value == "NOW()") $sql .= ", `".$key."`=".$value."";
				else $sql .= ", `".$key."`='".$value."'";
			}
			$sql = preg_replace('/SET ,/','SET',$sql);
			#echo $sql;
			if(mysql_query($sql)or die(mysql_error())){
				return true;
			} else return false;
		}
	}
	public function InsertOrUpdate($tbl,$data,$id_increment=true,$condition=null,$referencefield="ErrorCount",$replace=false){
		$check = 1; $sql="";
		if($condition != null){
			$sql = "SELECT * FROM `".$tbl."` WHERE ";
			foreach($condition as $a=>$b){
				$sql .= "&& `".$a."`='".$b."'";
			}
			$sql = preg_replace("/WHERE &&/","WHERE",$sql);
			#echo $sql;
			$result = mysql_query($sql)or die(mysql_error());
			if($result &&  mysql_num_rows($result) > 0 ) $check = 2;
		}
		#echo $check;
		if($check == 1){
			$sql = "INSERT INTO `".$tbl."` SET ";
			if($id_increment == true) $sql .="`ID`=NULL";
			foreach($data as $key=>$value){
				if($value == "NOW()") $sql .= ", `".$key."`=".$value."";
				else $sql .= ", `".$key."`='".$value."'";
			}
			$sql = preg_replace('/SET ,/','SET ',$sql);
		}
		elseif($check == 2){
			$sql = "UPDATE `".$tbl."` SET ";
			foreach($data as $key=>$value) {
				if($key == $referencefield && !$replace){
					if(is_numeric($value))$sql .= ", `".$key."`=".$key."+".$value."";
					else{
						$ext = DBConnector::select1cell($tbl,$key,$condition,true,false);
						#var_dump($ext);
						$sql .= ", `".$key."`=\"".trim($ext." ".$value)."\"";
					}
				}
				else{
					if($value == "NOW()") $sql .= ", `".$key."`=".$value."";
					else $sql .= ", `".$key."`='".$value."'";
				}
			}
			$sql .= " WHERE ";
			foreach($condition as $key=>$value) $sql .= "&& `".$key."`='".$value."'";
			$look_for = array("/SET ,/","/WHERE &&/");
			$replace_with = array("SET ","WHERE");
			$sql = preg_replace($look_for,$replace_with,$sql);
		}
		#echo $sql;return;
		if(mysql_query($sql)or die(mysql_error())) return true;
		else return false;
	}
	public function createTable($tbl,$fields){
		$sql = "CREATE TABLE IF NOT EXISTS `".$tbl."` (";
		for($i=0; $i<count($fields)-1; $i++){
			$field = $fields[$i];
			$sql .= "`".$field['NAME']."` ".$field['TYPE'];
			if(preg_match("/TEXT/",$field['TYPE'])) $sql .= " CHARACTER SET ".$field['CHARACTER_SET']." COLLATE ".$field['COLLATE'];
			if($field['LENGTH'] != null) $sql .= "(".$field['LENGTH'].")";
			if($field['NOT_NULL'] == true) $sql .= " NOT NULL ";
			else $sql .= " NULL ";
			if($field['AUTO_INCREMENT'] == true) $sql .= "AUTO_INCREMENT ";
			if($field['PRIMARY_KEY'] == true) $sql .= "PRIMARY KEY ";
			if($i < count($fields)-2) $sql .= ", ";
		}
		if($fields['UNIQUE'] != null){
			$sql .= ", ";
			$unique = $fields['UNIQUE'];
			if(count($unique)>0) $sql .= "UNIQUE(";
			foreach($unique as $key=>$value){
				if($key>0)$sql .= ", `".$value."` ";
				if($key == 0)$sql .= "`".$value."`";
			}
			if(count($unique)>0) $sql .= ")";
		}
		$sql .= ")";
		#echo $sql; return;
		mysql_query($sql)or die(mysql_error());
	}
	public function selectOneRowFromTable($tbl,$condition,$indexed=false){
		$sql = "SELECT*FROM `".$tbl."` WHERE ";
		foreach($condition as $key=>$value) $sql .= "&& `".$key."`='".$value."'";
		$sql = preg_replace("/WHERE &&/","WHERE ",$sql);
		#$sql;
		$res = mysql_query($sql)or die(mysql_error());
		if($res){
			if($indexed === false) return mysql_fetch_array($res,MYSQL_NUM);
			if($indexed === true) return mysql_fetch_array($res,MYSQL_ASSOC);
		}
		return null;
	}
	public function DropColomn($table,$colomn){
		if(!$table || !$colomn) return false;
		$sql = "ALTER TABLE `".$table."`";
		if(is_array($colomn)){
			for($i=0;$i<count($colomn);$i++){
				if($i>0) $sql .= ", ";
				$sql .= " DROP `".$colomn[$i]."`";
			}
		} else $sql .= " DROP `".$colomn."`";
		mysql_query( $sql);
	}
	public function AddColomn($table,$data){
		if(!$table || !$data) return false;
		$sql = "ALTER TABLE `".$table."`";
		for($i=0;$i<count($data);$i++){
			if($i>0) $sql .= ",";
			$sql .= " ADD ";
			$sql .= "`".$data[$i]['NAME']."` ".$data[$i]['TYPE'];
			if(preg_match("/TEXT/",$data[$i]['TYPE'])) $sql .= " CHARACTER SET ".$data[$i]['CHARACTER_SET']." COLLATE ".$data[$i]['COLLATE'];
			if($data[$i]['LENGTH'] != null) $sql .= "(".$data[$i]['LENGTH'].")";
			if($data[$i]['NOT_NULL'] == true) $sql .= " NOT NULL ";
			if(@$data[$i]['DEFAULT'] == true) $sql .= " DEFAULT '".$data[$i]['DEFAULT']."'";
			if(@$data[$i]['AUTO_INCREMENT'] == true) $sql .= "AUTO_INCREMENT ";
			if(@$data[$i]['PRIMARY_KEY'] == true) $sql .= "PRIMARY KEY ";
		}
		if(mysql_query($sql)) return true;
		else return false;
	}

	public function InsertData($tbl,$data,$id_increment=true){
		$sql = "INSERT INTO `".$tbl."` SET ";
		if($id_increment == true) $sql .="`ID`=NULL";
		foreach($data as $key=>$value){
			if($value == 'NOW()') $sql .= ", `".$key."`=".$value ;
			else $sql .= ", `".$key."`=\"".$value."\"";
		}
		$sql = preg_replace('/SET ,/','SET ',$sql);
		#echo $sql;
		if(mysql_query($sql)or die(mysql_error())) return true;
		else return false;
	}
	public function delete1row($tbl=null,$condition=null){
		if($tbl == null) return null;
		$sql = "DELETE FROM `".$tbl."`";
		if(count($condition) > 0){
			$sql .= " WHERE ";
			foreach($condition as $field=>$value) $sql .= "&& `".$field."`='".$value."'";
			$sql = preg_replace("/WHERE &&/","WHERE",$sql);
			#echo $sql;
		}
		mysql_query($sql);
	}
	public function updateCells($data=null,$tbl="",$condition=null){
		if($tbl == "" || $data == null || count($data) <1) return null;
		$sql = "UPDATE `".$tbl."` SET";
		foreach($data as $fld=>$value){
			#var_dump($value);
			if($value == null) $sql .= " ,`".$fld."`= NULL ";
			else $sql .= " ,`".$fld."`='".$value."' ";
		}
		if($condition != null){
			$sql .= "WHERE ";
			foreach($condition as $field=>$value) $sql .= ", `".$field."`='".$value."'";
		}
		$sql = preg_replace(array('/SET ,/','/WHERE ,/'),array('SET ','WHERE '),$sql);
		#echo $sql;
		//return;
		if(mysql_query($sql)or die(mysql_error())) return true;
		else return false;
	}
	public function emptyTables($tbl){
		mysql_query("TRUNCATE TABLE `".$tbl."`");
	}
	public function selectAllInTable($tbl,$indexed=false,$condition=null ,$order=""){
		if($tbl == "" || $tbl == null) return null;
		$sql = "SELECT * FROM `".$tbl."` ";
		if($condition != null && count($condition)>0) {
			$sql .= "WHERE ";
			foreach($condition as $key=>$value) $sql .= "&& `".$key."`='".$value."' ";
		}
		if($order != "") $sql .= $order;
		$sql = preg_replace('/WHERE &&/','WHERE',$sql);
		#echo $sql;
		$rs = mysql_query($sql)or die(mysql_error());
		$res = array();
		if($rs && mysql_num_rows($rs) >0){
			$i=0;
			if($indexed == false){
				while($row = mysql_fetch_array($rs,MYSQL_NUM)){
					$res[$i] = $row;
					$i++;
				}
			}
			if($indexed == true){
				while($row = mysql_fetch_array($rs,MYSQL_ASSOC)){
					$res[$i] = $row;
					$i++;
				}
			}
			return $res;
		}
		return null;
	}
	public function selectMax($tbl,$fld,$rtn=false, $condition=null,$multiplereference=false){
		$sql = "SELECT MAX(`".$fld."`) FROM `".$tbl."` ";
		if($condition != null){
			$sql .= "WHERE";
			if($multiplereference){
				foreach($condition as $key=>$value ){
					foreach($value as $value3){
						if(preg_match('/^LIKE/',$value3) || preg_match('/^NOT LIKE/',$value3)) $sql .= " && `".$key."` ".$value3."";
						else $sql .= " && `".$key."`='".$value3."'";
					}
				}
			} else{
				foreach($condition as $key=>$value ){
					if(preg_match('/^LIKE/',$value)) $sql .= " && `".$key."` ".$value."";
					else $sql .= " && `".$key."`='".$value."'";
				}
			}
		}
		$sql = preg_replace("/WHERE &&/","WHERE",trim($sql));
		#echo $sql;# return;
		$result = mysql_query($sql);
		$res = mysql_fetch_array($result,MYSQL_NUM);
		if($rtn == true) return $res;
		if($rtn == false) return $res[0];
		return null;
	}
	public function checkTable($tbl){
		$check = mysql_query("SHOW TABLES;");
		#var_dump($check);
		$tables = array();
		while($table = mysql_fetch_array($check,MYSQL_NUM)){
			$tables[] = $table[0];
		}
		#var_dump($tables); echo "<br>";
		if(in_array($tbl,$tables)) return true;
		else return false;
	}
	public function selectMin($tbl,$fld,$rtn=false,$condition=null){
		$sql = "SELECT MIN(`".$fld."`) FROM `".$tbl."`";
		if($condition != null){
			$sql .= " WHERE "; $i=0;
			foreach($condition as $field=>$data){
				if($i!=0) $sql .= "&& ";
				$sql .= " `".$field."`='".$data."'";
				$i++;
			}
		}
		#echo $sql;
		$result = mysql_query($sql) or die('error: '.mysql_error());
		$res = mysql_fetch_array($result,MYSQL_NUM);
		if($rtn == true) return $res;
		if($rtn == false) return $res[0];
		return null;
	}
	public function DropTable($tbl){
		if($tbl == null || $tbl='') return null;
		mysql_query("DROP TABLE `{$tbl}`");
	}
	function selectCount($tbl,$fld,$condition=null){
		if($tbl == null || $tbl=='' || $fld == null || $fld =='') return null;
		$sql = "SELECT COUNT(`".$fld."`) FROM `".$tbl."`";
		if($condition != null){
			$sql .= " WHERE "; $i=0;
			foreach($condition as $field=>$data){
				if($i!=0) $sql .= "&& ";
				$sql .= " `".$field."`='".$data."'";
				$i++;
			}
		}
		#echo $sql;
		$result = mysql_query($sql);
		$res = mysql_fetch_array($result,MYSQL_NUM);
		return $res[0];
		return null;
	}
}
?>
