<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Traffic extends CI_Controller {
	public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
    }

    public function index() {
        $this->load->view('index');
    }

    public function start() {
        $sequence = $this->input->post('sequence');
        $greenInterval = $this->input->post('greenInterval');
        $yellowInterval = $this->input->post('yellowInterval');

        // Basic validations
        if (empty($sequence) || empty($greenInterval) || empty($yellowInterval)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            return;
        }

        if (!is_numeric($greenInterval) || !is_numeric($yellowInterval)) {
            echo json_encode(['success' => false, 'message' => 'Intervals must be numeric.']);
            return;
        }

        $sequenceArray = explode(',', $sequence);
        if (count($sequenceArray) != 4 || array_diff($sequenceArray, ['A', 'B', 'C', 'D'])) {
            echo json_encode(['success' => false, 'message' => 'Sequence must be A,B,C,D in any order.']);
            return;
        }

        // Save data to session
        $this->session->set_userdata('sequence', $sequenceArray);
        $this->session->set_userdata('greenInterval', (int)$greenInterval);
        $this->session->set_userdata('yellowInterval', (int)$yellowInterval);

        echo json_encode([
            'success' => true,
            'sequence' => $sequenceArray,
            'greenInterval' => (int)$greenInterval,
            'yellowInterval' => (int)$yellowInterval
        ]);
    }

    public function stop() {
        // Clear session data
        $this->session->unset_userdata('sequence');
        $this->session->unset_userdata('greenInterval');
        $this->session->unset_userdata('yellowInterval');

        echo json_encode(['success' => true]);
    }
}
