<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/19/16
 * Time: 3:40 PM
 */

namespace CRMBundle\Controller;


use AppBundle\Controller\GlobalClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class StatisticDataController extends Controller
{
    /**
     * @Route("/basic_data",name="basic_data")
     * @Security("has_role('RETRIEVE_BASIC_DATA')")
     */
    public function basicDataAction(){
        $apiRes = GlobalClass::apiGetData('sum','tp');
        $investmentData = $apiRes['Data']['list'];
        $apiRes = GlobalClass::apiGetData('sum','am');
        $amountData = $apiRes['Data']['list'];
        $apiRes = GlobalClass::apiGetData('sum','rp');
        $paybackData = $apiRes['Data']['list'];
        
        return $this->render('@CRM/StatisticData/basicData.html.twig',[
            'investmentData'=>$investmentData,
            'amountData'=>$amountData,
            'paybackData'=>$paybackData
        ]);
        
    }

    /**
     * statement
     * @Route("/statement",name="statement")
     * @Security("has_role('RETRIEVE_STATEMENT')")
     */
    public function statement(){
        //asset

        $apiRes = GlobalClass::apiGetData('clientasset');
        $groupedByAssetLevel= $apiRes['clientAsset'];

        //investment type
        $original = array();
        $transfer = array();

        //investment type grouped by month, 3mothes, year
        $investmentTypeLastMonth = Array(
            'objects'=>['original'=>$apiRes['investmentType']['objects'][1]['original'],'transfer'=>$apiRes['investmentType']['objects'][1]['transfer']],
            'rest'=>['original'=>$apiRes['investmentType']['rest'][1]['original'],'transfer'=>$apiRes['investmentType']['rest'][1]['transfer']]
        );
        
        $investmentType3Months = array(
            'objects'=>['original'=>$apiRes['investmentType']['objects'][3]['original'],'transfer'=>$apiRes['investmentType']['objects'][3]['transfer']],
            'rest'=>['original'=>$apiRes['investmentType']['rest'][3]['original'],'transfer'=>$apiRes['investmentType']['rest'][3]['transfer']]
        );
        
        $investmentTypeHalfYear = array(
            'objects'=>['original'=>$apiRes['investmentType']['objects'][6]['original'],'transfer'=>$apiRes['investmentType']['objects'][6]['transfer']],
            'rest'=>['original'=>$apiRes['investmentType']['rest'][6]['original'],'transfer'=>$apiRes['investmentType']['rest'][6]['transfer']]
        );


        return $this->render('CRMBundle:StatisticData:Statement.html.twig',[
            'groupedByAssetLevel'=>$groupedByAssetLevel,
            'investmentTypeLastMonth'=>$investmentTypeLastMonth,
            'investmentType3Months'=>$investmentType3Months,
            'investmentTypeHalfYear'=>$investmentTypeHalfYear
            
        ]);
    }

    /**
     * @Route("/export/asset",name="export_asset")
     */
    public function exportAssetToExcel(){
        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();

        //set font
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
            ->setSize(10);

        //set width
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);

        //set horizontal alignment
        $objPHPExcel->getActiveSheet()->getStyle()->getAlignment()->setHorizontal();


        //set title of first row
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1',' 分类')
            ->setCellValue('A2',' 0.01-1K')
            ->setCellValue('A3',' 1K-100K')
            ->setCellValue('A4',' 100K-1M')
            ->setCellValue('A5',' 1M以上')
            ->setCellValue('B1','总资产')
            ->setCellValue('C1','可用余额')
            ->setCellValue('D1','人数');

        $groupedByAssetLevel = array(
            'total'=>['lessThan1K'=>'3900000','between1KAnd100K'=>'3662000','between100KAnd1M'=>'5420000','moreThan1M'=>'6000000'],
            'balance'=>['lessThan1K'=>'2550000','between1KAnd100K'=>'4250000','between100KAnd1M'=>'4439000','moreThan1M'=>'3000000'],
            'clientsCount'=>['lessThan1K'=>'221', 'between1KAnd100K'=>'169', 'between100KAnd1M'=>'102', 'moreThan1M'=>'76']
        );

        $i = 'B';
        foreach($groupedByAssetLevel as $key=>$value){
            $objPHPExcel->getActiveSheet()->setCellValue($i.'2',$value['lessThan1K']);
            $objPHPExcel->getActiveSheet()->setCellValue($i.'3',$value['between1KAnd100K']);
            $objPHPExcel->getActiveSheet()->setCellValue($i.'4',$value['between100KAnd1M']);
            $objPHPExcel->getActiveSheet()->setCellValue($i.'5',$value['moreThan1M']);
            $i++;
        }


        $objWriter = $this->get('phpexcel')->createWriter($objPHPExcel,'Excel2007');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="客户投资报表.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');exit;
    }

}