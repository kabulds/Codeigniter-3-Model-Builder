<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller {

  public $tb_employee;

	public function __construct()  {
		parent::__construct(); 
    $this->load->model("tb_employee");
  }

  public function show($array) {
    echo "<pre>"; print_r($array); echo "<pre>";
  }

	public function index() {
    $this->tb_employee->query();
    $this->show($this->tb_employee->fields);
	}

	public function insert($employee_no,$employee_name) {
    //check duplicate by employee_no
    $isfound = $this->tb_employee->select(0,$employee_no);
    if ( $isfound ) {
      echo "Employee number already exist";
      return;
    };
    //insert record
    $this->tb_employee->init();
    $this->tb_employee->field['employee_no'] = $employee_no;
    $this->tb_employee->field['employee_name'] = $employee_name;
    $result = $this->tb_employee->insert();
    //error handle
    if ( $result !== "" ) {
      echo "Error : " . $result;
      return;
    };
    $this->index();
	}

	public function update($employee_id,$employee_no,$employee_name) {
    //check duplicate by employee_no
    $isfound = $this->tb_employee->select($employee_id);
    if ( !$isfound ) {
      echo "Employee not found";
      return;
    };
    //update record
    $this->tb_employee->field['employee_no'] = $employee_no;
    $this->tb_employee->field['employee_name'] = $employee_name;
    $result = $this->tb_employee->update();
    //error handle
    if ( $result !== "" ) {
      echo "Error : " . $result;
      return;
    };
    $this->index();
	}

  public function delete($employee_id) {
    //check duplicate by employee_no
    $isfound = $this->tb_employee->select($employee_id);
    if ( !$isfound ) {
      echo "Employee not found";
      return;
    };
    //delete record
    $result = $this->tb_employee->delete();
    //error handle
    if ( $result !== "" ) {
      echo "Error : " . $result;
      return;
    };
    $this->index();
	}


  
}
