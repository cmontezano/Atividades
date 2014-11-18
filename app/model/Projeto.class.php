<?php
class Project extends TRecord
{
    const TABLENAME = 'projeto';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max';
    
    private $titulo;
    private $descricao;
    private $data_inicio;
    private $previsao_termino;
    private $solicitante;
    private $atividades;
}