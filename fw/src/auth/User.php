<?php


namespace Flix\FW\Auth;

use Flix\FW\Database\Dataset\Dataset;
use Flix\FW\Http\Request;

class User {

    private $db;
    private $params;

    /**
     * User constructor.
     * @param $params
     */
    public function __construct($db, $params) {
        $this->params = json_decode(json_encode($params));
        $this->db = $db;
    }


    public function store($params) {
        $params = json_decode(json_encode($params));
        return $this;
    }

    public function check() {
        $result = [];

        $value = null;

        if (isset($_COOKIE[$this->params->storage->name])) {
            $value = $_COOKIE[$this->params->storage->name];
        }

        if (in_array('encryped', $this->params->storage->settings) && $this->params->encryptionkey!=false) {
            $value = $this->encrypt_decrypt('d', $value);
        }

        if ($value==null) {
            Request::redirect($this->params->redirect->uri);
            exit();
        }

        $usr = (new Dataset($this->db))->table($this->params->storage->table)
                              ->select(implode(',', $this->params->storage->columns))
                              ->find($value);

        if ($usr==null) {
            Request::redirect($this->params->redirect->uri);
            exit();
        }

        //anexar permissões do usuário

        $usrd = json_decode(json_encode($usr), true);
        $permsResult = [];
        foreach ($this->params->permissions->columns as $column) {
            if (isset($usrd[$column])) {
                if ($usrd[$column] != null) {
                    $permissoes = json_decode($usrd[$column]);
                    $perms = [];
                    foreach ($permissoes as $permissoe) {
                        $perms[$permissoe->name] = array_merge(json_decode(json_encode($permissoe->permissoes), true), json_decode(json_encode($permissoe->options), true));
                    }
                    $permsResult = array_merge($permsResult, $perms);
                }
            }
        }

        if (isset($usr->preferencias)) {
            $usr->preferencias = json_decode($usr->preferencias);
        }

        $usr->permissions = json_decode(json_encode($permsResult));

        return $usr;
    }

    private function encrypt_decrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = $this->params->encryptionkey;
        $secret_iv = $this->params->encryptionkey;
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'e' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'd' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

}