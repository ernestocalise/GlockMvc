<?php
namespace ernestocalise\glockmvc\Exception;

class NotFoundException extends \Exception {
     protected $message = 'La pagina non è stata trovata';
     protected $code = 404;
}
