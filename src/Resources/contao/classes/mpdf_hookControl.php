<?php

/**
 * @copyright  Softleister 2018
 * @author     Softleister <info@softleister.de>
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

namespace Softleister\Mpdftemplate;

class mpdf_hookControl extends \Backend
{
    //-----------------------------------------------------------------
    // myPrintArticleAsPdf:  create PDF with template file
    //-----------------------------------------------------------------
    public function myPrintArticleAsPdf( $strArticle, $objArticle )
    {
        global $objPage;
        $root_details = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")
                                       ->limit( 1 )
                                       ->execute( $objPage->rootId );

        //-- check conditions for a return --
        if($root_details->mpdftemplate != '1') return;                         // PDF template == OFF

        // get template pdf
        $root_details->pdfTplSRC = \FilesModel::findByUuid($root_details->pdfTplSRC)->path;
        if( !file_exists(TL_ROOT . '/' . $root_details->pdfTplSRC) ) return;  // template file not found

        // URL decode image paths (see #6411)
        $strArticle = preg_replace_callback('@(src="[^"]+")@', function ($arg) {
            return rawurldecode($arg[0]);
        }, $strArticle);

        // Handle line breaks in preformatted text
        $strArticle = preg_replace_callback('@(<pre.*</pre>)@Us', function ($arg) {
            return str_replace("\n", '<br>', $arg[0]);
        }, $strArticle);

        $strArticle = str_replace( array(chr(0xC2).chr(0xA0), chr(0xC2).chr(0xA9), chr(0xC2).chr(0xAE), chr(0xC2).chr(0xB0), chr(0xC3).chr(0x84), chr(0xC3).chr(0x96), chr(0xC2).chr(0x9C), chr(0xC3).chr(0xA4), chr(0xC3).chr(0xB6), chr(0xC3).chr(0xBC), chr(0xC3).chr(0x9F) ),
                                   array(' ', '©', '®', '°', 'Ä', 'Ö', 'Ü', 'ä', 'ö', 'ü', 'ß'),
                                   $strArticle);

        // Default PDF export using TCPDF
        $arrSearch = array
        (
            '@<span style="text-decoration: ?underline;?">(.*)</span>@Us',
            '@(<img[^>]+>)@',
            '@(<div[^>]+block[^>]+>)@',
            '@[\n\r\t]+@',
            '@<br( /)?><div class="mod_article@',
            '@href="([^"]+)(pdf=[0-9]*(&|&amp;)?)([^"]*)"@'
        );

        $arrReplace = array
        (
            '<u>$1</u>',
            '<br>$1',
            '<br>$1',
            ' ',
            '<div class="mod_article',
            'href="$1$4"'
        );

        $strArticle = preg_replace($arrSearch, $arrReplace, $strArticle);

        // mPDF configuration
        $l['a_meta_dir'] = 'ltr';
        $l['a_meta_charset'] = \Config::get('characterSet');
        $l['a_meta_language'] = substr($GLOBALS['TL_LANGUAGE'], 0, 2);
        $l['w_page'] = 'page';

        // Include libraries
        require_once(TL_ROOT . '/vendor/contao/core-bundle/src/Resources/contao/config/tcpdf.php');

        //-- Calculating dimensions
        $margins = unserialize($root_details->pdfMargin);                     // Margins as an array
        switch( $margins['unit'] ) {
            case 'cm':  $factor = 10.0;     break;
            default:    $factor = 1.0;
        }

        $pdfconfig = array (
                        'mode'          => 'utf-8',
                        'format'        => PDF_PAGE_FORMAT,
                        'orientation'   => PDF_PAGE_ORIENTATION,
                        'margin_top'    => !is_numeric($margins['top'])    ? PDF_MARGIN_TOP    : $margins['top'] * $factor,
                        'margin_right'  => !is_numeric($margins['right'])  ? PDF_MARGIN_RIGHT  : $margins['right'] * $factor,
                        'margin_bottom' => !is_numeric($margins['bottom']) ? PDF_MARGIN_BOTTOM : $margins['bottom'] * $factor,
                        'margin_left'   => !is_numeric($margins['left'])   ? PDF_MARGIN_LEFT   : $margins['left'] * $factor,
                     );                    
                    
        // Create new mPDF document
        $pdf = new \Mpdf\Mpdf( $pdfconfig );

        $pdf->SetImportUse();                                                                   // Vorbereitung auf Templateseiten
        $pagecount = $pdf->SetDocTemplate( TL_ROOT . '/' . $root_details->pdfTplSRC, true );    // Set PDF template
        
        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(PDF_AUTHOR);
        $pdf->SetTitle($objArticle->title);
        $pdf->SetSubject($objArticle->title);
        $pdf->SetKeywords($objArticle->keywords);

        $pdf->SetDisplayMode('fullpage', 'continuous');

        // Initialize document and add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN);

        // Include CSS
        if( ($root_details->pdfIgnoreCSS != '1') && file_exists(TL_ROOT . '/' . $GLOBALS['TL_CONFIG']['uploadPath'] . '/mpdf.css') ) {
            $styles = "<style>\n" . $this->css_optimize(file_get_contents(TL_ROOT . '/' . $GLOBALS['TL_CONFIG']['uploadPath'] . '/mpdf.css')) . "\n</style>\n";
            $strArticle = $styles . $strArticle;
        }

        // Write the HTML content
        $pdf->writeHTML( $strArticle );

        // Close and output PDF document
		$pdf->Output( \StringUtil::standardize(ampersand($objArticle->title, false)) . '.pdf', \Mpdf\Output\Destination::DOWNLOAD );

        // Stop script execution
        exit;
    }

    function css_optimize($buffer)
    {
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer); // remove comments
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);  // remove tabs, newlines, etc.
        $buffer = preg_replace('/\s\s+/', ' ', $buffer);                      // remove multiple spaces

        return $buffer;
    }

   //-----------------------------------------------------------------
}
