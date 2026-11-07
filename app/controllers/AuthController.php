<?php
class AuthController extends Controller {
    private function redirectAuthenticated() {
        if (!empty($_SESSION['user'])) {
            $this->redirect($_SESSION['user']['role']==='admin'?'/admin':'/');
        }
    }

    public function registerForm() {
        $this->redirectAuthenticated();
        $this->view('auth/register',['errors'=>[],'old'=>[]]);
    }
    public function register() {
        verify_csrf();
        $this->redirectAuthenticated();
        $name=is_string($_POST['name']??null)?trim($_POST['name']):'';
        $email=is_string($_POST['email']??null)?strtolower(trim($_POST['email'])):'';
        $phone=is_string($_POST['phone']??null)?trim($_POST['phone']):'';
        $password=is_string($_POST['password']??null)?$_POST['password']:'';
        $old=['name'=>$name,'email'=>$email,'phone'=>$phone];
        $errors=[];

        if (mb_strlen($name)>120) $errors[]='Họ và tên không được vượt quá 120 ký tự.';
        if (mb_strlen($name)<2) $errors[]='Vui lòng nhập họ và tên có ít nhất 2 ký tự.';
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($email)>160) $errors[]='Vui lòng nhập địa chỉ email hợp lệ.';
        if (mb_strlen($phone)>30) $errors[]='Số điện thoại không được vượt quá 30 ký tự.';
        if (strlen($password)<8||strlen($password)>72) $errors[]='Mật khẩu phải có từ 8 đến 72 ký tự.';

        if (!$errors&&! (new User())->create($name,$email,$phone,$password)) {
            $errors[]='Email này đã được đăng ký. Hãy đăng nhập hoặc sử dụng email khác.';
        }
        if ($errors) {
            http_response_code(422);
            $this->view('auth/register',['errors'=>$errors,'old'=>$old]);
            return;
        }

        session_regenerate_id(true);
        flash('success','Tạo tài khoản thành công. Đăng nhập để bắt đầu đặt xe.');
        $this->redirect('/login');
    }
    public function loginForm() {
        $this->redirectAuthenticated();
        $this->view('auth/login',['errors'=>[],'old'=>[]]);
    }
    public function login() {
        verify_csrf();
        $this->redirectAuthenticated();
        $email=is_string($_POST['email']??null)?strtolower(trim($_POST['email'])):'';
        $password=is_string($_POST['password']??null)?$_POST['password']:'';
        $u=filter_var($email,FILTER_VALIDATE_EMAIL)?(new User())->findByEmail($email):false;
        if (!$u||!password_verify($password,$u['password'])) {
            http_response_code(422);
            $this->view('auth/login',[
                'errors'=>['Email hoặc mật khẩu không chính xác.'],
                'old'=>['email'=>$email]
            ]);
            return;
        }
        session_regenerate_id(true);
        $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];
        $destination=$_SESSION['redirect_after_login']??'';
        unset($_SESSION['redirect_after_login']);
        if (!is_string($destination)||strpos($destination,'/')!==0||strpos($destination,'//')===0||($u['role']!=='admin'&&strpos($destination,'/admin')===0)) {
            $destination=$u['role']==='admin'?'/admin':'/';
        }
        flash('success','Xin chào '.$u['name'].'!');
        $this->redirect($destination?:'/');
    }
    public function logout() {
        verify_csrf();
        $_SESSION=[];
        if (ini_get('session.use_cookies')) {
            $params=session_get_cookie_params();
            setcookie(session_name(),'',time()-42000,$params['path'],$params['domain'],$params['secure'],$params['httponly']);
        }
        session_destroy();
        header('Location: '.url('/'));
        exit;
    }
}
