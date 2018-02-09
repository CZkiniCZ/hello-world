<?php
namespace App\CoreModule\Model;

use App\Model\BaseManager;
use Nette\Database\Table\IRow;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;

/**
 * Meetody pro správu životopisů.
 * @package App\CoreModule\Model
 */
class CVManager extends BaseManager implements \Zet\FileUpload\Model\IUploadModel
{
	/** Konstanty pro manipulaci s modelem. */
    const
	TABLE_NAME = 'cv',
	COLUMN_ID = 'cv_id',
        COLUMN_FIRST_NAME = 'first_name',
        COLUMN_SURNAME = 'surname',
        COLUMN_DATE_OF_BIRTH = 'date_of_birth',
        COLUMN_STREET = 'street',
        COLUMN_CITY = 'city',
        COLUMN_MAIL = 'mail',
        COLUMN_TEL = 'tel',
        COLUMN_WEB = 'web',
        COLUMN_PSC = 'psc',
        COLUMN_URL = 'url',
        COLUMN_FOTO = 'foto',
        TABLE_CV_INFO_NAME = 'cvinfo',
        COLUMN_NAME_FROM = 'from',
        COLUMN_NAME_WHERE = 'where',
        COLUMN_CVS = 'cvs_id',
        COLUMN_NAME_TO = 'to';
        
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
     * Vrátí životopis podle url.
     * @param string $url URl stránky
     * @return bool|mixed|IRow první životopis, který odpovídá URL.
     */

    public function getCv($url)
    {
	return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_URL, $url)->fetch();
    }
        
    /**
     * Vrátí info k životopisu.
     * @param string $url URl životopisu
     */
        
    public function getCvInfo($url)
    {
	return $this->database->table(self::TABLE_CV_INFO_NAME)->where(self::COLUMN_CVS, $url)->fetchAll();
    }
    
    /**
     * Uložení nahraného souboru.
     * @param \Nette\Http\FileUpload $file
     * @param array $params Pole vlastních hodnot.
     * @return mixed Vlastní navrátová hodnota.
     */
    public function save(\Nette\Http\FileUpload $file, array $params = array()) 
    {    
        $paramss = implode(" ",$params);
        $file->move("../upload_files/".$paramss."/". $file->getSanitizedName());
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_URL, $paramss)->update(
            array(
                self::COLUMN_FOTO => $file->getSanitizedName(),
            )    
        );
    }
    /**
     * Uložení životopisu.
     */
    public function saveCV($first_name, $surname, $date_of_birth, $mail, $tel, $web, $street, $city, $psc, $url)
    {
            $this->database->table(self::TABLE_NAME)->insert(
                    array(
                        self::COLUMN_FIRST_NAME => $first_name,
                        self::COLUMN_SURNAME => $surname,
                        self::COLUMN_DATE_OF_BIRTH => $date_of_birth,
                        self::COLUMN_MAIL => $mail,
                        self::COLUMN_TEL => $tel,
                        self::COLUMN_WEB => $web,
                        self::COLUMN_STREET => $street,
                        self::COLUMN_CITY => $city,
                        self::COLUMN_PSC => $psc,
                        self::COLUMN_URL => $url,
                        self::COLUMN_FOTO => 'profil',
                    )
                );      
    }

    /**
     * Uloží nebo vytvoří cv (záleží na id)
     * @param array|ArrayHash $cv cv
     */
    public function saveCvInfo($cv)
    {
         $this->database->table(self::TABLE_CV_INFO_NAME)->insert($cv);
    }
 
}