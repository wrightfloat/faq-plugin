<?php
/**
 * Faqizer sanitization class
 *
 * @package Faqizer
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;



class validate_our_data {

    public $input; //the input from the user
    public $input_type;  //type of sanitzation 
    public $santized_value; 
    public $is_type_valid; 
    public $is_current_value_empty;
    public $type_of_error;

    public function __construct($input) {
        $this->input = $input;
    }

    /**
     * sets up input errors 
     *
     * @param [type] $input_error_array the input errors that we want too use
     * @return void
     */
    public function set_input_errors($input_error_array){
        $this->input_error_array = $input_error_array;
        return $this; 
    }
    
    /**
     * this sets the input type that we want to use so we can check if the input is valid later
     *
     * @param [type] $input_type
     * @return void
     */
    public function set_input_type($input_type){
        $this->input_type = $input_type;
        return $this; 
    }


    /**
     * making sure that the type is valid.
     */
    public function is_input_type_valid($type_to_check, $currentvalue){
            
        //checking to see if the type of value that was entered is valid.
        switch ($type_to_check) {
            case 'string':
                $is_type_valid = is_string( $currentvalue );
            break;

            //checking to see if the value is a integer
            case 'integer':
                $is_type_valid = is_numeric( $currentvalue );
            break;

            //can be used for cases that i don't want to check the users input the input type but still want to see if it's empty. 
            case 'none':
                $is_type_valid = true;
            break;
        }

        //returns the valid type. 
        $this->is_type_valid = $is_type_valid;
        return $this->is_type_valid; 

    }



    /**
     * checks to see if the inputed value is empty.
     * @return void
     */
    public function check_if_input_is_empty($value){

        //makes sure that the user added a value an not just spaces.  
        $this->is_current_value_empty = empty(trim( $value ) ); 
        return $this->is_current_value_empty;
    }

    
    /**
     * returns our errors if we currently have any errors to display. 
     *
     * @param boolean $return_array returns the array of any errors present or not present. By default does not return the array, but process's it via another function. 
     * @return void
     */
    public function return_errors($return_array = false){
        //fist make sure that i have a value by making sure that the input is not empty.
        if( $this->check_if_input_is_empty($this->input) !== true){

            //second make sure that the type of input is valid or return an error
            if( $this->is_input_type_valid($this->input_type, $this->input) !== true){
                //display the invalid type entered error.
                $this->type_of_error = array('the_error' => $this->input_error_array['invalid_input_type_error'], 'has_errors' => true);
            }
            else{
                $this->type_of_error = array('has_errors' => false);
            }
        }
        else{
            //display the empty value error.
            $this->type_of_error = array('the_error' => $this->input_error_array['empty_value_error'], 'has_errors' => true );
        }

        //returns the actual array when the return_array value is true. 
        if($return_array){
            return $this->type_of_error; 
        }

        else{
            return $this;
        }
        
    }
    
}