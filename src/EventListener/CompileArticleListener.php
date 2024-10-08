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
use Contao\StringUtil;
use Contao\Environment;
use Contao\FrontendTemplate;
use Softleister\Mpdftemplate\mpdf_tools;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Symfony\Component\VarDumper\VarDumper;

#[AsHook('compileArticle')]
class CompileArticleListener
{
    public function __invoke( FrontendTemplate $template, array $data, Module $module ): void
    {
        $request = Environment::get( 'requestUri' );

        // PrintAsPDF-Button freigeben
        $template->pdfButton = true;
        $template->href = $request . (str_contains($request, '?') ? '&amp;' : '?') . 'pdf=' . $template->id;
        $template->pdfTitle = StringUtil::specialchars( $GLOBALS['TL_LANG']['MSC']['printAsPdf'] );

        if( (int) Input::get( 'pdf' ) !== $template->id ) return;   // kein PDF-Download angefordert oder falsche ID
        if( empty( $module->printable ) ) return;                   // Keine Syndication gesetzt

        $arrSyn = StringUtil::deserialize( $module->printable, true );
        if( !in_array( 'pdf', $arrSyn ) ) return;                   // kein PrintAsPdf gesetzt

        // Inhalte der Content-Elemente anreihen
        $content = '';
        foreach( $template->elements AS $ce_content ) $content .= $ce_content;

        mpdf_tools::printArticleAsPdf( $content, $template );
    }
}
