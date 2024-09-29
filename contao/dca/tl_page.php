<?php

declare(strict_types=1);

/**
 * @copyright  Softleister 2018-2024
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'mpdftemplate';

PaletteManipulator::create( )
    ->addLegend( 'pdf_legend', 'cache_legend' )
    ->addField( 'mpdftemplate', 'pdf_legend', PaletteManipulator::POSITION_APPEND )
    ->applyToPalette( 'root', 'tl_page' )
    ->applyToPalette( 'rootfallback', 'tl_page' );

// add subpalette
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['mpdftemplate'] = 'pdfTplSRC,pdfMargin,mpdf_addon,pdfIgnoreCSS,pdfCustomCSS';

// add fields
$GLOBALS['TL_DCA']['tl_page']['fields']['mpdftemplate'] = [
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => ['submitOnChange'=>true],
    'sql'                     => ['type' => 'boolean', 'default' => false]
];

$GLOBALS['TL_DCA']['tl_page']['fields']['pdfTplSRC'] = [
    'exclude'                 => true,
    'inputType'               => 'fileTree',
    'eval'                    => ['filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'clr', 'extensions'=>'pdf'],
    'sql'                     => "binary(16) NULL",
];

$GLOBALS['TL_DCA']['tl_page']['fields']['pdfMargin'] = [
    'exclude'                 => true,
    'inputType'               => 'trbl',
    'options'                 => ['mm', 'cm'],
    'eval'                    => ['includeBlankOption'=>true, 'tl_class'=>'w50'],
    'sql'                     => ['type'=>'string', 'length'=>128, 'default'=>'']
];

$GLOBALS['TL_DCA']['tl_page']['fields']['pdfCustomCSS'] = [
    'exclude'                 => true,
    'inputType'               => 'fileTree',
    'eval'                    => ['filesOnly'=>true, 'fieldType'=>'checkbox', 'tl_class'=>'clr', 'extensions'=>'css', 'multiple'=>true],
    'sql'                     => "blob NULL"
];

$GLOBALS['TL_DCA']['tl_page']['fields']['mpdf_addon'] = [
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => ['tl_mpdf_page', 'getPdfTemplates'],
    'eval'                    => ['chosen'=>true, 'tl_class'=>'clr w50'],
    'sql'                     => ['type'=>'string', 'length'=>64, 'default'=>'']
];


class tl_mpdf_page extends tl_page
{
    /**
     * Return all PDF-templates as array
     *
     * @return array
     */
    public function getPdfTemplates( )
    {
        return $this->getTemplateGroup( 'mpdf_' );
    }
}
