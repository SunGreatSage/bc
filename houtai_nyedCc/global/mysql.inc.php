<?php
class dbbase_sql
{
    var $Host = "";
    var $Database = "";
    var $User = "";
    var $Password = "";
    var $Link_ID = 0;
    var $Query_ID = 0;
    var $Record = array();
	var $free = array();
    var $Row;
    var $Errno = 0;
    var $Error = "";
    var $Auto_free = 0;
    var $Auto_commit = 0;
    function connect()
    {
        if (0 == $this->Link_ID) {
            $this->Link_ID = mysql_connect($this->Host, $this->User, $this->Password);
            if ("4.1" < $this->version()) {
                global $dbcharset;
                global $charset;
                if (!$dbcharset && in_array(strtolower($charset), array(
                    "gbk",
                    "big5",
                    "utf-8"
                ))) {
                    $dbcharset = str_replace("-", "", $charset);
                }
                if ($dbcharset) {
                    mysql_query("SET NAMES '{$dbcharset}'");
                }
            }
            if ("5.0.1" < $this->version()) {
                mysql_query("SET sql_mode=''");
            }
            if (!$this->Link_ID) {
                $this->halt("Link-ID == false, Connect failed");
            }
            if (!mysql_query(sprintf("use %s", $this->Database), $this->Link_ID)) {
                $this->halt("cannot use database " . $this->Database);
            }
        }
    }
    function close()
    {
        if (0 != $this->Link_ID) {
            mysql_close($this->Link_ID);
        }
    }
    function query($Query_String)
    {
       $this->connect();
		if (!$this->Link_ID){
		  echo mysql_errno().mysql_error();
		}
        $this->Query_ID = mysql_query($Query_String, $this->Link_ID);
        $this->Row      = 0;
        if (!$this->Query_ID) {
            $this->Errno = mysql_errno();             $this->Error = mysql_error();
            $this->halt("Invalid SQL " . $Query_String . "<br>" . $this->Errno . ":" . $this->Error);
         
        }
        return $this->Query_ID;
    }
	
	function arr ($sql, $parameter)
	{
		$result = NULL;
		//$this->conn = $this->connect();
		$query = mysql_query ($sql,$this->Link_ID) or die ("Invalid query:".$sql);
		switch ($parameter)
		{
			case 0 : 
				while (!!$row = mysql_fetch_row($query)) { $result[] = $row; }
				break;
			case 1 :
				while (!!$row = mysql_fetch_assoc($query)){ $result[] = $row; }
				 break;
			case 2 : $result = mysql_affected_rows($this->Link_ID); //返回 INERT UPDATE DELETE 響應行數
				break;
			case 3 : $result = mysql_num_rows($query); 
				break;
			case 4 : $result = mysql_insert_id($this->Link_ID);
			break;

		}
		return $result;
	}
    function next_record()
    {
        $this->Record = mysql_fetch_array($this->Query_ID);
        $this->Row += 1;
        $stat = is_array($this->Record);
        if (!$stat && $this->Auto_free) {
            mysql_free_result($this->Query_ID);
            $this->Query_ID = 0;
        }
        return $stat;
    }

	function frees(){
        $this->free = mysql_free_result($this->Query_ID);
        return $this->free;
	}
    function version()
    {
        return mysql_get_server_info();
    }
    function seek($pos)
    {
        $status = mysql_data_seek($this->Query_ID, $pos);
        if ($status) {
            $this->Row = $pos;
        }
        return;
    }
    function metadata($table)
    {
        $count = 0;
        $id    = 0;
        $res   = array();
        $this->connect();
        $id = mysql_query("select * from $table limit 1");
        if ($id < 0) {
            $this->halt("Metadata query failed.");
        }
        $count = mysql_num_fields($id);
        $i     = 0;
        for (; $i < $count; $i++) {
            $res[$i]['table'] = mysql_field_table($id, $i);
            $res[$i]['name']  = mysql_field_name($id, $i);
            $res[$i]['type']  = mysql_field_type($id, $i);
            $res[$i]['len']   = mysql_field_len($id, $i);
            $res[$i]['flags'] = mysql_field_flags($id, $i);
        }
        mysql_free_result($id);
        return $res;
    }
    function affected_rows()
    {
        return mysql_affected_rows($this->Link_ID);
    }
    function num_rows()
    {
        return mysql_num_rows($this->Query_ID);
    }
    function num_fields()
    {
        return mysql_num_fields($this->Query_ID);
    }
    function nf()
    {
        return $this->num_rows();
    }
    function np()
    {
        print $this->num_rows();
    }
    function f($Name)
    {
        return $this->Record[$Name];
    }
    function p($Name)
    {
        print $this->Record[$Name];
    }
    function pos()
    {
        return $this->Row;
    }
    function instid()
    {
        return mysql_insert_id($this->Link_ID);
    }
    function halt($msg)
    {
        printf("</td></tr></table><b>Database error:</b> %s<br>\n", $msg);
        printf("<b>MySQL Error</b>: %s (%s)<br>\n", $this->Errno, $this->Error);
        exit("Session halted.");
    }
}
class sharp_sql extends dbbase_sql
{
    var $Host = "";
    var $Database = "";
    var $User = "";
    var $Password = "";
    var $Record = array();
    var $Row;
    var $Error = "";
    function sharp_sql()
    {
        global $dbHost;
        global $dbName;
        global $dbUser;
        global $dbPass;
        $this->Host     = $dbHost;
        $this->Database = $dbName;
        $this->User     = $dbUser;
        $this->Password = $dbPass;
    }
    function free_result()
    {
        return mysql_free_result($this->Query_ID);
    }
    function rollback()
    {
        return 1;
    }
    function commit()
    {
        return 1;
    }
    function autocommit($onezero)
    {
        return 1;
    }
    function insert_id($col = "", $tbl = "", $qual = "")
    {
        return mysql_insert_id($this->Query_ID);
    }
}
$pub_inc     = 1;
$databaseeng = "mysql";
$dialect     = "";
$msql        = new sharp_sql();
$fsql        = new sharp_sql();
$tsql        = new sharp_sql();
$psql        = new sharp_sql();
$msql->query("SET NAMES 'UTF8'");
?>