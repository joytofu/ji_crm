<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 5/31/16
 * Time: 4:41 PM
 */

namespace AppBundle\Controller;


class ExportToExcel
{
    private $data;
    private $fontSize;
    private $objPHPExcel;
    private $objWriter;

    public function __construct($data,$objPHPExcel,$objWriter,$fontSize = 10)
    {
        $this->data = $data;
        $this->fontSize = $fontSize;
        $this->objPHPExcel = $objPHPExcel;
        $this->objWriter = $objWriter;
    }

    public function exportSimpleData(){
        //set active sheet
        $objActiveSheet = $this->objPHPExcel->setActiveSheetIndex(0);

        //set font
        if(is_numeric($this->fontSize)){
            $this->objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize($this->fontSize);
        }

        //set width
        if(is_array($this->data)){
            for($i='A',$j=1;$j<=count($this->data[0]);$i++,$j++){
                $this->objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
            }
        }

        //set horizontal alignment
        $this->objPHPExcel->getActiveSheet()->getStyle()->getAlignment()->setHorizontal();
        
        
        if(is_array($this->data)){

            //set field name
            $i='A';
            foreach($this->data[0] as $key=>$value){
                if($key!='id'){
                    $objActiveSheet->setCellValue($i.'1',$key);
                    $i++;
                }
            }

            //write data to cell
            $baseRow = 2;
            foreach($this->data as $data_e){
                if(is_array($data_e)){
                    $i='A';
                    foreach($data_e as $key=>$value){
                        if($key!='id'){
                            $this->objPHPExcel->getActiveSheet()->setCellValue($i.$baseRow,$value);
                            $i++;
                        }
                    }
                    $baseRow++;
                }
            }
        }
        $filename = $this->random(7);
        //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Type: application/force-download");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename='{$filename}.csv'");
        header('Cache-Control: max-age=0');

        $this->objWriter->save('php://output');exit;
    }

    private function random($length){
        $captchaSource = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $captchaResult = time(); // 随机数返回值
        $captchaSentry = ""; // 随机数中间变量
        for($i=0;$i<$length;$i++){
            $n = rand(0, 35); #strlen($captchaSource));
            if($n >= 36){
                $n = 36 + ceil(($n-36)/3) * 3;
                $captchaResult .= substr($captchaSource, $n, 3);
            }else{
                $captchaResult .= substr($captchaSource, $n, 1);
            }
        }
        return $captchaResult;
    }

}