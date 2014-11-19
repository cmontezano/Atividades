<?php
class ProjectForm extends TPage
{
	private $form;

	public function __construct()
	{
		parent::construct();
		
		// security check
        if (TSession::getValue('logged') !== TRUE)
        {
            throw new Exception('Não logado');
        }
        
        // security check
        TTransaction::open('atividades');
        if (User::newFromLogin(TSession::getValue('login'))->papel->mnemonico !== 'OPERADOR')
        {
            throw new Exception('Permissão negada');
        }
        TTransaction::close();
        
        
	}

	public function onEdit()
	{
		
	}
}