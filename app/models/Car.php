<?php
class Car extends Model {
    public function all($search='', $type='') {
        $sql="SELECT * FROM cars WHERE active=1"; $args=[];
        if ($search!=='') { $sql.=" AND (name LIKE ? OR brand LIKE ?)"; $args[]="%$search%"; $args[]="%$search%"; }
        if ($type!=='') { $sql.=" AND type=?"; $args[]=$type; }
        $sql.=" ORDER BY created_at DESC"; $s=$this->db->prepare($sql); $s->execute($args); return $s->fetchAll();
    }
    public function find($id) { $s=$this->db->prepare("SELECT * FROM cars WHERE id=? AND active=1"); $s->execute([$id]); return $s->fetch(); }
    public function adminAll() { return $this->db->query("SELECT * FROM cars ORDER BY id DESC")->fetchAll(); }
    public function save($d) {
        if (!empty($d['id'])) {
            $s=$this->db->prepare("UPDATE cars SET name=?,brand=?,type=?,seats=?,price_per_day=?,image=?,description=?,active=? WHERE id=?");
            return $s->execute([$d['name'],$d['brand'],$d['type'],$d['seats'],$d['price'],$d['image'],$d['description'],$d['active'],$d['id']]);
        }
        $s=$this->db->prepare("INSERT INTO cars(name,brand,type,seats,price_per_day,image,description,active) VALUES(?,?,?,?,?,?,?,?)");
        return $s->execute([$d['name'],$d['brand'],$d['type'],$d['seats'],$d['price'],$d['image'],$d['description'],$d['active']]);
    }
    public function delete($id) {
        $s=$this->db->prepare("DELETE FROM cars WHERE id=?");
        try {
            $s->execute([$id]);
            return $s->rowCount()>0;
        } catch (PDOException $exception) {
            if ($exception->getCode()==='23000'&&isset($exception->errorInfo[1])&&(int)$exception->errorInfo[1]===1451) {
                return false;
            }
            throw $exception;
        }
    }
}
