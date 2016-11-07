<?php
//Funcions generals per a qualsevol projecte

function extract_email_address ($string) {
    foreach(preg_split('/\s/', $string) as $token) {
        $email = filter_var(filter_var($token, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
        if ($email !== false) {
            $emails[] = strtolower($email);
        }
    }
    return $emails;
}

?>