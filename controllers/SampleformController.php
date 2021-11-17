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
		// in this example we use base model to get connection to database
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
		// in this example we use model file
		$model = new Models\TabledemoModel();		
		// please check Models\TabledemoModel.php file
		// we put sql query in models, to get more readable code in controller files
		$listdata = $model->getdata("active");

		$data = [
			"data" => $listdata			
		];

		return $this->Json($data);
	}

	public function basicform($id)
	{
		// to secure this function, we call requrelogin from Base\UserController
    //$this->requirelogin();

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
    	//$this->requirelogin();
		$model = new Models\TabledemoModel();	
		$id = "";		
		
		// with example compcode is mandatory
		if (isset($_POST["form-submit"]) && $_POST["compcode"] != "") {
			// handling form submission		
			// using tabledemo model			
			// save data to database
			$id =  $model->updatedata($_POST, $_SESSION['login']['user_name'] ?? 'GUEST');			

			$this->flash("basicformmessage", ["message"=>"Save data success!","class"=>"text-success"]);
		} else {
			// set error message to be displayed at basicform
			$this->flash("basicformmessage", ["message"=>"Company Code cannot be empty!","class"=>"text-danger"]);
		}

		// redirect page to form
		return $this->RedirectToAction("basicform","sampleform",$id);
	}

}