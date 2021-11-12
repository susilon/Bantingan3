<?php
/*
Replace existing PHP session with load balancer friendly SQL Database Session Storage

Required :
Bantingan\Model
RedBeanPHP

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(32) NOT NULL,
  `access` int(10) unsigned DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

*/
namespace Susilon\SQLSession;

use Bantingan\Model;

class Session 
{
	private $model;

 	public function __construct(){
		// Instantiate new Database object		
        // select db from dbconnection.config
		$this->model = new Model("session");        

		// Set handler to overide SESSION
		session_set_save_handler(
		array($this, "_open"),
		array($this, "_close"),
		array($this, "_read"),
		array($this, "_write"),
		array($this, "_destroy"),
		array($this, "_gc")
		);

		// Start the session
		session_start();
	}

	private function _getsession($id) {			
		return $this->model->getrow("select id, access, data from sessions where id=?", [ $id ]);
	}

	/**
	 * Open
	 */
	public function _open(){
		return true;		
	}

	/**
	 * Close
	 */
	public function _close(){
	  return true;	  
	}	

	/**
	 * Read
	 */
	public function _read($id){				
		$sessionData = $this->_getsession($id);		
		if (count($sessionData) > 0) {
			return $sessionData['data'];
		} else {
			return "";
		}		
	}	

	/**
	 * Write
	 */
	public function _write($id, $data){
		// Create time stamp
		try
		{
			$access = time();
			$data = str_replace("'", "\"", $data);
			$sessionData = $this->_getsession($id);
			if ($sessionData != [])
			{				
				$this->model->execsql("UPDATE sessions SET access=?, data=? WHERE id=?",[$access, $data, $id]);
			} else {				
				$this->model->execsql("INSERT INTO sessions VALUES (?,?,?)", [$id, $access, $data] );	
			}

			return true;
		}
		catch (\Exception $ex)
		{
			// only return false when error occured
			return false;
		}		
	}	

	/**
	 * Destroy
	 */
	public function _destroy($id){
		try {
			$sessionData = $this->model->execsql("DELETE FROM sessions WHERE id = ?", [ $id ]);
		}
		catch (\Exception $ex)
		{
			// only return false when error occured
			return false;
		}		
	} 	

	/**
	 * Garbage Collection
	 */
	public function _gc($max){
		// Calculate what is to be deemed old
		$old = time() - $max;

		try {
			$sessionData = $this->model->find("access < ?", [ $old ]);
			if (isset($sessionData))
			{
				$this->trash($sessionData);
			}
		}
		catch (\Exception $ex)
		{
			// only return false when error occured
			return false;
		}		
	}	
}
?>