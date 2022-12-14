<?php

/**
 * @copyright  Softleister 2018-2022
 * @author     Softleister <info@softleister.de>
 * @package    mpdf-template
 * @license    LGPL
 * @see	       https://github.com/do-while/contao-mpdf-template-bundle
 *
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$GLOBALS['TL_DCA']['tl_article']['palettes']['__selector__'][] = 'mpdftemplate';

PaletteManipulator::create()
    ->addLegend('pdf_legend', 'syndication_legend')
    ->addField('mpdftemplate', 'pdf_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_article');

// add subpalette
$GLOBALS['TL_DCA']['tl_article']['subpalettes']['mpdftemplate'] = 'pdfTplSRC,pdfMargin,mpdf_addon';

// add fields
$GLOBALS['TL_DCA']['tl_article']['fields']['mpdftemplate'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_article']['mpdftemplate'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('submitOnChange'=>true),
    'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_article']['fields']['pdfTplSRC'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_article']['pdfTplSRC'],
    'exclude'                 => true,
    'inputType'               => 'fileTree',
    'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'clr', 'extensions'=>'pdf'),
    'sql'                     => "binary(16) NULL",
);

$GLOBALS['TL_DCA']['tl_article']['fields']['pdfMargin'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_article']['pdfMargin'],
    'exclude'                 => true,
    'inputType'               => 'trbl',
    'options'                 => array('mm', 'cm'),
    'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
    'sql'                     => "varchar(128) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_article']['fields']['mpdf_addon'] = array
(
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => array('tl_mpdf_page', 'getPdfTemplates'),
    'eval'                    => array('chosen'=>true, 'tl_class'=>'clr w50'),
    'sql'                     => "varchar(64) NOT NULL default ''"
);
