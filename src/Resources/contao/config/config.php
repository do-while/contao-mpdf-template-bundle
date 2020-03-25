<?php

/**
 * @copyright  Softleister 2020
 * @author     Softleister <info@softleister.de>
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

define('MPDFTEMPLATE_VERSION', '1.4');
define('MPDFTEMPLATE_BUILD'  , '0');

//-------------------------------------------------------------------------
//  HOOKS
//-------------------------------------------------------------------------
$GLOBALS['TL_HOOKS']['printArticleAsPdf'][] = array('Softleister\Mpdftemplate\mpdf_hookControl', 'myPrintArticleAsPdf');
