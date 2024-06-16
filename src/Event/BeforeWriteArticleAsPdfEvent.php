<?php

declare(strict_types=1);

namespace Softleister\MpdftemplateBundle\Event;

use Contao\ModuleArticle;
use Mpdf\Mpdf;

final class BeforeWriteArticleAsPdfEvent extends ArticleAsPdfEvent
{
    private string $html;

    public function __construct(ModuleArticle $article, Mpdf $pdf, string $html)
    {
        parent::__construct($article, $pdf);

        $this->html = $html;
    }
    
    public function getHtml(): string
    {
        return $this->html;
    }
}
