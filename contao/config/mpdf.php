<?php

declare(strict_types=1);

/**
 * @copyright  Softleister 2018-2024
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

namespace Contao;

\define( 'PDF_PAGE_FORMAT', 'A4' );
\define( 'PDF_PAGE_ORIENTATION', 'P' );
\define( 'PDF_CREATOR', 'Contao Open Source CMS' );
\define( 'PDF_AUTHOR', Environment::get( 'url' ) );
\define( 'PDF_MARGIN_TOP', 10 );
\define( 'PDF_MARGIN_BOTTOM', 10 );
\define( 'PDF_MARGIN_LEFT', 15 );
\define( 'PDF_MARGIN_RIGHT', 15 );
\define( 'PDF_FONT_NAME_MAIN', 'freeserif' );
\define( 'PDF_FONT_SIZE_MAIN', 12 );
