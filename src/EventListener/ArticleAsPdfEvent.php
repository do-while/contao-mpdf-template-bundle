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

abstract class ArticleAsPdfEvent
{
    private FrontendTemplate $article;
    
    private Mpdf $pdf;

    public function __construct( FrontendTemplate $article, Mpdf $pdf )
    {
        $this->article = $article;
        $this->pdf     = $pdf;
    }

    public function getArticle( ): FrontendTemplate
    {
        return $this->article;
    }

    public function getPdf( ): Mpdf
    {
        return $this->pdf;
    }
}
