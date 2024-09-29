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


final class BeforeOutputArticleAsPdfEvent extends ArticleAsPdfEvent
{
    private string $filename;

    public function __construct( FrontendTemplate $article, Mpdf $pdf, string $filename )
    {
        parent::__construct( $article, $pdf );
        $this->filename = $filename;
    }
    
    public function getFilename( ): string
    {
        return $this->filename;
    }

    public function setFilename( string $filename ): void
    {
        $this->filename = $filename;
    }
}
