<?php


namespace App\Presenters;

use App\Forms\BaseFormFactory;
use Nette\Http\Request;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
/**
 * Základní presenter pro všechny ostatní presentery aplikace.
 * @package App\Presenters
 */
abstract class BasePresenter extends Presenter
{
        /** Info zpráva */
        const MSG_INFO = 'info';
        /** Správa o úspěchu */
        const MSG_SUCCESS = 'success';
        /** Správa o neúspěchu */
        const MSG_ERROR = 'danger';
	/** @var null|string Adresa presenteru pro logování uživatele. */
	protected $loginPresenter = null;
        /** @var request HTTP request na stránku */
        protected $httpRequest;
        /** @var BaseFormFactory továrnička na formuláře */
        protected $formFactory;

        /**
	 * Volá se na začátku každé akce a kontroluje uživatelská oprávnění k této akci.
	 * @throws BadRequestException Jestliže je uživatel přihlášen, ale nemá oprávnění k této akci.
	 */
	protected function startup()
	{
		parent::startup();   
		if (!$this->getUser()->isAllowed($this->getName(), $this->getAction())) {
			$this->flashMessage('Nejste přihlášený, nebo nemáte dostatečná oprávnění.', self::MSG_ERROR);
			if ($this->loginPresenter) $this->redirect($this->loginPresenter);
		}
	}

	/** Volá se před vykreslením každého presenteru a předává společné proměné do celkového layoutu webu. */
	protected function beforeRender()
	{
		parent::beforeRender();
                $this->template->member = $this->getUser()->isInRole('member');
		$this->template->admin = $this->getUser()->isInRole('admin');
                $this->httpRequest = $this->context->getByType('Nette\Http\Request'); // získáme aktuální http request
                $this->template->domain = $this->httpRequest->getURL()->getHost(); // Předá jméno domény do šablony
                $this->template->formPath = __DIR__ . '/../templates/components/form.latte'; //Předá cestu k šabloně formulářu do šablony
	}
        /**
         * Speciální setter pro injectování továrničky na formuláře se společným nastavením
         *  @param BaseFormFactory $baseFormFactory Automaticky injektovaná továrnička na formuláře
         */
        public function injectBaseFormFactory(BaseFormFactory $baseFormFactory)
        {
            $this->formFactory = $baseFormFactory;
        }
      
}
