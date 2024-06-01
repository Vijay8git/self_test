<?php
class Signal_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }

    public function save_signal($data) {
        return $this->db->insert('signals', $data);
    }

    public function get_last_signal() {
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('signals', 1);
        return $query->row_array();
    }

    public function update_signal($data) {
        return $this->db->update('signals', $data, ['id' => 1]);
    }

    public function signal_exists() {
        $query = $this->db->get('signals');
        return $query->num_rows() > 0;
    }
}
?>
