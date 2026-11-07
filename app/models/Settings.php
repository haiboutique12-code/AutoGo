<?php
class Settings extends Model {
    public function all() {
        $rows=$this->db->query('SELECT setting_key,setting_value FROM app_settings')->fetchAll();
        $settings=[];
        foreach ($rows as $row) {
            $settings[$row['setting_key']]=$row['setting_value'];
        }
        return $settings;
    }

    public function saveMany(array $settings) {
        $statement=$this->db->prepare(
            'INSERT INTO app_settings(setting_key,setting_value) VALUES(?,?) '.
            'ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)'
        );
        $this->db->beginTransaction();
        try {
            foreach ($settings as $key=>$value) {
                $statement->execute([$key,$value]);
            }
            $this->db->commit();
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $exception;
        }
    }
}
