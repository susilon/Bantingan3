<?php
namespace Controllers;

use Bantingan\Controller;
use Bantingan\Model;
use Models;

class SampleformController extends Base\UserController
{
	public function index()
	{
		// function index is mandatory
		return $this->RedirectToAction("basic");
	}

	public function basic()
	{
		$model = new Model('tabledemo');		
		// select * from tabledemo
		$this->viewBag->listdata = $model->getall();		
		// highlight menu
		$this->viewBag->activeMenu = "menu-basic";
		return $this->View();
	}

	public function datatables()
	{		
		// highlight menu
		$this->viewBag->activeMenu = "menu-dt";
		return $this->View();
	}

	public function datatablesdata()
	{
		$model = new Model('tabledemo');		
		// select * from tabledemo
		$listdata = $model->getall();

		$data = [
			"data" => $listdata			
		];

		return $this->Json($data);
	}

	public function basicform($id)
	{
		// to secure this function, we call requrelogin from Base\UserController
    $this->requirelogin();

		$model = new Model('tabledemo');
		// select * from tabledemo where id=$id
		$data = $model->load($id);

		$this->viewBag->data = $data;		
		// error message come from basicformsave function
		$this->viewBag->resultMessage = $this->flash("basicformmessage");
		// highlight menu
		$this->viewBag->activeMenu = "menu-basic";
		return $this->View();
	}

	public function basicformsave()
	{
		// to secure this function, we call requrelogin from Base\UserController
    $this->requirelogin();
		
		$model = new Model('tabledemo');
		$id = "";

		// compcode is mandatory
		if (isset($_POST["form-submit"]) && $_POST["compcode"] != "") {
			// handling form submission
		
			// get existing data
			$data = $model->loadorcreate($_POST["id"]);
			$id = $_POST["id"];
			// assign new value to each column
			$data->compcode = $_POST["compcode"];
			// sample of assign value based on other value
			$data->compname = $_POST["compcode"] == "001" ? "Company 1":"Company 2";
			$data->branchcode = $_POST["branchcode"];
			$data->branchname = $_POST["branchname"];
			$data->address = $_POST["address"];
			$data->isactive = $_POST["isactive"];
			$data->username = $_POST["username"];
			$data->password = $_POST["password"];
			$data->salestarget = $_POST["salestarget"];
			$data->salesamount = $_POST["salesamount"];
			// save data
			$id = $model->save($data);
			$this->flash("basicformmessage", ["message"=>"Save data success!","class"=>"text-success"]);
		} else {
			// set error message to be displayed at basicform
			$this->flash("basicformmessage", ["message"=>"Company Code cannot be empty!","class"=>"text-danger"]);
		}

		// redirect page to form
		return $this->RedirectToAction("basicform","sampleform",$id);
	}

}