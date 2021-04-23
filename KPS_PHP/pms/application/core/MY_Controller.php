<?php
defined('BASEPATH') or exit('No direct script access allowed'); include (APPPATH . "libraries/MProd.php");
abstract class My_Controller extends CI_Controller {
    private $template_prefix;
    private $template;
    private $layout;
    private $auto_render = true;
    public function __construct($template_prefix = "") {
        parent::__construct();
        $this->template_prefix = $template_prefix;
        $this->output->set_header('requestURL: ' . current_url());
    }
    public function render($template = null, $layout = null, $die = true) {
        
        if (!is_null($template)) $this->setTemplate($template);
        if (!is_null($layout)) $this->setLayout($layout);
        $template_path = $this->getTemplatePath();
        $layout_path = $this->getLayoutPath();
        $content = tpl_fetch($template_path);
        $this->renderLayout($layout_path, $content);
        if ($die) die();
        return true;
    }
    public function renderText($text, $render_layout = false) {
        if ($this->getAutoRender()) {
            $this->setAutoRender(false);
        }
        if ($render_layout) {
            $layout_path = $this->getLayoutPath();
            $this->renderLayout($layout_path, $text);
        } else {
            print $text;
        }
    }
    private function renderLayout($layout_path, $content = null) {
        
        tpl_assign('content_for_layout', $content);
        return tpl_display($layout_path);
    }
    public function getTemplate() {
        return $this->template;
    }
    public function setTemplate($value) {
        $this->template = $value;
    }
    public function getLayout() {
        return $this->layout;
    }
    public function setLayout($value) {
        $this->layout = $value;
    }
    public function getAutoRender() {
        return $this->auto_render;
    }
    public function setAutoRender($value) {
        $this->auto_render = (boolean)$value;
    }
    public function getTemplatePath() {
        $template = trim($this->getTemplate()) == '' ? $this->template_prefix . strtolower($this->router->class) . '/' . $this->router->method : $this->getTemplate();
        $path = APPPATH . "views/$template.php";
        if (!is_file($path)) {
            throw new Exception("Template file '$path' does not exists");
        }
        return $template;
    }
    public function getLayoutPath() {
        $layout = trim($this->getLayout()) == '' ? strtolower($this->router->class) : $this->getLayout();
        $path = APPPATH . "views/$layout.php";
        if (!is_file($path)) {
            throw new Exception("Layout file '$path' does not exists");
        }
        return $layout;
    }
    public function _output($output) {

        if ($this->getAutoRender()) $this->render(null, null, false);
        echo $output;
    }
}
class Application_controller extends My_Controller {
    public function __construct() {
        parent::__construct();
        $main_product = new MainProduct();
        $main_product->initialize("Initial_Data");
    }
}
class Admin_controller extends My_Controller {
    public function __construct() {
        parent::__construct("admin/"); // prefix with trailing slash
        $main_product = new MainProduct();
        $main_product->initialize("Initial_Admin_Data");
    } 
}
