<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * PHP version 5
 * @copyright  Glen Langer 2012 
 * @author     Glen Langer 
 * @package    BotStatistics 
 * @license    LGPL 
 * @filesource
 */


/**
 * -------------------------------------------------------------------------
 * BACK END MODULES
 * -------------------------------------------------------------------------
 *
 * Back end modules are stored in a global array called "BE_MOD". Each module 
 * has certain properties like an icon, an optional callback function and one 
 * or more tables. Each module belongs to a particular group.
 * 
 *   $GLOBALS['BE_MOD'] = array
 *   (
 *       'group_1' => array
 *       (
 *           'module_1' => array
 *           (
 *               'tables'       => array('table_1', 'table_2'),
 *               'key'          => array('Class', 'method'),
 *               'callback'     => 'ClassName',
 *               'icon'         => 'path/to/icon.gif',
 *               'stylesheet'   => 'path/to/stylesheet.css',
 *               'javascript'   => 'path/to/javascript.js'
 *           )
 *       )
 *   );
 * 
 * Use function array_insert() to modify an existing modules array.
 */
$GLOBALS['BE_MOD']['system']['botstatistics'] = array
(
        'callback'   => 'ModuleBotStatisticsStat',
        'icon'       => 'system/modules/botstatistics/html/botstatistics2.png',
        'stylesheet' => 'system/modules/botstatistics/html/mod_botstatistics_be.css',
);

/**
 * -------------------------------------------------------------------------
 * FRONT END MODULES
 * -------------------------------------------------------------------------
 *
 * List all front end modules and their class names.
 * 
 *   $GLOBALS['FE_MOD'] = array
 *   (
 *       'group_1' => array
 *       (
 *           'module_1' => 'Contentlass',
 *           'module_2' => 'Contentlass'
 *       )
 *   );
 * 
 * Use function array_insert() to modify an existing CTE array.
 */
array_insert($GLOBALS['FE_MOD']['miscellaneous'], 0, array
(
        'botstatistics' => 'ModuleBotStatistics',
)); 


/**
 * -------------------------------------------------------------------------
 * HOOKS
 * -------------------------------------------------------------------------
 *
 * Hooking allows you to register one or more callback functions that are 
 * called on a particular event in a specific order. Thus, third party 
 * extensions can add functionality to the core system without having to
 * modify the source code.
 * 
 *   $GLOBALS['TL_HOOKS'] = array
 *   (
 *       'hook_1' => array
 *       (
 *           array('Class', 'Method'),
 *           array('Class', 'Method')
 *       )
 *   );
 * 
 * Use function array_insert() to modify an existing hooks array.
 */
$GLOBALS['TL_HOOKS']['parseBackendTemplate'][]  = array('BotStatisticsHelper', 'checkExtensions');
$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('ModuleBotStatisticsTag', 'replaceInsertTagsBotStatistics');


/**
 * -------------------------------------------------------------------------
 * CRON JOBS
 * -------------------------------------------------------------------------
 *
 * Register methods to be executed at certain intervals.
 *
 *   weekly = run once a week
 *   daily  = run once a day
 *   hourly = run every hour
 */
$GLOBALS['TL_CRON']['daily'][]  = array('BotStatisticsCron', 'deleteStatisticsData');

?>