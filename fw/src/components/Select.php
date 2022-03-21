<?php

namespace Flix\FW\Components;

class Select {

    private $properties;

    function create($properties) {
        $this->properties = $properties;
        return $this;
    }
    
    function addItem($iten) {
        $this->properties['itens'][] = $iten;
        return $this;
    }

    function render() {
        $properties = json_decode(json_encode($this->properties));

        $options = "";
        foreach ($properties->itens as $iten) {
            $selected = $iten->selected?"selected='selected'":"";
            $options .= "<option value='{$iten->id}' {$selected}>{$iten->name}</option>";
        }

        echo <<<HTML
            <select name="{$properties->name}" id="{$properties->id}" onchange="{$properties->change}" class="{$properties->class}">$options</select>
        HTML;
    }

}