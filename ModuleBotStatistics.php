<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * Module BotStatistics - FE
 *
 * PHP version 5
 * @copyright  Glen Langer 2012 
 * @author     Glen Langer 
 * @package    BotStatistics 
 * @license    LGPL 
 * @filesource
 */


/**
 * Class ModuleBotStatistics 
 *
 * @copyright  Glen Langer 2012 
 * @author     Glen Langer 
 * @package    BotStatistics
 */
class ModuleBotStatistics extends Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_botstatistics_fe';

	protected $BotStatus = false;
	protected $BotName   = '';
	
	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
	    if (TL_MODE == 'BE')
	    {
	        $objTemplate = new BackendTemplate('be_wildcard');
	        $objTemplate->wildcard = '### BotStatistics Counter ###';
	        $objTemplate->title = $this->headline;
	        $objTemplate->id = $this->id;
	        $objTemplate->link = $this->name;
	        $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;
	        return $objTemplate->parse();
	    }
	
	    return parent::generate();
	}
	
	/**
	 * Generate module
	 */
	protected function compile()
	{
	    $this->import('Database');
	    $this->BotCountUpdate('1'); // Modul ID
	    return;
	}
	
	/**
	 * Insert/Update Counter
	 */
	protected function BotCountUpdate($bid)
	{
	    $ClientIP = bin2hex(sha1($bid . $this->Environment->remoteAddr,true)); // sha1 20 Zeichen, bin2hex 40 zeichen
	    $BlockTime = 1800; //Sekunden
	    $CURDATE = date('Y-m-d');
	    //Bot Blocker
	    $this->Database->prepare("DELETE FROM tl_botstatistics_blocker"
                	            ." WHERE CURRENT_TIMESTAMP - INTERVAL ? SECOND > bot_tstamp"
                	            ." AND bid=? AND bot_ip=?")
             ->executeUncached($BlockTime, $bid, $ClientIP);
	    
	    if ($this->CheckBot() === false) 
	    {
	        return false;
	    }
	    if ($this->BotName === false) {
	        //keine Advanced Kennung :-(
	        $this->BotName = 'noname';
	    }
	    // Doppelte Einträge verhindern bei zeitgleichen Zugriffen wenn noch kein Eintrag vorhanden ist
	    $arrSet = array
	    (
	            'bid'          => $bid,
	            'bot_date'     => $CURDATE,
	            'bot_name'     => $this->BotName,
	            'bot_counter'  => 1
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
	    return;
	}
	
	/**
	 * Spider Bot Check
	 */
	protected function CheckBot()
	{
	    if (!in_array('botdetection', $this->Config->getActiveModules()))
	    {
	        //botdetection Modul fehlt, Abbruch
	        $this->log('BotDetection extension required!', 'ModuleBotStatistics CheckBot', TL_ERROR);
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