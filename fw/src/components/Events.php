<?php


namespace Flix\FW\Components;


class Events {

    public function click($object, $callback) {
        return <<<JS
            $('{$object}').unbind('click');
            $('{$object}').click(function () {
                {$callback}
            });
        JS;
    }

    public function modal($params) {
        return <<<JS
            $('#modal').modal('show');
            $.ajax({
                method: "POST",
                url: "{$params['url']}",
                data: {},
                beforeSend: function(){
                }
            }).done(function( res ) {
                $('#modaldiv').html(res);
                $(".modal-dialog").draggable({
                    handle: ".modal-header"
                });
                {$params['callback']}
            });
        JS;
    }

    public function post($params) {
        $data = [];
        foreach ($params['data'] as $key => $value) {
            $data[] = "$key : $value";
        }
        $data = implode(',', $data);
        return <<<JS
            $.ajax({
                method: "POST",
                url: "{$params['url']}",       
                dataType: "json",         
                data: {{$data}},   
                beforeSend: function(){        
                }
            }).done(function( res ) {
               {$params['callback']}  
            });
        JS;
    }

}