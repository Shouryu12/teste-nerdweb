<?php
/**
 * Nerdpress - CRM Nerdweb
 * PHP Version 7.2
 *
 * @package    Nerdweb
 * @author     Rafael Rotelok rotelok@nerdweb.com.br
 * @author     Junior Neves junior@nerdweb.com.br
 * @author     Adriano Buba adriano.buba@nerdweb.com.br
 * @author     Hiago Klapowsko hiago.kalpowsko@nerdweb.com.br
 * @copyright  2012-2020 Extreme Hosting Servicos de Internet LTDA
 * @license    https://nerdpress.com.br/license.txt
 * @version    Release: 2.5.0
 * @revision   2020-02-10
 */
namespace Nerdweb {

    /**
     * Class Template
     * @package Nerdweb
     */
    class Template {
        public function insert($file, $data = [], $customPath = NULL) {
            if (isset($customPath)) {
                $file = $customPath . $file;
            }
            else {
                $file = $_SERVER["DOCUMENT_ROOT"] . "/templates/" . $file;
            }

            $this->data = $data;

            if (!file_exists($file) && !file_exists($file . ".php")) {
                return ("Template " . $file . " not found");
            }
            elseif (file_exists($file . ".php")) {
                $file = $file . ".php";
            }

            require($file);
        }
    }
}
