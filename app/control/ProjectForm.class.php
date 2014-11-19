<?php
class ProjectForm extends TPage
{
	private $form;

	public function __construct()
	{
		parent::__construct();
		
		// security check
        if (TSession::getValue('logged') !== TRUE)
        {
            throw new Exception('Não logado');
        }
        
        // security check
        TTransaction::open('atividades');
        if (Usuario::newFromLogin(TSession::getValue('login'))->papel->mnemonico !== 'OPERADOR')
        {
            throw new Exception('Permissão negada');
        }
        TTransaction::close();
        
        // creates the form
        $this->form = new TForm('form_Project');
        
        $table1 = new TTable();
        $table2 = new TTable();
        
        $notebook = new TNotebook(600, 430);
        $notebook->appendPage('Dados básicos', $table1);
        $notebook->appendPage('Atividades', $table2);
        
        $this->form->add($notebook);
        
        $id               = new TEntry('id');
        $titulo           = new TEntry('titulo');
        $descricao        = new TText('descricao');
        $data_inicio      = new TDate('data_inicio');
        $previsao_termino = new TDate('previsao_termino');
        $solicitante      = new TEntry('solicitante');
        
        // define the sizes
        $id->setSize(100);
        $titulo->setSize(340);
        $descricao->setSize(400, 40);
        $data_inicio->setSize(100);
        $previsao_termino->setSize(100);
        $solicitante->setSize(200);
        
        $id->setEditable(false);
        
        // add a row for the field ID
        $row = $table1->addRow();
        $row->addCell(new TLabel('ID'));
        $cell = $row->addCell($id);
        $cell->colspan = 3;
        
        // add a row for the field title
        $row = $table1->addRow();
        $row->addCell(new TLabel('Título'));
        $cell = $row->addCell($titulo);
        $cell->colspan = 3;
        
        // add a row for the field descrição
        $row = $table1->addRow();
        $row->addCell(new TLabel('Descrição' . ': '));
        $cell = $row->addCell($descricao);
        $cell->colspan = 3;
        
        // add a row for the field data_inicio
        $row=$table1->addRow();
        $row->addCell(new TLabel('Data de início' . ': '));
        $row->addCell($data_inicio);
        
        // add a row for the field previsao_termino
        $row=$table1->addRow();
        $row->addCell(new TLabel('Término previsto' . ': '));
        $row->addCell($previsao_termino);
        
        // add a row for the field solicitante
        $row = $table1->addRow();
        $row->addCell(new TLabel('Solicitante'));
        $cell = $row->addCell($solicitante);
        $cell->colspan = 3;
        
        
        // tensão total -> EIuhEAUHae atividades
        $atividades                 = new TMultiField('atividades_list');
        $atividade_id               = new TEntry('id');
        $titulo_atividade           = new TEntry('titulo_atividade');
        $descricao_atividade        = new TText('descricao_atividade');
        $usuario_id                 = new TSeekButton('usuario_id');
        $usuario_name               = new TEntry('usuario_name');
        $previsao_termino_atividade = new TDate('previsao_termino_atividade');
        
        $atividade_id->setEditable(false);
        $usuario_name->setEditable(false);
        $usuario_id->setSize(50);
        $usuario_name->setSize(300);
        $obj = new TStandardSeek;
        $action = new TAction(array($obj, 'onSetup'));
        $action->setParameter('database',      'atividades');
        $action->setParameter('parent',        'form_Project');
        $action->setParameter('model',         'Usuario');
        $action->setParameter('display_field', 'name');
        $action->setParameter('receive_key',   'usuario_id');
        $action->setParameter('receive_field', 'usuario_name');
        $usuario_id->setAction($action);
        
        $atividades->setHeight(200);
        $atividades->setClass('Atividade');
        
        $atividades->addField('id', 'Atividade', $atividade_id, 50);
        $atividades->addField('usuario_id', 'Usuário', $usuario_id, 50);
        $atividades->addField('usuario_name', 'Nome', $usuario_name, 165);
        $atividades->addField('titulo_atividade', 'Título', $titulo_atividade, 165);
        $atividades->addField('previsao_termino', 'Término previsto', $previsao_termino_atividade, 150);
        
        $row = $table2->addRow();
        $row->addCell($l = new TLabel('Atividades'));
        $l->setFontStyle('b');
        
        $row = $table2->addRow();
        $row->addCell($atividades);
        
        // create an action button (save)
        $save_button=new TButton('save');
        // define the button action
        $save_button->setAction(new TAction(array($this, 'onSave')), _t('Save'));
        $save_button->setImage('ico_save.png');
        
        // add a row for the form action
        $row=$table1->addRow();
        $row->addCell($save_button);

        // define wich are the form fields
        $this->form->setFields(array($id, $titulo, $descricao, $data_inicio, $previsao_termino, $solicitante, $atividades,
                                     $titulo_atividade, $previsao_termino_atividade, $usuario_id, 
                                     $usuario_name, $save_button));

        // add the form to the page
        parent::add($this->form);
	}

	public function onEdit()
	{
		
	}
	
	public function onSave()
	{
	    
	}
}