<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/pData.class.php';
require_once dirname(__FILE__) . '/pDraw.class.php';
require_once dirname(__FILE__) . '/pImage.class.php';

/**
 * Basic class of PChartBasic
 */
class PChartBasic extends pData{
    
    public $pImage;

    // Constructor
    public function __construct()
    {
        parent::__construct();
      
    }
    
    // Set X and Y
    public function setYXData($Values,$SerieName){
        parent::addPoints($Values, $SerieName);
    }
    
    // set serie weight
    public function setSerieWeight($Series, $Weight) {
        parent::setSerieWeight($Series, $Weight);
    }
    
    // set abscissa
    public function setAbscissa($Serie) {
        parent::setAbscissa($Serie);
    }

    // render image bar char location report
    public function renderImageBarChartLocationReport($FileName, $arrImg, $arrText, $arrGrapArea, $data_y, $data_x, $arrName, $arrSeries, $arrFillRectangle,$Weight = 2 ){
        
        // Add data in your dataset 
        $this->setYXData($data_y,$arrName[0]);
        $this->setYXData($data_x,$arrName[1]);
        $this->setSerieWeight($arrSeries[0], $Weight);
        $this->setAbscissa($arrSeries[1]);
        
        // Create a pChart object and associate your dataset 
        $this->pImage = new pImage($arrImg[0], $arrImg[1],$this);
        
        // Define the boundaries of the graph area
        $this->pImage->setGraphArea($arrGrapArea[0], $arrGrapArea[1], $arrGrapArea[2], $arrGrapArea[3]);
        
        // Draw text
        $this->pImage->drawText($arrText[0],$arrText[1],$arrText[2],array("FontSize"=>$arrText[3],"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

        $this->pImage->drawFilledRectangle($arrFillRectangle[0],$arrFillRectangle[1],$arrFillRectangle[2],$arrFillRectangle[3],array("Surrounding"=>$arrFillRectangle[4],"Alpha"=>$arrFillRectangle[5]));
        
         // Draw the scale, keep everything automatic 
        $this->pImage->drawScale();
        
         // Draw the scale, keep everything automatic  
        $this->pImage->drawSplineChart();
                
        // Render the picture (choose the best way)
        $this->pImage->render($FileName);
    }
   
    
}
