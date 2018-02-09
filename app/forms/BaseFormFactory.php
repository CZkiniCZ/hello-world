<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Forms;

use App\Presenters\BasePresenter;
use Nette\Application\UI\Form;
use Nette\Object;

/**
 * Továrnička na formuláře se společným nastavením.
 * @package App\Forms
 */
class BaseFormFactory extends Object
{
    /**
     * Vytváří a vrací formulář se společným nastavením.
     * @return Form formulář se společným nastavením
    */
    public function create()
    {
        $form = new Form;
        
        $form->onError[] = [$this, 'formError'];
        return $form;
    }
    
   

    /**
     * Pževádí výpis chyb validace formuláře na zprávy.
     * @param Form $form Formulář ze kterého chyby pochází
     */
    public function formError($form)
    {
        $errors = $form->getErrors();
        $presenter = $form->getPresenter();
        if($presenter) foreach($errors as $error) $presenter->flashMessage ($error, BasePresenter::MSG_ERROR);
    }
}
