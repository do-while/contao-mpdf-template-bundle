<?php

/**
 * @copyright  Softleister 2018
 * @author     Softleister <info@softleister.de>
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_page']['mpdftemplate'] = array('PDF output with PDF template', 'PDF output with a template design, i.e. with company letterhead.');
$GLOBALS['TL_LANG']['tl_page']['pdfTplSRC']    = array('PDF template file', 'Enter a template file that will be used for PDF output.');
$GLOBALS['TL_LANG']['tl_page']['pdfMargin']    = array('Marginal areas', 'Adjust the margins up, right, bottom and left corresponding to the template file.');
$GLOBALS['TL_LANG']['tl_page']['pdfIgnoreCSS'] = array('Skip '.$GLOBALS['TL_CONFIG']['uploadPath'].'/mpdf.css', 'Do not include the mPDF style sheet ('.$GLOBALS['TL_CONFIG']['uploadPath'].'/mpdf.css).');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_page']['pdf_legend']   = 'PDF template';
