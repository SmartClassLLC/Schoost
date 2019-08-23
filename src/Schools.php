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

class Schools {
    
    private $campusId = 0;
    private $schoolId = 0;
    private $noneCampus = false;
    private $campuses = array();
    private $campusInfo = array();
    private $schools = array();
    private $schoolInfo = array();
    
    /* function */
	function setCampusId($Id)
	{
		$this->campusId = $Id;
        
		return $this;
	}

    /* function */
	function getCampusId()
	{
		return $this->campusId;
	}
    
    /* function */
	function setSchoolId($Id)
	{
		$this->schoolId = $Id;
        
		return $this;
	}

    /* function */
	function getSchoolId()
	{
		return $this->schoolId;
	}

    /* function */
	function getCampuses($n = "all")
	{
        global $dbi;
            
        $dbi->orderBy("campusTitle", "asc");
		
		$this->campuses = $dbi->get(_CAMPUSES_);
        
		return $this->campuses;
	}

    /* function */
	function getCampusInfo($Id)
	{
        global $dbi;
        
        if(empty($this->campusId)) $this->campusId = $Id;
        
        $dbi->where("Id", $this->campusId);
		$this->campusInfo = $dbi->getOne(_CAMPUSES_);
        
		return $this->campusInfo;
	}

    /* function */
    function getCampusTitle($Id)
    {
        if(empty($this->campusId)) $this->campusId = $Id;
        
        if($this->campusId == "0")
        {
            return _GENEL_MUDURLUK;
        }
        else
        {
            $myCampusInfo = $this->getCampusInfo($this->campusId);
            return $myCampusInfo["campusTitle"];
        }
    }

    /* function */
	function getSchoolIds($campusId = "0", $noneCampus = false)
	{
        global $dbi, $seasonSchools;
        
        $this->campusId = $campusId;
        $this->noneCampus = $noneCampus;

        $dbi->where("subeID", $seasonSchools, "IN");        
        
        if($this->noneCampus) $dbi->where("campusID=? OR campusID=?", array($this->campusId, "0"));
        else $dbi->where("campusID", $this->campusId);
    	
    	$dbi->orderBy("subeAdi", "ASC");
    	
    	$schoolIds = $dbi->getValue(_SUBELER_, "subeID", null);
        
		return $schoolIds;
	}

    /* function */
	function getSchools($campusId = 0, $noneCampus = false)
	{
        global $dbi, $seasonSchools;
        
        $this->campusId = $campusId;
        $this->noneCampus = $noneCampus;
        
    	if($this->noneCampus)
    	{
			$dbi->where("subeID", $seasonSchools, "IN");
			$dbi->where("(campusID = ? OR campusID = ?)", array($this->campusId, "0"));
			$dbi->orderBy("subeAdi", "ASC");
			$this->schools = $dbi->get(_SUBELER_);
    	}
    	else
    	{
			$dbi->where("subeID", $seasonSchools, "IN");
			$dbi->where("campusID", $this->campusId);
			$dbi->orderBy("subeAdi", "ASC");
			$this->schools = $dbi->get(_SUBELER_);
    	}
        
		return $this->schools;
	}

    /* function */
	function getSchoolInfo($Id = 0)
	{
        global $dbi;
        
        if(empty($this->schoolId)) $this->schoolId = $Id;
        
        $dbi->where("subeID", $this->schoolId);
		$this->schoolInfo = $dbi->getOne(_SUBELER_);
        
		return $this->schoolInfo;
	}

    /* function */
	function getSchoolCampusId($Id)
	{
        global $dbi;
        
        if(empty($this->schoolId)) $this->schoolId = $Id;
        
        $schoolInfo = $this->getSchoolInfo($this->schoolId);
        
		return $schoolInfo["campusID"];
	}

    /* function */
    function getSchoolTitle($schoolId)
    {
        $this->schoolId = $schoolId;
        
        $mySchoolInfo = $this->getSchoolInfo($this->schoolId);
        
        return $mySchoolInfo["subeAdi"];
    }

    /* function */
	function getCampusesSchools($hq = true, $noneCampus = true, $htmlTag = "")
	{
        global $db;
        
        $returnData = "";
        
        //add hq if it set
        if($hq) $returnData .= "<".$htmlTag."><a class='sims-campuses-schools sims-hq' href='#' data-id='0' data-title='"._GENEL_MUDURLUK."'><i class='fa fa-fw fa-institution'></i> "._GENEL_MUDURLUK."</a></".$htmlTag.">";

        //add campuses
        $qCampuses = $db->sql_query("SELECT * FROM "._CAMPUSES_." ORDER BY `Id` ASC");
        while($rCampuses = $db->sql_fetchrow($qCampuses))
        {
            //echo campus
            $returnData .= "<".$htmlTag."><a class='sims-campuses-schools sims-campuses' href='#' data-id='".$rCampuses["Id"]."' data-title='".$rCampuses["campusTitle"]."'><i class='fa fa-fw fa-map'></i> ".$rCampuses["campusTitle"]."</a></".$htmlTag.">";

            //get schools for the campus
    	    $qSchools = $db->sql_query("SELECT * FROM "._SUBELER_." WHERE `campusId`='".$rCampuses["Id"]."' ORDER BY `subeID` ASC");
    	    while($rSchools = $db->sql_fetchrow($qSchools))
    	    {
    	        $returnData .= "<".$htmlTag." style='padding-left: 7px'><a class='sims-campuses-schools sims-schools' href='#' data-id='".$rSchools["subeID"]."' data-title='".$rSchools["subeAdi"]."'><i class='fa fa-fw fa-building-o'></i> ".$rSchools["subeAdi"]."</a></".$htmlTag.">";
    	    }            
        }
        
        //add none campus schools
    	if($this->noneCampus)
    	{
    	    $qNoneCampuses = $db->sql_query("SELECT * FROM "._SUBELER_." WHERE `campusId`='0' ORDER BY `subeID` ASC");
    	    while($rNoneCampuses = $db->sql_fetchrow($qNoneCampuses))
    	    {
    	        $returnData .= "<".$htmlTag."><a class='sims-campuses-schools sims-schools' href='#' data-id='".$rNoneCampuses["subeID"]."' data-title='".$rNoneCampuses["subeAdi"]."'><i class='fa fa-fw fa-building'></i> ".$rNoneCampuses["subeAdi"]."</a></".$htmlTag.">";
    	    }
    	}
        
		return $returnData;
	}    
}