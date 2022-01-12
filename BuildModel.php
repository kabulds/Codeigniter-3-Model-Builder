<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BuildModel extends CI_Controller {

	public function __construct()
  {
      parent::__construct();
  }

  public function index() { 
    $this->load->database();
    $this->load->helper('url');
    $this->page_start();
    $tab = chr(9);
    $table = $this->query("show tables");
    $maxline = round(count($table)/4,PHP_ROUND_HALF_UP);
    $line = 0;
    echo "<h4 class='text-center mt-3'>Codeigniter 3 Model Builder</h4>";
    echo "<div class='card'>";
    echo "<div class='card-header'>";
    echo "<div class='card-title'>Select table</div>";
    echo "</div>";
    echo "<div class='card-body row'>";
        foreach ( $table as $tbl ) {
          echo "<div class='col-sm-3'>";
          $tblname = $tbl["Tables_in_" . $this->db->database];
          echo '<a href="' . base_url() . "index.php/buildmodel/?table=" . $tblname . '" target="_self" >';
          echo $tblname . "</a></div>";
        }
    echo '</div>';
    if ( isset($_GET["table"]) ) {
      $tablename = $_GET["table"];
      $filename = $this->save_class($tablename);
      echo "<div class='card-footer'>";
      echo "<b>File create on </b> :" . $filename;
      echo "</div>";
    };
    echo "</div>";
    $this->page_end();
  }

  public function query($sql) {
    $temp = $this->db->query($sql);
    $result = array();
    if ( $temp->num_rows()>0) {
      $result = $temp->result_array();
    };
    return $result;
  }

  public function all() {
    $table = $this->query("show tables");
    foreach ( $table as $tbl ) {
      $tblname = $tbl["Tables_in_" . $this->db->database];
      $filename = $this->save_class($tblname);
      echo $filename . "</br>";
    };
  }
  public function page_start() {
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<meta http-equiv="x-ua-compatible" content="ie=edge">';
    echo '<title>Class tools</title> ';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">';
    echo '</head>';
    echo '<body>';
    echo '<div class="container">';
  }
  public function page_end() {
    echo '</div>';
    echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>';
    echo '<body>';
  }


  public function getdefault($fld) {
    $type = $fld['Type']; $pos = strpos($type,"(",0); 
    if ( $pos > 0 ) {
      $type = substr($type,0,$pos);
    };
    $result = '""'; 
    $pos = strpos("nothing|int|float|decimal|double|real:bit|boolean", $type, 0);
    if ( $pos > 0 ) { $result = "0"; };
    if ( $type == "datetime" ) { $result = 'date("Y-m-d h:i:s")'; };
    if ( $type == "date" ) { $result = 'date("Y-m-d")'; };
    if ( $type == "tinyint" ) { $result = true; };
    if ( $fld['Field'] == "lastupdate" ) { $result = 'date("Y-m-d h:i:s")'; };
    return $result;
  }

  function root_path() {
    $path = explode("/",$_SERVER["SCRIPT_FILENAME"]);
    $result = "";
    for ($x = 0; $x <= count($path)-2; $x++) {
      $result = $result . $path[$x] . "/";
    };
    return $result;
  }

  public function addline($file, $ntab =0, $txt = "", $enter = 1) {
    $txt = str_repeat(chr(9), $ntab) . $txt . str_repeat(chr(13), $enter)  ;
    fwrite($file,$txt);
  }

  public function save_class($tblname) {

    $tab = chr(9);
    $crlf = chr(13);
    $path = explode("/",$_SERVER["SCRIPT_FILENAME"]);
    $filename = "";
    for ($x = 0; $x <= count($path)-2; $x++) {
      $filename = $filename . $path[$x] . "/";
    };
    $classname = strtoupper(substr($tblname,0,1)) . substr($tblname,1,strlen($tblname)-1);
    $filename = $filename . "application/models/" . $classname . ".php";
    if ( file_exists($filename) ) {
      unlink($filename);
    };
    $myfile = fopen($filename, "w") or die("Unable to open file!");
    $hasid = false;

    $this->addline($myfile,0,"<?php");
    $this->addline($myfile,0,"/*");
    $this->addline($myfile,0,"Codeigniter 3 Model Builder");
    $this->addline($myfile,0,"Table Name : " . $tblname);
    $this->addline($myfile,0,"Create by  : Kabul Dedi Sutrisno");
    $this->addline($myfile,0,"Create at  : " . date("Y-m-d h:i:s"));
    $this->addline($myfile,0,"*/");
    $this->addline($myfile,0,"defined('BASEPATH') OR exit('No direct script access allowed');");
    $this->addline($myfile);

    $this->addline($myfile,0,"class " . strtoupper(substr($tblname,0,1)) . substr($tblname,1,strlen($tblname)) . " extends CI_Model {",2) ;
      $this->addline($myfile);
      $this->addline($myfile,1,'public $fields = array();');
      $this->addline($myfile,1,'public $field = array();');  
      $this->addline($myfile);
      $this->addline($myfile,1,"public function __construct()  {");
        $this->addline($myfile,2,"parent::__construct();");  
        $this->addline($myfile,2,'$this->init();');
      $this->addline($myfile,1,"}");
      $this->addline($myfile);
      $field = $this->query("show columns from " . $tblname);
      $index = $this->query("SHOW INDEXES IN " . $tblname);
			$default_value = array();
      // init
      $this->addline($myfile,1,'public function init() {');
        $this->addline($myfile,2,'$' . 'this->fields = array();');
        $this->addline($myfile,2,'$' . 'this->field = array(');
        foreach ( $field as $fld ) {
					if ( $fld['Field'] == "companyid"  ) { $hasid = true; };
					if (( $tblname == "tb_company") or ( $tblname == "tb_user" ) ) {$hasid = false; };
					$default = $this->getdefault($fld);
          $this->addline($myfile,3,'"' . $fld['Field'] . '" => ' . $default . ",");
          $default_value[$fld['Field']]=$default;
        };
        $this->addline($myfile,2,');');
        $this->addline($myfile,1,'}');
      $this->addline($myfile);

      $key = "";      
      foreach ( $index as $i ) {
        if ( $tblname !== "tb_company" ) { 
          if ( $i['Column_name'] !== "companyid" ) {
            $key = $key . "$" . $i['Column_name'] . "=" . $default_value[$i['Column_name']] . ",";
          };
        } else {
          $key = $key . "$" . $i['Column_name'] . "=" . $default_value[$i['Column_name']] . ",";
        }
      };
      $key=substr($key,0,strlen($key)-1);

      $filter = '$filter = "';
			$primary = "";
			$unique = "";
      foreach ( $index as $i ) {
				if ( $i['Key_name'] == "PRIMARY") {
					$primary = $primary . $i['Column_name'] . "='" . '" . $' . $i['Column_name'] . " . " . '"' . "' and ";
				} else {
					$unique = $unique . $i['Column_name'] . "='" . '" . $' . $i['Column_name'] . " . " . '"' . "' and ";
				}
      };
			$primary = substr($primary,0,strlen($primary)- 5 );
			if ( $unique == "" ) {
				$filter = $primary;
			} else {
				$unique = substr($unique,0,strlen($unique)- 5 );
				$filter = "(" . $primary . ") or (" . $unique . ")";
			};
      $filter = '$filter="' . $filter . '";';

      //query
      $this->addline($myfile,1,'public function query(' . '$' . 'filter = "",' . '$' . 'sort = "") {');
        $this->addline($myfile,2,'$' . 'this->init();');
        $this->addline($myfile,2,'$' . 'sql = "select * from ' . $tblname . '" ;');
        $this->addline($myfile,2,'if ( $' .'filter !== "" ) { $' .'sql = $' .'sql . " where " . $' .'filter; };');
        $this->addline($myfile,2,'if ( $' .'sort !== "" ) { $' .'sql = $' .'sql . " order by " . $' .'sort; };');
        $this->addline($myfile,2,'$' .'result = $' .'this->db->query($' .'sql);');
        $this->addline($myfile,2,'if ($' .'result->num_rows()>0) {');
          $this->addline($myfile,3,'$' .'this->fields = $' .'result->result_array();');
        $this->addline($myfile,2,'};');
        $this->addline($myfile,2,'return $' .'this->fields;');
        $this->addline($myfile,1,'}');
      $this->addline($myfile);


      //select
      $this->addline($myfile,1,'function select(' . $key . ") {");
        $this->addline($myfile,2,"$". "this->init();");
        if ( $hasid ) {
          $this->addline($myfile,2,'$companyid = current_user("companyid");');
        };
        $this->addline($myfile,2,$filter);
        $this->addline($myfile,2,"$" . "this->query(" ."$" ."filter,'');");
        $this->addline($myfile,2,"if ( count(" . "$" ."this->fields)>0 ) {");
          $this->addline($myfile,3,'$' . 'this->field = ' . '$' . 'this->fields[0];');
          $this->addline($myfile,3,'return true;');
        $this->addline($myfile,2,"};");
        $this->addline($myfile,2,"return false;");
      $this->addline($myfile,1,'}');
      $this->addline($myfile);
      
      //insert
      $this->addline($myfile,1,"public function insert() {");
      if ( $hasid ) {
        $this->addline($myfile,2,'$this->field["companyid"] = current_user("companyid");');
      };
      $this->addline($myfile,2,'$result = $this->db->insert("'. $tblname . '",$this->field);');
      foreach ( $field as $f ) {
        if ( $f['Extra'] == "auto_increment" ) {
          $this->addline($myfile,2,'$this->field["' . $f["Field"] . '"] = ( $result == true )  ? $this->db->insert_id() : 0;');
        };
      };
      $this->addline($myfile,2,'return ( $result == true ) ? "" : $this->db->error()["message"];');
      $this->addline($myfile,1,"}");
      $this->addline($myfile);

      //update
      $this->addline($myfile,1,"public function update() {");
      if ( $hasid ) {
        $this->addline($myfile,2,'$this->field["companyid"] = current_user("companyid");');
      };  
      foreach ( $index as $i ) {
				if ( $i['Key_name'] == "PRIMARY") {
		      $this->addline($myfile,2,'$this->db->where("' . $i['Column_name'] . '",$this->field["' . $i['Column_name'] . '"]);');
				};
      };
      $this->addline($myfile,2,'$result = $this->db->update("'. $tblname . '",$' . 'this->field);');
      $this->addline($myfile,2,'return ( $result == true ) ? "" : $this->db->error()["message"];');
      $this->addline($myfile,1,"}");
      $this->addline($myfile);

      //delete
      $this->addline($myfile,1,"public function delete() {");
      foreach ( $index as $i ) {
				if ( $i['Key_name'] == "PRIMARY") {
					$this->addline($myfile,2,'$' . 'this->db->where("' . $i['Column_name'] . '",$this->field["' . $i['Column_name'] . '"]);');
				};
      };
      $this->addline($myfile,2,'$result = $this->db->delete("'. $tblname . '");');
      $this->addline($myfile,2,'return ( $result == true ) ? "" : $this->db->error()["message"];');
      $this->addline($myfile,1,"}");
      $this->addline($myfile);

      //execute
      $this->addline($myfile,1,"public function execute($" . "action) {");
      $this->addline($myfile,2,'switch ( $' . 'action ) {');
      $this->addline($myfile,3,"case 'insert': return $" . "this->insert(); break ;");
      $this->addline($myfile,3,"case 'update': return $" . "this->update(); break ;");
      $this->addline($myfile,3,"case 'delete': return $" . "this->delete(); break ;");
      $this->addline($myfile,2,'};');
      $this->addline($myfile,1,"}");
      $this->addline($myfile);

      $this->addline($myfile,0,"}");
      $this->addline($myfile);
      $this->addline($myfile,0,"?>");

      fclose($myfile);
      
      return $filename;
  }

}
