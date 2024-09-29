<?php

declare(strict_types=1);

/**
 * @copyright  Softleister 2018-2024
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

namespace Softleister\Mpdftemplate;

use Mpdf\Mpdf;
use Contao\Input;
use Contao\System;
use Contao\Backend;

use Contao\Database;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\BackendTemplate;
use Contao\FrontendTemplate;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Softleister\MpdftemplateBundle\EventListener\BeforeWriteArticleAsPdfEvent;
use Softleister\MpdftemplateBundle\EventListener\BeforeOutputArticleAsPdfEvent;
use Symfony\Component\VarDumper\VarDumper;

class mpdf_tools extends Backend
{
    //-----------------------------------------------------------------
    // printArticleAsPdf:  create PDF with template file
    //-----------------------------------------------------------------
    public static function printArticleAsPdf( String $strArticle, FrontendTemplate $template )
    {
        global $objPage;
        $db = Database::getInstance( );

        $rootDir = System::getContainer( )->getParameter( 'kernel.project_dir' );

        $objRootPage = $db->prepare("SELECT * FROM tl_page WHERE id=?")
                          ->limit( 1 )
                          ->execute( $objPage->rootId );

        if( $objRootPage->numRows < 1 ) {
            $mpdftemplate = false;
            $pdfTplSRC    = null;
            $pdfAddon     = 'mpdf_default';
            $pdfMargin    = ['bottom' => '0', 'left' => '0', 'right' => '0', 'top' => '0', 'unit' => 'mm'];
        }
        else {
            $mpdftemplate = $objRootPage->mpdftemplate;
            $pdfTplSRC    = $objRootPage->pdfTplSRC;
            $pdfAddon     = $objRootPage->mpdf_addon;
            $pdfMargin    = StringUtil::deserialize( $objRootPage->pdfMargin, true );
        }

        // ggf. Umrechnung in [mm]
        $factor = $pdfMargin['unit'] === 'cm' ? 10.0 : 1.0;
        if( !empty( $pdfMargin['bottom'] ) && is_numeric( $pdfMargin['bottom'] ) ) $pdfMargin['bottom'] *= $factor;
        if( !empty( $pdfMargin['left'] )   && is_numeric( $pdfMargin['left'] ) )   $pdfMargin['left']   *= $factor;
        if( !empty( $pdfMargin['right'] )  && is_numeric( $pdfMargin['right'] ) )  $pdfMargin['right']  *= $factor;
        if( !empty( $pdfMargin['top'] )    && is_numeric( $pdfMargin['top'] ) )    $pdfMargin['top']    *= $factor;

        if( $template->mpdftemplate ) {                                                     // IF( Overwrite settings in article )
            $mpdftemplate = true;                                                           //   PDF template = ON
            if( !empty( $template->pdfTplSRC ) ) $pdfTplSRC = $template->pdfTplSRC;         //   IF( File specified ) Overwrite the UUID

            $margin = StringUtil::deserialize( $template->pdfMargin );
            $factor = $margin['unit'] === 'cm' ? 10.0 : 1.0;
            if( !empty( $margin['bottom'] ) && is_numeric( $margin['bottom'] ) ) $pdfMargin['bottom'] = $margin['bottom'] * $factor;
            if( !empty( $margin['left'] )   && is_numeric( $margin['left'] ) )   $pdfMargin['left']   = $margin['left']   * $factor;
            if( !empty( $margin['right'] )  && is_numeric( $margin['right'] ) )  $pdfMargin['right']  = $margin['right']  * $factor;
            if( !empty( $margin['top'] )    && is_numeric( $margin['top'] ) )    $pdfMargin['top']    = $margin['top']    * $factor;

            // AddOn-Template verarbeiten
            if( !empty( $template->mpdf_addon ) ) $pdfAddon = $template->mpdf_addon;        // Addon im Artikel überschreibt das AddOn im Startpunkt
        }

        //-- check conditions for a return --
        if( !$mpdftemplate ) return;                            // PDF template == OFF

        // URL decode image paths (see #6411)
        $strArticle = preg_replace_callback( '@(src="[^"]+")@', function ($arg) { return rawurldecode( $arg[0] ); }, $strArticle );

        // Handle line breaks in preformatted text
        $strArticle = preg_replace_callback( '@(<pre.*</pre>)@Us', function ($arg) { return str_replace( "\n", '<br>', $arg[0] ); }, $strArticle );

        $strArticle = str_replace( array(chr(0xC2).chr(0xA0), chr(0xC2).chr(0xA9), chr(0xC2).chr(0xAE), chr(0xC2).chr(0xB0), chr(0xC3).chr(0x84), chr(0xC3).chr(0x96), chr(0xC2).chr(0x9C), chr(0xC3).chr(0xA4), chr(0xC3).chr(0xB6), chr(0xC3).chr(0xBC), chr(0xC3).chr(0x9F) ),
                                   array(' ', '©', '®', '°', 'Ä', 'Ö', 'Ü', 'ä', 'ö', 'ü', 'ß'),
                                   $strArticle );

        // Default PDF export using TCPDF
        $arrSearch = [
            '@<form.*</form>@Us',
            '@<a [^>]*href="[^"]*javascript:[^>]+>.*</a>@Us',
            '@<span style="text-decoration: ?underline;?">(.*)</span>@Us',
            '@(<img[^>]+>)@',
            '@(<div[^>]+block[^>]+>)@',
            '@[\n\r\t]+@',
            '@<br( /)?><div class="mod_article@',
            '@href="([^"]+)(pdf=[0-9]*(&|&amp;)?)([^"]*)"@',
        ];

        $arrReplace = [
            '',
            '',
            '<u>$1</u>',
            '<br>$1',
            '<br>$1',
            ' ',
            '<div class="mod_article',
            'href="$1$4"',
        ];

        $strArticle = preg_replace( $arrSearch, $arrReplace, $strArticle );

        //-- Include Settings
        require_once( $rootDir . '/vendor/do-while/contao-mpdf-template-bundle/contao/config/mpdf.php' );

        // mPDF configuration
        $pdfconfig = array (
                        'mode'          => System::getContainer( )->getParameter( 'kernel.charset' ),
                        'format'        => PDF_PAGE_FORMAT,
                        'orientation'   => PDF_PAGE_ORIENTATION,
                        'margin_top'    => !is_numeric( $pdfMargin['top'] )    ? PDF_MARGIN_TOP    : $pdfMargin['top'],
                        'margin_right'  => !is_numeric( $pdfMargin['right'] )  ? PDF_MARGIN_RIGHT  : $pdfMargin['right'],
                        'margin_bottom' => !is_numeric( $pdfMargin['bottom'] ) ? PDF_MARGIN_BOTTOM : $pdfMargin['bottom'],
                        'margin_left'   => !is_numeric( $pdfMargin['left'] )   ? PDF_MARGIN_LEFT   : $pdfMargin['left'],
                     );

        // Create new mPDF document
        $pdf = new Mpdf( $pdfconfig );

        // get template pdf
        if( $pdfTplSRC && null !== ( $tplFile = FilesModel::findByUuid( $pdfTplSRC ) ) ) {
            if( file_exists( $rootDir . '/' . $tplFile->path ) ) {
                $pdf->SetDocTemplate( $rootDir . '/' . $tplFile->path, true );              // Set PDF template
            }
        }

        // Set document information
        $pdf->SetCreator( PDF_CREATOR );
        $pdf->SetAuthor( PDF_AUTHOR );
        $pdf->SetTitle( $template->title );
        $pdf->SetSubject( $template->title );
        $pdf->SetDisplayMode( 'fullpage', 'continuous' );

        $pdf->SHYlang = $GLOBALS['TL_LANGUAGE'];                                            // Sprache für CSS hyphens korrekt setzen
        $pdf->SHYleftmin = 3;

        // AddOn-Template verarbeiten
        $tpl = empty( $pdfAddon ) ? 'mpdf_default' : $pdfAddon;                             // PDF-Template für ergänzende Einträge, wie Header, Footer, ...

        // AddOn-Init
        $objmPdfTpl = new BackendTemplate( $tpl );
        $objmPdfTpl->language = $GLOBALS['TL_LANGUAGE'];
        $objmPdfTpl->init = true;                                                           // INIT-Aufruf
        $objmPdfTpl->pdf = $pdf;
        $objmPdfTpl->parse( );                                                              // Template verarbeiten

        // Initialize document and add a page
        $pdf->AddPage( );

        // Set font
        $pdf->SetFont( PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN );

        // AddOn-Content
        $objmPdfTpl = new BackendTemplate( $tpl );
        $objmPdfTpl->language = $GLOBALS['TL_LANGUAGE'];
        $objmPdfTpl->init = false;                                                          // 2. Aufruf auf Seite 1
        $objmPdfTpl->pdf = $pdf;
        $objmPdfTpl->parse( );                                                              // Template verarbeiten

        // Include one or more CSS files from file system
        $styles = '';
        if( $objRootPage->pdfCustomCSS ) {
            $cssFiles = StringUtil::deserialize( $objRootPage->pdfCustomCSS, true );
            $styles .= "<style>\n";

            foreach( $cssFiles as $cssFileUuid ) {
                if( null !== ( $cssFile = FilesModel::findByUuid( $cssFileUuid ) ) && file_exists( $rootDir . '/' . $cssFile->path ) ) {
                    $styles .= self::css_optimize( file_get_contents( $rootDir . '/' . $cssFile->path ) );
                }
            }
            $styles .= "\n</style>\n";
        }

        // Styles ins HTML einfügen
        $strArticle = $styles . $strArticle;

        // Dispatch an event before writing html
        $event = new BeforeWriteArticleAsPdfEvent( $template, $pdf, $strArticle );
        self::dispatchEvent( $event );

        // Write the HTML content
        $pdf->writeHTML( $event->getHtml( ) );

        // file name can get from URL (default is the title of the article)
        $filename = $template->title;
        if( !empty( Input::get( 't' ) ) ) {
            $filename = Input::get( 't' );
        }

        // Dispatch an event before outputting generated pdf
        $event = new BeforeOutputArticleAsPdfEvent( $template, $pdf, $filename );
        self::dispatchEvent( $event );

        // Close and output PDF document
        $pdf->Output( StringUtil::standardize( StringUtil::ampersand( $event->getFilename( ), false ) ) . '.pdf', 'D' );

        exit;
    }

    private static function css_optimize( $buffer )
    {
        $buffer = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer ); // remove comments
        $buffer = str_replace( ["\r\n", "\r", "\n", "\t"], '', $buffer );       // remove tabs, newlines, etc.
        $buffer = preg_replace( '/\s\s+/', ' ', $buffer );                      // remove multiple spaces

        return $buffer;
    }

    private static function dispatchEvent( $event ): void
    {
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = self::getContainer( )->get( 'event_dispatcher' );
        $dispatcher->dispatch( $event );
    }

   //-----------------------------------------------------------------
}
