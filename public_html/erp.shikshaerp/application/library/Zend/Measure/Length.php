<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_Measure
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: Length.php 24594 2012-01-05 21:27:01Z matthew $
 */

/**
 * Implement needed classes
 */
require_once 'Zend/Measure/Abstract.php';
require_once 'Zend/Locale.php';

/**
 * Class for handling length conversions
 *
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Length
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Length extends Zend_Measure_Abstract
{
    const STANDARD = 'METER';

    const AGATE                 = 'AGATE';
    const ALEN_DANISH           = 'ALEN_DANISH';
    const ALEN                  = 'ALEN';
    const ALEN_SWEDISH          = 'ALEN_SWEDISH';
    const ANGSTROM              = 'ANGSTROM';
    const ARMS                  = 'ARMS';
    const ARPENT_CANADIAN       = 'ARPENT_CANADIAN';
    const ARPENT                = 'ARPENT';
    const ARSHEEN               = 'ARSHEEN';
    const ARSHIN                = 'ARSHIN';
    const ARSHIN_IRAQ           = 'ARSHIN_IRAQ';
    const ASTRONOMICAL_UNIT     = 'ASTRONOMICAL_UNIT';
    const ATTOMETER             = 'ATTOMETER';
    const BAMBOO                = 'BAMBOO';
    const BARLEYCORN            = 'BARLEYCORN';
    const BEE_SPACE             = 'BEE_SPACE';
    const BICRON                = 'BICRON';
    const BLOCK_US_EAST         = 'BLOCK_US_EAST';
    const BLOCK_US_WEST         = 'BLOCK_US_WEST';
    const BLOCK_US_SOUTH        = 'BLOCK_US_SOUTH';
    const BOHR                  = 'BOHR';
    const BRACCIO               = 'BRACCIO';
    const BRAZA_ARGENTINA       = 'BRAZA_ARGENTINA';
    const BRAZA                 = 'BRAZA';
    const BRAZA_US              = 'BRAZA_US';
    const BUTTON                = 'BUTTON';
    const CABLE_US              = 'CABLE_US';
    const CABLE_UK              = 'CABLE_UK';
    const CALIBER               = 'CALIBER';
    const CANA                  = 'CANA';
    const CAPE_FOOT             = 'CAPE_FOOT';
    const CAPE_INCH             = 'CAPE_INCH';
    const CAPE_ROOD             = 'CAPE_ROOD';
    const CENTIMETER            = 'CENTIMETER';
    const CHAIN                 = 'CHAIN';
    const CHAIN_ENGINEER        = 'CHAIN_ENGINEER';
    const CHIH                  = 'CHIH';
    const CHINESE_FOOT          = 'CHINESE_FOOT';
    const CHINESE_INCH          = 'CHINESE_INCH';
    const CHINESE_MILE          = 'CHINESE_MILE';
    const CHINESE_YARD          = 'CHINESE_YARD';
    const CITY_BLOCK_US_EAST    = 'CITY_BLOCK_US_EAST';
    const CITY_BLOCK_US_WEST    = 'CITY_BLOCK_US_WEST';
    const CITY_BLOCK_US_SOUTH   = 'CITY_BLOCK_US_SOUTH';
    const CLICK                 = 'CLICK';
    const CUADRA                = 'CUADRA';
    const CUADRA_ARGENTINA      = 'CUADRA_ARGENTINA';
    const CUBIT_EGYPT           = 'Length:CUBIT_EGYPT';
    const CUBIT_ROYAL           = 'CUBIT_ROYAL';
    const CUBIT_UK              = 'CUBIT_UK';
    const CUBIT                 = 'CUBIT';
    const CUERDA                = 'CUERDA';
    const DECIMETER             = 'DECIMETER';
    const DEKAMETER             = 'DEKAMETER';
    const DIDOT_POINT           = 'DIDOT_POINT';
    const DIGIT                 = 'DIGIT';
    const DIRAA                 = 'DIRAA';
    const DONG                  = 'DONG';
    const DOUZIEME_WATCH        = 'DOUZIEME_WATCH';
    const DOUZIEME              = 'DOUZIEME';
    const DRA_IRAQ              = 'DRA_IRAQ';
    const DRA                   = 'DRA';
    const EL                    = 'EL';
    const ELL                   = 'ELL';
    const ELL_SCOTTISH          = 'ELL_SCOTTISH';
    const ELLE                  = 'ELLE';
    const ELLE_VIENNA           = 'ELLE_VIENNA';
    const EM                    = 'EM';
    const ESTADIO_PORTUGAL      = 'ESTADIO_PORTUGAL';
    const ESTADIO               = 'ESTADIO';
    const EXAMETER              = 'EXAMETER';
    const FADEN_AUSTRIA         = 'FADEN_AUSTRIA';
    const FADEN                 = 'FADEN';
    const FALL                  = 'FALL';
    const FALL_SCOTTISH         = 'FALL_SCOTTISH';
    const FATHOM                = 'FATHOM';
    const FATHOM_ANCIENT        = 'FATHOM_ANCIENT';
    const FAUST                 = 'FAUST';
    const FEET_OLD_CANADIAN     = 'FEET_OLD_CANADIAN';
    const FEET_EGYPT            = 'FEET_EGYPT';
    const FEET_FRANCE           = 'FEET_FRANCE';
    const FEET                  = 'FEET';
    const FEET_IRAQ             = 'FEET_IRAQ';
    const FEET_NETHERLAND       = 'FEET_NETHERLAND';
    const FEET_ITALIC           = 'FEET_ITALIC';
    const FEET_SURVEY           = 'FEET_SURVEY';
    const FEMTOMETER            = 'FEMTOMETER';
    const FERMI                 = 'FERMI';
    const FINGER                = 'FINGER';
    const FINGERBREADTH         = 'FINGERBREADTH';
    const FIST                  = 'FIST';
    const FOD                   = 'FOD';
    const FOOT_EGYPT            = 'FOOT_EGYPT';
    const FOOT_FRANCE           = 'FOOT_FRANCE';
    const FOOT                  = 'FOOT';
    const FOOT_IRAQ             = 'FOOT_IRAQ';
    const FOOT_NETHERLAND       = 'FOOT_NETHERLAND';
    const FOOT_ITALIC           = 'FOOT_ITALIC';
    const FOOT_SURVEY           = 'FOOT_SURVEY';
    const FOOTBALL_FIELD_CANADA = 'FOOTBALL_FIELD_CANADA';
    const FOOTBALL_FIELD_US     = 'FOOTBALL_FIELD_US';
    const FOOTBALL_FIELD        = 'FOOTBALL_FIELD';
    const FURLONG               = 'FURLONG';
    const FURLONG_SURVEY        = 'FURLONG_SURVEY';
    const FUSS                  = 'FUSS';
    const GIGAMETER             = 'GIGAMETER';
    const GIGAPARSEC            = 'GIGAPARSEC';
    const GNATS_EYE             = 'GNATS_EYE';
    const GOAD                  = 'GOAD';
    const GRY                   = 'GRY';
    const HAIRS_BREADTH         = 'HAIRS_BREADTH';
    const HAND                  = 'HAND';
    const HANDBREADTH           = 'HANDBREADTH';
    const HAT                   = 'HAT';
    const HECTOMETER            = 'HECTOMETER';
    const HEER                  = 'HEER';
    const HIRO                  = 'HIRO';
    const HUBBLE                = 'HUBBLE';
    const HVAT                  = 'HVAT';
    const INCH                  = 'INCH';
    const IRON                  = 'IRON';
    const KEN                   = 'KEN';
    const KERAT                 = 'KERAT';
    const KILOFOOT              = 'KILOFOOT';
    const KILOMETER             = 'KILOMETER';
    const KILOPARSEC            = 'KILOPARSEC';
    const KILOYARD              = 'KILOYARD';
    const KIND                  = 'KIND';
    const KLAFTER               = 'KLAFTER';
    const KLAFTER_SWISS         = 'KLAFTER_SWISS';
    const KLICK                 = 'KLICK';
    const KYU                   = 'KYU';
    const LAP_ANCIENT           = 'LAP_ANCIENT';
    const LAP                   = 'LAP';
    const LAP_POOL              = 'LAP_POOL';
    const LEAGUE_ANCIENT        = 'LEAGUE_ANCIENT';
    const LEAGUE_NAUTIC         = 'LEAGUE_NAUTIC';
    const LEAGUE_UK_NAUTIC      = 'LEAGUE_UK_NAUTIC';
    const LEAGUE                = 'LEAGUE';
    const LEAGUE_US             = 'LEAGUE_US';
    const LEAP                  = 'LEAP';
    const LEGOA                 = 'LEGOA';
    const LEGUA                 = 'LEGUA';
    const LEGUA_US              = 'LEGUA_US';
    const LEGUA_SPAIN_OLD       = 'LEGUA_SPAIN_OLD';
    const LEGUA_SPAIN           = 'LEGUA_SPAIN';
    const LI_ANCIENT            = 'LI_ANCIENT';
    const LI_IMPERIAL           = 'LI_IMPERIAL';
    const LI                    = 'LI';
    const LIEUE                 = 'LIEUE';
    const LIEUE_METRIC          = 'LIEUE_METRIC';
    const LIEUE_NAUTIC          = 'LIEUE_NAUTIC';
    const LIGHT_SECOND          = 'LIGHT_SECOND';
    const LIGHT_MINUTE          = 'LIGHT_MINUTE';
    const LIGHT_HOUR            = 'LIGHT_HOUR';
    const LIGHT_DAY             = 'LIGHT_DAY';
    const LIGHT_YEAR            = 'LIGHT_YEAR';
    const LIGNE                 = 'LIGNE';
    const LIGNE_SWISS           = 'LIGNE_SWISS';
    const LINE                  = 'LINE';
    const LINE_SMALL            = 'LINE_SMALL';
    const LINK                  = 'LINK';
    const LINK_ENGINEER         = 'LINK_ENGINEER';
    const LUG                   = 'LUG';
    const LUG_GREAT             = 'LUG_GREAT';
    const MARATHON              = 'MARATHON';
    const MARK_TWAIN            = 'MARK_TWAIN';
    const MEGAMETER             = 'MEGAMETER';
    const MEGAPARSEC            = 'MEGAPARSEC';
    const MEILE_AUSTRIAN        = 'MEILE_AUSTRIAN';
    const MEILE                 = 'MEILE';
    const MEILE_GERMAN          = 'MEILE_GERMAN';
    const METER                 = 'METER';
    const METRE                 = 'METRE';
    const METRIC_MILE           = 'METRIC_MILE';
    const METRIC_MILE_US        = 'METRIC_MILE_US';
    const MICROINCH             = 'MICROINCH';
    const MICROMETER            = 'MICROMETER';
    const MICROMICRON           = 'MICROMICRON';
    const MICRON                = 'MICRON';
    const MIGLIO                = 'MIGLIO';
    const MIIL                  = 'MIIL';
    const MIIL_DENMARK          = 'MIIL_DENMARK';
    const MIIL_SWEDISH          = 'MIIL_SWEDISH';
    const MIL                   = 'MIL';
    const MIL_SWEDISH           = 'MIL_SWEDISH';
    const MILE_UK               = 'MILE_UK';
    const MILE_IRISH            = 'MILE_IRISH';
    const MILE                  = 'MILE';
    const MILE_NAUTIC           = 'MILE_NAUTIC';
    const MILE_NAUTIC_UK        = 'MILE_NAUTIC_UK';
    const MILE_NAUTIC_US        = 'MILE_NAUTIC_US';
    const MILE_ANCIENT          = 'MILE_ANCIENT';
    const MILE_SCOTTISH         = 'MILE_SCOTTISH';
    const MILE_STATUTE          = 'MILE_STATUTE';
    const MILE_US               = 'MILE_US';
    const MILHA                 = 'MILHA';
    const MILITARY_PACE         = 'MILITARY_PACE';
    const MILITARY_PACE_DOUBLE  = 'MILITARY_PACE_DOUBLE';
    const MILLA                 = 'MILLA';
    const MILLE                 = 'MILLE';
    const MILLIARE              = 'MILLIARE';
    const MILLIMETER            = 'MILLIMETER';
    const MILLIMICRON           = 'MILLIMICRON';
    const MKONO                 = 'MKONO';
    const MOOT                  = 'MOOT';
    const MYRIAMETER            = 'MYRIAMETER';
    const NAIL                  = 'NAIL';
    const NANOMETER             = 'NANOMETER';
    const NANON                 = 'NANON';
    const PACE                  = 'PACE';
    const PACE_ROMAN            = 'PACE_ROMAN';
    const PALM_DUTCH            = 'PALM_DUTCH';
    const PALM_UK               = 'PALM_UK';
    const PALM                  = 'PALM';
    const PALMO_PORTUGUESE      = 'PALMO_PORTUGUESE';
    const PALMO                 = 'PALMO';
    const PALMO_US              = 'PALMO_US';
    const PARASANG              = 'PARASANG';
    const PARIS_FOOT            = 'PARIS_FOOT';
    const PARSEC                = 'PARSEC';
    const PE                    = 'PE';
    const PEARL                 = 'PEARL';
    const PERCH                 = 'PERCH';
    const PERCH_IRELAND         = 'PERCH_IRELAND';
    const PERTICA               = 'PERTICA';
    const PES                   = 'PES';
    const PETAMETER             = 'PETAMETER';
    const PICA                  = 'PICA';
    const PICOMETER             = 'PICOMETER';
    const PIE_ARGENTINA         = 'PIE_ARGENTINA';
    const PIE_ITALIC            = 'PIE_ITALIC';
    const PIE                   = 'PIE';
    const PIE_US                = 'PIE_US';
    const PIED_DE_ROI           = 'PIED_DE_ROI';
    const PIK                   = 'PIK';
    const PIKE                  = 'PIKE';
    const POINT_ADOBE           = 'POINT_ADOBE';
    const POINT                 = 'POINT';
    const POINT_DIDOT           = 'POINT_DIDOT';
    const POINT_TEX             = 'POINT_TEX';
    const POLE                  = 'POLE';
    const POLEGADA              = 'POLEGADA';
    const POUCE                 = 'POUCE';
    const PU                    = 'PU';
    const PULGADA               = 'PULGADA';
    const PYGME                 = 'PYGME';
    const Q                     = 'Q';
    const QUADRANT              = 'QUADRANT';
    const QUARTER               = 'QUARTER';
    const QUARTER_CLOTH         = 'QUARTER_CLOTH';
    const QUARTER_PRINT         = 'QUARTER_PRINT';
    const RANGE                 = 'RANGE';
    const REED                  = 'REED';
    const RI                    = 'RI';
    const RIDGE                 = 'RIDGE';
    const RIVER                 = 'RIVER';
    const ROD                   = 'ROD';
    const ROD_SURVEY            = 'ROD_SURVEY';
    const ROEDE                 = 'ROEDE';
    const ROOD                  = 'ROOD';
    const ROPE                  = 'ROPE';
    const ROYAL_FOOT            = 'ROYAL_FOOT';
    const RUTE                  = 'RUTE';
    const SADZHEN               = 'SADZHEN';
    const SAGENE                = 'SAGENE';
    const SCOTS_FOOT            = 'SCOTS_FOOT';
    const SCOTS_MILE            = 'SCOTS_MILE';
    const SEEMEILE              = 'SEEMEILE';
    const SHACKLE               = 'SHACKLE';
    const SHAFTMENT             = 'SHAFTMENT';
    const SHAFTMENT_ANCIENT     = 'SHAFTMENT_ANCIENT';
    const SHAKU                 = 'SHAKU';
    const SIRIOMETER            = 'SIRIOMETER';
    const SMOOT                 = 'SMOOT';
    const SPAN                  = 'SPAN';
    const SPAT                  = 'SPAT';
    const STADIUM               = 'STADIUM';
    const STEP                  = 'STEP';
    const STICK                 = 'STICK';
    const STORY                 = 'STORY';
    const STRIDE                = 'STRIDE';
    const STRIDE_ROMAN          = 'STRIDE_ROMAN';
    const TENTHMETER            = 'TENTHMETER';
    const TERAMETER             = 'TERAMETER';
    const THOU                  = 'THOU';
    const TOISE                 = 'TOISE';
    const TOWNSHIP              = 'TOWNSHIP';
    const T_SUN                 = 'T_SUN';
    const TU                    = 'TU';
    const TWAIN                 = 'TWAIN';
    const TWIP                  = 'TWIP';
    const U                     = 'U';
    const VARA_CALIFORNIA       = 'VARA_CALIFORNIA';
    const VARA_MEXICAN          = 'VARA_MEXICAN';
    const VARA_PORTUGUESE       = 'VARA_PORTUGUESE';
    const VARA_AMERICA          = 'VARA_AMERICA';
    const VARA                  = 'VARA';
    const VARA_TEXAS            = 'VARA_TEXAS';
    const VERGE                 = 'VERGE';
    const VERSHOK               = 'VERSHOK';
    const VERST                 = 'VERST';
    const WAH                   = 'WAH';
    const WERST                 = 'WERST';
    const X_UNIT                = 'X_UNIT';
    const YARD                  = 'YARD';
    const YOCTOMETER            = 'YOCTOMETER';
    const YOTTAMETER            = 'YOTTAMETER';
    const ZEPTOMETER            = 'ZEPTOMETER';
    const ZETTAMETER            = 'ZETTAMETER';
    const ZOLL                  = 'ZOLL';
    const ZOLL_SWISS            = 'ZOLL_SWISS';

    /**
     * Calculations for all length units
     *
     * @var array
     */
    protected $_units = array(
        'AGATE'           => array(array('' => '0.0254', '/' => '72'), 'agate'),
        'ALEN_DANISH'     => array('0.6277',           'alen'),
        'ALEN'            => array('0.6',              'alen'),
        'ALEN_SWEDISH'    => array('0.5938',           'alen'),
        'ANGSTROM'        => array('1.0e-10',          'Å'),
        'ARMS'            => array('0.7',              'arms'),
        'ARPENT_CANADIAN' => array('58.47',            'arpent'),
        'ARPENT'          => array('58.471308',        'arpent'),
        'ARSHEEN'         => array('0.7112',           'arsheen'),
        'ARSHIN'          => array('1.04',             'arshin'),
        'ARSHIN_IRAQ'     => array('74.5',             'arshin'),
        'ASTRONOMICAL_UNIT' => array('149597870691',   'AU'),
        'ATTOMETER'       => array('1.0e-18',          'am'),
        'BAMBOO'          => array('3.2',              'bamboo'),
        'BARLEYCORN'      => array('0.0085',           'barleycorn'),
        'BEE_SPACE'       => array('0.0065',           'bee space'),
        'BICRON'          => array('1.0e-12',          '��'),
        'BLOCK_US_EAST'   => array('80.4672',          'block'),
        'BLOCK_US_WEST'   => array('100.584',          'block'),
        'BLOCK_US_SOUTH'  => array('160.9344',         'block'),
        'BOHR'            => array('52.918e-12',       'a�'),
        'BRACCIO'         => array('0.7',              'braccio'),
        'BRAZA_ARGENTINA' => array('1.733',            'braza'),
        'BRAZA'           => array('1.67',             'braza'),
        'BRAZA_US'        => array('1.693',            'braza'),
        'BUTTON'          => array('0.000635',         'button'),
        'CABLE_US'        => array('219.456',          'cable'),
        'CABLE_UK'        => array('185.3184',         'cable'),
        'CALIBER'         => array('0.0254',           'cal'),
        'CANA'            => array('2',                'cana'),
        'CAPE_FOOT'       => array('0.314858',         'cf'),
        'CAPE_INCH'       => array(array('' => '0.314858','/' => '12'), 'ci'),
        'CAPE_ROOD'       => array('3.778296',         'cr'),
        'CENTIMETER'      => array('0.01',             'cm'),
        'CHAIN'           => array(array('' => '79200','/' => '3937'),  'ch'),
        'CHAIN_ENGINEER'  => array('30.48',            'ch'),
        'CHIH'            => array('0.35814',          "ch'ih"),
        'CHINESE_FOOT'    => array('0.371475',         'ft'),
        'CHINESE_INCH'    => array('0.0371475',        'in'),
        'CHINESE_MILE'    => array('557.21',           'mi'),
        'CHINESE_YARD'    => array('0.89154',          'yd'),
        'CITY_BLOCK_US_EAST'  => array('80.4672',      'block'),
        'CITY_BLOCK_US_WEST'  => array('100.584',      'block'),
        'CITY_BLOCK_US_SOUTH' => array('160.9344',     'block'),
        'CLICK'           => array('1000',             'click'),
        'CUADRA'          => array('84',               'cuadra'),
        'CUADRA_ARGENTINA'=> array('130',              'cuadra'),
        'Length:CUBIT_EGYPT'      => array('0.45',             'cubit'),
        'CUBIT_ROYAL'     => array('0.5235',           'cubit'),
        'CUBIT_UK'        => array('0.4572',           'cubit'),
        'CUBIT'           => array('0.444',            'cubit'),
        'CUERDA'          => array('21',               'cda'),
        'DECIMETER'       => array('0.1',              'dm'),
        'DEKAMETER'       => array('10',               'dam'),
        'DIDOT_POINT'     => array('0.000377',         'didot point'),
        'DIGIT'           => array('0.019',            'digit'),
        'DIRAA'           => array('0.58',             ''),
        'DONG'            => array(array('' => '7','/' => '300'), 'dong'),
        'DOUZIEME_WATCH'  => array('0.000188',         'douzi�me'),
        'DOUZIEME'        => array('0.00017638888889', 'douzi�me'),
        'DRA_IRAQ'        => array('0.745',            'dra'),
        'DRA'             => array('0.7112',           'dra'),
        'EL'              => array('0.69',             'el'),
        'ELL'             => array('1.143',            'ell'),
        'ELL_SCOTTISH'    => array('0.945',            'ell'),
        'ELLE'            => array('0.6',              'ellen'),
        'ELLE_VIENNA'     => array('0.7793',           'ellen'),
        'EM'              => array('0.0042175176',     'em'),
        'ESTADIO_PORTUGAL'=> array('261',              'estadio'),
        'ESTADIO'         => array('174',              'estadio'),
        'EXAMETER'        => array('1.0e+18',          'Em'),
        'FADEN_AUSTRIA'   => array('1.8965',           'faden'),
        'FADEN'           => array('1.8',              'faden'),
        'FALL'            => array('6.858',            'fall'),
        'FALL_SCOTTISH'   => array('5.67',             'fall'),
        'FATHOM'          => array('1.8288',           'fth'),
        'FATHOM_ANCIENT'  => array('1.829',            'fth'),
        'FAUST'           => array('0.10536',          'faust'),
        'FEET_OLD_CANADIAN' => array('0.325',          'ft'),
        'FEET_EGYPT'      => array('0.36',             'ft'),
        'FEET_FRANCE'     => array('0.3248406',        'ft'),
        'FEET'            => array('0.3048',           'ft'),
        'FEET_IRAQ'       => array('0.316',            'ft'),
        'FEET_NETHERLAND' => array('0.28313',          'ft'),
        'FEET_ITALIC'     => array('0.296',            'ft'),
        'FEET_SURVEY'     => array(array('' => '1200', '/' => '3937'), 'ft'),
        'FEMTOMETER'      => array('1.0e-15',          'fm'),
        'FERMI'           => array('1.0e-15',          'f'),
        'FINGER'          => array('0.1143',           'finger'),
        'FINGERBREADTH'   => array('0.01905',          'fingerbreadth'),
        'FIST'            => array('0.1',              'fist'),
        'FOD'             => array('0.3141',           'fod'),
        'FOOT_EGYPT'      => array('0.36',             'ft'),
        'FOOT_FRANCE'     => array('0.3248406',        'ft'),
        'FOOT'            => array('0.3048',           'ft'),
        'FOOT_IRAQ'       => array('0.316',            'ft'),
        'FOOT_NETHERLAND' => array('0.28313',          'ft'),
        'FOOT_ITALIC'     => array('0.296',            'ft'),
        'FOOT_SURVEY'     => array(array('' => '1200', '/' => '3937'), 'ft'),
        'FOOTBALL_FIELD_CANADA' => array('100.584',    'football field'),
        'FOOTBALL_FIELD_US'     => array('91.44',      'football field'),
        'FOOTBALL_FIELD'  => array('109.728',          'football field'),
        'FURLONG'         => array('201.168',          'fur'),
        'FURLONG_SURVEY'  => array(array('' => '792000', '/' => '3937'), 'fur'),
        'FUSS'            => array('0.31608',          'fuss'),
        'GIGAMETER'       => array('1.0e+9',           'Gm'),
        'GIGAPARSEC'      => array('30.85678e+24',     'Gpc'),
        'GNATS_EYE'       => array('0.000125',         "gnat's eye"),
        'GOAD'            => array('1.3716',           'goad'),
        'GRY'             => array('0.000211667',      'gry'),
        'HAIRS_BREADTH'   => array('0.0001',           "hair's breadth"),
        'HAND'            => array('0.1016',           'hand'),
        'HANDBREADTH'     => array('0.08',             "hand's breadth"),
        'HAT'             => array('0.5',              'hat'),
        'HECTOMETER'      => array('100',              'hm'),
        'HEER'            => array('73.152',           'heer'),
        'HIRO'            => array('1.818',            'hiro'),
        'HUBBLE'          => array('9.4605e+24',       'hubble'),
        'HVAT'            => array('1.8965',           'hvat'),
        'INCH'            => array('0.0254',           'in'),
        'IRON'            => array(array('' => '0.0254', '/' => '48'), 'iron'),
        'KEN'             => array('1.818',            'ken'),
        'KERAT'           => array('0.0286',           'kerat'),
        'KILOFOOT'        => array('304.8',            'kft'),
        'KILOMETER'       => array('1000',             'km'),
        'KILOPARSEC'      => array('3.0856776e+19',    'kpc'),
        'KILOYARD'        => array('914.4',            'kyd'),
        'KIND'            => array('0.5',              'kind'),
        'KLAFTER'         => array('1.8965',           'klafter'),
        'KLAFTER_SWISS'   => array('1.8',              'klafter'),
        'KLICK'           => array('1000',             'klick'),
        'KYU'             => array('0.00025',          'kyu'),
        'LAP_ANCIENT'     => array('402.336',          ''),
        'LAP'             => array('400',              'lap'),
        'LAP_POOL'        => array('100',              'lap'),
        'LEAGUE_ANCIENT'  => array('2275',             'league'),
        'LEAGUE_NAUTIC'   => array('5556',             'league'),
        'LEAGUE_UK_NAUTIC'=> array('5559.552',         'league'),
        'LEAGUE'          => array('4828',             'league'),
        'LEAGUE_US'       => array('4828.0417',        'league'),
        'LEAP'            => array('2.0574',           'leap'),
        'LEGOA'           => array('6174.1',           'legoa'),
        'LEGUA'           => array('4200',             'legua'),
        'LEGUA_US'        => array('4233.4',           'legua'),
        'LEGUA_SPAIN_OLD' => array('4179.4',           'legua'),
        'LEGUA_SPAIN'     => array('6680',             'legua'),
        'LI_ANCIENT'      => array('500',              'li'),
        'LI_IMPERIAL'     => array('644.65',           'li'),
        'LI'              => array('500',              'li'),
        'LIEUE'           => array('3898',             'lieue'),
        'LIEUE_METRIC'    => array('4000',             'lieue'),
        'LIEUE_NAUTIC'    => array('5556',             'lieue'),
        'LIGHT_SECOND'    => array('299792458',        'light second'),
        'LIGHT_MINUTE'    => array('17987547480',      'light minute'),
        'LIGHT_HOUR'      => array('1079252848800',    'light hour'),
        'LIGHT_DAY'       => array('25902068371200',   'light day'),
        'LIGHT_YEAR'      => array('9460528404879000', 'ly'),
        'LIGNE'           => array('0.0021167',        'ligne'),
        'LIGNE_SWISS'     => array('0.002256',         'ligne'),
        'LINE'            => array('0.0021167',        'li'),
        'LINE_SMALL'      => array('0.000635',         'li'),
        'LINK'            => array(array('' => '792','/' => '3937'), 'link'),
        'LINK_ENGINEER'   => array('0.3048',           'link'),
        'LUG'             => array('5.0292',           'lug'),
        'LUG_GREAT'       => array('6.4008',           'lug'),
        'MARATHON'        => array('42194.988',        'marathon'),
        'MARK_TWAIN'      => array('3.6576074',        'mark twain'),
        'MEGAMETER'       => array('1000000',          'Mm'),
        'MEGAPARSEC'      => array('3.085677e+22',     'Mpc'),
        'MEILE_AUSTRIAN'  => array('7586',             'meile'),
        'MEILE'           => array('7412.7',           'meile'),
        'MEILE_GERMAN'    => array('7532.5',           'meile'),
        'METER'           => array('1',                'm'),
        'METRE'           => array('1',                'm'),
        'METRIC_MILE'     => array('1500',             'metric mile'),
        'METRIC_MILE_US'  => array('1600',             'metric mile'),
        'MICROINCH'       => array('2.54e-08',         '�in'),
        'MICROMETER'      => array('0.000001',         '�m'),
        'MICROMICRON'     => array('1.0e-12',          '��'),
        'MICRON'          => array('0.000001',         '�'),
        'MIGLIO'          => array('1488.6',           'miglio'),
        'MIIL'            => array('7500',             'miil'),
        'MIIL_DENMARK'    => array('7532.5',           'miil'),
        'MIIL_SWEDISH'    => array('10687',            'miil'),
        'MIL'             => array('0.0000254',        'mil'),
        'MIL_SWEDISH'     => array('10000',            'mil'),
        'MILE_UK'         => array('1609',             'mi'),
        'MILE_IRISH'      => array('2048',             'mi'),
        'MILE'            => array('1609.344',         'mi'),
        'MILE_NAUTIC'     => array('1852',             'mi'),
        'MILE_NAUTIC_UK'  => array('1853.184',         'mi'),
        'MILE_NAUTIC_US'  => array('1852',             'mi'),
        'MILE_ANCIENT'    => array('1520',             'mi'),
        'MILE_SCOTTISH'   => array('1814',             'mi'),
        'MILE_STATUTE'    => array('1609.344',         'mi'),
        'MILE_US'         => array(array('' => '6336000','/' => '3937'), 'mi'),
        'MILHA'           => array('2087.3',           'milha'),
        'MILITARY_PACE'   => array('0.762',            'mil. pace'),
        'MILITARY_PACE_DOUBLE' => array('0.9144',      'mil. pace'),
        'MILLA'           => array('1392',             'milla'),
        'MILLE'           => array('1949',             'mille'),
        'MILLIARE'        => array('0.001478',         'milliare'),
        'MILLIMETER'      => array('0.001',            'mm'),
        'MILLIMICRON'     => array('1.0e-9',           'm�'),
        'MKONO'           => array('0.4572',           'mkono'),
        'MOOT'            => array('0.0762',           'moot'),
        'MYRIAMETER'      => array('10000',            'mym'),
        'NAIL'            => array('0.05715',          'nail'),
        'NANOMETER'       => array('1.0e-9',           'nm'),
        'NANON'           => array('1.0e-9',           'nanon'),
        'PACE'            => array('1.524',            'pace'),
        'PACE_ROMAN'      => array('1.48',             'pace'),
        'PALM_DUTCH'      => array('0.10',             'palm'),
        'PALM_UK'         => array('0.075',            'palm'),
        'PALM'            => array('0.2286',           'palm'),
        'PALMO_PORTUGUESE'=> array('0.22',             'palmo'),
        'PALMO'           => array('0.20',             'palmo'),
        'PALMO_US'        => array('0.2117',           'palmo'),
        'PARASANG'        => array('6000',             'parasang'),
        'PARIS_FOOT'      => array('0.3248406',        'paris foot'),
        'PARSEC'          => array('3.0856776e+16',    'pc'),
        'PE'              => array('0.33324',          'p�'),
        'PEARL'           => array('0.001757299',      'pearl'),
        'PERCH'           => array('5.0292',           'perch'),
        'PERCH_IRELAND'   => array('6.4008',           'perch'),
        'PERTICA'         => array('2.96',             'pertica'),
        'PES'             => array('0.2967',           'pes'),
        'PETAMETER'       => array('1.0e+15',          'Pm'),
        'PICA'            => array('0.0042175176',     'pi'),
        'PICOMETER'       => array('1.0e-12',          'pm'),
        'PIE_ARGENTINA'   => array('0.2889',           'pie'),
        'PIE_ITALIC'      => array('0.298',            'pie'),
        'PIE'             => array('0.2786',           'pie'),
        'PIE_US'          => array('0.2822',           'pie'),
        'PIED_DE_ROI'     => array('0.3248406',        'pied de roi'),
        'PIK'             => array('0.71',             'pik'),
        'PIKE'            => array('0.71',             'pike'),
        'POINT_ADOBE'     => array(array('' => '0.3048', '/' => '864'), 'pt'),
        'POINT'           => array('0.00035',          'pt'),
        'POINT_DIDOT'     => array('0.000377',         'pt'),
        'POINT_TEX'       => array('0.0003514598035',  'pt'),
        'POLE'            => array('5.0292',           'pole'),
        'POLEGADA'        => array('0.02777',          'polegada'),
        'POUCE'           => array('0.02707',          'pouce'),
        'PU'              => array('1.7907',           'pu'),
        'PULGADA'         => array('0.02365',          'pulgada'),
        'PYGME'           => array('0.346',            'pygme'),
        'Q'               => array('0.00025',          'q'),
        'QUADRANT'        => array('10001300',         'quad'),
        'QUARTER'         => array('402.336',          'Q'),
        'QUARTER_CLOTH'   => array('0.2286',           'Q'),
        'QUARTER_PRINT'   => array('0.00025',          'Q'),
        'RANGE'           => array(array('' => '38016000','/' => '3937'), 'range'),
        'REED'            => array('2.679',            'reed'),
        'RI'              => array('3927',             'ri'),
        'RIDGE'           => array('6.1722',           'ridge'),
        'RIVER'           => array('2000',             'river'),
        'ROD'             => array('5.0292',           'rd'),
        'ROD_SURVEY'      => array(array('' => '19800', '/' => '3937'), 'rd'),
        'ROEDE'           => array('10',               'roede'),
        'ROOD'            => array('3.7783',           'rood'),
        'ROPE'            => array('3.7783',           'rope'),
        'ROYAL_FOOT'      => array('0.3248406',        'royal foot'),
        'RUTE'            => array('3.75',             'rute'),
        'SADZHEN'         => array('2.1336',           'sadzhen'),
        'SAGENE'          => array('2.1336',           'sagene'),
        'SCOTS_FOOT'      => array('0.30645',          'scots foot'),
        'SCOTS_MILE'      => array('1814.2',           'scots mile'),
        'SEEMEILE'        => array('1852',             'seemeile'),
        'SHACKLE'         => array('27.432',           'shackle'),
        'SHAFTMENT'       => array('0.15124',          'shaftment'),
        'SHAFTMENT_ANCIENT' => array('0.165',          'shaftment'),
        'SHAKU'           => array('0.303',            'shaku'),
        'SIRIOMETER'      => array('1.4959787e+17',    'siriometer'),
        'SMOOT'           => array('1.7018',           'smoot'),
        'SPAN'            => array('0.2286',           'span'),
        'SPAT'            => array('1.0e+12',          'spat'),
        'STADIUM'         => array('185',              'stadium'),
        'STEP'            => array('0.762',            'step'),
        'STICK'           => array('3.048',            'stk'),
        'STORY'           => array('3.3',              'story'),
        'STRIDE'          => array('1.524',            'stride'),
        'STRIDE_ROMAN'    => array('1.48',             'stride'),
        'TENTHMETER'      => array('1.0e-10',          'tenth-meter'),
        'TERAMETER'       => array('1.0e+12',          'Tm'),
        'THOU'            => array('0.0000254',        'thou'),
        'TOISE'           => array('1.949',            'toise'),
        'TOWNSHIP'        => array(array('' => '38016000','/' => '3937'), 'twp'),
        'T_SUN'           => array('0.0358',           "t'sun"),
        'TU'              => array('161130',           'tu'),
        'TWAIN'           => array('3.6576074',        'twain'),
        'TWIP'            => array('0.000017639',      'twip'),
        'U'               => array('0.04445',          'U'),
        'VARA_CALIFORNIA' => array('0.83820168',       'vara'),
        'VARA_MEXICAN'    => array('0.83802',          'vara'),
        'VARA_PORTUGUESE' => array('1.10',             'vara'),
        'VARA_AMERICA'    => array('0.864',            'vara'),
        'VARA'            => array('0.83587',          'vara'),
        'VARA_TEXAS'      => array('0.84666836',       'vara'),
        'VERGE'           => array('0.9144',           'verge'),
        'VERSHOK'         => array('0.04445',          'vershok'),
        'VERST'           => array('1066.8',           'verst'),
        'WAH'             => array('2',                'wah'),
        'WERST'           => array('1066.8',           'werst'),
        'X_UNIT'          => array('1.0020722e-13',    'Xu'),
        'YARD'            => array('0.9144',           'yd'),
        'YOCTOMETER'      => array('1.0e-24',          'ym'),
        'YOTTAMETER'      => array('1.0e+24',          'Ym'),
        'ZEPTOMETER'      => array('1.0e-21',          'zm'),
        'ZETTAMETER'      => array('1.0e+21',          'Zm'),
        'ZOLL'            => array('0.02634',          'zoll'),
        'ZOLL_SWISS'      => array('0.03',             'zoll'),
        'STANDARD'        => 'METER'
    );
}
