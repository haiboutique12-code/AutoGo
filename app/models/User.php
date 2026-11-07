<?php
class User extends Model {
    public function findByEmail($email) {
        $s=$this->db->prepare("SELECT * FROM users WHERE email=? LIMIT 1"); $s->execute([$email]); return $s->fetch();
    }
    public function create($name,$email,$phone,$password) {
        $s=$this->db->prepare("INSERT INTO users(name,email,phone,password,role) VALUES(?,?,?,?, 'customer')");
        try {
            return $s->execute([$name,$email,$phone,password_hash($password,PASSWORD_DEFAULT)]);
        } catch (PDOException $exception) {
            if ($exception->getCode()==='23000'&&isset($exception->errorInfo[1])&&(int)$exception->errorInfo[1]===1062) {
                return false;
            }
            throw $exception;
        }
    }
    public function all() { return $this->db->query("SELECT id,name,email,phone,role,created_at FROM users ORDER BY id DESC")->fetchAll(); }
}
