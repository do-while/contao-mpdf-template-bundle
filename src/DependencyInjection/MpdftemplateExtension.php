<?php

declare(strict_types=1);

/**
 * @copyright  Softleister 2018-2024
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

namespace Softleister\MpdftemplateBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MpdftemplateExtension extends Extension
{
    public function load( array $configs, ContainerBuilder $container ): void
    {
        ( new YamlFileLoader( $container, new FileLocator( __DIR__ . '/../../config' ) ) )
            ->load( 'services.yaml' )
        ;
    }
}
