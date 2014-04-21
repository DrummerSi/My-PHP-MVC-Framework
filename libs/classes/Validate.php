<?php

//Todo: remote
//Todo: Token (xscf)
//maybe: integer

class Validate {
    
    //Hold an array of the rules, custom names, etc
    private $rules = array(),
            $customNames = array(),
            $customErrors = array(),
            $defaultValues = array();
    
    
    private $useSessionFlash = true, //Use session flashes for error messages
            $useValidationToken = true, //Use session validation token to stop XSS attacks
            $clientSide = true, //Are we doing client-side validtion?
            $customClientSideErrors = false; //Show custom errors for client-side validation?
    
    //What are the valid rules for client-side outputting
    private $clientside_valid_rules = array("required", "email", "minlength", "maxlength", "rangelength", "min", "max", "range", "equalto", "url", "date", "remote");
    
    //Hold a list of any erros
    private $errors = array();
    
    //Has the validation passed?
    private $hasPassed = true;
        
    /**
     * Validate->rules
     * Adds rules to the validation class 
     * @param array $items
     */
    public function rules($items = array()){
        //Merge the entered rules with the existing rules
        $this->rules = array_merge($this->rules, $items);
    }
    
    /**
     * Validate->customNames
     * Adds custom field names to the validation class
     * @param array $items
     */
    public function customNames($items = array()){
        //Merge the new custom names with any existing custom names
        $this->customNames = array_merge($this->customNames, $items);
    }
    
    /**
     * Validate->customErrors
     * Adds custom error messages to the validation class
     * @param array $items
     */
    public function customErrors($items = array()){
        //Merge the new custom error messages with any existing ones
        $this->customErrors = array_merge($this->customErrors, $items);
    }
    
    /**
     * Validate->defaultValues
     * Adds default values for each field in the validator
     * @param array $items
     */
    public function defaultValues($items = array()){
        //Merge the default vales entered with any existing ones
        $this->defaultValues = array_merge($this->defaultValues, $items);
    }
    
    /**
     * Sets if the validation class should output errors directly into the session flashes
     * @param boolean $use
     */
    public function useSessionFlash($use){
        $this->useSessionFlash = $use;
    }
    
    public function useValidationToken($use){
        $this->useValidationToken = $use;
    }
    
    /**
     * Sets if the validation class should do client-side validation
     * @param boolean $flag
     */
    public function clientSide($flag = true){
        $this->clientSide = $flag;
    }
    
    /**
     * By default custom error messages are NOT shown in client-side validation
     * You can turn this on my setting customClientSideErrors to true
     * @param boolean $flag
     */
    public function customClientSideErrors($flag = false){
        $this->customClientSideErrors = $flag;
    }
    
    
    /**
     * Validate->renderRules
     * Writes the neccasary client-side validation rules to page based on inputted rules for field
     * @param string $field
     */
    public function renderRules($field = null){
        
        //Exit if clientSide rules are turned off, or no field name was passed in
        if($field === null || $this->clientSide === false)
            return;
        
        //String to store thr output
        $client_output = '';
        
        if(array_key_exists($field, $this->rules)){            
            foreach($this->rules[$field] as $rule => $rule_value){
                
                //Only output a rule, if the rule is allowed in the valid_rules propertly.
                if(in_array($rule, $this->clientside_valid_rules)){
                    
                    //Add a leading # to the equalto rule
                    if($rule=='equalto'){ $rule_value = '#'.$rule_value; } 
                    
                    //Add square brackets to 'range' and 'rangelength'
                    if($rule=='range' || $rule=='rangelength'){ $rule_value = '['.$rule_value."]"; } 
                    
                    $client_output .= ' data-rule-' . $rule . '="' . $rule_value . '" ';
                }
            }
            
            //Custom client-side errors are enabled.. Lets check if a custom error exists..
            if($this->customClientSideErrors == true && isset($rule)){  
                $message = ($this->customErrorExists($field,$rule)) ?: '';
                if($message!=''){
                    $client_output .= ' data-msg-' . $rule . '="' . $message . '"';
                }
            }
            
        }
        
        return $client_output;        
    }
    
    /**
     * Creates a session token and generates the html for forms
     * @return string 
     */
    function renderToken(){
        if($this->useValidationToken == true){
            //Generate token and place it in a session
            $token = md5(uniqid());
            Session::put("form_token", $token);
            
            //Render the token code out to use on forms
            return '<input type="hidden" name="form_token" value="'.$token.'" />';
        }
    }
    
    /**
     * If form has been posted, returns posted values, else returns a default value (if it exists)
     * @param string $field
     * @return string
     */
    public function value($field){
        
        if(isPostBack()){
            return Input::get($field);
        } else {            
            if(array_key_exists($field, $this->defaultValues) ){
                return $this->defaultValues[$field];
            }
        }
    }
    
    /**
     * Performs both renderRules & value functions.
     * Results of value function are wrapped in a 'value' html attribute
     * @param string $field
     * @return string
     */
    public function renderRulesAndValue($field){
        return ' ' . $this->renderRules($field) . ' value="' . encode($this->value($field)) . '" ';
    }
    
    /**
     * Returns 'selected' html attribute if value of $field and $value match
     * for use on 'select' html tabs
     * @param string $field
     * @param string $value
     * @return string
     */
    public function selectValue($field, $value){
        if($this->value($field) == $value){
            return "selected";
        }        
        return "";
    }
    
    /**
     * Returns 'checked' html attribute if value of $field and $value match
     * for use on 'checkbox' html tabs
     * @param string $field
     * @param string $value
     * @return string
     */
    public function checkValue($field, $value){
        if($this->value($field) == $value){
            return "checked";
        }        
        return "";
    }
    
    /**
     * Validate->passed
     * Returns TRUE or FALSE depending on if validation passed or failed respectively
     */
    public function passed(){
        
        //Loop through all the fields in the ruleset
        foreach($this->rules as $fieldName => $ruleCollection){
            foreach($ruleCollection as $rule => $rule_value){
                $this->validateField($fieldName, $rule, $rule_value);                
            }
        }
        
        if($this->useValidationToken){
            if( Input::get("form_token") != Session::get("form_token") ){
                $this->addError("form_token", "token", "Could not process form form at this time. Please try again.");
            }
        }
        
        //If we're using session flash, add a formatted list of errors to the session flash system
        if($this->useSessionFlash){
            Session::addFlash($this->formattedErrors("list"), "danger", "Please correct the following errors");
        }
        
        return $this->hasPassed;
    }
    
    /**
     * Internal function used to validate a fieldName with a specific rule and value
     * @param string $fieldName
     * @param string $rule
     * @param string $rule_value
     */
    private function validateField($fieldName, $rule, $rule_value){
        
        switch(strtolower($rule)){

            case "required":
                if(strtolower($rule_value) == "true"){
                    if(!Input::exists($fieldName) || !Input::entered($fieldName)){
                        $this->addError($fieldName, $rule, $this->fieldName($fieldName) . " is required.");
                    }
                }
                break;
                
            case "email":
                if(strtolower($rule_value) == "true"){
                    if( (Input::entered($fieldName)) && !filter_var(Input::get($fieldName), FILTER_VALIDATE_EMAIL)){
                        $this->addError($fieldName, $rule, $this->fieldName($fieldName) . " is not a valid email address.");
                    }
                }
                break;
                
            case "minlength":
                if( (Input::entered($fieldName)) && strlen(Input::get($fieldName)) < $rule_value){
                    $this->addError($fieldName, $rule, $this->fieldName($fieldName) . " must be at least {$rule_value} characters long.");
                }
                break;
                
            case "maxlength":
                if( (Input::entered($fieldName)) && strlen(Input::get($fieldName)) > $rule_value){
                    $this->addError($fieldName, $rule, $this->fieldName($fieldName) . " must not exceed {$rule_value} characters.");
                }
                break;
                
            case "rangelength":
                list($min, $max) = explode(',', $rule_value);
                if(  (Input::entered($fieldName)) && (strlen(Input::get($fieldName)) > (float)$max || strlen(Input::get($fieldName)) < (float)$min) ){
                    $this->addError($fieldName, $rule, $this->fieldName($fieldName) . " must be between {$min} and {$max} characters.");
                }
                break;
                
            case "min":
                if( (Input::entered($fieldName)) && ((float)Input::get($fieldName) < (float)$rule_value)){
                    $this->addError($fieldName, $rule, $this->fieldName($fieldName) . " must be greater than or equal to {$rule_value}.");
                }
                break;
                
            case "max":
                if( (Input::entered($fieldName)) && ((float)Input::get($fieldName) > (float)$rule_value)){
                    $this->addError($fieldName, $rule, $this->fieldName($fieldName) . " must be lesser than or equal to {$rule_value}.");
                }
                break;
            
            case "range":
                list($min, $max) = explode(',', $rule_value);
                if( (Input::entered($fieldName)) && ((float)Input::get($fieldName) > (float)$max || (float)Input::get($fieldName) < (float)$min) ){
                    $this->addError($fieldName, $rule, $this->fieldName($fieldName) . " must be between {$min} and {$max}.");
                }
                break;
            
            case "equalto":
                if( (Input::entered($fieldName)) && (Input::get($fieldName) !== Input::get($rule_value))  ){
                    $this->addError($fieldName, $rule, $this->fieldName($fieldName) . " must match " . $this->fieldName($rule_value) . " field.");
                }
                break;
                
            case "date":
                if(strtolower($rule_value) == "true"){
                    if( (Input::entered($fieldName)) && !$this->validateDate(Input::get($fieldName), 'd/m/Y')){
                        $this->addError($fieldName, $rule, $this->fieldName($fieldName) . " is not a valid URL.");
                    }
                }
                break;
                
            case "url":
                if(strtolower($rule_value) == "true"){
                    if( (Input::entered($fieldName)) && !filter_var(Input::get($fieldName), FILTER_VALIDATE_URL)){
                        $this->addError($fieldName, $rule, $this->fieldName($fieldName) . " is not a valid date.");
                    }
                }
                break;
                
            case "remote":                  
                if( (Input::entered($fieldName)) && $this->get_data($rule_value) == "false"){
                    //echo "ERROR IS TRUE";
                    $this->addError($fieldName, $rule, $this->fieldName($fieldName) . " is not valid.");
                }
                break;
                
        }        
    }
    
    /**
     * fieldName
     * If a custom field name has been passed over for a field, that gets returned
     * Else, the original field name is returned
     * @param string $fieldName
     */
    private function fieldName($fieldName){
        if(array_key_exists($fieldName, $this->customNames)){
            return $this->customNames[$fieldName];
        }
        return ucfirst($fieldName);
    }
    
    /**
     * addError
     * Adds an error message to the error array
     * 
     * @param string $field - Field that this message belongs to
     * @param string $rule - Rule that this message belongs to
     * @param string $message - Default message, if a custom one is not found in the collection
     */
    private function addError($field, $rule, $message){
        $this->errors[] = ($this->customErrorExists($field,$rule)) ?: $message;
        $this->hasPassed = false;
    }
    
    /**
     * Returns a custom error for $field & $rule if one exists, otherwise returns null
     * @param string $field
     * @param string $rule
     * @return mixed
     */
    private function customErrorExists($field, $rule){
        return (isset($this->customErrors[$field][$rule])) ? $this->customErrors[$field][$rule] : null;
    }
    
    /**
     * errors
     * Returns an array of errors that occured during validation
     * @return array string 
     */
    public function errors(){
        return $this->errors;
    }
    
    /**
     * formattedErrors
     * Returns a formatted string of errors for displaying
     * @param string $style - Can be "bootstrap", "list", or "plain"
     * @return string
     */
    public function formattedErrors($style = "bootstrap"){
        $string = '';
        
        switch($style){
            case 'bootstrap':
            case 'list':
                $string = '<ul>';
                foreach($this->errors as $error){
                    $string .= "<li>{$error}</li>";
                }
                $string .= '</ul>';
                
                if($style=="bootstrap"){
                    $string = '<div class="alert alert-danger"><strong>Please correct the following errors:</strong>' . $string . '</div>';                    
                }
                
                break;
            case 'plain':
                foreach($this->errors as $error){
                    $string .= $error . "<br />";
                }
                break;
        }

        return $string;
    }
    
    /**
     * Returns an array of strings to be used for MVC templating engines, allowing generation of client-side validation rules
     */
    public function templateStrings(){
        
        foreach($this->rules as $field => $rules){
            $array[$field] = array(
                'rules' => $this->renderRules($field),
                'value' => $this->value($field) 
            );
        }
        
        $array['form_token'] = $this->renderToken();
        
        return $array;
    }
    
    /**
     * Returns true if a valid date is inputted
     * @param string $date
     * @param string $format
     * @return boolean 
     */
    private function validateDate($date, $format = 'Y-m-d H:i:s'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    /**
     * Simply returns the contents of a remote webpage (for remote validation rule)
     * @param string $url
     * @return string
     */
    private function get_data($url) {  
        
	$ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        
        //curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        //curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
        //curl_setopt($ch,CURLOPT_MAXREDIRS,50);
        
        $data = curl_exec($ch);
	curl_close($ch);
	return $data;
    }
    
}
