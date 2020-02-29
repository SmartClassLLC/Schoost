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

class Core
{
    public $allLanguages = array();
    public $currentLangInfo = array();
    public $freqMenus = array();
    public $mainMenus = array();
    public $students = array();
    public $currentStudent = array();
    public $seasons = array();
    public $campuses = array();
    public $campusInfo = array();
    public $campusTitle = '';
    public $nonCampusSchools = array();
    public $urlSchools = array();
    public $simsVersion = array();
    public $gorebilenSubeler = array();
    public $userHQ = '0';
    public $userPhoto = '';
    public $userTypeTitle = '';
    public $subeleriGoster = '1';
    public $nofSchools = '0';
    public $school_logo = '';
    public $school_name = '';
    public $season_name = '';
    public $student_picture = '';
    
	function __construct()
	{
		global $dbi, $ySubeKodu, $yCampusID, $dbname2, $aid, $userType, $myStudent, $currentSeasonInfo, $globalUserType, $globalUserFolder, $globalUserManagerMenu, $availableLanguages, $currentlang, $generalSettings, $schoolUrl, $schoolSimsUrl, $domain;

        //user id
        $userId = fnUserId2UserInfo($aid, "id");
        
        //set variables for thempalte
        $this->school_logo = scSchoolLogo("1");
        $this->school_name = BranchName($ySubeKodu);
        $this->season_name = $currentSeasonInfo["donem"];
        $this->seasons = $dbi->where("aktif", "on")->orderBy("veritabani", "DESC")->get(_DONEMLER_, null, "donem, gorebilenSubeler, veritabani");
        
        if($globalUserType == "parent") $this->student_picture = showPhoto($myStudent["Foto"], "", "40px", "sims-dashboard-student img-rounded");

        $isAdmin = Admin();
        
        //frequent menus
        $dbi->join(_MENUS_. " m", "f.menu_id=m.id", "INNER");
        if(!$isAdmin) $dbi->where("m.aktif", "1");
        $dbi->where("m.". $globalUserManagerMenu, "on");
        $dbi->where("f.user_id", $userId);
        $dbi->orderBy("numberOfUse", "DESC");
        $this->freqMenus = $dbi->get(_FREQUENT_MENUS_. " f", 10, "f.id AS fId, m.id, m.menu, m.resim, m.url");
        
        foreach($this->freqMenus as $k => $f)
        {
        	$this->freqMenus[$k]["menuType"] = (strpos($f["url"], "blank") == false) ? "maintab" : "blank";
        	$this->freqMenus[$k]["menuTitle"] = (substr($f["menu"], 0, 1) == "_") ? translateWord($f["menu"]) : $f["menu"];
        	$this->freqMenus[$k]["newPageUrl"] = str_replace(array("?blank=1", "&blank=1"), array("", ""), $f["url"]);
        }
        
        //if no definition by the school create all menus to the school as on by default
        $dbi->where("branchID", $ySubeKodu);
        $varmi = $dbi->getOne(_SCHOOL_MENUS_);
        
        if(empty($varmi))
        {
        	//get default menus
        	$dbi->where("aktif", "1");
        	$dbi->where("(teacherMenu=? OR parentMenu=? OR studentMenu=?)", array("on", "on", "on"));
        	$menus = $dbi->get(_MENUS_, null, "id, teacherMenu, parentMenu, studentMenu");
        
        	foreach ($menus as $menu)
        	{
        		if($menu["teacherMenu"] == "on")
        		{
        			$queryData = array(
        				"active"	=> "on",
        				"menuID"	=> $menu["id"],
        				"menuType"	=> "teacherMenu",
        				"branchID"	=> $ySubeKodu
        			);
        			
        			$dbi->insert(_SCHOOL_MENUS_, $queryData);
        		}
        
        		if($menu["parentMenu"] == "on")
        		{
        			$queryData = array(
        				"active"	=> "on",
        				"menuID"	=> $menu["id"],
        				"menuType"	=> "parentMenu",
        				"branchID"	=> $ySubeKodu
        			);
        			
        			$dbi->insert(_SCHOOL_MENUS_, $queryData);
        		}
        
        		if($menu["studentMenu"] == "on")
        		{
        			$queryData = array(
        				"active"	=> "on",
        				"menuID"	=> $menu["id"],
        				"menuType"	=> "studentMenu",
        				"branchID"	=> $ySubeKodu
        			);
        			
        			$dbi->insert(_SCHOOL_MENUS_, $queryData);
        		}
        	}
        } 
        
        $this->simsVersion = $dbi->getOne(_UPDATES_, "MAX(`version`) AS `sVersion`, MAX(`updateDate`) AS `uDate`");
        $this->simsVersion["uDateFormatted"] = FormatDateNumeric2Local($simsVersion["uDate"]);
        
        //user info
        $this->userPhoto = showUserPhoto($aid, "img-circle user-image", "");
        $this->userTypeTitle = KullaniciTuru($userType);
        $this->userHQ = GenelMudurluk($aid);
	}
	
	function getSchools()
	{
		global $dbi, $ySubeKodu, $yCampusID, $dbname2, $aid, $userType, $myStudent, $currentSeasonInfo, $globalUserType, $globalUserFolder, $globalUserManagerMenu, $availableLanguages, $currentlang, $generalSettings, $schoolUrl, $schoolSimsUrl, $domain, $userPersonnelInfo;

        if($domain == $_SERVER["SERVER_NAME"])
        {
        	//if show branches is on then show branches and login options else do not show them all
        	$this->subeleriGoster = ($generalSettings["showBranches"] == "on") ? "1" : "0";
        }
        else
        {
        	$this->subeleriGoster = "1";
        }
        
        //season info
        $seasonInfo = $dbi->where("veritabani", $dbname2)->getOne(_DONEMLER_);
        
        //available schools
        $availableSchools = explode(",", $seasonInfo["gorebilenSubeler"]);
        
        //check the number of schools
        $dbi->where("subeID", $availableSchools, "IN");
        $dbi->where("stype", "school");
        $nofSchoolsArray = $dbi->getValue(_SUBELER_, "subeID", null);
        $this->nofSchools = empty($nofSchoolsArray) ? 0 : sizeof($nofSchoolsArray);
        
        //check if there is any additional campuses
        $dbi->where("aid", $aid);
        $addCampuses = $dbi->getOne(_USERS_, "campusID, additionalCampuses");
        
        if(!empty($addCampuses["campusID"]))
        {
        	$allCampuses = array($addCampuses["campusID"]);
        	if(!empty($addCampuses["additionalCampuses"]))
        	{
        		$additionalCampuses = explode(",", $addCampuses["additionalCampuses"]);
        		foreach($additionalCampuses as $k => $c) $allCampuses[] = substr($c, 1);
        	}
        	
        	$allCampuses = array_unique($allCampuses);
        }
        
        if($globalUserFolder == "headquarters")
        {
        	if($schoolUrl)
        	{
        		$dbi->where("scUrl", $schoolSimsUrl);
        		$this->urlSchools = $dbi->get(_SUBELER_, null, "subeID, menuSubeAdi");
        	}
        	else
        	{
        		if(!empty($allCampuses)) $dbi->where("Id", $allCampuses, "IN");
        		$this->campuses = $dbi->get(_CAMPUSES_, null, "Id, campusTitle");
        		
        		foreach($this->campuses as $k => $campus)
        		{
        			$dbi->where("campusID", $campus["Id"]);
        			$dbi->where("subeID", $availableSchools, "IN");
        			$dbi->orderBy("subeID", "asc");
        			$schools = $dbi->get(_SUBELER_, null, "subeID, menuSubeAdi");
        			
        			$this->campuses[$k]["schools"] = $schools;
        		}
        		
                //non campus schools
                $dbi->where("campusID", "0");
                $dbi->where("subeID", $availableSchools, "IN");
                $dbi->orderBy("subeID", "asc");
                $this->nonCampusSchools = $dbi->get(_SUBELER_, null, "subeID, menuSubeAdi");
        	}
        }
        else if($globalUserFolder == "campus")
        {
        	$dbi->where("Id", $yCampusID);
        	$this->campusInfo = $dbi->getOne(_CAMPUSES_, "Id, campusTitle");
        	
        	$dbi->where("campusID", $this->campusInfo["Id"]);
        	$dbi->where("subeID", $availableSchools, "IN");
        	$dbi->orderBy("subeID", "asc");
        	$schools = $dbi->get(_SUBELER_, null, "subeID, menuSubeAdi");
        	
        	$this->campusInfo["schools"] = $schools;
        	
            //campus info
            $this->campusTitle = $this->campusInfo["campusTitle"];

            //get all campuses            
    		if(!empty($allCampuses)) $dbi->where("Id", $allCampuses, "IN");
    		$this->campuses = $dbi->get(_CAMPUSES_, null, "Id, campusTitle");
    		
    		foreach($this->campuses as $k => $campus)
    		{
    			$dbi->where("campusID", $campus["Id"]);
    			$dbi->where("subeID", $availableSchools, "IN");
    			$dbi->orderBy("subeID", "asc");
    			$schools = $dbi->get(_SUBELER_, null, "subeID, menuSubeAdi");
    			
    			$this->campuses[$k]["schools"] = $schools;
    		}
        }
        else if ($globalUserFolder == "school")
        {
        	$dbi->orderBy("Id","asc");
        	$this->campuses = $dbi->get(_CAMPUSES_, null, "Id, campusTitle");
        	foreach($this->campuses as $k => $campus)
        	{
        		$dbi->where("campusID", $campus["Id"]);
        		$dbi->where("subeID", $availableSchools, "IN");
        		$dbi->orderBy("subeID", "asc");
        		$schools = $dbi->get(_SUBELER_, null, "subeID, menuSubeAdi");
        		
        		$this->campuses[$k]["schools"] = $schools;
        	}
        	
            //non campus schools
            $dbi->where("campusID", "0");
            $dbi->where("subeID", $availableSchools, "IN");
            $dbi->orderBy("subeID", "asc");
            $this->nonCampusSchools = $dbi->get(_SUBELER_, null, "subeID, menuSubeAdi");
        }
        else if ($globalUserFolder == "teacher")
        {
            //set teachers own school
            $tschools = array($userPersonnelInfo["SubeKodu"]);
            
            //non campus schools
            $dbi->where("active", "on");
            $dbi->where("perId", $userPersonnelInfo["perID"]);
            $transfers = $dbi->getValue(_PERSONEL_TRANSFER_, "transferSchoolId", null);
            
            if(!empty($transfers)) $tschools = array_merge($tschools, $transfers);
            
            //schools
            $dbi->where("subeID", $tschools, "IN");
            $dbi->orderBy("subeID", "asc");
            $this->nonCampusSchools = $dbi->get(_SUBELER_, null, "subeID, menuSubeAdi");
        }
    }
	
	function getLanguages()
	{
	    global $dbi, $availableLanguages, $currentlang;

        //languages
        if(is_array($availableLanguages)) $dbi->where("language", $availableLanguages, "IN");
        $dbi->orderBy("langID", "asc");
        $langs = $dbi->get(_AVAILABLE_LANGUAGES_, null, "language, langTitle, flag");
        
        //current lang
        $dbi->where("language", $currentlang);
        $clang = $dbi->getOne(_AVAILABLE_LANGUAGES_, "language, langTitle, flag");

        //set all languages
        $this->allLanguages = $langs;
        
        //set current lang info
        $this->currentLangInfo = $clang;
	}
	
	function getStudents()
	{
	    global $dbi, $aid, $ogrID, $globalZone;
	    
	    if($globalZone != "parent") return false;
	    
    	$dbi->join(_OGRENCILER_. " s", "s.ogrID=p.ogrID", "INNER");
    	$dbi->join(_BATCHES_. " b", "b.sinifID=s.SinifKodu", "LEFT");
    	$dbi->where("s.KayitliMi", "1");
    	$dbi->where("p.v_tc_kimlik_no", $aid);
    	$dbi->orderBy("s.Adi", "ASC");
    	$dbi->orderBy("s.IkinciAdi", "ASC");
    	$dbi->orderBy("s.Soyadi", "ASC");
    	$parentStudents = $dbi->get(_VELILER_. " p", null, "s.ogrID, s.Adi, s.IkinciAdi, s.Soyadi, s.Foto, s.SubeKodu, b.sinifAdi");

        foreach($parentStudents as $k => $parentStudent)
        {
            $parentStudents[$k]["fullName"] = fnStudentName($parentStudent["Adi"], $parentStudent["IkinciAdi"], $parentStudent["Soyadi"]);
            if($parentStudent["ogrID"] == $ogrID) $currentStdId = $k; 
        }
        
        $this->students = $parentStudents;
        $this->currentStudent = $parentStudents[$currentStdId];
	}
	
	function getMainMenus()
	{
	    global $dbi, $dbnamePrefix, $aid, $ySubeKodu, $yCampusID, $userType, $simsDate, $genelAyarlar, $partnerId, $integrations, $integrationSchoolParameters, $globalUserFolder, $globalUserManagerMenu;
		
				
		if(!empty($integrations) && !empty($integrationSchoolParameters["disabled_menus"])) $disabledMenus = explode(",", $integrationSchoolParameters["disabled_menus"]);
		else $disabledMenus = array();
		
		//manager type based on school id
		//$managerMenuType = empty($ySubeKodu) ? (empty($yCampusID) ? "headQuarterMenu" : "campusMenu") : "branchMenu";
		
		$isAdmin = Admin();
		
		//start query for menus
		$dbi->where($globalUserManagerMenu, "on");
		$dbi->where("parent_id", NULL, "IS");
		
		//if dev or admin then show all menus else show only active ones
		if((defined("DEV_MODE") && DEV_MODE == "1") || ($isAdmin && $_GET["showAllMenus"] == "1")) $dbi->where("aktif", array("0", "1"), "IN");
		else $dbi->where("aktif", "1");
		
		$dbi->orderBy("menuSirasi", "ASC");
		$mainMenus = $dbi->get(_MENUS_, null, "id, menu, resim, url, aktif, newBadge, userExceptions");
		
		foreach($mainMenus as $k => $mainMenu)
		{
		    
		    if(in_array($mainMenu["id"], $disabledMenus)) continue;
		    
		    //check exceptions
		    $exceptionSchools = explode(",", $mainMenu["userExceptions"]);
			
			//if the current school is in the exceptions list then continue    
		    if(in_array($dbnamePrefix, $exceptionSchools)) continue;
		
			//title
			$mainMenus[$k]["title"] = substr($mainMenu["menu"], 0, 1) == "_" ? translateWord($mainMenu["menu"]) : $mainMenu["menu"];

            //menu type
            $mainMenus[$k]["menuType"] = (strpos($mainMenu["url"], "blank") == false) ? "maintab" : "blank";
            
            //menu new page url
            $mainMenus[$k]["url"] = str_replace(array("?blank=1", "&blank=1"), array("", ""), $mainMenu["url"]);
			
            //badge
			$mainMenus[$k]["badge"] = "";
          	if($mainMenu["newBadge"] != "0000-00-00")
			{
				$expDate = date("Y-m-d", strtotime($mainMenu["newBadge"]." +7 days"));
				
				//if expired then update it
				if($expDate < $simsDate) $dbi->where("id", $mainMenu["id"])->update(_MENUS_, array("newBadge" => "0000-00-00"));
				else $mainMenus[$k]["badge"] = _LC_NEW;
			}

			//start query for sub menus
			$dbi->where($globalUserManagerMenu, "on");
			$dbi->where("parent_id", $mainMenu["id"]);
			
			//if dev or admin then show all menus else show only active ones
			if((defined("DEV_MODE") && DEV_MODE == "1") || ($isAdmin && $_GET["showAllMenus"] == "1")) $dbi->where("aktif", array("0", "1"), "IN");
			else $dbi->where("aktif", "1");
			
			$dbi->orderBy("menuSirasi", "ASC");
			$subMenus = $dbi->get(_MENUS_, null, "id, menu, resim, url, aktif, newBadge, userExceptions");
							
			foreach($subMenus as $s => $subMenu)
			{
				//skip disabled menus for partners
				if(in_array($subMenu["id"], $disabledMenus)) continue;
								
				//if it is eokul and it is not in integrations then skip it
				if($subMenu["id"] == "864" && !empty($integrations) && !in_array("eokul", $integrations)) continue;
								
                //title
                $subMenus[$s]["title"] = substr($subMenu["menu"], 0, 1) == "_" ? translateWord($subMenu["menu"]) : $subMenu["menu"];

                //menu type
                $subMenus[$s]["menuType"] = (strpos($subMenu["url"], "blank") == false) ? "maintab" : "blank";
                
                //menu new page url
                //$subMenus[$s]["newPageUrl"] = str_replace(array("?blank=1", "&blank=1"), array("", ""), $subMenu["url"]);
        		
                //badge
                $subMenus[$s]["badge"] = "";
                if($subMenu["newBadge"] != "0000-00-00")
                {
                    $expDate = date("Y-m-d", strtotime($subMenu["newBadge"]." +7 days"));
                    
                    //if expired then update it
                    if($expDate < $simsDate) $dbi->where("id", $subMenu["id"])->update(_MENUS_, array("newBadge" => "0000-00-00"));
                    else $subMenus[$s]["badge"] = _LC_NEW;
                }
                
                //submenus
                $subMenus[$s]["submenus"] = array();
								
				//this one has sub menus
                if(empty($subMenu["url"]))
                {
                    /* sub sub menus */
                    //@TODO
                    /* We are not showing calendars in the menu now so we can think of removing this part */
					if($subMenu["id"] == "487" && $globalUserFolder != "admin") //Calendars submenu under School Operations
					{
						//get calendars as sub menus
						$activeCalendars = $dbi->where("schoolId", $ySubeKodu)->where("active", "on")->orderBy("calendarOrder", "asc")->get(_CALENDARS_);
						foreach($activeCalendars as $activeCalendar)
						{
							//if user type can see the calendar then show it in the menu
							if(empty($activeCalendar["whoCanSee"]) || in_array($userType, explode(",", $activeCalendar["whoCanSee"])))
							{
                                $subMenus[$s]["submenus"][] = array(
                                    'mID'       => $activeCalendar["id"],
                                    'menuType'  => "maintab",
                                    'title'     => $activeCalendar["title"],
                                    'resim'     => "calendar",
                                    'url'       => "index.php?op=myCalendars&newPanel&Id=".$activeCalendar["id"],
                                    'aktif'     => "1",
                                );
							}
						}
					}
                    //end of TODO
					else
					{
						//start query for sub sub menus
						$dbi->where($globalUserManagerMenu, "on");
						$dbi->where("parent_id", $subMenu["id"]);
						
						//if dev or admin then show all menus else show only active ones
						if((defined("DEV_MODE") && DEV_MODE == "1") || ($isAdmin && $_GET["showAllMenus"] == "1")) $dbi->where("aktif", array("0", "1"), "IN");
						else $dbi->where("aktif", "1");
						
						$dbi->orderBy("menuSirasi", "ASC");
						$subSubMenus = $dbi->get(_MENUS_, null, "id, menu, resim, url, aktif, newBadge, userExceptions");
						
						foreach($subSubMenus as $u => $subSubMenu)
						{
							//check timetabler menu if the integration is active
							if($subSubMenu["id"] == "849" && $genelAyarlar["timetabler_import"] == "0") continue;
							if(in_array($subSubMenu["id"], $disabledMenus)) continue;
						
                            //title
                			$title = substr($subSubMenu["menu"], 0, 1) == "_" ? translateWord($subSubMenu["menu"]) : $subSubMenu["menu"];
                            
                            //menu type
                            $menuType = (strpos($subSubMenu["url"], "blank") == false) ? "maintab" : "blank";
                            
                            //menu new page url
                            //$subSubMenus[$u]["newPageUrl"] = str_replace(array("?blank=1", "&blank=1"), array("", ""), $subSubMenu["url"]);
                            
                            //badge
                            $subSubMenus[$u]["badge"] = "";
				          	if($subSubMenu["newBadge"] != "0000-00-00")
							{
								$expDate = date("Y-m-d", strtotime($subSubMenu["newBadge"]." +7 days"));
								
								//if expired then update it
								if($expDate < $simsDate) $dbi->where("id", $subSubMenu["id"])->update(_MENUS_, array("newBadge" => "0000-00-00"));
								else $subSubMenus[$u]["badge"] = _LC_NEW;
								
							}
							
                            $subMenus[$s]["submenus"][] = array(
                                'mID'       => $subSubMenu["id"],
                                'menuType'  => $menuType,
                                'title'     => $title,
                                'resim'     => $subSubMenu["resim"],
                                'url'       => $subSubMenu["url"],
                                'aktif'     => $subSubMenu["aktif"]
                            );
						}
					}
												
				}
				
				/*
				//add vcloud manually
				if(!empty($integrations) && in_array("vcloud", $integrations) && $subMenu["id"] == "560" && empty($partnerId))
				{
					?>
						<li><a href="index.php?op=vcloud" target="_blank"><img src="img/partners/vcloud-icon.png"> Sebit VCloud</a></li>
					<?
				}
				*/
			}
			
			//if it is the forms then add custom forms here
			if($mainMenu["id"] == "594" && $globalUserFolder != "admin") //Forms Main Menu
			{
				//get custom forms as sub menus
				$dbi->join(_FORM_SETTINGS_. " s", "f.Id=s.formId", "LEFT");
				$dbi->where("f.schoolId", array("0", "$ySubeKodu"), "IN");
				$dbi->where("FIND_IN_SET(?, f.showInMenu)", array("$globalUserManagerMenu"));
				$dbi->orderBy("f.id", "asc");
				$activeForms = $dbi->get(_FORMS_. " f", null, "f.Id, f.title, s.publicLink");

				foreach($activeForms as $form)
				{
                    $subMenus[] = array(
                        'mID'       => $form["Id"],
                        'menuType'  => $menuType,
                        'title'     => $form["title"],
                        'resim'     => "wf-forms",
                        'url'       => "index.php?op=forms&action=" . $form["formId"] . "&formId=" . $form["Id"],
                        'aktif'     => "1"
                    );

				}
			}
			
			$mainMenus[$k]["submenus"] = $subMenus;
		}
		
		$this->mainMenus = $mainMenus;
	}
	
	function getVariables()
	{
	    global $globalUserFolder;
	    
	    //get schools
	    if($globalUserFolder != "admin") $this->getSchools();
	    
	    //get languages
	    $this->getLanguages();
	    
	    //get students
	    $this->getStudents();
	    
	    //get menus
	    $this->getMainMenus();
	    
	    //get all variables
	    $vars = get_object_vars($this);
	    
	    //return all variables
	    return $vars;
	}
	
}
?>
