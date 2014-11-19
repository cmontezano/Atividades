<?php
class ProjectList extends TPage
{
    private $form;     // registration form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // security check
        if (TSession::getValue('logged') !== TRUE)
        {
            throw new Exception('Não está logado');
        }
        
        // security check
        TTransaction::open('atividades');
        if (Usuario::newFromLogin(TSession::getValue('login'))->papel->mnemonico !== 'OPERADOR')
        {
            throw new Exception('Permissão negada');
        }
        TTransaction::close();
        
        // creates the form
        $this->form = new TForm('form_search_Project');
        $this->form->class = 'tform';
        // creates a table
        $table = new TTable;
        $table->width = '100%';
        
        $table->addRowSet(new TLabel('Projetos'), '')->class = 'tformtitle';
        
        // add the table inside the form
        $this->form->add($table);
        
        // create the form fields
        $titulo = new TEntry('titulo');
        $titulo->setValue(TSession::getValue('Project_titulo'));
        $titulo->setSize(320);
        
        // add a row for the title field
        $row = $table->addRow();
        $row->addCell(new TLabel('Título' . ': '));
        $cell = $row->addCell($titulo);
        
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
        $id                = new TDataGridColumn('id', 'ID', 'right', 40);
        $titulo            = new TDataGridColumn('titulo', 'Título', 'left', 140);
        $descricao         = new TDataGridColumn('descricao', 'Descricao', 'left', 160);
        $data_inicio       = new TDataGridColumn('data_inicio', 'Data de início', 'left', 50);
        $previsao_termino  = new TDataGridColumn('previsao_termino', 'Término previsto', 'left', 50);
        $solicitante       = new TDataGridColumn('solicitante', 'Solicitante', 'left', 80);
        
        // creates the datagrid actions
        $order1 = new TAction(array($this, 'onReload'));
        $order2 = new TAction(array($this, 'onReload'));

        // define the ordering parameters
        $order1->setParameter('order', 'id');
        $order2->setParameter('order', 'titulo');

        // assign the ordering actions
        $id    ->setAction($order1);
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
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        $action2->setLabel('Deletar');
        $action2->setImage('ico_delete.png');
        $action2->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
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
    
    /**
     * method onSearch()
     * Register the filter in the session when the user performs a search
     */
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        $filters = array();
        TSession::setValue('Project_titulo', '');
        TSession::setValue('Project_filters',   array());
        
        // check if the user has filled the form
        if ($data->titulo)
        {
            // creates a filter using what the user has typed
            $filter = new TFilter('titulo', 'like', "%{$data->titulo}%");
            
            // stores the filter in the session
            TSession::setValue('Project_titulo', $data->titulo);
            $filters[] = $filter;
        }
        
        //echo '<pre>'; print_r($filters); die;
        
        if ($filters)
        {
            TSession::setValue('Project_filters', $filters);
        }
        
        // fill the form with data again
        $this->form->setData($data);
        $param = array();
        $param['offset']     = 0;
        $param['first_page'] = 1;
        $this->onReload($param);
    }
    
    /**
     * method onReload()
     * Load the datagrid with the database objects
     */
    function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'library'
            TTransaction::open('atividades');
            
            // creates a repository for Book
            $repository = new TRepository('Project');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            //echo '<pre>'; print_r(TSession::getValue('Project_filters')); die;
            
            if (TSession::getValue('Project_filters'))
            {
                foreach (TSession::getValue('Project_filters') as $filter)
                {
                    if ($filter instanceof TFilter)
                    {
                        // add the filter stored in the session to the criteria
                        $criteria->add($filter);
                    }
                }
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    //echo '<pre>'; var_dump($object);
                    $this->datagrid->addItem($object);
                }
                //die;
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method onDelete()
     * executed whenever the user clicks at the delete button
     * Ask if the user really wants to delete the record
     */
    function onDelete($param)
    {
    /*
        // get the parameter $key
        $key=$param['key'];
        
        // define two actions
        $action = new TAction(array($this, 'Delete'));
        
        // define the action parameters
        $action->setParameter('key', $key);
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
        */
    }
    
    /**
     * method Delete()
     * Delete a record
     */
    function Delete($param)
    {
    /*
        try
        {
            // get the parameter $key
            $key=$param['key'];
            // open a transaction with database 'library'
            TTransaction::open('library');
            
            // instantiates object Book
            $object = new Book($key);
            
            // deletes the object from the database
            $object->delete();
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload();
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'));
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
        */
    }
    
    /**
     * method show()
     * Shows the page
     */
    function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded)
        {
            $this->onReload();
        }
        parent::show();
    }
}
?>
