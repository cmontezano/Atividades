<?php
class UserForm extends TPage
{
	private $form;
	private $datagrid;
	private $pageNavigation;
	private $loaded;

	public function __construct()
	{
		parent::__construct();

		// security check
		if (TSession::getValue('logged') !== true) {
			throw new Exception("Não está logado");
		}

		// security check
		TTransaction::open('atividades');
		if (Usuario::newFromLogin(TSession::getValue('login'))->papel->mnemonico !== 'OPERADOR') {
			throw new Exception("Permissão negada");
		}
		TTransaction::close();

		// creates the form
		$this->form = new TForm('form_User');

		try {
			// TUIBuilder object
			$ui = new TUIBuilder(500,500);
			$ui->setController($this);
			$ui->setForm($this->form);

			// reads the xml form
			$ui->parseFile('app/forms/user.form.xml');
			$ui->getWidget('code_label')->setValue('Código');
			$ui->getWidget('name_label')->setValue('Nome');
			$ui->getWidget('login_label')->setValue('Login');
			$ui->getWidget('senha_label')->setValue('Senha');
			$ui->getWidget('save_button')->setValue('Salvar');
			
			// get the interface widgets
			$fields = $ui->getWidgets();
			
			// look for the TDataGrid object
			foreach ($fields as $name => $field) {
			    if ($fields instanceof TDataGrid) {
			        $this->datagrid = $field;
			        $this->pageNavigation = $this->datagrid->getPageNavigation();
			    }
			}
			
			// add the TUIBuilder panel inside the TForm object
			$this->form->add($ui);
			// set form fields from interface fields
			$this->form->setFields($ui->getFields());
		} catch (Exception $e) {
            new TMessage('error', $e->getMessage());
		}
		
		// add the form to te page
		parent::add($this->form);
	}

	public function onEdit($param)
	{
		try {
			if (isset($param['key'])) {
				// get the parameter $key
				$key = $param['key'];

				// open a transaction with database 'atividades'
				TTransaction::open('atividades');

				// instantiates object Usuario
				$object = new Usuario($key);

				// fill the form with the active record data
				$this->form->setData($object);

				// close the transaction
				TTransaction::close();
			} else {
				$this->form->clear();
			}
		} catch (Exception $e) {
			// shows the exception error message
			new TMessage('error', '<b>Erro</b>' . $e->getMessage());

			// undo all pendind operations
			TTransaction::rollback();
		}
	}
	
	public function onSave()
	{
	    try {
	        // open a transaction with database 'atividades'
	        TTransaction::open('atividades');
	        
	        // get the form data into an active record Usuario
	        $object = $this->form->getData('Usuario');
	        
	        // form validation
	        $this->form->validate();
	        
	        // stores the object
	        $object->store();
	        
	        // set the data back to the form
	        $this->form->setData($object);
	        
	        // close the transaction
	        TTransaction::close();
	        
	        // show the success message
	        new TMessage('info', 'Registro salvo');
	        // reload the listing
	    } catch (Exception $e) {
	        // shows the exception error message
	        new TMessage('error', '<b>Erro</b> ' . $e->getMessage());
	        // undo all pending operations
	        TTransaction::rollback();
	    }
	}
}