<?php

/**
 * @copyright  Softleister 2018-2022
 * @author     Softleister <info@softleister.de>
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_page']['mpdftemplate'] = array('PDF-Ausgabe mit PDF-Vorlage', 'PDF-Ausgaben mit Vorlage gestalten, z.B. mit Firmenbriefbogen.');
$GLOBALS['TL_LANG']['tl_page']['pdfTplSRC']    = array('PDF-Vorlagedatei', 'Geben Sie eine Vorlagedatei an, die für PDF-Ausgaben verwendet werden soll.');
$GLOBALS['TL_LANG']['tl_page']['pdfMargin']    = array('Randbereiche', 'Passen Sie die Ränder oben, rechts, unten, links an die Vorlagedatei an.');
$GLOBALS['TL_LANG']['tl_page']['pdfIgnoreCSS'] = array($GLOBALS['TL_CONFIG']['uploadPath'].'/mpdf.css ignorieren', 'Das mPDF-Stylesheet '.$GLOBALS['TL_CONFIG']['uploadPath'].'/mpdf.css nicht einbinden.');
$GLOBALS['TL_LANG']['tl_page']['pdfCustomCSS'] = array('Eigenes CSS', 'Die ausgewählten Stylesheets werden bei der PDF Ausgabe inkludiert.');
$GLOBALS['TL_LANG']['tl_page']['mpdf_addon']   = array('AddOn-Template', 'In diesem Template können zusätzliche Befehle an mpdf übergeben werden.');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_page']['pdf_legend']   = 'PDF-Vorlage';
