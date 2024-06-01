<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Traffic extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('url', 'form'));
        $this->load->library(array('session', 'form_validation'));
        $this->load->model('Signal_model');
    }

    public function index() {
        $last_signal = $this->Signal_model->get_last_signal();
        $data = [
            'sequence' => isset($last_signal['sequence']) ? explode(',', $last_signal['sequence']) : [],
            'green_interval' => isset($last_signal['green_interval']) ? $last_signal['green_interval'] : '',
            'yellow_interval' => isset($last_signal['yellow_interval']) ? $last_signal['yellow_interval'] : ''
        ];
        $this->load->view('index', $data);
    }

    public function start() {
        $this->form_validation->set_rules('sequence1', 'Signal 1', 'required|numeric|in_list[1,2,3,4]');
        $this->form_validation->set_rules('sequence2', 'Signal 2', 'required|numeric|in_list[1,2,3,4]');
        $this->form_validation->set_rules('sequence3', 'Signal 3', 'required|numeric|in_list[1,2,3,4]');
        $this->form_validation->set_rules('sequence4', 'Signal 4', 'required|numeric|in_list[1,2,3,4]');
        $this->form_validation->set_rules('greenInterval', 'Green Interval', 'required|numeric');
        $this->form_validation->set_rules('yellowInterval', 'Yellow Interval', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $sequence1 = $this->input->post('sequence1');
        $sequence2 = $this->input->post('sequence2');
        $sequence3 = $this->input->post('sequence3');
        $sequence4 = $this->input->post('sequence4');
        $greenInterval = $this->input->post('greenInterval');
        $yellowInterval = $this->input->post('yellowInterval');

        $sequenceArray = [$sequence1, $sequence2, $sequence3, $sequence4];
        if (count(array_unique($sequenceArray)) != 4) {
            echo json_encode(['success' => false, 'message' => 'Sequence must be unique numbers 1, 2, 3, and 4.']);
            return;
        }

        $data = [
            'sequence' => implode(',', $sequenceArray),
            'green_interval' => $greenInterval,
            'yellow_interval' => $yellowInterval
        ];

        if ($this->Signal_model->signal_exists()) {
            if ($this->Signal_model->update_signal($data)) {
                echo json_encode(['success' => true, 'sequence' => $sequenceArray, 'greenInterval' => $greenInterval, 'yellowInterval' => $yellowInterval]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update data.']);
            }
        } else {
            if ($this->Signal_model->save_signal($data)) {
                echo json_encode(['success' => true, 'sequence' => $sequenceArray, 'greenInterval' => $greenInterval, 'yellowInterval' => $yellowInterval]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save data.']);
            }
        }
    }

    public function stop() {
        $this->session->unset_userdata('sequence');
        $this->session->unset_userdata('greenInterval');
        $this->session->unset_userdata('yellowInterval');

        echo json_encode(['success' => true]);
    }
}
?>
