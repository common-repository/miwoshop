<?php
/*
* @package		MiwoShop
* @copyright	2009-2016 Miwisoft LLC, miwisoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceShop www.joomace.net
*/

// No Permission
defined('MIWI') or die('Restricted access');

class DB {
	private $driver;

	public function __construct($driver, $hostname, $username, $password, $database) {
		$this->driver = MiwoShop::get('db')->getDbo();
	}

  	public function query($sql) {
        $this->driver->setQuery($sql);

		$isSelect = $this->isSelect($sql);

		if ($isSelect) {
			$data =  $this->driver->loadAssocList();

            $query = new stdClass();
            $query->row = isset($data[0]) ? $data[0] : array();
            $query->rows = $data;
            $query->num_rows = count($data);
		}
		else {
            $query =  $this->driver->execute();
		}

        return $query;
  	}

	public function run($sql) {
        $this->driver->setQuery($sql);
        $data =  $this->driver->execute();

        return $data;
  	}

	public function escape($value) {
		return $this->driver->escape($value);
	}

  	public function countAffected() {
		return $this->driver->getAffectedRows();
  	}

  	public function getLastId() {
		return $this->driver->insertid();
  	}

	public function isSelect($query) {
		$query = trim($query);
		$isselect = strpos(strtoupper($query), 'SELECT');
		$isshow = strpos(strtoupper($query), 'SHOW');
		$isdescibe = strpos(strtoupper($query), 'DESCRIBE');

		if($isselect !== 0 and $isshow !== 0 and $isdescibe !== 0) {
			return false;
		}
		else{
			return true;
		}
	}
}
?>
