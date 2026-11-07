<?php
class HomeController extends Controller {
    public function index() { $cars=(new Car())->all(); $this->view('home',['cars'=>array_slice($cars,0,6)]); }
}
