<?php
class LoginForm extends TPage
{
	protected $form; //formulario

	/**
	 * método construtor
	 * Cria a página e o formulário de cadastro
	 */
	function __construct()
    {
        parent::__construct();
        
        // instancia um formulário
        $this->form = new TForm('form_login');
        
        // cria um notebook
        $notebook = new TNotebook;
        $notebook->setSize(340, 110);
        
        // instancia uma tabela
        $table = new TTable;
        
        // adiciona a tabela ao formulário
        $this->form->add($table);
        
        // cria os campos do formulário
        $user = new TEntry('user');
        $pass = new TPassword('password');

        // adiciona uma linha para o campo
        $row=$table->addRow();
        $row->addCell(new TLabel(('Login').':'));
        $row->addCell($user);
        
        // adiciona uma linha para o campo
        $row=$table->addRow();
        $row->addCell(new TLabel(('Senha').':'));
        $row->addCell($pass);
        
        // cria um botão de ação (salvar)
        $save_button=new TButton('login');
        // define a ação do botão
        $save_button->setAction(new TAction(array($this, 'onLogin')), ('Login'));
        $save_button->setImage('ico_apply.png');
        
        // adiciona uma linha para a ação do formulário
        $row=$table->addRow();
        $row->addCell($save_button);
        
        // define quais são os campos do formulário
        $this->form->setFields(array($user, $pass, $save_button));
        
        $notebook->appendPage(('Data'), $this->form);
        // adiciona o notebook à página
        parent::add($notebook);
    }

	/**
     * Validate the login
     */
    function onLogin()
    {
        try
        {
            TTransaction::open('atividades');
            $data = $this->form->getData('StdClass');
            
            // validate form data
            $this->form->validate();
            
            $auth = Usuario::autenticate($data->{'user'}, $data->{'password'} );
            if ($auth)
            {
                TSession::setValue('logged', TRUE);
                TSession::setValue('login', $data->{'user'});
                
                // reload page
                TApplication::gotoPage('SetupPage', 'onSetup');
            }
            TTransaction::close();
            // finaliza a transação
        }
        catch (Exception $e) // em caso de exceção
        {
            TSession::setValue('logged', FALSE);
            
            // exibe a mensagem gerada pela exceção
            new TMessage('error', '<b>Erro</b> ' . $e->getMessage());
            // desfaz todas alterações no banco de dados
            TTransaction::rollback();
        }
    }
    
    /**
     * método onLogout
     * Executado quando o usuário clicar no botão logout
     */
    function onLogout()
    {
        TSession::setValue('logged', FALSE);
        TApplication::gotoPage('LoginForm', '');
    }
}