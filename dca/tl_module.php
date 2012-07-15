<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 * 
 * Modul BotStatistics - Backend DCA tl_module
 *
 * This file modifies the data container array of table tl_module.
 *
 * PHP version 5
 * @copyright  Glen Langer 2009..2012
 * @author     Glen Langer
 * @package    BotStatistics
 * @license    LGPL
 */


/**
 * Add palettes to tl_module
 */
//$GLOBALS['TL_DCA']['tl_module']['palettes']['botstatistics']   = 'name,type,headline;botstatistics_name;guests,protected;align,space,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['botstatistics']   = 'name,type;botstatistics_name,botstatistics_details';



/**
 * Add fields to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['botstatistics_name'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['botstatistics_name'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'search'                  => true,
	'eval'                    => array('mandatory'=>true, 'maxlength'=>64, 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['botstatistics_details'] = array(
        'label'		=> &$GLOBALS['TL_LANG']['tl_module']['botstatistics_details'],
        'inputType'	=> 'checkbox',
        'eval'      => array('mandatory'=>false, 'tl_class'=>'w50 m12')
);

?>