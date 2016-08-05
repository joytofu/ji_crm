<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 5/28/16
 * Time: 1:43 PM
 */

namespace AppBundle\Controller;


use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class CRMPaginator
{
    private $request;
    private $query;
    private $page;
    private $page_size;
    private $page_range;
    private $offset;
    

    public function __construct(Request $request,$query = null,$page_size=10,$page_range=5)
    {
        $this->request = $request;
        $this->query = $query;
        $this->page = null !== $this->request->get('page') ? $this->request->get('page') : 1;
        $this->page_size = $page_size;
        $this->page_range = $page_range;
        $this->offset = ((int)($this->page) - 1) * ((int)$this->page_size);
        
    }
    
    public function getMainResult($ifArrayResult = false){
        if($ifArrayResult==true){
            return $this->query->setFirstResult($this->offset)->setMaxResults($this->page_size)->getArrayResult();
        }else{
            return $this->query->setFirstResult($this->offset)->setMaxResults($this->page_size)->getResult();
        }
    }
    

    public function paginate($queryCount = null){

        //pagination
        $page_offset = ($this->page_range-1)/2;
        if($queryCount){
            $count = $queryCount;
        }else{
            if(!$count = count($this->query->getArrayResult())){
                $count = 0;
            }
        }
        
        $total_pages = ceil($count/$this->page_size);
        $page_start = 1;
        $page_end = $total_pages;
        if($this->page > $page_offset){
            $page_start = $this->page - $page_offset;
            $page_end = $total_pages > $this->page + $page_offset ? $this->page + $page_offset : $total_pages;
        }else{
            $page_start = 1;
            $page_end = $total_pages > $this->page_range ? $this->page_range : $total_pages;
        }
        /*if($this->page + $page_offset > $total_pages){
            $page_start = $page_start - ($this->page + $page_offset - $page_end);
        }*/
        
        
        /*if((is_array($mainResult)&&!empty($mainResult)) && (is_array($sideResult)&&!empty($this->sideResult))){
            $result = $this->dataMerge($mainResult,$sideResult);
        }else{
            if(!is_array($mainResult) || empty($mainResult)){
                die('$mainResult is empty or its not an array. Please check again!');
            }elseif(!is_array($sideResult) || empty($sideResult)){
                die('$sideResult is empty or its not an array. Please check again!');
            }
        }*/
        //$result = $this->dataMerge($mainResult,$sideResult);

        return $pagination = array(
            'page_count' => $total_pages,
            'page_start' => $page_start,
            'page_end' => $page_end,
        );
    }
    
    public function dataMerge($mainResult = array(),$sideResult = array()){
        $callback = function($mainResult_e,$sideResult_e){
            if(is_array($mainResult_e) && is_array($sideResult_e)){
                return array_merge($mainResult_e,$sideResult_e);
            }else{
                die('elements of either mainResult or sideResult is not an array');
            }
        };
        return array_map($callback,$mainResult,$sideResult);
    }

    /*public function getMergedData(){
        $query = $this->query->setFirstResult($this->offset)->setMaxResults($this->page_size);
        $mainResult = $query->getArrayResult();
        return $this->dataMerge($mainResult,$this->sideResult);
    }*/

}