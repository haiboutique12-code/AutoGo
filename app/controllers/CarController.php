<?php
class CarController extends Controller {
    public function index() {
        $query=$_GET['q']??'';
        $type=$_GET['type']??'';
        $query=is_string($query)?trim($query):'';
        $type=is_string($type)?trim($type):'';
        $cars=(new Car())->all($query,$type);
        $this->view('cars/index',['cars'=>$cars,'q'=>$query,'type'=>$type]);
    }
    public function show() {
        $car=(new Car())->find(positive_int($_GET['id']??null));
        if (!$car) { http_response_code(404); exit('Không tìm thấy xe.'); }
        $this->view('cars/show',['car'=>$car]);
    }
}
