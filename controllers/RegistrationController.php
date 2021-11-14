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

use Susilon\PHPLogin\Login;

class RegistrationController extends Base\UserController
{
  public function userregistration()
	{
		$this->viewBag->pageTitle = "Registration Form";

		if (isset($_POST["register"])) {
			// get registration class from Base\Usercontroller
			$registration = $this->getregistration();
			if (!$registration->registerNewUser()) {
				// error message
				$this->flash("user_name", strip_tags($_POST['user_name']));
				$this->flash("user_email", strip_tags($_POST['user_email']));
				$this->flash("messages", join("<br/>",$registration->errors));				
				return $this->RedirectToAction('userregistration');	
			} else {
				// info page
				$this->flash("infoTitle","Registration Success");
				$this->flash("infoMessage", join("<br/>",$registration->messages));
				$this->flash("infoLink",$this->baseUrl."login");
				$this->flash("infoLinkTitle","Back To Login Page");
				$this->flash("pageTitle","Registration Success");
				return $this->RedirectToAction('info','home');
			}
		} else {
			$this->viewBag->returnmessage = $this->flash("messages");
			$this->viewBag->user_name = $this->flash("user_name");
			$this->viewBag->user_email = $this->flash("user_email");

			return $this->View();
		}		
	}

	public function useractivation($registrationtimestamp, $user_activation_verification_code)
	{
		$registration = $this->getregistration();
		$userinfo = explode("rEts", $registrationtimestamp);
		if (count($userinfo) < 2) {
			// info page
			$this->flash("infoTitle","Invalid Activation Code");
			$this->flash("infoMessage", "Please Request Activation Code Again");
			$this->flash("infoLink",$this->baseUrl."/resendactivationcode/".$registrationtimestamp);
			$this->flash("infoLinkTitle","Resend Activation Code");
			$this->flash("pageTitle","Invalid Code");
			return $this->RedirectToAction('info','home');
		}
		$user_creation_timestamp = $userinfo[1];
		$this->viewBag->success = $registration->verifyNewUser($user_creation_timestamp, $user_activation_verification_code);
		$this->viewBag->returnmessage = join("<br/>",$registration->errors);
		$this->viewBag->registrationtimestamp = $registrationtimestamp;
		return $this->View();
	}

	public function resendactivationcode($registrationtimestamp)
	{
		$registration = $this->getregistration();
		$userinfo = explode("rEts", $registrationtimestamp);
		$success = $registration->resendVerificationEmail($userinfo[0]);
		
		// info page
		$this->flash("infoTitle",$success?MESSAGE_VERIFICATION_MAIL_SENT:MESSAGE_VERIFICATION_MAIL_NOT_SENT);
		$this->flash("infoMessage", join("<br/>",$success?$registration->messages:$registration->errors));
		$this->flash("infoLink",$this->baseUrl."login");
		$this->flash("infoLinkTitle","Back To Login Page");
		$this->flash("pageTitle","Resend Email");
		return $this->RedirectToAction('info','home');
	}

	public function testinfo() {
		$this->flash("infoTitle","Judul Info ini");
		$this->flash("infoMessage", "Isi pesan yang harusnya panjang");
		$this->flash("infoLink",$this->baseUrl."login");
		$this->flash("infoLinkTitle","Back To Login Page");
		return $this->RedirectToAction('','info');
	}

	public function resetpassword($user_name, $verification_code)
	{
		$flashmsg = $this->flash("messages");
		$flashsuccess = $this->flash("success");

		if ($flashmsg == null) {
			// get login from Base\UserController
			$login = $this->getlogin();

			if (isset($_POST["resetPassword"])) {
				// recheck verification code
				$success = $login->verifyPasswordReset($user_name, $verification_code);	
				$this->flash("success",$success);
				if ($success) {
					$success = $login->resetPassword($user_name, $_POST["captcha"], $_POST["user_password_new"], $_POST["user_password_repeat"]);						
					$this->flash("success",!$success); // reverse to show message only page
					// info page
				}
				$this->flash("messages", join("<br/>",$success?$login->messages:$login->errors));				
				return $this->RedirectToAction('resetpassword','registration',$user_name."/".$verification_code);	
			} else {
				$success = $login->verifyPasswordReset($user_name, $verification_code);	
				$flashsuccess = $success;
				$flashmsg = join("<br/>",$success?[]:$login->errors);		
			}					
		}

		$this->viewBag->success = $flashsuccess;
		$this->viewBag->returnmessage = $flashmsg;
		$this->viewBag->user_name = $user_name;
		$this->viewBag->verification_code = $verification_code;

		return $this->View();
	}

	public function resetpasswordrequest()
	{
		$this->viewBag->pageTitle = "Reset Password Request";

		// get login from Base\UserController
		$login = $this->getlogin();

		$flashmsg = $this->flash("messages");
		$flashsuccess = $this->flash("success");

		if ($flashmsg == null) {
			$errors = count($login->errors)>0?true:false;
			$redirect = (count($login->errors)>0?true:count($login->messages)>0)?true:false;
			if ($redirect) {
				$this->flash("messages", join("<br/>",$errors?$login->errors:$login->messages));
				$this->flash("success",!$errors?count($login->messages)>0?true:false:false);
				return $this->RedirectToAction('resetpasswordrequest');
			}
		}

		$this->viewBag->success = $flashsuccess;
		$this->viewBag->returnmessage = $flashmsg;

		return $this->View();
	}

}