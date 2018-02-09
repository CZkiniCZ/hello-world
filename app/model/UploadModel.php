<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Model;

use Nette;
use App\Model\BaseManager;
use Zet\FileUpload\Model;
use Nette\Utils\ArrayHash;

class UploadModel extends BaseManager implements \Zet\FileUpload\Model\IUploadModel {
    /**
     * Zpracování požadavku o smazání souboru.
     * @param $uploaded Hodnota navrácená funkcí save.
     */
    public function remove($uploaded) {


    }

    /**
     * Zpracování přejmenování souboru.
     * @param $upload Hodnota navrácená funkcí save.
     * @param $newName Nové jméno souboru.
     * @return mixed Vlastní návratová hodnota.
     */
    public function rename($upload, $newName) {

    }

    /**
     * Uložení nahraného souboru.
     * @param \Nette\Http\FileUpload $file
     * @param array $params Pole vlastních hodnot.
     * @return mixed Vlastní navrátová hodnota.
     */
    public function save(Nette\Http\FileUpload $file, array $params = array()) {
        $file->move("../upload_files/". $file->getSanitizedName());
      
    }
}

