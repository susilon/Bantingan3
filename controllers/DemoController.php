<?php
/*
Copyright (c) <2021> Susilo Nurcahyo

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is furnished
to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

namespace Controllers;

use Bantingan\Controller;

class DemoController extends Controller
{	
  public function index($parameter=null)
	{			
    // redirected to other function
		return $this->RedirectToAction('typography');
	}	

	public function typography()
	{
		// if pageTitle aren't set, function name will be used as page title
		return $this->View();
	}

	public function tablesample()
	{
		// check how this demopath variable used in html template
		$this->viewBag->demopath = "https://templates-flatlogic.herokuapp.com/sing-app/html5/";
		return $this->View();
	}

	public function notifications()
	{
		return $this->View();
	}	

	public function tnc()
	{
		return $this->View();
	}	

	public function setflashmessage()
	{
		// flash message are, read once session memory
		// to set flash memory, parameters are : (key, value)		
		$this->flash("greetings", "Hi, I can only read once");
		// this message can be read later from any page controller, but only one time read
				
		// this function doesn't need view, because request will be redirected to 'readflashmessage()'
		return $this->RedirectToAction("readflashmessage");
	}	

	public function readflashmessage()
	{
		// if using pjax, in redirected page, set pjax url header to update url in browser address
		header('X-PJAX-URL: '.$_SERVER['REQUEST_URI']);

		// flash message are, read once session memory
		// to read message from available flash memory, parameters are : (key)
		$this->viewBag->flashmessagedemo = $this->flash("greetings");
		// after this, you cannot get the message again, try refresh this page

		$this->viewBag->activeMenu = "menu-flash";
		return $this->View();
	}	

	public function emaildemo() {
		$this->viewBag->activeMenu = "menu-email-demo";
		return $this->View();
	}

  public function pdfdemo() {
		$this->viewBag->activeMenu = "menu-pdf";
		// render HTML file using PDF converter
		if (isset($_GET["view"])) {
			return $this->DOMPDFView("Demo/htmldocument.html");
		} else if (isset($_GET["file"])) {
			return $this->DOMPDFFile("Demo/htmldocument.html");
		} else if (isset($_GET["html"])) {
			return $this->View("Demo/htmldocument.html");
		}
		// return main page
		return $this->View();
	}

  public function exceldemo() {
		$this->viewBag->activeMenu = "menu-excel";
		// render HTML file using XLSX converter
		if (isset($_GET["xls"])) {
			return $this->XLSFile("Demo/htmltabledemo.html");
		} else if (isset($_GET["html"])) {
			return $this->View("Demo/htmltabledemo.html");
		} else {
			// return main page
			return $this->View();
		}		
	}
}