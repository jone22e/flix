<?php

namespace Flix\Components;

class Select {

    private $properties;

    function create($properties) {
        $this->properties = $properties;
    }

    function render() {
        $properties = json_decode(json_encode($this->properties));

        $options = "";
        foreach ($this->properties->itens as $iten) {
            $selected = $iten->selected?"selected='selected'":"";
            $options .= "<option value='{$iten->id}' {$selected}>{$iten->name}</option>";
        }

        echo <<<HTML
            <select name="{$properties->name}" id="{$properties->id}" onchange="{$properties->change}" class="{$properties->class}">$options</select>
        HTML;
    }

}