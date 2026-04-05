<?php

namespace photopro\core\application\ports\spi\repositoryInterfaces;

interface MailSenderInterface {
    public function send(string $to, string $subject, string $htmlBody) : void;
}