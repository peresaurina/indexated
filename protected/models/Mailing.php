<?php

class Mailing {

// http://localhost/hourcontrol/website/supplier/mailing/auto_mail.php

    protected $from;
    protected $from_name;
    protected $subject;
    protected $to;
    protected $body;
    protected $file_name;
    protected $path;

    function __construct($id, $fields = null) {
        if ($id) {
            $this->__constructById($id);
        }
        if (is_array($fields) && !is_null($fields)) {
            $this->__constructByFields($fields);
        }
    }

    function __constructById($id) {
        if ($id != null) {
            $this->__constructByFields();
        }
    }

    function __constructByFields($fields) {
        $this->from = isset($fields["from"]) ? $fields["from"] : null;
        $this->from_name = isset($fields["from_name"]) ? $fields["from_name"] : null;
        $this->subject = isset($fields["subject"]) ? $fields["subject"] : null;
        $this->to = isset($fields["to"]) ? $fields["to"] : null;
        $this->body = isset($fields["body"]) ? $fields["body"] : null;
        $this->file_name = isset($fields["file_name"]) ? $fields["file_name"] : null;
        $this->path = isset($fields["path"]) ? $fields["path"] : null;
    }

    public function enviar() {

        $uri = 'https://mandrillapp.com/api/1.0/messages/send.json/';

        $params = array(
            "key" => "IYrMagHnJGmKQ2ZZO_Ai9w",
            "message" => array(
                "html" => $this->body,
                "text" => "",
                "to" => array(
                    //array("name" => "", "email" => "pere.saurina@gmail.com"),
                    array("name" => "", "email" => $this->to),                    
                ),
                "from_email" => $this->from,
                "from_name" => $this->from_name,
                "subject" => $this->subject,
                "track_opens" => true,
                "track_clicks" => true,
                "attachments" => array(
                    array(
                        'path' => $this->path,
                        'type' => "application/pdf",
                        'name' => $this->file_name,
                    )
                )
            ),
            "async" => false
        );

        $postString = json_encode($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $result = curl_exec($ch);
        curl_close($ch);

        //echo "<br>";
        //print_r($this);
        //echo "<br>";
        //print_r($result);

        $mandril_result = json_decode($result);
        $mandril_result_message = $mandril_result[0];

        // Retornem el resultat (OK o KO) en un JSON perquè la crida es fa en JS.
        if ($mandril_result_message->status == "sent") {
            return "ok";
            //$this->renderJson(array('success' => true, 'timestamp' => date("Y-m-d H:i:s"), 'message' => 'RecordCreated', 'users' => array('id' => $model->id)));
        } else {
            return "ERROR";
            //$this->renderJson(array('success' => false, 'timestamp' => date("Y-m-d H:i:s"), 'message' => 'RecordNotCreated', 'errorDetail' => $mandril_result_message->reject_reason, 'users' => null));
        }
    }

    function getFrom() {
        return $this->from;
    }

    function getFrom_name() {
        return $this->from_name;
    }

    function getSubject() {
        return $this->subject;
    }

    function getTo() {
        return $this->to;
    }

    function getBody() {
        return $this->body;
    }

    function setFrom($from) {
        $this->from = $from;
    }

    function setFrom_name($from_name) {
        $this->from_name = $from_name;
    }

    function setSubject($subject) {
        $this->subject = $subject;
    }

    function setTo($to) {
        $this->to = $to;
    }

    function setBody($body) {
        $this->body = $body;
    }
    function getFile_name() {
        return $this->file_name;
    }

    function getPath() {
        return $this->path;
    }

    function setFile_name($file_name) {
        $this->file_name = $file_name;
    }

    function setPath($path) {
        $this->path = $path;
    }


}

?>
