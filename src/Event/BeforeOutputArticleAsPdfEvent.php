<?php

declare(strict_types=1);

namespace Softleister\MpdftemplateBundle\Event;

use Contao\ModuleArticle;
use Mpdf\Mpdf;

final class BeforeOutputArticleAsPdfEvent extends ArticleAsPdfEvent
{
    private string $filename;

    public function __construct(ModuleArticle $article, Mpdf $pdf, string $filename)
    {
        parent::__construct($article, $pdf);
        $this->filename = $filename;
    }
    
    public function getFilename(): string
    {
        return $this->filename;
    }
}
