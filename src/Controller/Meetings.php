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

class Meetings {
    
    private $total = 0;
    
    function __construct()
    {
        global $dbi, $ySubeKodu;
		
		$dbi->where("stype", "headquarters");
    	$hqID = $dbi->getValue(_SUBELER_, "subeID");
		
		//get total records
		if($ySubeKodu == "0" || $ySubeKodu == $hqID) $dbi->where("schoolId", array("0",$hqID), "IN");
		else $dbi->where("schoolId", $ySubeKodu);
		
		$this->total = $dbi->getValue(_MEETINGS_, "COUNT(Id)");

    }
    
    /* function */
	function getTotal()
	{
		return $this->total;
	}
    
	/* function */
	function getMeetings()
	{
        global $dbi, $ySubeKodu, $simsDateTime;
        
        $dbi->where("stype", "headquarters");
    	$hqID = $dbi->getValue(_SUBELER_, "subeID");
        
		if($ySubeKodu == "0" || $ySubeKodu == $hqID) $dbi->where("schoolId IN '".array("0",$hqID)."' OR Id IN(SELECT meetingId FROM meeting_attendees WHERE schoolId != '0' AND schoolId != '".$hqID."' AND userId = '".$aid."')");
		else $dbi->where("schoolId", $ySubeKodu);
        
        $dbi->where("schoolId='".$schoolID."' OR Id IN(SELECT meetingId FROM "._MEETING_ATTENDEES_." WHERE schoolId != '".$schoolID."' AND userId = '".$aid."')");
        $dbi->orderBy("meetingStart", "DESC");
        $meetings = $dbi->get(_MEETINGS_);

		return $meetings;
	}

}
