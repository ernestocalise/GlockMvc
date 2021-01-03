<?php
namespace glockmvc\core\Exception;

class NotFoundException extends \Exception {
     protected $message = 'La pagina non è stata trovata';
     protected $code = 404;
}
