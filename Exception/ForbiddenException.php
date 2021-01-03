<?php
namespace ernestocalise\glockmvc\Exception;
class ForbiddenException extends \Exception{
     protected $message = 'Non hai i permessi per visualizzare questa pagina';
     protected $code = 403;
}
?>
