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

$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'mpdftemplate';

PaletteManipulator::create()
    ->addLegend('pdf_legend', 'cache_legend')
    ->addField('mpdftemplate', 'pdf_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('root', 'tl_page')
    ->applyToPalette('rootfallback', 'tl_page');

// add subpalette
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['mpdftemplate'] = 'pdfTplSRC,pdfMargin,mpdf_addon,pdfIgnoreCSS,pdfCustomCSS';

// add fields
$GLOBALS['TL_DCA']['tl_page']['fields']['mpdftemplate'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['mpdftemplate'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('submitOnChange'=>true),
    'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pdfTplSRC'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['pdfTplSRC'],
    'exclude'                 => true,
    'inputType'               => 'fileTree',
    'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'clr', 'extensions'=>'pdf'),
    'sql'                     => "binary(16) NULL",
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pdfMargin'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['pdfMargin'],
    'exclude'                 => true,
    'inputType'               => 'trbl',
    'options'                 => array('mm', 'cm'),
    'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
    'sql'                     => "varchar(128) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pdfIgnoreCSS'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['pdfIgnoreCSS'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50 clr'),
    'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['pdfCustomCSS'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['pdfCustomCSS'],
    'exclude'                 => true,
    'inputType'               => 'fileTree',
    'eval'                    => array('filesOnly'=>true, 'fieldType'=>'checkbox', 'tl_class'=>'clr', 'extensions'=>'css', 'multiple'=>true),
    'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['mpdf_addon'] = array
(
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => array('tl_mpdf_page', 'getPdfTemplates'),
    'eval'                    => array('chosen'=>true, 'tl_class'=>'clr w50'),
    'sql'                     => "varchar(64) NOT NULL default ''"
);


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
