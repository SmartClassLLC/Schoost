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

class Users {

    private $userId = 0;
    
    /* function */
	function setUserId($id)
	{
        $this->userId = $id;
        
        return $this;
	}

    /* function */
	function getUserId($id)
	{
        return $this->userId;
	}

    /* function */
	function getUserInfo()
	{
        global $dbi;

		//join user types
		$dbi->join(_USER_TYPES_. " ut", "ut.typeID=u.userType", "LEFT");

		//join schools
		$dbi->join(_SUBELER_. " s", "s.subeID=u.ySubeKodu", "LEFT");
		
		//get user info            
		$dbi->where("u.aid", $this->userId);
		$userInfo = $dbi->getOne(_USERS_. " u", "u.*, ut.userType as userTypeTitle, s.subeAdi as schoolTitle");
		
		//translate user type title
		$userInfo["userTypeTitle"] = translateWord($userInfo["userTypeTitle"]);
		
		//fix school title
		if(empty($userInfo["schoolTitle"]) && empty($userInfo["ySubeKodu"])) $userInfo["schoolTitle"] = _GENEL_MUDURLUK;
		
		return $userInfo;
	}

    /* function */
	function getPersonnelInfo()
	{
        global $dbi, $globalZone;
        
        if($globalZone == "admin") return false;
        
        //join blood type
		$dbi->join(_BLOOD_GROUPS_. " bt", "bt.Id=p.kanGrubuKodu", "LEFT");
		
        //join personnel categories
		$dbi->join(_PERSONNEL_CATEGORIES_. " pc", "pc.cat_id=p.cat_code", "LEFT");
		
        //get personnel info
		$dbi->where("p.tckimlikno", $this->userId);
		$personnelInfo = $dbi->getOne(_PERSONEL_. " p", "p.*, pc.cat_name as categoryTitle, bt.name as bloodTypeTitle");
		
		if(empty($personnelInfo)) return array();
		
		//translate personnel category title
		$personnelInfo["categoryTitle"] = translateWord($personnelInfo["categoryTitle"]);
		
		//get and translate personnel gender
		$personnelInfo["gender"] = translateWord(fnPersonnelGender($personnelInfo["cinsiyeti"]));
		
		return $personnelInfo;
	}

}