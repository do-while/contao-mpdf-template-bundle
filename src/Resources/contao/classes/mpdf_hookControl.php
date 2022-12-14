<?php

/**
 * @copyright  Softleister 2018-2022
 * @author     Softleister <info@softleister.de>
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

namespace Softleister\Mpdftemplate;

use Contao\Backend;
use Contao\StringUtil;
use Contao\Config;
use Contao\FilesModel;
use Contao\BackendTemplate;
use Contao\Input;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class mpdf_hookControl extends Backend
{
    //-----------------------------------------------------------------
    // myPrintArticleAsPdf:  create PDF with template file
    //
    //  $objArticle->mpdftemplate
    //  $objArticle->pdfTplSRC
    //  $objArticle->pdfMargin
    //-----------------------------------------------------------------
    public function myPrintArticleAsPdf( $strArticle, $objArticle )
    {
        global $objPage;

        $root_details = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")
                                       ->limit( 1 )
                                       ->execute( $objPage->rootId );

        $mpdftemplate = $root_details->mpdftemplate;
        $pdfTplSRC    = $root_details->pdfTplSRC;
        $pdfAddon     = $root_details->mpdf_addon;
        $pdfMargin    = StringUtil::deserialize( $root_details->pdfMargin, true );
        if( $pdfMargin['unit'] ?? null === 'cm' ) {
            if( !empty($pdfMargin['bottom']) && is_numeric($pdfMargin['bottom']) ) $pdfMargin['bottom'] *= 10.0;
            if( !empty($pdfMargin['left'])   && is_numeric($pdfMargin['left']) )   $pdfMargin['left']   *= 10.0;
            if( !empty($pdfMargin['right'])  && is_numeric($pdfMargin['right']) )  $pdfMargin['right']  *= 10.0;
            if( !empty($pdfMargin['top'])    && is_numeric($pdfMargin['top']) )    $pdfMargin['top']    *= 10.0;
        }

        if( $objArticle->mpdftemplate == 1 ) {                                              // IF( Overwrite settings )
            $mpdftemplate = 1;                                                              //   PDF template = ON
            if( !empty( $objArticle->pdfTplSRC ) ) $pdfTplSRC = $objArticle->pdfTplSRC;     //   IF( File specified ) Overwrite the UUID

            $margin = StringUtil::deserialize( $objArticle->pdfMargin );
            $factor = $margin['unit'] === 'cm' ? 10.0 : 1.0;
            if( !empty($margin['bottom']) ) $pdfMargin['bottom'] = $margin['bottom'] * $factor;
            if( !empty($margin['left']) )   $pdfMargin['left']   = $margin['left']   * $factor;
            if( !empty($margin['right']) )  $pdfMargin['right']  = $margin['right']  * $factor;
            if( !empty($margin['top']) )    $pdfMargin['top']    = $margin['top']    * $factor;
        }

        //-- check conditions for a return --
        if( $mpdftemplate != '1' ) return;                         // PDF template == OFF

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
        $l['a_meta_charset'] = Config::get('characterSet');
        $l['a_meta_language'] = substr($GLOBALS['TL_LANGUAGE'], 0, 2);
        $l['w_page'] = 'page';


        //-- Include Settings
        $tcpdfinit = Config::get("pdftemplateTcpdf");

        // 1: Own settings addressed via app/config/config.yml
        if( !empty($tcpdfinit) && file_exists(TL_ROOT . '/' . $tcpdfinit) ) {
            require_once(TL_ROOT . '/' . $tcpdfinit);
        }
        // 2: Own tcpdf.php from files directory
        else if( file_exists(TL_ROOT . '/files/tcpdf.php') ) {
            require_once(TL_ROOT . '/files/tcpdf.php');
        }
        // 3: From config directory (up to Contao 4.6)
        else if( file_exists(TL_ROOT . '/vendor/contao/core-bundle/src/Resources/contao/config/tcpdf.php') ) {
            require_once(TL_ROOT . '/vendor/contao/core-bundle/src/Resources/contao/config/tcpdf.php');
        }
        // 4: From config directory of tcpdf-bundle (from Contao 4.7)
        else if( file_exists(TL_ROOT . '/vendor/contao/tcpdf-bundle/src/Resources/contao/config/tcpdf.php') ) {
            require_once(TL_ROOT . '/vendor/contao/tcpdf-bundle/src/Resources/contao/config/tcpdf.php');
        }
        // 5: not found? - Then take it from this extension
        else {
            require_once(TL_ROOT . '/vendor/do-while/contao-mpdf-template-bundle/src/Resources/contao/config/tcpdf.php');
        }

        $pdfconfig = array (
                        'mode'          => 'utf-8',
                        'format'        => PDF_PAGE_FORMAT,
                        'orientation'   => PDF_PAGE_ORIENTATION,
                        'margin_top'    => !is_numeric( $pdfMargin['top'] )    ? PDF_MARGIN_TOP    : $pdfMargin['top'],
                        'margin_right'  => !is_numeric( $pdfMargin['right'] )  ? PDF_MARGIN_RIGHT  : $pdfMargin['right'],
                        'margin_bottom' => !is_numeric( $pdfMargin['bottom'] ) ? PDF_MARGIN_BOTTOM : $pdfMargin['bottom'],
                        'margin_left'   => !is_numeric( $pdfMargin['left'] )   ? PDF_MARGIN_LEFT   : $pdfMargin['left'],
                     );                    
                    
        // Create new mPDF document
        $pdf = new Mpdf( $pdfconfig );

        //=== mPDF Versioncheck ===
        if( method_exists( $pdf, 'SetImportUse' ) ) {               // up to mPDF Version < 8.0 only
            $pdf->SetImportUse();                                   // Vorbereitung auf Templateseiten
        }

        // get template pdf
        if( $pdfTplSRC && null !== ($tplFile = FilesModel::findByUuid( $pdfTplSRC )) ) {
            if (file_exists(TL_ROOT . '/' . $tplFile->path)) {
                $pdf->SetDocTemplate(TL_ROOT . '/' . $tplFile->path, true);     // . Set PDF template
            }
        }
        
        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(PDF_AUTHOR);
        $pdf->SetTitle($objArticle->title);
        $pdf->SetSubject($objArticle->title);
        $pdf->SetKeywords($objArticle->keywords);

        $pdf->SetDisplayMode('fullpage', 'continuous');

        // AddOn-Template verarbeiten
        if( !empty($this->mpdf_addon) ) $pdfAddon = $this->mpdf_addon;      // Addon im Artikel überschreibt das AddOn im Startpunkt
        $tpl = empty($pdfAddon) ? 'mpdf_default' : $pdfAddon;               // PDF-Template für ergänzende Einträge, wie Header, Footer, ...

        // AddOn-Init
        $objmPdfTpl = new BackendTemplate( $tpl );
        $objmPdfTpl->language = $language;
        $objmPdfTpl->init = true;                                           // INIT-Aufruf
        $objmPdfTpl->pdf = $pdf;
        $objmPdfTpl->parse( );                                              // Template verarbeiten

        // Initialize document and add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN);

        // AddOn-Content
        $objmPdfTpl = new BackendTemplate( $tpl );
        $objmPdfTpl->language = $language;
        $objmPdfTpl->init = false;                                          // 2. Aufruf auf Seite 1
        $objmPdfTpl->pdf = $pdf;
        $objmPdfTpl->parse( );                                              // Template verarbeiten

        // Include CSS
        $styles = '';

        if( ($root_details->pdfIgnoreCSS != '1') && file_exists(TL_ROOT . '/' . $GLOBALS['TL_CONFIG']['uploadPath'] . '/mpdf.css') ) {
            $styles .= "<style>\n" . $this->css_optimize(file_get_contents(TL_ROOT . '/' . $GLOBALS['TL_CONFIG']['uploadPath'] . '/mpdf.css')) . "\n</style>\n";
        }

        if ($root_details->pdfCustomCSS) {
            $cssFiles = StringUtil::deserialize($root_details->pdfCustomCSS, true);
            $styles .= "<style>\n";

            foreach ($cssFiles as $cssFileUuid) {
                if (null !== ($cssFile = FilesModel::findByUuid($cssFileUuid)) && file_exists(TL_ROOT . '/' . $cssFile->path)) {
                    $styles .= $this->css_optimize(file_get_contents(TL_ROOT . '/' . $cssFile->path));
                }
            }

            $styles .= "\n</style>\n";
        }

        $strArticle = $styles . $strArticle;

        // Write the HTML content
        $pdf->writeHTML( $strArticle );

        // file name can get from URL (default is the title of the article)
        $filename = $objArticle->title;
        if( !empty( Input::get('t') ) ) {
            $filename = Input::get('t');
        }

        // Close and output PDF document
		$pdf->Output( StringUtil::standardize(ampersand($filename, false)) . '.pdf', Destination::DOWNLOAD );

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
