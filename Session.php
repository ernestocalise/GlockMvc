<?php
namespace ernestocalise\glockmvc;
class Session {
     protected const FLASH_KEY = 'flash_messagges';
     protected const CSRF_KEY = 'csrf_token';
     public function __construct(){
          session_start();
          $flashMessagges = $_SESSION[self::FLASH_KEY] ?? [];
          $csrfTokens = $_SESSION[self::CSRF_KEY] ?? [];
          foreach($flashMessagges as $key => &$flashMessagge){
               //Mark to be removed
               $flashMessagge['remove'] = true;
          }
          foreach ($csrfTokens as $key => &$csrfToken) {
               $csrfToken['requestOld'] = $csrfToken['requestOld']+1;
          }
          $_SESSION[self::CSRF_KEY] = $csrfTokens;
          $_SESSION[self::FLASH_KEY] = $flashMessagges;
     }
     public function setFlash($key, $message) {
          $_SESSION[self::FLASH_KEY][$key] = [
               'remove' => false,
               'value' => $message
          ];
     }
     public function getFlash($key) {
          return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
     }
     public function setCSRFToken($action) {
          $token = bin2hex(random_bytes(32));
          $_SESSION[self::CSRF_KEY][$token] = [
               'action' => $action,
               'requestOld' => 0
          ];
          return $token;
     }
     public function checkCSRFToken($tokenName, $action){
          if ($_SESSION[self::CSRF_KEY][$tokenName]){
               if($_SESSION[self::CSRF_KEY][$tokenName]['action'] === $action){
                    unset($_SESSION[self::CSRF_KEY][$tokenName]);
                    return true;
               }
          }
          return false;
     }
     public function __destruct() {
          // Iterate over marked to be removed flash messages
          $flashMessagges = $_SESSION[self::FLASH_KEY] ?? [];
          foreach($flashMessagges as $key => &$flashMessagge){
               if($flashMessagge['remove']){
                    unset($flashMessagges[$key]);
               }
          }
          $_SESSION[self::FLASH_KEY] = $flashMessagges;
          // clear older request csrf tokens
          $csrfTokens = $_SESSION[self::CSRF_KEY] ?? [];
          foreach($csrfTokens as $key => &$csrfToken) {
               if($csrfToken['requestOld'] >=15){
                    unset($csrfTokens[$key]);
               }
          }
          $_SESSION[self::CSRF_KEY] = $csrfTokens;
     }
     public function set ($key, $value){
          $_SESSION[$key] = $value;
     }
     public function get ($key) {
          return $_SESSION[$key] ?? false;
     }
     public function remove ($key) {
          unset($_SESSION[$key]);
     }
}
?>
