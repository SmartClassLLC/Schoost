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

use Schoost\LMS\LMS;

class Authentication extends LMS {

    protected $userId = "";
    protected $userSchoolId = "";
    protected $userPassword = "";
    protected $authDbIdSetting = "off";
    protected $authDbEmailSetting = "off";
    protected $authDbStdSsnSetting = "off";
    protected $authLdapIdSetting = "off";
    protected $authLdapEmailSetting = "off";
    protected $authLoginTypes = array();
    
	/* function */
	function setUserId($Id)
	{
		//set user Id
		$this->userId = $Id;
	}

	/* function */
	function setSchoolId($Id)
	{
		//set user school Id
		$this->userSchoolId = $Id;
	}

	/* function */
	function setUserPassword($pwd)
	{
		//set user password
		$this->userPassword = $pwd;
	}

	/* function */
	function setAuthDbIdSetting($onoff)
	{
		//set db authentication aid setting
		$this->authDbIdSetting = $onoff;
	}

	/* function */
	function setAuthDbEmailSetting($onoff)
	{
		//set db authentication email setting
		$this->authDbEmailSetting = $onoff;
	}

	/* function */
	function setAuthDbStdSsnSetting($onoff)
	{
		//set db authentication email setting
		$this->authDbStdSsnSetting = $onoff;
	}

	/* function */
	function setAuthLdapIdSetting($onoff)
	{
		//set db authentication aid setting
		$this->authLdapIdSetting = $onoff;
	}

	/* function */
	function setAuthLdapEmailSetting($onoff)
	{
		//set db authentication email setting
		$this->authLdapEmailSetting = $onoff;
	}

	/* function */
	function setAuthLoginType($type)
	{
		//set authentication login types
		$this->authLoginTypes[] = $type;
	}

	/* function */
	function getAuthenticationInfo()
	{
		global $dbi, $globalZone;
		
		$dbi->join(_USER_TYPES_. " t", "t.typeID=u.userType", "INNER");
		
		if($this->authDbStdSsnSetting == "on")
		{
			if($this->authDbIdSetting == "on" && $this->authDbEmailSetting == "on") $dbi->where("(u.aid=? OR u.email=? OR FIND_IN_SET(?, ogrIDs) > 0)", array($this->userId, $this->userId, $this->userId));
			else if($this->authDbEmailSetting == "on") $dbi->where("(u.email=? OR FIND_IN_SET(?, ogrIDs) > 0)", array($this->userId, $this->userId));
			else if($this->authDbIdSetting == "on") $dbi->where("(u.aid=? OR FIND_IN_SET(?, ogrIDs) > 0)", array($this->userId, $this->userId));
	
			else if($this->authLdapIdSetting == "on" && $this->authLdapEmailSetting == "on") $dbi->where("(u.ldapAid=? OR u.email=?)", array($this->userId, $this->userId));
			else if($this->authLdapEmailSetting == "on") $dbi->where("u.email", $this->userId);
			else if($this->authLdapIdSetting == "on") $dbi->where("u.ldapAid", $this->userId);
		}
		else
		{
			if($this->authDbIdSetting == "on" && $this->authDbEmailSetting == "on") $dbi->where("(u.aid=? OR u.email=?)", array($this->userId, $this->userId));
			else if($this->authDbEmailSetting == "on") $dbi->where("u.email", $this->userId);
			else if($this->authDbIdSetting == "on") $dbi->where("u.aid", $this->userId);
	
			else if($this->authLdapIdSetting == "on" && $this->authLdapEmailSetting == "on") $dbi->where("(u.ldapAid=? OR u.email=?)", array($this->userId, $this->userId));
			else if($this->authLdapEmailSetting == "on") $dbi->where("u.email", $this->userId);
			else if($this->authLdapIdSetting == "on") $dbi->where("u.ldapAid", $this->userId);
		}
		
		$dbi->where("u.active", "1");
		$dbi->where("t.loginType", $this->authLoginTypes, "IN");
		$authenticationInfo = $dbi->getOne(_USERS_. " u", "u.aid, u.pwd, u.email, u.picture, u.name, u.lastName, u.ySubeKodu, u.campusID, u.userType, u.expireDate, u.firebaseId, u.firebaseToken, u.pwdPlain, u.id, t.loginType");
		
		//check picture
		if(empty($authenticationInfo["picture"])) $authenticationInfo["picture"] = "https://schst.in/nopicture";
		
		//school id is comint then check if it has a transfer id
		if($this->userSchoolId != "" && $authenticationInfo["ySubeKodu"] != $this->userSchoolId)
		{
			//get defautl season db
			$defaultSeasonDB = $dbi->where("ontanimli", "on")->where("aktif", "on")->getValue(_DONEMLER_, "veritabani");
			
			$dbi->join($defaultSeasonDB.".personel p", "p.perID=t.perId", "INNER");
			$dbi->where("p.tckimlikno", $authenticationInfo["aid"]);
			$dbi->where("t.mainSchoolId", $authenticationInfo["ySubeKodu"]);
			$getTransferSchoolIds = $dbi->getValue($defaultSeasonDB.".personel_transfer t", "transferSchoolId", null);
			
			if(in_array($this->userSchoolId, $getTransferSchoolIds)) $authenticationInfo["ySubeKodu"] = $this->userSchoolId;
		}

		//create user in lms if does not exist
		if($globalZone != "admin")
		{
			//set lms info
			//$this->lmsSetSchoolInfo($this->userSchoolId);
			$this->lmsSetSchoolInfo($authenticationInfo["ySubeKodu"]);
			
			//crate lms user if exists
			$criteria = array(
				'key'   => 'username',
				'value' => $authenticationInfo["aid"]
			);
			$getLmsUser = $this->lmsGetUser($criteria);
			
			if(empty($getLmsUser["users"][0]))
			{
				
				$users = array(
				    'username'      => $authenticationInfo["aid"],
				    'password'      => $authenticationInfo["pwdPlain"],
				    'firstname'     => $authenticationInfo["name"],
				    'lastname'      => $authenticationInfo["lastName"],
				    'email'         => $authenticationInfo["email"],
				    'studentno'		=> $this->createStudentNo($authenticationInfo["id"]),
				    'auth'			=> 'lti'
				);
				
				$createLMSUser = $this->lmsCreateUsers($users);
				
				if(!empty($createLMSUser))
				{
					$queryData = array('lmsUserId' => $createLMSUser[0]["id"]);
					
					$dbi->where("aid", $authenticationInfo["aid"]);
					$dbi->where("id", $authenticationInfo["id"]);
					$update = $dbi->update(_USERS_, $queryData);
				}
				
			}
		}
		
		//return info
		return $authenticationInfo;
	}

	/* function */
	function checkLdapUser($username, $userpassword)
	{
		global $dbi;

    	//check if it is email
		$isEmail = strpos($username, "@");
		if($isEmail != false)
		{
			$usernamex = explode("@", $username);
			$username = $usernamex[0];
		}
		
		//ldap attributes
		$ldapattr = array("memberof", "sn", "givenname", "samaccountname", "distinguishedname", "mail", "description", "cn");
		
		//ldap servers
		$ldapServers = $dbi->orderBy("branchID", "ASC")->get(_LDAP_SERVERS_);
		foreach($ldapServers as $ldapServer)
		{
	    	$adServer = $ldapServer["ldap_port"] == "389" ? "ldap://".$ldapServer["ldap_server"] : ($ldapServer["ldap_port"] == "636" ? "ldaps://".$ldapServer["ldap_server"] : "error");
		    $ldap = ldap_connect($adServer, $ldapServer["ldap_port"]);
			if($ldap)
			{
			    $ldaprdn = $ldapServer['ldap_bind_dn']."\\".$username;
				//ldap connection parameters
			    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
			    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
				//bind connection
			    $bind = @ldap_bind($ldap, $ldaprdn, $userpassword);

				if($bind)
				{
			        $filter = "(sAMAccountName=$username)";
			        $result = ldap_search($ldap, $ldapServer['ldap_base_dn'], $filter, $ldapattr);
			        //ldap_sort($ldap, $result, "sn");
			        $info = ldap_get_entries($ldap, $result);

					if($info['count'] == 1) return $ldapServer["userType"];
				}
			}
		}
		
		return false;
	}

	/* function */
	function getAuthentication()
	{
		$authInfo = $this->getAuthenticationInfo();
		
		if($authInfo["pwd"] == $this->userPassword) return true;
		else return false;
	}
}