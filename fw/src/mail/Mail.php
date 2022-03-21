<?php


namespace Flix\FW\Mail;


class Mail {

    private $recipient;

    public function to($email) {
        $this->recipient  =$email;
        return $this;
    }

    private function send($template) {

    }

}