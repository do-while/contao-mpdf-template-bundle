<?php

declare(strict_types=1);

/**
 * @copyright  Softleister 2018-2024
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

namespace Softleister\MpdftemplateBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Softleister\MpdftemplateBundle\MpdftemplateBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;


class Plugin implements BundlePluginInterface
{
    public function getBundles( ParserInterface $parser )
    {
        return [
            BundleConfig::create( MpdftemplateBundle::class )
                ->setLoadAfter( [ContaoCoreBundle::class] )
                ->setReplace( ['mPDF'] ),
        ];
    }
}
