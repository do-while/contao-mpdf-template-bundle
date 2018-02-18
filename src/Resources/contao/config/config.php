<?php

/**
 * @copyright  Softleister 2018
 * @author     Softleister <info@softleister.de>
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

define('MPDFTEMPLATE_VERSION', '1.0');
define('MPDFTEMPLATE_BUILD'  , '0');

//-------------------------------------------------------------------------
//  HOOKS
//-------------------------------------------------------------------------
$GLOBALS['TL_HOOKS']['printArticleAsPdf'][] = array('Softleister\Mpdftemplate\mpdf_hookControl', 'myPrintArticleAsPdf');
