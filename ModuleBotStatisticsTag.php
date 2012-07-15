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
	
	/**
	 * Generate module
	 */
	public function replaceInsertTagsBotStatistics($strTag)
	{
	    $arrTag = trimsplit('::', $strTag);
	    if ($arrTag[0] != 'cache_botstatistics')
	    {
	        return false; // nicht für uns
	    }
	    if (!isset($arrTag[2])) 
	    {//TODO: Meldung im Systemlog
	        log_message('replaceInsertTagsBotStatistics Tag-2 fehlt (count): ','debug.log');
	        return false;  // da fehlt was
	    }
        if (!isset($arrTag[3]) || strlen($arrTag[3])<1) 
        {
            $arrTag[3] = 0; // no page alias
        }
	    if ($arrTag[2] == 'count')
	    {
	        $this->import('Database');
	        $status = $this->setBotCounter( (int)$arrTag[1], $arrTag[3] ); // Modul ID, Page Alias
	        
	        if ($status == true)
	        {
	            return '<!-- c0n740 f0r3v3r '.$arrTag[3].' -->';
	        }
	        else
	        {
	            return '<!-- n0 c0un7 '.$arrTag[3].' -->';
	        }
	    }
	}// BotStatReplaceInsertTags
	
	/**
	 * Insert/Update Counter
	 */
	protected function setBotCounter($bid,$page_alias)
	{
	    $ClientIP = bin2hex(sha1($bid . $this->Environment->remoteAddr,true)); // sha1 20 Zeichen, bin2hex 40 zeichen
	    $BlockTime = 60; //Sekunden
	    $CURDATE = date('Y-m-d');
	    
	    if ($this->isSetBot() === false)
	    {
	        return false;
	    }

	    if ($this->BotName === false)
	    {
	        //keine Advanced Kennung :-(
	        $this->BotName = 'noname';
	        return false; // vorerst nicht zählen (GitHub #13)
	    }
	    
	    //Bot Blocker
	    $this->Database->prepare("DELETE FROM tl_botstatistics_blocker"
                	            ." WHERE CURRENT_TIMESTAMP - INTERVAL ? SECOND > bot_tstamp"
                	            ." AND bid=?")
                       ->executeUncached($BlockTime, $bid);
	    
	    //Test ob Bot Visits gesetzt werden muessen
	    $objBotIP = $this->Database->prepare("SELECT id"
                            	            ." FROM tl_botstatistics_blocker"
                            	            ." WHERE bid=? AND bot_ip=?")
                            	   ->limit(1)
	                               ->executeUncached($bid, $ClientIP);
	    
	    if ($objBotIP->numRows == 0) 
	    {
	        // nicht geblockt Visit zählen
	        
    	    // Doppelte Einträge verhindern bei zeitgleichen Zugriffen wenn noch kein Eintrag vorhanden ist
    	    // durch Insert Ignore und Unique Key
    	    $arrSet = array
    	    (
    	            'bid'          => $bid,
    	            'bot_date'     => $CURDATE,
    	            'bot_name'     => $this->BotName,
    	            'bot_counter'  => 0
    	    );
    	    $this->Database->prepare("INSERT IGNORE INTO tl_botstatistics_counter %s")->set($arrSet)->executeUncached();
    	    
    	    //Bot Visits lesen
    	    $objBotCounter = $this->Database->prepare("SELECT id, bot_counter"
                                    	            ." FROM tl_botstatistics_counter"
                                    	            ." WHERE bid=?"
                    	                            ." AND bot_date=?"
                                    	            ." AND bot_name=?")
    	                          ->executeUncached($bid, $CURDATE, $this->BotName);
    	    $objBotCounter->next();
    	    //zählen per update
    	    $this->Database->prepare("UPDATE tl_botstatistics_counter SET bot_counter=? WHERE id=?")
    	                   ->executeUncached($objBotCounter->bot_counter +1, $objBotCounter->id);
    	    //blocken
    	    $this->Database->prepare("INSERT INTO tl_botstatistics_blocker"
    	                            ." SET bid=?, bot_tstamp=CURRENT_TIMESTAMP, bot_ip=?")
    	                   ->executeUncached($bid, $ClientIP);
        }
        
        //Detail Zählung
	    //detail on/off ermmitteln
	    //tl_botstatistics_counter.id ermitteln als pid
	    $objBotModul = $this->Database->prepare("SELECT tl_botstatistics_counter.id AS pid
                                                       ,tl_module.botstatistics_details
                                                FROM tl_botstatistics_counter
                                                INNER JOIN tl_module ON tl_botstatistics_counter.bid=tl_module.id
                                                WHERE tl_botstatistics_counter.bid=?
                                                  AND tl_botstatistics_counter.bot_name=?
                                                  AND tl_botstatistics_counter.bot_date=?")
	                                  ->executeUncached($bid, $this->BotName, $CURDATE);
	    $objBotModul->next();
	    if ($objBotModul->botstatistics_details) 
	    {
	        $this->setBotCounterDetails($objBotModul->pid, $page_alias);
	    }
	    
	    return true;
	} //BotCountUpdate
	
	/**
	 * Insert/Update Counter Details
	 */
	protected function setBotCounterDetails($pid,$page_alias)
	{
	    // Doppelte Einträge verhindern bei zeitgleichen Zugriffen wenn noch kein Eintrag vorhanden ist
	    // durch Insert Ignore und Unique Key
	    $arrSet = array
	    (
	            'id'  => 0,
	            'pid' => $pid,
	            'bot_page_alias'         => $page_alias,
	            'bot_page_alias_counter' => 0
	    );
	    $this->Database->prepare("INSERT IGNORE INTO tl_botstatistics_counter_details %s")
	                   ->set($arrSet)->executeUncached();
	    
	    $this->Database->prepare("UPDATE tl_botstatistics_counter_details 
                                  SET bot_page_alias_counter=bot_page_alias_counter+1
                                  WHERE pid=? AND bot_page_alias=?")
                       ->executeUncached($pid,$page_alias);
	    return true;
	}
	
	/**
	 * Spider Bot Check, set Bot Name
	 */
	protected function isSetBot()
	{
	    if (!in_array('botdetection', $this->Config->getActiveModules()))
	    {
	        //botdetection Modul fehlt, Abbruch
	        //$this->log('BotDetection extension required!', 'ModuleBotStatistics CheckBot', TL_ERROR);
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