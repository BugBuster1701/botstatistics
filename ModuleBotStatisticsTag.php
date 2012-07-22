<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * Module BotStatistics - FE for InsertTags
 *
 * PHP version 5
 * @copyright  Glen Langer 2012 
 * @author     Glen Langer 
 * @package    BotStatistics 
 * @license    LGPL 
 */


/**
 * Class ModuleBotStatisticsTag 
 *
 * @copyright  Glen Langer 2012 
 * @author     Glen Langer 
 * @package    BotStatistics
 */
class ModuleBotStatisticsTag extends Frontend
{
	protected $BotStatus = false;
	protected $BotName   = '';
	protected $CURDATE   = '';
	
	/**
	 * Generate module
	 */
	public function replaceInsertTagsBotStatistics($strTag)
	{
	    require_once(TL_ROOT . '/system/modules/botstatistics/ModuleBotStatisticsVersion.php');
	    $arrTag = trimsplit('::', $strTag);
	    if ($arrTag[0] != 'cache_botstatistics')
	    {
	        return false; // nicht für uns
	    }
	    if (!isset($arrTag[2])) 
	    {
	        $this->loadLanguageFile('tl_botstatistics');
	        $this->log($GLOBALS['TL_LANG']['tl_botstatistics']['no_key'], 'ModuleBotStatisticsTag replaceInsertTagsBotStatistics '. BOTSTATISTICS_VERSION .'.'. BOTSTATISTICS_BUILD, 'ERROR');
	        return false;  // da fehlt was
	    }
        if (!isset($arrTag[3]) || strlen($arrTag[3])<1) 
        {
            $arrTag[3] = 0; // no page alias
        }
	    if ($arrTag[2] == 'count')
	    {
	        $this->import('Database');
	        $statusVisit  = $this->setBotCounter( (int)$arrTag[1] ); // Modul ID
	        $statusDetail = $this->setBotCounterDetails( (int)$arrTag[1], $arrTag[3] ); // Modul ID, Page Alias
	        
	        if ($statusVisit == true || $statusDetail == true)
	        {
	            return '<!-- c0n740 f0r3v3r '.$arrTag[3].' -->';
	        }
	        else
	        {
	            return '<!-- n0 c0un7 '.$arrTag[3].' -->';
	        }
	    }
	    else
	    {
	        $this->loadLanguageFile('tl_botstatistics');
	        $this->log($GLOBALS['TL_LANG']['tl_botstatistics']['wrong_key'], 'ModuleBotStatisticsTag replaceInsertTagsBotStatistics '. BOTSTATISTICS_VERSION .'.'. BOTSTATISTICS_BUILD, 'ERROR');
	        return false;  // da ist was falsch
	    }
	}// BotStatReplaceInsertTags
	
	/**
	 * Insert/Update Counter
	 */
	protected function setBotCounter($bid)
	{
	    $visit = false;
	    $ClientIP = bin2hex(sha1($bid . $this->Environment->remoteAddr,true)); // sha1 20 Zeichen, bin2hex 40 zeichen
	    $BlockTime = 60; //Sekunden
	    $this->CURDATE = date('Y-m-d');
	    
	    // Check Bot und setze $this->BotName
	    if ($this->isSetBot() === false)
	    {
	        return false;
	    }

	    if ($this->BotName === false)
	    {
	        //Bot erkannt aber keine Advanced Kennung :-(
	        $this->BotName = 'noname';
	        return false; // vorerst nicht zählen (GitHub #13)
	    }
	    
	    //Bot Blocker
	    $this->Database->prepare("DELETE FROM tl_botstatistics_blocker"
                	            ." WHERE CURRENT_TIMESTAMP - INTERVAL ? SECOND > bot_tstamp"
                	            ." AND bot_module_id=?")
                       ->executeUncached($BlockTime, $bid);
	    
	    //Test ob Bot Visits gesetzt werden muessen
	    $objBotIP = $this->Database->prepare("SELECT id"
                            	            ." FROM tl_botstatistics_blocker"
                            	            ." WHERE bot_module_id=? AND bot_ip=?")
                            	   ->limit(1)
	                               ->executeUncached($bid, $ClientIP);
	    
	    if ($objBotIP->numRows == 0) 
	    {
	        // nicht geblockt Visit zählen
	        
    	    // Doppelte Einträge verhindern bei zeitgleichen Zugriffen wenn noch kein Eintrag vorhanden ist
    	    // durch Insert Ignore und Unique Key
    	    $arrSet = array
    	    (
    	            'bot_module_id'=> $bid,
    	            'bot_date'     => $this->CURDATE,
    	            'bot_name'     => $this->BotName,
    	            'bot_counter'  => 0
    	    );
    	    $this->Database->prepare("INSERT IGNORE INTO tl_botstatistics_counter %s")->set($arrSet)->executeUncached();
    	    
    	    //Bot Visits lesen
    	    $objBotCounter = $this->Database->prepare("SELECT id, bot_counter"
                                    	            ." FROM tl_botstatistics_counter"
                                    	            ." WHERE bot_module_id=?"
                    	                            ." AND bot_date=?"
                                    	            ." AND bot_name=?")
    	                          ->executeUncached($bid, $this->CURDATE, $this->BotName);
    	    $objBotCounter->next();
    	    //zählen per update
    	    $this->Database->prepare("UPDATE tl_botstatistics_counter SET bot_counter=? WHERE id=?")
    	                   ->executeUncached($objBotCounter->bot_counter +1, $objBotCounter->id);
    	    //blocken
    	    $this->Database->prepare("INSERT INTO tl_botstatistics_blocker"
    	                            ." SET bot_module_id=?, bot_tstamp=CURRENT_TIMESTAMP, bot_ip=?")
    	                   ->executeUncached($bid, $ClientIP);
    	    $visit = true;
        }
	    return $visit;
	} //BotCountUpdate
	
	/**
	 * Insert/Update Counter Details
	 */
	protected function setBotCounterDetails($bid,$page_alias)
	{
	    if ($this->BotName === false)
	    {
	        return false; // vorerst nicht zählen (GitHub #13)
	    }
	    
	    //Detail Zählung
	    //detail on/off ermmitteln
	    //tl_botstatistics_counter.id ermitteln als pid
	    $objBotModul = $this->Database->prepare("SELECT tl_botstatistics_counter.id AS pid
                                        	            ,tl_module.botstatistics_details
                        	            FROM tl_botstatistics_counter
                        	            INNER JOIN tl_module ON tl_botstatistics_counter.bot_module_id=tl_module.id
                        	            WHERE tl_botstatistics_counter.bot_module_id=?
                        	            AND tl_botstatistics_counter.bot_name=?
                        	            AND tl_botstatistics_counter.bot_date=?")
                        	          ->executeUncached($bid, $this->BotName, $this->CURDATE);
	    $objBotModul->next();
	    if ($objBotModul->botstatistics_details)
	    {
    	    // Doppelte Einträge verhindern bei zeitgleichen Zugriffen wenn noch kein Eintrag vorhanden ist
    	    // durch Insert Ignore und Unique Key
    	    $arrSet = array
    	    (
    	            'id'  => 0,
    	            'pid' => $objBotModul->pid,
    	            'bot_page_alias'         => $page_alias,
    	            'bot_page_alias_counter' => 0
    	    );
    	    $this->Database->prepare("INSERT IGNORE INTO tl_botstatistics_counter_details %s")
    	                   ->set($arrSet)->executeUncached();
    	    
    	    $this->Database->prepare("UPDATE tl_botstatistics_counter_details 
                                      SET bot_page_alias_counter=bot_page_alias_counter+1
                                      WHERE pid=? AND bot_page_alias=?")
                           ->executeUncached($objBotModul->pid,$page_alias);
    	    return true;
    	}
	    return false;
	}
	
	/**
	 * Spider Bot Check, set Bot Name
	 */
	protected function isSetBot()
	{
	    if (!in_array('botdetection', $this->Config->getActiveModules()))
	    {
	        //botdetection Modul fehlt, Abbruch, Meldung kommt bereits per Hook
	        return false;
	    }
	    $this->import('ModuleBotDetection');
	    if ($this->ModuleBotDetection->BD_CheckBotAgent() || $this->ModuleBotDetection->BD_CheckBotIP()) 
	    {
	        //log_message('BotStatus True','debug.log');
	        $this->BotStatus = true;
	    }
	    
	    $this->BotName = $this->ModuleBotDetection->BD_CheckBotAgentAdvanced();
	    //log_message('BotName: '.$this->BotName,'debug.log');
	    if ($this->BotStatus === true || $this->BotName !== false) 
	    {
	        return true;
	    }
	    return false;
	} //CheckBot
	
}//class

?>