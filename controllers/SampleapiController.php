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

// use model to access database
use Bantingan\Model;
use Models;

// user mailer to send email
use Susilon\Mailer\Mail;

// please note that only the first letter of controller name and the word Controller must be capitalized
// you can set custom route in route config
// for example, this controller can also be accessed using /api path
class SampleapiController extends Base\UserController
{
  // parameter are set in url path, each parameter separated by /
  // so for this example : /api/sampledata/your_mode_choice
	public function sampledata($mode)
	{
    $dataToBeReturned;

    switch ($mode) {
      case "array":
        $dataToBeReturned = [];
        $dataToBeReturned[]["data"] = "array item 1";
        $dataToBeReturned[]["data"] = "array item 2";
        break;
      case "object":
        $dataToBeReturned = new \StdClass();
        $dataToBeReturned->dataitem = "object";
        break;
      case "database":
        // access to database using model, 
        // database connection settings are set in database.config.yml in config directory

        // using default database connection, set default table name as parameter
        $model = new Model('tabledemo');		

        // get all rows, or set query as parameter                
        $dataToBeReturned = $model->getall();
        break;
      case "database2":
        // connect to other database connection, table name parameter is optional
        $model = new Model();
        $model->selectedDB = "usermanagement"; // set connection name 

        // get all rows, or set query as parameter                
        $dataToBeReturned = $model->getall("select id, user_name, user_email from users");
        break;
      default:
        $dataToBeReturned = [
          "dataitem" => "default"
        ];
        break;
    }
    
    // this function doesn't need view html
		return $this->Json($dataToBeReturned);
	}

  public function sendemaildemo() {
    // to secure this function
    // set authjsonresponse for non page response
    // this will response { success: false, messages: 'user must login!'}, please check Base/UserController.php
    $this->authjsonresponse();
    // call requrelogin from Base\UserController
    $this->requirelogin();

    // to be used from Email Sending Demo
    if (isset($_POST["recipient_email"])) {
        $result = Mail::send([$_POST["recipient_email"]],
        MAIL_SETTINGS["MailDefaultFromEmail"], // from web.config.yml
        MAIL_SETTINGS["MailDefaultFromName"], // from web.config.yml
        "Basic Send Email Demo",
        "Hi,<br>
        This is Basic Send Email Demo sent from PHP Application."
      );

      return $this->Json($result);       
    } else {
      return $this->Json(["success" => false, "messages" => "recipient is empty"]);
    }    
  }
}