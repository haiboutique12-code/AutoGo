<?php
class Controller {
    protected function view($name, $data = []) {
        extract($data);
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/' . $name . '.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
    protected function redirect($path) { header('Location: ' . url($path)); exit; }
    protected function requireLogin() {
        if (empty($_SESSION['user'])) {
            if (($_SERVER['REQUEST_METHOD']??'GET')==='GET') {
                $requestUri=$_SERVER['REQUEST_URI']??'';
                $path=parse_url($requestUri,PHP_URL_PATH);
                $query=parse_url($requestUri,PHP_URL_QUERY);
                $basePath=rtrim((require __DIR__.'/../../config/config.php')['base_url'],'/');
                if ($basePath!==''&&($path===$basePath||strpos($path,$basePath.'/')===0)) {
                    $path=substr($path,strlen($basePath))?:'/';
                }
                if (is_string($path)&&strpos($path,'/')===0&&strpos($path,'//')!==0) {
                    $_SESSION['redirect_after_login']=$path.($query!==null?'?'.$query:'');
                }
            }
            flash('error', 'Vui lòng đăng nhập để tiếp tục.');
            $this->redirect('/login');
        }
    }
    protected function requireAdmin() {
        $this->requireLogin();
        if ($_SESSION['user']['role'] !== 'admin') { http_response_code(403); exit('Bạn không có quyền truy cập.'); }
    }
}
