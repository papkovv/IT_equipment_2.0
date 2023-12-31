<?php
/**
 * index.php
 *
 * @since       2017-05-08
 * @category    Library
 * @package     Pdf
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2002-2017 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-pdf
 *
 * This file is part of tc-lib-pdf software library.
 */

// NOTE: run make deps fonts in the project root to generate the dependencies and example fonts.

// autoloader when using Composer
require ('../vendor/autoload.php');

use \Com\Tecnick\Color\Model\Cmyk as ColorCMYK;
use \Com\Tecnick\Color\Model\Gray as ColorGray;
use \Com\Tecnick\Color\Model\Hsl as ColorHSL;
use \Com\Tecnick\Color\Model\Rgb as ColorRGB;

define('OUTPUT_FILE', '../target/example.pdf');

// define fonts directory
define('K_PATH_FONTS', '../vendor/tecnickcom/tc-lib-pdf-font/target/fonts/core/');

// autoloader when using RPM or DEB package installation
//require ('/usr/share/php/Com/Tecnick/Pdf/autoload.php');

// main TCPDF object
$pdf = new \Com\Tecnick\Pdf\Tcpdf('mm', true, false, true, '');

// ----------
// Set Metadata

$pdf->setCreator('tc-lib-pdf');
$pdf->setAuthor('John Doe');
$pdf->setSubject('tc-lib-pdf example');
$pdf->setTitle('Example');
$pdf->setKeywords('TCPDF','tc-lib-pdf','example');

// ----------
// Insert fonts

$bfont = $pdf->font->insert($pdf->pon, 'helvetica');
$bfont = $pdf->font->insert($pdf->pon, 'times', 'BI');

// ----------
// Add first page

$page01 = $pdf->page->add();

// Add Images

$iid01 = $pdf->image->add('../vendor/tecnickcom/tc-lib-pdf-image/test/images/200x100_CMYK.jpg');
$iid01_out = $pdf->image->getSetImage($iid01, 0, 0, 40, 20, $page01['height']);
$pdf->page->addContent($iid01_out);

$iid02 = $pdf->image->add('../vendor/tecnickcom/tc-lib-pdf-image/test/images/200x100_GRAY.jpg');
$iid02_out = $pdf->image->getSetImage($iid02, 40, 0, 40, 20, $page01['height']);
$pdf->page->addContent($iid02_out);

$iid03 = $pdf->image->add('../vendor/tecnickcom/tc-lib-pdf-image/test/images/200x100_GRAY.png');
$iid03_out = $pdf->image->getSetImage($iid03, 80, 0, 40, 20, $page01['height']);
$pdf->page->addContent($iid03_out);

$iid04 = $pdf->image->add('../vendor/tecnickcom/tc-lib-pdf-image/test/images/200x100_INDEX16.png');
$iid04_out = $pdf->image->getSetImage($iid04, 120, 0, 40, 20, $page01['height']);
$pdf->page->addContent($iid04_out);

$iid05 = $pdf->image->add('../vendor/tecnickcom/tc-lib-pdf-image/test/images/200x100_INDEX256.png');
$iid05_out = $pdf->image->getSetImage($iid05, 160, 0, 40, 20, $page01['height']);
$pdf->page->addContent($iid05_out);

$iid06 = $pdf->image->add('../vendor/tecnickcom/tc-lib-pdf-image/test/images/200x100_INDEXALPHA.png');
$iid06_out = $pdf->image->getSetImage($iid06, 0, 20, 40, 20, $page01['height']);
$pdf->page->addContent($iid06_out);

$iid07 = $pdf->image->add('../vendor/tecnickcom/tc-lib-pdf-image/test/images/200x100_RGB.jpg');
$iid07_out = $pdf->image->getSetImage($iid07, 40, 20, 40, 20, $page01['height']);
$pdf->page->addContent($iid07_out);

$iid08 = $pdf->image->add('../vendor/tecnickcom/tc-lib-pdf-image/test/images/200x100_RGB.png');
$iid08_out = $pdf->image->getSetImage($iid08, 80, 20, 40, 20, $page01['height']);
$pdf->page->addContent($iid08_out);

$iid09 = $pdf->image->add('../vendor/tecnickcom/tc-lib-pdf-image/test/images/200x100_RGBALPHA.png');
$iid09_out = $pdf->image->getSetImage($iid09, 120, 20, 40, 20, $page01['height']);
$pdf->page->addContent($iid09_out);

$iid10 = $pdf->image->add('../vendor/tecnickcom/tc-lib-pdf-image/test/images/200x100_RGBICC.jpg');
$iid10_out = $pdf->image->getSetImage($iid10, 160, 20, 40, 20, $page01['height']);
$pdf->page->addContent($iid10_out);

$iid11 = $pdf->image->add('../vendor/tecnickcom/tc-lib-pdf-image/test/images/200x100_RGBICC.png');
$iid11_out = $pdf->image->getSetImage($iid11, 0, 40, 40, 20, $page01['height']);
$pdf->page->addContent($iid11_out);

$iid12 = $pdf->image->add('../vendor/tecnickcom/tc-lib-pdf-image/test/images/200x100_RGBINT.png');
$iid12_out = $pdf->image->getSetImage($iid12, 40, 40, 40, 20, $page01['height']);
$pdf->page->addContent($iid12_out);

// ----------
// Add second page

$page02 = $pdf->page->add();

$style1 = array(
    'lineWidth' => 0.5,
    'lineCap'   => 'butt',
    'lineJoin'  => 'miter',
    'dashArray' => array(5,2,1,2),
    'dashPhase' => 0,
    'lineColor' => 'red',
    'fillColor' => 'powderblue',
);

$style2 = array(
    'lineWidth' => 0.5,
    'lineCap'   => 'butt',
    'lineJoin'  => 'miter',
    'dashArray' => array(),
    'dashPhase' => 0,
    'lineColor' => 'green',
    'fillColor' => 'powderblue',
);

$style3 = array(
    'lineWidth' => 1,
    'lineCap'   => 'round',
    'lineJoin'  => 'round',
    'dashArray' => array(6,2),
    'dashPhase' => 0,
    'lineColor' => 'blue',
    'fillColor' => 'powderblue',
);

$style4 = array(
    'all' => array(
        'lineWidth'  => 0.5,
        'lineCap'    => 'butt',
        'lineJoin'   => 'miter',
        'miterLimit' => 0.5,
        'dashArray'  => array(),
        'dashPhase'  => 0,
        'lineColor'  => 'black',
        'fillColor'  => 'aliceblue',
    ),
    0 => array(
        'lineWidth' => 0.25,
        'lineCap'   => 'butt',
        'lineJoin'  => 'miter',
        'dashArray' => array(),
        'dashPhase' => 0,
        'lineColor' => 'red',
        'fillColor' => 'powderblue',
    ),
    1 => array(
        'lineWidth' => 0.25,
        'lineCap'   => 'butt',
        'lineJoin'  => 'miter',
        'dashArray' => array(),
        'dashPhase' => 0,
        'lineColor' => 'green',
        'fillColor' => 'powderblue',
    ),
    2 => array(
        'lineWidth' => 0.50,
        'lineCap'   => 'round',
        'lineJoin'  => 'miter',
        'dashArray' => array(),
        'dashPhase' => 0,
        'lineColor' => 'blue',
        'fillColor' => 'powderblue',
    ),
    3 => array(
        'lineWidth' => 0.75,
        'lineCap'   => 'square',
        'lineJoin'  => 'miter',
        'dashArray' => array(6,3,2,3),
        'dashPhase' => 0,
        'lineColor' => 'yellow',
        'fillColor' => 'powderblue',
    )
);

$style5 = array(
    'lineWidth' => 0.25,
    'lineCap'   => 'butt',
    'lineJoin'  => 'miter',
    'dashArray' => array(),
    'dashPhase' => 0,
    'lineColor' => 'darkblue',
    'fillColor' => 'mistyrose',
);

$style6 = array(
    'lineWidth' => 0.5,
    'lineCap'   => 'butt',
    'lineJoin'  => 'miter',
    'dashArray' => array(3,3),
    'dashPhase' => 0,
    'lineColor' => 'green',
    'fillColor' => 'powderblue',
);

$style7 = array(
    'lineWidth' => 0.5,
    'lineCap'   => 'butt',
    'lineJoin'  => 'miter',
    'dashArray' => array(),
    'dashPhase' => 0,
    'lineColor' => 'darkorange',
    'fillColor' => 'palegreen',
);

$pdf->graph->setPageWidth($page02['width']);
$pdf->graph->setPageHeight($page02['height']);

// Line

$line1 = $pdf->graph->getLine(5, 10, 80, 30, $style1);
$pdf->page->addContent($line1);

$line2 = $pdf->graph->getLine(5, 10, 5, 30, $style2);
$pdf->page->addContent($line2);

$line3 = $pdf->graph->getLine(5, 10, 80, 10, $style3);
$pdf->page->addContent($line3);

// Rectangle

$rect1 = $pdf->graph->getRect(100, 10, 40, 20, 'DF', $style4);
$pdf->page->addContent($rect1);

$rect2 = $pdf->graph->getRect(145, 10, 40, 20, 'D', array('all' => $style3));
$pdf->page->addContent($rect2);

// Curve

$curve1 = $pdf->graph->getCurve(5, 40, 30, 55, 70, 45, 60, 75, '', $style6);
$pdf->page->addContent($curve1);

$curve2 = $pdf->graph->getCurve(80, 40, 70, 75, 150, 45, 100, 75, 'F', $style6);
$pdf->page->addContent($curve2);

$curve3 = $pdf->graph->getCurve(140, 40, 150, 55, 180, 45, 200, 75, 'DF', $style6);
$pdf->page->addContent($curve3);

// Circle

$circle1 = $pdf->graph->getCircle(25,105,20);
$pdf->page->addContent($circle1);

$circle2 = $pdf->graph->getCircle(25,105,10, 90, 180, '', $style7);
$pdf->page->addContent($circle2);

$circle3 = $pdf->graph->getCircle(25,105,10, 270, 360, 'F');
$pdf->page->addContent($circle3);

$circle4 = $pdf->graph->getCircle(25,105,10, 270, 360, 'C', $style7);
$pdf->page->addContent($circle4);

// Ellipse

$ellipse1 = $pdf->graph->getEllipse(100,103,40,20);
$pdf->page->addContent($ellipse1);

$ellipse2 = $pdf->graph->getEllipse(100,105,20,10, 0, 90, 180, '', $style7);
$pdf->page->addContent($ellipse2);

$ellipse3 = $pdf->graph->getEllipse(100,105,20,10, 0, 270, 360, 'DF', $style7);
$pdf->page->addContent($ellipse3);

$ellipse4 = $pdf->graph->getEllipse(175,103,30,15,45);
$pdf->page->addContent($ellipse4);

$ellipse5 = $pdf->graph->getEllipse(175,105,15,7.50, 45, 90, 180, '', $style7);
$pdf->page->addContent($ellipse5);

$ellipse6 = $pdf->graph->getEllipse(175,105,15,7.50, 45, 270, 360, 'F', $style7, 4);
$pdf->page->addContent($ellipse6);

// Polygon

$polygon1 = $pdf->graph->getPolygon(array(5,135,45,135,15,165), 's');
$pdf->page->addContent($polygon1);

$polygon2 = $pdf->graph->getPolygon(array(60,135,80,135,80,155,70,165,50,155), 'DF', array($style6, $style7, $style7, $style6, $style6));
$pdf->page->addContent($polygon2);

$polygon3 = $pdf->graph->getPolygon(array(120,135,140,135,150,155,110,155), 'D', array($style5, $style6, $style7, $style6));
$pdf->page->addContent($polygon3);

$polygon4 = $pdf->graph->getPolygon(array(160,135,190,155,170,155,200,160,160,165), 'DF', array('all' => $style6));
$pdf->page->addContent($polygon4);

$polygon5 = $pdf->graph->getPolygon(array(80,165,90,160,100,165,110,160,120,165,130,160,140,165), 'D', array('all' => $style1));
$pdf->page->addContent($polygon5);

// Regular Polygon

$regpoly1 = $pdf->graph->getRegularPolygon(20, 190, 15, 6, 0, 'b', array('all' => $style6), 's', $style5);
$pdf->page->addContent($regpoly1);

$regpoly2 = $pdf->graph->getRegularPolygon(55, 190, 15, 6, 0, 's');
$pdf->page->addContent($regpoly2);

$regpoly3 = $pdf->graph->getRegularPolygon(55, 190, 10, 6, 45, 'DF', array($style6, $style5, $style7, $style5, $style7, $style7));
$pdf->page->addContent($regpoly3);

$regpoly4 = $pdf->graph->getRegularPolygon(90, 190, 15, 3, 0, 'b', array('all' => $style5), 'F', $style6);
$pdf->page->addContent($regpoly4);

$regpoly5 = $pdf->graph->getRegularPolygon(125, 190, 15, 4, 30, 'b', array('all' => $style5), 's', $style1);
$pdf->page->addContent($regpoly5);

$regpoly6 = $pdf->graph->getRegularPolygon(160, 190, 15, 10, 0, 's');
$pdf->page->addContent($regpoly6);


// Star Polygon

$startpoly1 = $pdf->graph->getStarPolygon(20, 230, 15, 20, 3, 0, 's', array('all' => $style2), 'b', $style5);
$pdf->page->addContent($startpoly1);

$startpoly2 = $pdf->graph->getStarPolygon(55, 230, 15, 12, 5, 15, 's');
$pdf->page->addContent($startpoly2);

$startpoly3 = $pdf->graph->getStarPolygon(55, 230, 7, 12, 5, 45, 'b', array('all' => $style7), 'F', $style6);
$pdf->page->addContent($startpoly3);

$startpoly4 = $pdf->graph->getStarPolygon(90, 230, 15, 20, 6, 0, 's', array('all' => $style5), 'F', $style6);
$pdf->page->addContent($startpoly4);

$startpoly5 = $pdf->graph->getStarPolygon(125, 230, 15, 5, 2, 30, 's', array('all' => $style5), 's', $style6);
$pdf->page->addContent($startpoly5);

$startpoly6 = $pdf->graph->getStarPolygon(160, 230, 15, 10, 3, 0, 's');
$pdf->page->addContent($startpoly6);

$startpoly7 = $pdf->graph->getStarPolygon(160, 230, 7, 50, 26, 10, 's');
$pdf->page->addContent($startpoly7);

// Rounded Rectangle

$roundrect1 = $pdf->graph->getRoundedRect(5, 255, 40, 30, 3.50, 3.50, '1111', 'DF');
$pdf->page->addContent($roundrect1);

$roundrect2 = $pdf->graph->getRoundedRect(50, 255, 40, 30, 6.50, 6.50, '1000', 'b');
$pdf->page->addContent($roundrect2);

$roundrect3 = $pdf->graph->getRoundedRect(95, 255, 40, 30, 10.0, 5.0, '1111', 's', $style6);
$pdf->page->addContent($roundrect3);

$roundrect4 = $pdf->graph->getRoundedRect(140, 255, 40, 30, 8.0, 8.0, '0101', 'DF', $style6);
$pdf->page->addContent($roundrect4);

// Arrows

$arrow1 = $pdf->graph->getArrow(200, 280, 185, 266, 0, 5, 15);
$pdf->page->addContent($arrow1);

$arrow2 = $pdf->graph->getArrow(200, 280, 190, 263, 1, 5, 15);
$pdf->page->addContent($arrow2);

$arrow3 = $pdf->graph->getArrow(200, 280, 195, 261, 2, 5, 15);
$pdf->page->addContent($arrow3);

$arrow4 = $pdf->graph->getArrow(200, 280, 200, 260, 3, 5, 15);
$pdf->page->addContent($arrow4);

// ----------
// Add third page

$page03 = $pdf->page->add();

$pdf->graph->setPageWidth($page03['width']);
$pdf->graph->setPageHeight($page03['height']);

// center of ellipse
$xc=100;
$yc=100;
// X Y axis
$arc1 = $pdf->graph->getLine($xc-50, $yc, $xc+50, $yc, $style1);
$pdf->page->addContent($arc1);

$arc2 = $pdf->graph->getLine($xc, $yc-50, $xc, $yc+50, $style2);
$pdf->page->addContent($arc2);

// ellipse axis
$arc3 = $pdf->graph->getLine($xc-50, $yc-50, $xc+50, $yc+50, $style3);
$pdf->page->addContent($arc3);

$arc4 = $pdf->graph->getLine($xc-50, $yc+50, $xc+50, $yc-50, $style5);
$pdf->page->addContent($arc4);

// ellipse
$arc5 = $pdf->graph->getEllipse($xc, $yc, 30, 15, 45, 0, 360, 'D', $style6, 2);
$pdf->page->addContent($arc5);

// ellipse arc
$arc6 = $pdf->graph->getEllipse($xc, $yc, 30, 15, 45, 45, 90, 'D', $style7, 2);
$pdf->page->addContent($arc6);

// ----------
// Add fourth page

$page04 = $pdf->page->add();

$pdf->graph->setPageWidth($page04['width']);
$pdf->graph->setPageHeight($page04['height']);

$xc = 105;
$yc = 100;
$r = 50;

$pie1 = $pdf->graph->getPieSector($xc, $yc, $r, 20, 120, 'FD', $style5, 2);
$pdf->page->addContent($pie1);

$pie2 = $pdf->graph->getPieSector($xc, $yc, $r, 120, 250, 'FD', $style6, 2);
$pdf->page->addContent($pie2);

$pie3 = $pdf->graph->getPieSector($xc, $yc, $r, 250, 20, 'FD', $style7, 2);
$pdf->page->addContent($pie3);


// ----------
// Add fifth page

$page05 = $pdf->page->add();

$pdf->graph->setPageWidth($page05['width']);
$pdf->graph->setPageHeight($page05['height']);



$pdf->graph->add($style7);

$style8 = $pdf->graph->getCurrentStyleArray();

// Crop Marks

$crpmrk1 = $pdf->graph->getCropMark(50, 70, 10, 10, 'TL', $style8);
$pdf->page->addContent($crpmrk1);

$crpmrk2 = $pdf->graph->getCropMark(140, 70, 10, 10, 'TR', $style8);
$pdf->page->addContent($crpmrk2);

$crpmrk3 = $pdf->graph->getCropMark(50, 120, 10, 10, 'BL', $style8);
$pdf->page->addContent($crpmrk3);

$crpmrk4 = $pdf->graph->getCropMark(140, 120, 10, 10, 'BR', $style8);
$pdf->page->addContent($crpmrk4);

$crpmrk5 = $pdf->graph->getCropMark(95, 65, 5, 5, 'LTR');
$pdf->page->addContent($crpmrk5);

$crpmrk6 = $pdf->graph->getCropMark(95, 125, 5, 5, 'LBR');
$pdf->page->addContent($crpmrk6);

$crpmrk7 = $pdf->graph->getCropMark(45, 95, 5, 5, 'TLB');
$pdf->page->addContent($crpmrk7);

$crpmrk8 = $pdf->graph->getCropMark(145, 95, 5, 5, 'TRB');
$pdf->page->addContent($crpmrk8);

$crpmrk9 = $pdf->graph->getCropMark(95, 140, 5, 5, 'TLBR', array('lineColor' => 'lime'));
$pdf->page->addContent($crpmrk9);


// Registration Marks

$regmrk1 = $pdf->graph->getRegistrationMark(40, 60, 5, false);
$pdf->page->addContent($regmrk1);

$regmrk2 = $pdf->graph->getRegistrationMark(150, 60, 5, true);
$pdf->page->addContent($regmrk2);

$regmrk3 = $pdf->graph->getRegistrationMark(40, 130, 5, true);
$pdf->page->addContent($regmrk3);

$regmrk4 = $pdf->graph->getRegistrationMark(150, 130, 5, false, 'blue');
$pdf->page->addContent($regmrk4);

// CYMK Registration Mark
$regmrk5 = $pdf->graph->getCmykRegistrationMark(150, 155, 8);
$pdf->page->addContent($regmrk5);

// Add Spot Colors

$color_custom_dark_green =  new \Com\Tecnick\Color\Model\Cmyk(
    array(
        'cyan'    => 1,
        'magenta' => 0.5,
        'yellow'  => 0.8,
        'key'     => 0.45,
        'alpha'   => 0
    )
);
$pdf->color->addSpotColor('Custom Dark Green', $color_custom_dark_green);

$color_custom_light_yellow =  new \Com\Tecnick\Color\Model\Cmyk(
    array(
        'cyan'    => 0,
        'magenta' => 0,
        'yellow'  => 0.55,
        'key'     => 0,
        'alpha'   => 0
    )
);
$pdf->color->addSpotColor('Custom Light Yellow', $color_custom_light_yellow);

$color_custom_black =  new \Com\Tecnick\Color\Model\Cmyk(
    array(
        'cyan'    => 0,
        'magenta' => 0,
        'yellow'  => 0,
        'key'     => 1,
        'alpha'   => 0
    )
);
$pdf->color->addSpotColor('Custom Black', $color_custom_black);

$color_custom_red =  new \Com\Tecnick\Color\Model\Cmyk(
    array(
        'cyan'    => 0.3,
        'magenta' => 1,
        'yellow'  => 0.9,
        'key'     => 0.1,
        'alpha'   => 0
    )
);
$pdf->color->addSpotColor('Custom Red', $color_custom_red);

$color_custom_green =  new \Com\Tecnick\Color\Model\Cmyk(
    array(
        'cyan'    => 1,
        'magenta' => 0.3,
        'yellow'  => 1,
        'key'     => 0,
        'alpha'   => 0
    )
);
$pdf->color->addSpotColor('Custom Green', $color_custom_green);

$color_custom_blue =  new \Com\Tecnick\Color\Model\Cmyk(
    array(
        'cyan'    => 1,
        'magenta' => 0.6,
        'yellow'  => 0.1,
        'key'     => 0.05,
        'alpha'   => 0
    )
);
$pdf->color->addSpotColor('Custom Blue', $color_custom_blue);

$color_custom_yellow =  new \Com\Tecnick\Color\Model\Cmyk(
    array(
        'cyan'    => 0,
        'magenta' => 0.2,
        'yellow'  => 1,
        'key'     => 0,
        'alpha'   => 0
    )
);
$pdf->color->addSpotColor('Custom Yellow', $color_custom_yellow);

// Color Registration Bars with spot colors

$colregspot = $pdf->graph->getColorRegistrationBar(30, 150, 100, 10, true,
array(
'black',
'red',
'green',
'blue',
'Custom Dark Green',
'Custom Light Yellow',
'Custom Black',
'Custom Red',
'Custom Green',
'Custom Blue',
'Custom Yellow'
));
$pdf->page->addContent($colregspot);



//$colreg1 = $pdf->graph->getColorRegistrationBar(50, 70, 40, 40, true);
//$pdf->page->addContent($colreg1);


// ----------


// PDF document as string
$doc = $pdf->getOutPDFString();

// Debug document output:
//var_export($doc);

// Save the PDF document as a file
file_put_contents(OUTPUT_FILE, $doc);

echo 'OK: '.OUTPUT_FILE;
