<?php

/*
 * This file is part of Schoost.
 *
 * (c) SmartClass, LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schoost;

class Basics
{
    public $ySubeKodu = "";
    public $yCampusID = "";
    public $dbname2 = "";
    public $aid = "";
    public $userType = "";
    public $myStudent = "";
    public $currentSeasonInfo = array();
    public $globalUserType = "";
    public $globalUserFolder = "";
    public $globalUserManagerMenu = "";
    public $globalUserTypeClass = "";
    public $availableLanguages = array();
    public $currentlang = "";
    public $schoolUrl = "";
    public $schoolSimsUrl = "";
    public $domain = "";
    public $generalSettings = array();
    
	function __construct()
	{
		global $ySubeKodu, $yCampusID, $dbname2, $aid, $userType, $myStudent, $currentSeasonInfo, $globalUserType, $globalUserFolder, $globalUserManagerMenu, $availableLanguages, $currentlang, $generalSettings, $schoolUrl, $schoolSimsUrl, $domain, $globalUserTypeClass;
		
        $this->ySubeKodu = $ySubeKodu;
        $this->yCampusID = $yCampusID;
        $this->dbname2 = $dbname2;
        $this->aid = $aid;
        $this->userType = $userType;
        $this->myStudent = $myStudent;
        $this->currentSeasonInfo = $currentSeasonInfo;
        $this->globalUserType = $globalUserType;
        $this->globalUserFolder = $globalUserFolder;
        $this->globalUserTypeClass = $globalUserTypeClass;
        $this->globalUserManagerMenu = $globalUserManagerMenu;
        $this->availableLanguages = $availableLanguages;
        $this->currentlang = $currentlang;
        $this->schoolUrl = $schoolUrl;
        $this->schoolSimsUrl = $schoolSimsUrl;
        $this->domain = $domain;
        $this->generalSettings = $generalSettings;
	}
	
	function getBasics()
	{
	    //get all variables
	    $vars = get_object_vars($this);
	    
	    //return all variables
	    return $vars;
	}
	
}