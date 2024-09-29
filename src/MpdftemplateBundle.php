<?php

declare(strict_types=1);

/**
 * @copyright  Softleister 2018-2024
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

namespace Softleister\MpdftemplateBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Configures the Contao mpdf-template bundle.
 */
class MpdftemplateBundle extends Bundle
{
    public function getPath( ): string
    {
        return \dirname( __DIR__ );
    }
}
