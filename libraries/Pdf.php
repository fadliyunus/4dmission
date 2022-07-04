<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter PDF Library
 *
 * Generate PDF's in your CodeIgniter applications.
 *
 * @package			CodeIgniter
 * @subpackage		Libraries
 * @category		Libraries
 * @author			Chris Harvey
 * @license			MIT License
 * @link			https://github.com/chrisnharvey/CodeIgniter-PDF-Generator-Library
 */
//require_once(dirname(__FILE__) . '/dompdf/dompdf_config.inc.php');
require_once(dirname(__FILE__) . '/dompdf/autoload.inc.php');

use Dompdf\Dompdf;

class pdf extends Dompdf {

    /**
     * Get an instance of CodeIgniter
     *
     * @access	protected
     * @return	void
     */
    protected function ci() {
        return get_instance();
    }

    /**
     * Load a CodeIgniter view into domPDF
     *
     * @access	public
     * @param	string	$view The view to load
     * @param	array	$data The view data
     * @return	void
     */
    public function load_view($view, $data = array()) {
        $html = $this->ci()->load->view($view, $data, TRUE);
        $this->load_html($html);
    }

    public function generate($html, $filename) {
        $this->load_html($html);
        $this->render();
        $this->stream($filename, array("Attachment" => 0));
    }

    public function save($html, $filename) {
        $this->load_html($html);
        $this->render();
        $output = $this->output();
        file_put_contents($filename, $output);
    }

}
