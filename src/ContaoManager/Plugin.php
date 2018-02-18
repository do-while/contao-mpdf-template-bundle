<?php

/**
 * @copyright  Softleister 2018
 * @author     Softleister <info@softleister.de>
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

namespace Softleister\MpdftemplateBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
//use Symfony\Component\Config\Loader\LoaderResolverInterface;
//use Symfony\Component\HttpKernel\KernelInterface;


/**
 * Plugin for the Contao Manager.
 *
 * @author Softleister
 */
class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles( ParserInterface $parser )
    {
        return [
            BundleConfig::create( 'Softleister\MpdftemplateBundle\SoftleisterMpdftemplateBundle' )
                ->setLoadAfter( ['Contao\CoreBundle\ContaoCoreBundle'] )
                ->setReplace( ['mPDF'] ),
        ];
    }
}
