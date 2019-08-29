<?php

namespace Schoost\LMS;

//get lms provider
/*
if($globalZone != "admin")
{
    if($ySubeKodu > 0) $dbi->where("schoolId", array("0", "$ySubeKodu"), "IN");
    else $dbi->where("schoolId", "0");
    $dbi->orderBy("schoolId", "desc");
    $lmsProvider = $dbi->getValue(_LMS_CONFIG_, "lms");
}

if(empty($lmsProvider)) $lmsProvider = "Moodle";
*/

//include the lms provider class
//include "Providers/Moodle.php";
use Schoost\LMS\Providers\Moodle;

class LMS extends Moodle {

    private $batchId = "";
    
    function simsSetBatchId($batchId)
    {
        $this->batchId = $batchId;    
    }

    function simsCreateBatchInLMS($batches)
    {
        $response = $this->lmsCreateBatches($batches);
        
        return $response;
    }

    function simsAssignCourse2Class()
    {
        
    }
    
    function simsAssignCourse2BatchInLMS($course)
    {
        $response = $this->lmsAssignCourseToBatch($this->batchId, $course);
        
        return $response;
    }

    function simsAssignStds2BatchInLMS($stds)
    {
        global $dbi, $ySubeKodu;
        
        $batchMembers = array();
        $lmsUserIds = array();
        
        $getBatchMembers = $this->lmsGetBatchmembers($this->batchId);

        foreach($getBatchMembers as $batchMember)
        {
        	$batchMembers[] = $batchMember["id"];
        }
        
        $dbi->where("aid", $stds, "IN");
        $dbi->where("ySubeKodu", $ySubeKodu);
        $dbi->where("lmsUserId", "0", "!=");
        $getLmsUserId = $dbi->get(_USERS_, NULL, "lmsUserId");
        
        foreach($getLmsUserId as $lmsUser)
        {
        	if(!in_array($lmsUser["lmsUserId"], $batchMembers))
        	{
        		$lmsUserIds[] = array('userid' => $lmsUser["lmsUserId"]);
        	}
        }
        
        if(!empty($lmsUserIds))
        {
            $response = $this->lmsBulkAssignBatchmembers($this->batchId, $lmsUserIds);
        }
        
        return $response;
    }
    
    function simsAssignTeacher2BatchInLMS($classId)
    {
        global $dbi, $ySubeKodu;
        
        $batchMembersId = array();
        $batchMembers = $this->lmsGetBatchmembers($this->batchId);
        
        foreach($batchMembers as $batchMember)
        {
            $batchMembersId[] = $batchMember["id"];
        }
        
        $batchMemberUserId = array();
        $dbi->join(_PERSONEL_ . " p","p.perID=ct.teacherId", "LEFT");
        
        $dbi->where("ct.classId", $classId);
        $dbi->where("ct.schoolId", $ySubeKodu);
        $getTeachers = $dbi->get(_CLASS_TEACHERS_ . " ct", null, "p.tckimlikno");
        
        foreach($getTeachers as $teacher)
        {
            $dbi->where("aid", $teacher["tckimlikno"]);
            $dbi->where("lmsUserId", "", "!=");
            $lmsUserIDs = $dbi->get(_USERS_, null, "lmsUserId");
        
            foreach($lmsUserIDs as $userIds)
            {
                if(!in_array($userIds["lmsUserId"], $batchMembersId))
                {
                    $batchMemberUserId[] = array(
                        'userid'        => $userIds["lmsUserId"]
                    );
                }
            }
        }
        
        if(!empty($batchMemberUserId))
        {
            $response = $this->lmsBulkAssignBatchmembers($this->batchId, $batchMemberUserId);
        }
        
        
    }
    
    function simsTeacherManuelEnrolInLMS($classId, $lmsCourseId, $lmsBatchId)
    {
        global $dbi, $ySubeKodu;
        
        $manuelEnrolData = array();
        $dbi->join(_PERSONEL_ . " p","p.perID=ct.teacherId", "LEFT");
        
        $dbi->where("ct.classId", $classId);
        $dbi->where("ct.schoolId", $ySubeKodu);
        $getTeachers = $dbi->get(_CLASS_TEACHERS_ . " ct", null, "p.tckimlikno");
        
        foreach($getTeachers as $teacher)
        {
            $dbi->where("aid", $teacher["tckimlikno"]);
            $dbi->where("lmsUserId", "", "!=");
            $lmsUserIDs = $dbi->get(_USERS_, null, "lmsUserId");
        
            foreach($lmsUserIDs as $userIds)
            {
                $manuelEnrolData[] = array(
                    'userid'        => $userIds["lmsUserId"],
                    'courseid'      => $lmsCourseId,
                    'batchid'       => $lmsBatchId,
                    'roleshortname' => "editingteacher"
                );
            }
        }
        
        $response = $this->lmsBulkManuelEnrol($manuelEnrolData);
        
        return $response;
    }
    
    function simsGetLMSCourse()
    {
        $response = $this->lmsGetCourses();
        
        return $response;
    }
    
    function simsGetLMSBatchCourses()
    {
        $response = $this->lmsGetBatchCourses($this->batchId);
        
        return $response;
    }
    
    function simsUnassignCourse2BatchInLMS($course)
    {
        $response = $this->lmsUnassignCourseToBatch($this->batchId, $course);
        
        return $response;
    }
    
    function simsGetLmsUser($criteria)
    {
        $response = $this->lmsGetUser($criteria);
        
        return $response;
    }
    
    function createStudentNo($stdno)
    {
        $strlen = strlen($stdno);
    					
    	if($strlen < 6)
    	{
    	    $eksikSayi = 6 - $strlen;
            for($i = 0; $i < $eksikSayi; $i++)
    		{
    		   $ekle = $ekle . "0";
    		}
    		$studentNo = $ekle . $stdno;
    	}
    	else $studentNo = $stdno;
    	
    	return $studentNo;
    }
    
    

}
?>
