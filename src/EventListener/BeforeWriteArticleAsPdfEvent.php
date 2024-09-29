<?php

declare(strict_types=1);

/**
 * @copyright  Softleister 2018-2024
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

namespace Softleister\MpdftemplateBundle\EventListener;

use Mpdf\Mpdf;
use Contao\FrontendTemplate;


final class BeforeWriteArticleAsPdfEvent extends ArticleAsPdfEvent
{
    private string $html;

    public function __construct( FrontendTemplate $article, Mpdf $pdf, string $html )
    {
        parent::__construct( $article, $pdf );

        $this->html = $html;
    }
    
    public function getHtml( ): string
    {
        return $this->html;
    }
}
