<?php

namespace CRMBundle\Controller;

use AppBundle\Controller\CRMPaginator;
use AppBundle\Controller\ExportToExcel;
use CRMBundle\Entity\Distribution;
use CRMBundle\Entity\Mapping;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
    /**
     * @Route("/",name="index")
     */
    public function indexAction()
    {
        return $this->render('@CRM/Default/index.html.twig',[]);
    }

    /**
     * @Route("/test_basic")
     */
    public function testBasicData($url){
        $url = 'http://localhost:8888/51jili_api/api.php';
        $post_data = array('type'=>'user','all'=>true);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        print_r($output);exit;
        curl_close($ch);

    }
    

    /**
     * @Route("/test_arr")
     */
    public function test_arr(){
        $ar = array(
            2 => array(
                'catid' => 2,
                'catdir' => 'notice',
            ),
            5 => array(
                'catid' => 5,
                'catdir' => 'subject',
            ),
            6=> array(
                'catid' => 6,
                'catdir' => 'news'
            ),
        );

        $catid = 6;
        $r = array_filter($ar, function($t) use ($catid) { return $t['catid'] == $catid; });
        print_r($r);exit;
    }
    

    /**
     * @Route("/test_combo")
     */
    public function test_combo(Request $request){
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT m FROM CRMBundle:Mapping m";
        $sideResult = array(
            ['username'=>'aaaaa','cellphone'=>'18929911111'],
            ['username'=>'bbbbb','cellphone'=>'18929911112'],
            ['username'=>'ccccc','cellphone'=>'18929911113'],
            ['username'=>'ddddd','cellphone'=>'18929911114'],
            ['username'=>'eeeee','cellphone'=>'18929911115']
        );
        $paginator = new CRMPaginator($em,$request,$dql,5);
        $pagination = $paginator->paginate();
        $result = $paginator->dataMerge($paginator->getMainResult(true),$sideResult);

        return $this->render('@CRM/Distribution/test_combo.html.twig',array(
            'result'=>$result,
            'pagination'=>$pagination
        ));

    }
    

    /**
     * @Route("/test_export")
     */
    public function test_export(Request $request){
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
            ->setCellValue('A1','UID')
            ->setCellValue('B1','MID')
            ->setCellValue('C1','USERNAME')
            ->setCellValue('D1','CELLPHONE')
            ->setCellValue('E1','lastFollowTime');
        
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT m FROM CRMBundle:Mapping m";
        $sideResult = array(
            ['username'=>'aaaaa','cellphone'=>'18929911111'],
            ['username'=>'bbbbb','cellphone'=>'18929911112'],
            ['username'=>'ccccc','cellphone'=>'18929911113'],
            ['username'=>'ddddd','cellphone'=>'18929911114'],
            ['username'=>'eeeee','cellphone'=>'18929911115']
        );
        $pagination = new Pagination($em,$request,$dql,$sideResult,5);
        $response = $pagination->paginate();
        $result = $response['result'];

        $baseRow = 2;
        foreach($result as $key=>$value){
            $row = $baseRow + $key;
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A'.$row,$value['uid'])
                ->setCellValue('B'.$row,$value['mid'])
                ->setCellValue('C'.$row,$value['username'])
                ->setCellValue('D'.$row,$value['cellphone'])
                ->setCellValue('E'.$row,$value['lastfollowtime']);

        }

        $objWriter = $this->get('phpexcel')->createWriter($objPHPExcel,'Excel2007');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="test_export.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');exit;

    }

    /**
     * @Route("/test_export2")
     */
    public function test_export2(Request $request){
        $em = $this->getDoctrine()->getManager();
        $dql = "SELECT m FROM CRMBundle:Mapping m";
        $sideResult = array(
            ['username'=>'aaaaa','cellphone'=>'18929911111'],
            ['username'=>'bbbbb','cellphone'=>'18929911112'],
            ['username'=>'ccccc','cellphone'=>'18929911113'],
            ['username'=>'ddddd','cellphone'=>'18929911114'],
            ['username'=>'eeeee','cellphone'=>'18929911115']
        );
        $pagination = new Pagination($em,$request,$dql,$sideResult,5);
        $response = $pagination->paginate();
        $result = $response['result'];

        $objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();
        $objWriter = $this->get('phpexcel')->createWriter($objPHPExcel,'Excel2007');

        $objExport = new ExportToExcel($result,$objPHPExcel,$objWriter);
        $objExport->exportSimpleData();exit;
    }

    public function test_role(){

    }




}
