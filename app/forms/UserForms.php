<?php
namespace App\Forms;

use Nette\Application\UI\Form;
use Nette\Object;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use Nette\Utils\ArrayHash;
use App\Forms\BaseFormFactory;
use App\Presenters\BasePresenter;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

/**
 * Class UserFormsFactory
 * @package App\Forms
 */
class UserForms extends Object
{
	/** @var User Uživatel. */
	private $user;
        /** @var BaseFormFactory Továtnička na formuláře */
        private $formFactory;


	/**
	 * Konstruktor s injektovanou třidou uživatele.
	 * @param User $user automaticky injektovaná třída uživatele
	 */
	public function __construct(User $user, BaseFormFactory $baseFormFactory)
	{
		$this->user = $user;
                $this->formFactory = $baseFormFactory;
	}

	/**
	 * Přihlašuje a případně registruje uživatele.
	 * @param Form $form                   formulář, ze kterého se metoda volá
	 * @param null|ArrayHash $instructions uživatelské instrukce
	 * @param bool $register               registruj nového uživatele
	 */
	private function login($form, $instructions, $register = false)
	{
		$presenter = $form->getPresenter(); // Získej presenter ve kterém je formulář umístěn.
		try {
			// Extrakce hodnot z formuláře.
			$mail = $form->getValues()->mail;
			$password = $form->getValues()->password;
                        $code = md5($form->getValues()->mail);


			// Zkusíme zaregistrovat nového uživatele.
			if ($register)
                        {
                            $this->user->getAuthenticator()->register($mail, $password, $code);
                            $this->user->login($mail, $password); // Zkusíme se přihlásit.
                        }else{
                                                    $permanent = $form->getValues()->permanent;
			$this->user->login($mail, $password); // Zkusíme se přihlásit.
                        if(@$permanent == TRUE){
                            $presenter->user->setExpiration('1 year', FALSE);
                        }else{
                            $presenter->user->setExpiration('1 day', TRUE);
                        }
                        }
			// Pokud jsou zadány uživatelské instrukce a formulář je umístěn v presenteru.
			if ($instructions && $presenter) {
				// Pokud instrukce obsahují zprávu, tak ji pošli do příslušného presenteru.
				if (isset($instructions->message)) $presenter->flashMessage($instructions->message,
                                        isset($instructions->msg_type) ? $instructions->msg_type : BasePresenter::MSG_INFO
                                );
				// Pokud instrukce obsahují přesměrování, tak ho proveď na příslušném presenteru.
				if (isset($instructions->redirection)) $presenter->redirect($instructions->redirection);
			}
		} catch (AuthenticationException $ex) { // Registrace nebo přihlášení selhali.
			if ($presenter) { // Pokud je formulář v presenteru.
				$presenter->flashMessage($ex->getMessage(), BasePresenter::MSG_ERROR); // Pošli chybovou zprávu tam.
				$presenter->redirect('this');// Proveď přesměrování.
			} else { // Jinak přidej chybovou zprávu alespoň do samotného formuláře.
				$form->addError($ex->getMessage());
			}
		}
	}

	/**
	 * Vrací formulář se společným základem.
	 * @param null|Form $form formulář, který se má rozšířit o společné prky, nebo null, pokud se má vytvořit nový formulář
	 * @return Form formulář se společným základem
	 */
	private function createBasicForm(Form $form = null)
	{
		$form = $form ? $form : $this->formFactory->create();
		$form->addText('mail', 'Email')->setType('email')->setRequired();
		$form->addPassword('password', 'Heslo');
		return $form;
	}

	/**
	 * Vrací komponentu formuláře s přihlašovacími prvky a zpracování přihlašování podle uživatelských instrukcí.
	 * @param null|Form $form              komponenta formuláře, která se má rozšířit o přihlašovací prvky, nebo null, pokud se má vytvořit nový formulář
	 * @param null|ArrayHash $instructions uživatelské instrukce pro zpracování registrace
	 * @return Form komponenta formuláře s přihlašovacími prky
	 */
	public function createLoginForm($instructions = null, Form $form = null)
	{
		$form = $this->createBasicForm($form);
                $form->addCheckbox('permanent', 'Zůstat přihlášen')->setDefaultValue(TRUE);
		$form->addSubmit('submit', 'Přihlásit');
		$form->onSuccess[] = function (Form $form) use ($instructions) {
			$this->login($form, $instructions);
		};
		return $form;
	}

	/**
	 * Vrací komponentu formuláře s registračními prvky a zpracování registrace podle uživatelských instrukcí.
	 * @param null|Form $form              komponenta formuláře, která se má rozšířit o registrační prvky, nebo null, pokud se má vytvořit nový formulář
	 * @param null|ArrayHash $instructions uživatelské instrukce pro zpracování registrace
	 * @return Form komponenta formuláře s registračními prky
	 */
	public function createRegisterForm($instructions = null, Form $form = null)
	{
		$form = $this->createBasicForm($form);
 
                
		$form->addPassword('password_repeat', 'Heslo znovu')
                        ->setRequired()
			->addRule(Form::EQUAL, 'Hesla nesouhlasí.', $form['password']);
		$form->addText('y', 'Zadejte aktuální rok (antispam)')->setType('number')->setRequired()
			->addRule(Form::EQUAL, 'Špatně vyplněný antispam.', date("Y"));
		$form->addSubmit('register', 'Registrovat');
		$form->onSuccess[] = function (Form $form) use ($instructions) {
			$this->login($form, $instructions, true);
		};
		return $form;
	}
}
