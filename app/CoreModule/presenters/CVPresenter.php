<?php
namespace App\CoreModule\Presenters;

use App\CoreModule\Model\CVManager;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nextras\Forms\Bridges\NetteDI\FormsExtension;
use Nextras\Forms;  
use Nette\Database\UniqueConstraintViolationException;
use Nette\Forms\Rendering\DefaultFormRenderer;
use Nextras\Forms\Rendering\Bs3FormRenderer;
use Nette\Forms\Container;

/**
 * Zpracovává práci se cv.
 * @package App\CoreModule\Presenters
 */
class CVPresenter extends BaseCorePresenter
{
    /** @var     Nette\Database\IRow */
    private $cv;

    /** @var CVManager Instance třídy modelu pro cv. */
    protected $cvManager;

    /**
     * Konstruktor s injektovaným modelem pro cv.
     * @param CVManager $cvManager automaticky injektovaná třída modelu pro cv.
     */
    public function __construct(CVManager $cvManager)
    {
	parent::__construct();
	$this->cvManager = $cvManager;
    }
    /** Vykreslí stránku podle url..
	 * @param string $url URL stránky
	 * @throws BadRequestException pokut stránka neexistuje.
	 */
	public function actionDefault($url)
	{
		$cv = $this->cvManager->getCv($url);
		$this->template->cv = $cv; // Předá stránku do šablony.
                $cvinfo = $this->cvManager->getCvInfo($url);
		$this->template->cvinfo = $cvinfo; // Předá stránku do šablony.
        }
    /**
     * Vykresluje editaci cv podle URL.
     * @param string $url URL stránky kterou editujeme, pokut neexistuje vytvoříme novou.
     */
    public function actionEditor($url)
    {
	$this->template->form = $this['editorForm'];
    }
    /**
     * Vykresluje editaci cv podle URL.
     */
    public function actionCvInfo($url)
    {
	$this->template->form = $this['cvInfoForm'];
    }

    /**
     * Editor životopisu (info).
     * @return Form editor životopisu
     */
    protected function createComponentEditorForm()
    {  
        $form = $this->formFactory->create();
        $form->setRenderer(new Bs3FormRenderer);
        $presenter = $this;
        $invalidateCallback = function () use ($presenter) {
            $presenter->redrawControl('education');
            $presenter->redrawControl('pr');
            $presenter->redrawControl('abilities');
            $presenter->redrawControl('languages');
            $presenter->redrawControl('others');
            $presenter->redrawControl('hobbys');
        };
        $education = $form->addDynamic('education', function (Container $container)  use ($invalidateCallback) {         
            $container->addDatePicker('from', 'Nástup do školy')
                    //->setRequired()
                    ->setOption('timezone', '0')
                    ->setAttribute('class', 'form-control');
            $container->addText('where', 'Škola')
                    ->setAttribute('class', 'form-control date');
            $container->addHidden('type')
                    ->setValue('eddd')
                    ->setAttribute('class', 'form-control');
            $cvs_id = $this->getParameter('url');
            $container->addHidden('cvs_id')
                    ->setValue($cvs_id)
                    ->setAttribute('class', 'form-control');
            $container->addDatePicker('to', 'Ukončení docházky')
                    ->setAttribute('class', 'form-control');
                    //->setRequired();
            $container->addSubmit('remove', 'Zrušit')
                  ->setValidationScope(FALSE)
                  ->setAttribute('class', 'ajax btn btn-back-black')->addRemoveOnClick($invalidateCallback);
            $container->addSubmit('add', 'Přidat další školu')
              ->setValidationScope(FALSE)
              ->setAttribute('class', 'ajax btn btn-primary pull-right')->addCreateOnClick($invalidateCallback);          
        }, 1 );
      
        $pr = $form->addDynamic('pr', function (Container $container)  use ($invalidateCallback) {            
            $container->addDatePicker('from', 'Nástup do školy')
                    //->setRequired()
                    ->setOption('timezone', '0')
                    ->setAttribute('class', 'form-control');
            $container->addText('where', 'Škola')
                    ->setAttribute('class', 'form-control');
            $container->addHidden('type')
                    ->setValue('eddd')
                    ->setAttribute('class', 'form-control');
            $cvs_id = $this->getParameter('url');
            
            $container->addText('cvs_id')
                    ->setValue($cvs_id)
                    ->setAttribute('class', 'form-control');
            $container->addDatePicker('to', 'Ukončení docházky')
                    ->setAttribute('class', 'form-control');
                    //->setRequired();           
            $container->addSubmit('remove', 'Zrušit')
                  ->setValidationScope(FALSE)
                  ->setAttribute('class', 'ajax btn btn-back-black')->addRemoveOnClick($invalidateCallback);
            $container->addSubmit('add', 'Přidat další zaměstnání')
              ->setValidationScope(FALSE)
              ->setAttribute('class', 'ajax btn btn-primary pull-right')->addCreateOnClick($invalidateCallback);          
        }, 1 );
        
        $abilities = $form->addDynamic('abilities', function (Container $container)  use ($invalidateCallback) {            
            $container->addText('where', 'Dovednost')
                    ->setAttribute('class', 'form-control');
            $container->addHidden('type')
                    ->setValue('eddd')
                    ->setAttribute('class', 'form-control');
            $cvs_id = $this->getParameter('url');
            $container->addText('cvs_id')
                    ->setValue($cvs_id)
                    ->setAttribute('class', 'form-control');  
            $container->addSubmit('remove', 'Zrušit')
                  ->setValidationScope(FALSE)
                  ->setAttribute('class', 'ajax btn btn-back-black')->addRemoveOnClick($invalidateCallback);
            $container->addSubmit('add', 'Přidat další dovednost')
              ->setValidationScope(FALSE)
              ->setAttribute('class', 'ajax btn btn-primary pull-right')->addCreateOnClick($invalidateCallback);          
        }, 1 );
        
        $languages = $form->addDynamic('languages', function (Container $container)  use ($invalidateCallback) {            
            $container->addText('where', 'Language')
                    ->setAttribute('class', 'form-control');
            $container->addText('type')
                    ->setValue('eddd')
                    ->setAttribute('class', 'form-control');
            $cvs_id = $this->getParameter('url');
            $container->addText('cvs_id')
                    ->setValue($cvs_id)
                    ->setAttribute('class', 'form-control');  
            $container->addSubmit('remove', 'Zrušit')
                  ->setValidationScope(FALSE)
                  ->setAttribute('class', 'ajax btn btn-back-black')->addRemoveOnClick($invalidateCallback);
            $container->addSubmit('add', 'Přidat další jazyk')
              ->setValidationScope(FALSE)
              ->setAttribute('class', 'ajax btn btn-primary pull-right')->addCreateOnClick($invalidateCallback);          
        }, 1 );
        
        $others = $form->addDynamic('others', function (Container $container)  use ($invalidateCallback) {            
            $container->addText('where', 'dovednost')
                    ->setAttribute('class', 'form-control');
            $container->addText('type')
                    ->setValue('abilitie')
                    ->setAttribute('class', 'form-control');
            $cvs_id = $this->getParameter('url');
            $container->addText('cvs_id')
                    ->setValue($cvs_id)
                    ->setAttribute('class', 'form-control');  
            $container->addSubmit('remove', 'Zrušit')
                  ->setValidationScope(FALSE)
                  ->setAttribute('class', 'ajax btn btn-back-black')->addRemoveOnClick($invalidateCallback);
            $container->addSubmit('add', 'Přidat další dovednost')
              ->setValidationScope(FALSE)
              ->setAttribute('class', 'ajax btn btn-primary pull-right')->addCreateOnClick($invalidateCallback);          
        }, 1 );
        
        $hobbys = $form->addDynamic('hobbys', function (Container $container)  use ($invalidateCallback) {            
            $container->addText('where', 'Koníček')
                    ->setAttribute('class', 'form-control');
            $container->addText('type')
                    ->setValue('eddd')
                    ->setAttribute('class', 'form-control');
            $cvs_id = $this->getParameter('url');
            $container->addText('cvs_id')
                    ->setValue($cvs_id)
                    ->setAttribute('class', 'form-control');  
            $container->addSubmit('remove', 'Zrušit')
                  ->setValidationScope(FALSE)
                  ->setAttribute('class', 'ajax btn btn-back-black')->addRemoveOnClick($invalidateCallback);
            $container->addSubmit('add', 'Přidat další koníček')
              ->setValidationScope(FALSE)
              ->setAttribute('class', 'ajax btn btn-primary pull-right')->addCreateOnClick($invalidateCallback);          
        }, 1 );
        $url = $this->getParameter('url');
        $form->addFileUpload('Foto')
            ->setUIMode(\Zet\FileUpload\FileUploadControl::UI_MINIMAL)
            ->setParams(["owner" => $url]);
        $form->addSubmit('submit', 'Zpracovat');
        $form->onSuccess[] = [$this, 'cvInfoFormSucceeded'];
        return $form;
    }
    /**
     * Editor životopisu.
     * @return Form editor životopisu
     */  
    public function createComponentCvInfoForm() 
    {
        $form = $this->formFactory->create();
        $form->addGroup('Osobní informace');
        $form->setRenderer(new Bs3FormRenderer);
        $form->addHidden('cv_id'); 
        $form->addText('first_name', 'Jméno');
        $form->addText('surname', 'Příjmení');
        $form->addText('date_of_birth', 'Datum narození');
        $form->addText('mail', 'Email');
        $form->addText('tel', 'Telefon');
        $form->addText('web', 'Webová stránka');
        $form->addText('street', 'Ulice');
        $form->addText('city', 'Město');
        $form->addText('psc', 'PSČ');
        $form->setCurrentGroup(NULL);

        $form->addSubmit('submit', 'Uložit');
	$form->onSuccess[] = [$this, 'editorFormSucceeded'];	     
        return $form;
    }
	/**
	 * Funkce se vykonaná při úspěsném odeslání formuláře; zpracuje hodnoty formuláře.
         * @param Form $form        formulář editoru
         * @param ArrayHash $values odeslané hodnoty formuláře
	 */
	public function editorFormSucceeded($form, $values, $exclude = array())
	{
            try {
                $first_name = $values->first_name;
                $surname = $values->surname;
                $date_of_birth = $values->date_of_birth;
                $mail = $values->mail;
                $tel = $values->tel;
                $web = $values->web;
                $street = $values->street;
                $city = $values->city;
                $psc = $values->psc;                        
                $rand = rand(5, 15);
                $string = $mail . $rand;
                $url = md5($string);                
		$this->cvManager->saveCV($first_name, $surname, $date_of_birth, $mail, $tel, $web, $street, $city, $psc, $url);
		$this->flashMessage('Životopis byl úspěšně vytvořen.', self::MSG_SUCCESS);
                $this->redirect(':Core:CV:editor', $url);	
            } catch (UniqueConstraintViolationException $ex) {
		$this->flashMessage('Nastala nějaká chyba, zkuste to znovu.', self::MSG_ERROR);                 
            }
        }
        
        /**
	 * @param Nette\Forms\Controls\SubmitButton $button
	 */
	public function cvInfoFormSucceeded($form, $values)
	{
            $cvs_id = $this->getParameter('url');
            foreach ($form['education']->values as $educationId => $ed) {    
                $valuess = array(
                    "from" => $ed['from'],
                    "where" => $ed['where'],
                    "to" => $ed['to'],
                    "type" => "ed",
                    "cvs_id" => $cvs_id,
                );
                
                if(!empty($ed['where'])){
                    $this->cvManager->saveCvInfo($valuess);
                }
            }
            
             foreach ($form['pr']->values as $prId => $pr) {    
                $valuess = array(
                    "from" => $pr['from'],
                    "where" => $pr['where'],
                    "to" => $pr['to'],
                    "type" => "pr",
                    "cvs_id" => $cvs_id,
                );
                
                if(!empty($pr['where'])){
                    $this->cvManager->saveCvInfo($valuess);
                }
            }
            
             foreach ($form['languages']->values as $languageId => $lang) {
                $values = array(
                    "where" => $lang['where'],
                    "type" => "lang",
                    "cvs_id" => $cvs_id,
                    "from" => "a",
                    "to" => "b",
                );
                
                if(!empty($lang['where'])){
                    $this->cvManager->saveCvInfo($values);
                }
             }
             
            foreach ($form['abilities']->values as $abilityId => $ab) {
                $values = array(
                    "where" => $ab['where'],
                    "type" => "ability",
                    "cvs_id" => $cvs_id,
                    "from" => "a",
                    "to" => "b",
                );
               
                if(!empty($ab['where'])){
                    $this->cvManager->saveCvInfo($values);
                }
            }
            
            foreach ($form['others']->values as $otherId => $ot) {
                $values = array(
                    "where" => $ot['where'],
                    "type" => "other",
                    "cvs_id" => $cvs_id,
                    "from" => "a",
                    "to" => "b",
                );
               
                if(!empty($ot['where'])){
                    $this->cvManager->saveCvInfo($values);
                }
            }
            foreach ($form['hobbys']->values as $hobbyId => $ho) {
                $values = array(
                    "where" => $ho['where'],
                    "type" => "hobby",
                    "cvs_id" => $cvs_id,
                    "from" => "a",
                    "to" => "b",
                );           
                
                if(!empty($ho['where'])){
                    $this->cvManager->saveCvInfo($values);
                }
            }
            $pdf_url = $this->getParameter('url');
            $template = $this->cv=file_get_contents('http://localhost/curriculumvitae/cv/' .$pdf_url);
            $pdf = new \Joseki\Application\Responses\PdfResponse($template);
            $this->sendResponse($pdf);
            $this->redirect(':Core:CV:cveditor');
        }
         
}

        
        
        
        
        
        

        
        
        


