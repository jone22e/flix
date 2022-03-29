<?php


namespace Flix\FW\Database\Dataset;


class Dataset {

    private $db;
    private $table;
    private $where;
    private $inner;
    private $columns;
    private $order;
    private $filtrar_excluidos = false;

    /**
     * Datasource constructor.
     * @param $db
     * @param bool $filtrar_excluidos
     */
    public function __construct($db, bool $filtrar_excluidos = false) {
        $this->db = $db;
        $this->filtrar_excluidos = $filtrar_excluidos;
    }


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
                if ($v!='0' && $v=='null') {
                    $this->where .= " where {$filters[0]} = null ";
                } else {
                    $this->where .= " where {$filters[0]} = '{$v}' ";
                }
            } else {
                if ($v!='0' && $v=='null') {
                    $this->where .= " and {$filters[0]} = null ";
                } else {
                    $this->where .= " and {$filters[0]} = '{$v}' ";
                }
            }
        } else if ($ct==3) {
            $v = $filters[2];
            if (is_bool($v)) {
                $v = $v?1:0;
            }
            if ($this->where == '') {
                if ($v!='0' && $v=='null') {
                    $this->where .= " where {$filters[0]} {$filters[1]} null ";
                } else {
                    $this->where .= " where {$filters[0]} {$filters[1]} '{$v}' ";
                }
            } else {
                if ($v!='0' && $v=='null') {
                    $this->where .= " and {$filters[0]} {$filters[1]} null ";
                } else {
                    $this->where .= " and {$filters[0]} {$filters[1]} '{$v}' ";
                }
            }
        }
        return $this;
    }

    public function join($table, $filter = []) {
        foreach ($filter as $filters) {
            $ct = count($filters);
            $on_where = "";
            if ($ct==2) {
                $v = $filters[1];
                if (is_bool($v)) {
                    $v = $v?1:0;
                }
                if ($on_where == '') {
                    $on_where .= " on {$filters[0]} = {$v} ";
                } else {
                    $on_where .= " and {$filters[0]} = {$v} ";
                }
            } else if ($ct==3) {
                $v = $filters[2];
                if (is_bool($v)) {
                    $v = $v?1:0;
                }
                if ($on_where == '') {
                    $on_where .= " on {$filters[0]} {$filters[1]} {$v} ";
                } else {
                    $on_where .= " and {$filters[0]} {$filters[1]} {$v} ";
                }
            }
        }
        $this->inner .= " inner join {$table} {$on_where} ";

        return $this;
    }

    public function leftJoin($table, $filter = []) {
        foreach ($filter as $filters) {
            $ct = count($filters);
            $on_where = "";
            if ($ct==2) {
                $v = $filters[1];
                if (is_bool($v)) {
                    $v = $v?1:0;
                }
                if ($on_where == '') {
                    $on_where .= " on {$filters[0]} = {$v} ";
                } else {
                    $on_where .= " and {$filters[0]} = {$v} ";
                }
            } else if ($ct==3) {
                $v = $filters[2];
                if (is_bool($v)) {
                    $v = $v?1:0;
                }
                if ($on_where == '') {
                    $on_where .= " on {$filters[0]} {$filters[1]} {$v} ";
                } else {
                    $on_where .= " and {$filters[0]} {$filters[1]} {$v} ";
                }
            }
        }
        $this->inner .= " left join {$table} {$on_where} ";

        return $this;
    }

    public function order($order) {
        if ($this->order == '') {
            $this->order .= " order by {$order}  ";
        } else {
            $this->order .= " ,{$order} ";
        }
        return $this;
    }

    public function first() {
        $db = $this->db;
        $this->prepare();
        $res = $db->getResultSet("select {$this->columns} from {$this->table} {$this->inner} {$this->where} order by id asc limit 1");
        if ($res!=null) {
            return json_decode(json_encode($res));
        } else {
            return null;
        }
    }

    public function find($id) {
        $db = $this->db;
        $this->prepare();
        $res = $db->getResultSet("select {$this->columns} from {$this->table} {$this->inner} where id = {$id}");
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
        $db = $this->db;

        $this->prepare();

        $lista = $db->listar("select {$this->columns} from {$this->table} {$this->inner} {$this->where} {$this->order} limit $n offset $ini ");
        if (!$db->isEmpty($lista)) {
            return json_decode(json_encode(pg_fetch_all($lista)));
        } else {
            return null;
        }
    }

    public function get() {
        $db = $this->db;

        $this->prepare();

        $lista = $db->listar("select {$this->columns} from {$this->table} {$this->inner} {$this->where} {$this->order} ");
        if (!$db->isEmpty($lista)) {
            return json_decode(json_encode(pg_fetch_all($lista)));
        } else {
            return null;
        }
    }

    public function sql($sql) {
        $db = $this->db;
        $lista = $db->listar($sql);
        if (!$db->isEmpty($lista)) {
            return json_decode(json_encode(pg_fetch_all($lista)));
        } else {
            return null;
        }
    }

    public function insert($array=[]) {
        $db = $this->db;

        $cols = [];
        $vals = [];
        foreach ($array as $col=>$val) {
            $cols[] = $col;
            if ($val!=null) {
                $vals[] = "'" . $val . "'";
            } else {
                $vals[] = "null";
            }
        }

        $cols = implode(',', $cols);
        $vals = implode(',', $vals);

        $db->sql_insert("insert into {$this->table} ($cols) values ($vals)");
        return true;
    }

    public function insertGetId($array=[]) {
        $db = $this->db;
        $this->insert($array);
        return $db->getUltimoId($this->table);
    }

    public function update($array=[], $forceWithoutWhere = false) {
        $db = $this->db;
        if ($this->where=='' && !$forceWithoutWhere) {
            return false;
        }

        $colsMount = [];
        foreach ($array as $col=>$val) {
            $colsMount[] = "$col = '{$val}'";
        }

        $colsMount = implode(',', $colsMount);

        $db->sql_insert("update {$this->table} set $colsMount {$this->where}");
        return true;
    }

    public function delete($justMark = true, $forceWithoutWhere = false) {
        //delete
        $db = $this->db;
        if ($this->where=='' && !$forceWithoutWhere) {
            return false;
        }

        if ($justMark) {
            $db->sql_insert("update {$this->table} set excluido = 0 {$this->where}");
        } else {
            $db->sql_insert("delete from {$this->table} {$this->where}");
        }
    }

}