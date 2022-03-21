<?php


namespace Flix\Datasource;


class Datasource {

    private $table;
    private $where;
    private $columns;
    private $filtrar_excluidos = false;

    /**
     * Datasource constructor.
     * @param bool $filtrar_excluidos
     */
    public function __construct(bool $filtrar_excluidos) { $this->filtrar_excluidos = $filtrar_excluidos; }


    public function table($table) {
        $this->table = $table;
        $this->where = '';
        return $this;
    }

    public function select($cols) {
        $this->columns = $cols;
        return $this;
    }

    public function where(...$filters) {
        $ct = count($filters);
        if ($ct==2) {
            $v = $filters[1];
            if (is_bool($v)) {
                $v = $v?1:0;
            }
            if ($this->where == '') {
                $this->where .= " where {$filters[0]} = '{$v}' ";
            } else {
                $this->where .= " and {$filters[0]} = '{$v}' ";
            }
        } else if ($ct==3) {
            $v = $filters[2];
            if (is_bool($v)) {
                $v = $v?1:0;
            }
            if ($this->where == '') {
                $this->where .= " where {$filters[0]} {$filters[1]} '{$v}' ";
            } else {
                $this->where .= " and {$filters[0]} {$filters[1]} '{$v}' ";
            }
        }
        return $this;
    }

    public function first() {
        global $db;
        $this->prepare();
        $res = $db->getResultSet("select {$this->columns} from {$this->table} {$this->where} order by id asc limit 1");
        if ($res!=null) {
            return json_decode(json_encode($res));
        } else {
            return null;
        }
    }

    public function find($id) {
        global $db;
        $this->prepare();
        $res = $db->getResultSet("select {$this->columns} from {$this->table} where id = {$id}");
        if ($res!=null) {
            return json_decode(json_encode($res));
        } else {
            return null;
        }
    }

    private function prepare() {
        if ($this->columns=='') {
            $this->columns = '*';
        }

        if ($this->filtrar_excluidos) {
            if ($this->where == '') {
                $this->where .= " where excluido = 0 ";
            } else {
                $this->where .= " and excluido = 0 ";
            }
        }
    }

    public function chunk($n, $ini = 0) {
        global $db;

        $this->prepare();

        $lista = $db->listar("select {$this->columns} from {$this->table} {$this->where} order by id asc limit $n offset $ini ");
        if (!$db->isEmpty($lista)) {
            return json_decode(json_encode(pg_fetch_all($lista)));
        } else {
            return null;
        }
    }

    public function get() {
        global $db;

        $this->prepare();

        $lista = $db->listar("select {$this->columns} from {$this->table} {$this->where} order by id asc  ");
        if (!$db->isEmpty($lista)) {
            return json_decode(json_encode(pg_fetch_all($lista)));
        } else {
            return null;
        }
    }

}