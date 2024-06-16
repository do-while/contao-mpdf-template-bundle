<?php

declare(strict_types=1);

namespace Softleister\MpdftemplateBundle\Event;

use Contao\ModuleArticle;
use Mpdf\Mpdf;

abstract class ArticleAsPdfEvent
{
    private ModuleArticle $article;
    
    private Mpdf $pdf;

    public function __construct(ModuleArticle $article, Mpdf $pdf)
    {
        $this->article = $article;
        $this->pdf     = $pdf;
    }
    
    public function getArticle(): ModuleArticle
    {
        return $this->article;
    }
    
    public function getPdf(): Mpdf
    {
        return $this->pdf;
    }
}
