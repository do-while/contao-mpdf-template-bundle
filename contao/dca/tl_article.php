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

$GLOBALS['TL_DCA']['tl_article']['fields']['printable']['options'][] = 'pdf';       // Syndication-Options erweitern
$GLOBALS['TL_DCA']['tl_article']['palettes']['__selector__'][] = 'mpdftemplate';

PaletteManipulator::create( )
    ->addLegend( 'pdf_legend', 'syndication_legend' )
    ->addField( 'mpdftemplate', 'pdf_legend', PaletteManipulator::POSITION_APPEND )
    ->applyToPalette( 'default', 'tl_article' );

// add subpalette
$GLOBALS['TL_DCA']['tl_article']['subpalettes']['mpdftemplate'] = 'pdfTplSRC,pdfMargin,mpdf_addon';

// add fields
$GLOBALS['TL_DCA']['tl_article']['fields']['mpdftemplate'] = [
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => ['submitOnChange'=>true],
    'sql'                     => ['type' => 'boolean', 'default' => false]
];

$GLOBALS['TL_DCA']['tl_article']['fields']['pdfTplSRC'] = [
    'exclude'                 => true,
    'inputType'               => 'fileTree',
    'eval'                    => ['filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'clr', 'extensions'=>'pdf'],
    'sql'                     => "binary(16) NULL"
];

$GLOBALS['TL_DCA']['tl_article']['fields']['pdfMargin'] = [
    'exclude'                 => true,
    'inputType'               => 'trbl',
    'options'                 => ['mm', 'cm'],
    'eval'                    => ['includeBlankOption'=>true, 'tl_class'=>'w50'],
    'sql'                     => array('type'=>'string', 'length'=>128, 'default'=>'')
];

$GLOBALS['TL_DCA']['tl_article']['fields']['mpdf_addon'] = [
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => ['tl_mpdf_page', 'getPdfTemplates'],
    'eval'                    => ['chosen'=>true, 'tl_class'=>'clr w50'],
    'sql'                     => array('type'=>'string', 'length'=>64, 'default'=>'')
];
