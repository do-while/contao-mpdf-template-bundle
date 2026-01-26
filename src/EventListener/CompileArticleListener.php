<?php

declare(strict_types=1);

/**
 * @copyright  Softleister 2018-2024
 * @author     Softleister <info@softleister.de>
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

namespace Softleister\MpdftemplateBundle\EventListener;

use Contao\Input;
use Contao\Module;
use Contao\System;
use Contao\Controller;
use Contao\StringUtil;
use Contao\Environment;
use Contao\FrontendTemplate;
use Softleister\Mpdftemplate\mpdf_tools;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;

#[AsHook('compileArticle')]
class CompileArticleListener
{
    public function __invoke( FrontendTemplate $template, array $data, Module $module ): void
    {
        $template->pdfButton = false;
        $urlParts = parse_url( Environment::get( 'requestUri' ) );

        $path  = $urlParts['path'] ?? '';
        $query = $urlParts['query'] ?? '';
        $params = [];                                               // Vorhandene GET-Parameter übernehmen
        if( $query !== '' ) {
            parse_str( $query, $params );
        }
        $arrSyn = StringUtil::deserialize( $module->printable, true );

        System::loadLanguageFile('tl_article');

        // PrintAsPDF-Button freigeben
        if( in_array( 'pdf', $arrSyn ) ) {
            $template->pdfButton = true;
            $params['pdf'] = $template->id;

            $template->href = $path . '?' . StringUtil::ampersand( http_build_query( $params ) );
            $template->pdfTitle = StringUtil::specialchars( $GLOBALS['TL_LANG']['tl_article']['printAsPdf'] );
        }
        else return;                                                // Syndication ohne PDF => Controller beenden

        if( (int) Input::get( 'pdf' ) !== $template->id ) return;   // kein PDF-Download angefordert oder falsche ID

        // Inhalte der Content-Elemente anreihen
        $content = '';
        foreach( $template->elements AS $ce_content ) $content .= $ce_content;

        mpdf_tools::printArticleAsPdf( $content, $template );       // Die Funktion startet den Download, wenn sie zurückkommt, ist kein Download erfolgt

        unset( $params['pdf'] );
        $redirectUrl = $path . ( empty( $params ) ? '' : '?' . http_build_query( $params ) );

        Controller::redirect( $redirectUrl );                       // wenn kein PDF erstellt: zurück zur aufrufenden Seite
    }
}
