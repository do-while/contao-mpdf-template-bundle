<?php

/**
 * @copyright  Softleister 2022
 * @author     Softleister <info@softleister.de>
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

//-------------------------------------------------------------------------
//  HOOKS
//-------------------------------------------------------------------------
$GLOBALS['TL_HOOKS']['printArticleAsPdf'][] = array('Softleister\Mpdftemplate\mpdf_hookControl', 'myPrintArticleAsPdf');
