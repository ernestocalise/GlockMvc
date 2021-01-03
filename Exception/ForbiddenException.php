<?php
namespace glockmvc\core\\Exception;
class ForbiddenException extends \Exception{
     protected $message = 'Non hai i permessi per visualizzare questa pagina';
     protected $code = 403;
}
?>
