<?php
class Email {
    
    private $to, $from, $subject, $body;
    private $status; //Holds warning messages

    public function __construct(){
        
    }
    
    
    /**
     * Receiver of email 
     * Can be email or name & email: "123@test.com", "Mr test <123@test.com>"
     * @param string $value
     * @return \Email
     */
    public function to($value){
        $this->to = $value;
        return $this;
    }
    
    /**
     * Sender of email
     * Can be email or name & email: "123@test.com", "Mr test <123@test.com>"
     * @param string $value
     * @return \Email
     */
    public function from($value){
        $this->from = $value;
        return $this;
    }
    
    /**
     * Sets the subject/ title of the email
     * @param string $value
     * @return \Email#
     */
    public function subject($value){
        $this->subject = $value;
        return $this;
    }
    
    /**
     * Sets the body text of the email
     * @param string $value
     * @return \Email
     */
    public function body($value){
        $this->body = $value;
        return $this;
    }
    
    /**
     * Loads a template for us as body text. 
     * $vars can be passed over from $_GET, $_POST, or any array
     * @param string $template
     * @param array $vars
     * @throws Exception
     */
    public function loadBodyTemplate($template, $vars){
        throw new Exception("Not implemented");
    }
    
    /**
     * Tells the mail class to send a message
     * @return boolean sent?
     */
    public function send(){
        
        $headers =  'From:' . $this->from . '\r\n'.
                    'Reply-to:'. $this->from . '\r\n'.
                    'X-Mailer: PHP/' . phpversion();
        
        try {
            mail($this->to, $this->subject, $this->body, $headers);
        } catch (Exception $ex) {
            $this->status = $ex;
            return false;
        }
        
        return true;        
    }
    
    
    /**
     * Returns the status of the previous mail attempt 
     * @return boolean status
     */
    public function status(){
        return $this->status;
    }
    
    
}
