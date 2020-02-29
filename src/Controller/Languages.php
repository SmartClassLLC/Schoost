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

class Languages {
    
    private $languages = array();

    /* function */
	function constructor()
	{
	}
	
    /* function */
	function getLanguages($local = false)
	{
		global $dbi;

		$this->languages = $dbi->map("lang_code")->get(_GLOBAL_LANGUAGES_, null, array("lang_code", "lang_name"));		

		return $this->languages;
	}

}
