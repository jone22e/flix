<?php


namespace Flix\FW\Mail;


class Mail {

    private $recipient;
    /**
     * @var \Flix\FW\Mail\From
     */
    private $fromObj;

    /**
     * @param \Flix\FW\Mail\From $from
     * @return $this
     */
    public function from($from) {
        $this->fromObj = $from;
        return $this;
    }

    public function to($email) {
        $this->recipient = $email;
        return $this;
    }

    private function send($template) {

    }

}