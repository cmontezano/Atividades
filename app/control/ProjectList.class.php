<?php
class ProjectList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    
    public function __construct()
    {
        parent::__construct();

        if (TSession::getValue('logged') !== true) {
            throw new Exception('Não está logado');
        }

        TTransaction::open('atividades');
        if (Usuario::newFromLogin(TSession::getValue('login'))->papel->mnemonico !== 'OPERADOR') {
            throw new Exception('Permissão negada');
        }
        TTransaction::close();
                
        $this->form = new TForm('form_search_Project');
        $this->form->clas = 'tform';
        
        $table = new TTable();
        $table->width = '100%';

        $table->addRowSet(new TLabel('Projetos'), '')->class = 'tformtitle';
        
        $this->form->add($table);
        
        $titulo = new TEntry('titulo');
        
        $row = $table->addRow();
        $row->addCell(new TLabel('Título'));
        
        // create two action buttons to the form
        $find_button = new TButton('find');
        $new_button  = new TButton('new');
        // define the button actions
        $find_button->setAction(new TAction(array($this, 'onSearch')), 'Buscar');
        $find_button->setImage('ico_find.png');
        
        $new_button->setAction(new TAction(array('ProjectForm', 'onEdit')), 'Novo');
        $new_button->setImage('ico_new.png');
        
        $table->addRowSet('', array($find_button, $new_button))->class = 'tformaction';
        
        // define wich are the form fields
        $this->form->setFields(array($titulo, $find_button, $new_button));
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setHeight(280);
        
        // creates the datagrid columns
        $id                = new TDataGridColumn('id', 'ID', 'right', 50);
        $titulo            = new TDataGridColumn('titulo', 'Título', 'left', 200);
        $descricao         = new TDataGridColumn('descricao', 'Descrição', 'left', 160);
        $data_inicio       = new TDataGridColumn('data_inicio', 'Data de início', 'left', 50);
        $previsao_termino  = new TDataGridColumn('previsao_termino', 'Término previsto', 'left', 80);
        $solicitante       = new TDataGridColumn('solicitante', 'Solicitante', 'left', 80);

        // creates the datagrid actions
        $order1= new TAction(array($this, 'onReload'));
        $order2= new TAction(array($this, 'onReload'));

        // define the ordering parameters
        $order1->setParameter('order', 'id');
        $order2->setParameter('order', 'titulo');

        // assign the ordering actions
        $id->setAction($order1);
        $titulo->setAction($order2);
        
        // add the columns to the DataGrid
        $this->datagrid->addColumn($id);
        $this->datagrid->addColumn($titulo);
        $this->datagrid->addColumn($descricao);
        $this->datagrid->addColumn($data_inicio);
        $this->datagrid->addColumn($previsao_termino);
        $this->datagrid->addColumn($solicitante);
        
        // creates two datagrid actions
        $action1 = new TDataGridAction(array('ProjectForm', 'onEdit'));
        $action1->setLabel('Editar');
        $action1->setImage('ico_edit.png');
        $action1->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // creates the page structure using a vbox
        $container = new TVBox;
        $container->add($this->form);
        $container->add($this->datagrid);
        $container->add($this->pageNavigation);
        // add the vbox inside the page
        parent::add($container);
    }

    public function onSearch()
    {
        //
    }

    public function onReload()
    {
        //
    }
}