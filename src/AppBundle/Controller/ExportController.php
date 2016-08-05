<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 8/4/16
 * Time: 4:55 PM
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ExportController extends Controller
{
    public function exportAction($data){
        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();
        $objWrite = $this->get('phpexcel')->createWriter($objPHPExcel,'Excel5');
        $export = new ExportToExcel($data,$objPHPExcel,$objWrite);
        $export->exportSimpleData();
    }
}