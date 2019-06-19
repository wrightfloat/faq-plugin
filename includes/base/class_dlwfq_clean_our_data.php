<?php
/**
 * Faqizer input sanitization class
 *
 * @package Faqizer
 * @since   0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
 
class dlwfq_clean_our_data {

    // Varibles to be used in this class. 
    public $user_input; 
    public $type_of_data;
    public $return_our_data; 

    // the main contstuctor that takes the data input, and the data type that we want to clean. 
    public function __construct($user_input, $type_of_data) {
        $this->user_input = $user_input; //the input from the user.
        $this->type_of_data = $type_of_data;//the type of data that i want too clean.  
        $this->clean_our_data(); 
    }

    /**
     * remove excess whitespace from a varible. 
     *
     * @param [type] $v_to_search_in the value that we want to remove excess from.
     * @param boolean $trim_white_spance_from_end when true this will remove whitespace from the end of the varible that is being searched in. 
     * @return void
     */
    private function remove_excess_white_space( $v_to_search_in, $trim_white_spance_from_end = false ){

        // removes white space from the end of a string and removes excess whitespace in between words. 
        // preg_replace('/\s+/',' ', $v_to_search_in) - can remove excess white space between words and numbers in a varible. 
        // rtrim() removes the white space at the end of the varible. 
        if( $trim_white_spance_from_end ){
            $v_to_search_in = rtrim( preg_replace('/\s+/',' ', $v_to_search_in) );
            return $v_to_search_in;    
        }

        //removes excess whitespace in between words
        else{
            return preg_replace('/\s+/',' ', $v_to_search_in);
        } 
    }


    /**
     * remove excess whitespace from a varible and parses it as an int. 
     */
    private function remove_all_white_space_an_parse_as_int( $v_to_search_in){

        // removes all whitespace and parses it as a int.  
        return intval( rtrim( preg_replace('/\s+/','', $v_to_search_in) ) ); 
    }

    /**
     * This is for setting up our error messages. 
     *
     * @param [type] error_type
     * @param [type] $value
     * @return void
     */
    private function error_message($error_type, $value){
        $this->error_message = ['has_an_error' => true, 'error_type' => $error_type, 'value' => $value];
        return $this->error_message; 
    }


    /**
     * 
     * making sure that the type of data the user submits is not empty, valid, and sanitized the way we want the data to be added too the db.
     * If the data is not valid then we return an error based on what was wrong with the data. 
     */
    private function clean_our_data(){ 

        //checking to see if the type of value that was entered is valid.
        switch ($this->type_of_data) {
            
            // Used to clean slugs when we are allowing users to input them within the settings area of wordpress. 
            // Needs too allow users to enter letters and numbers, and have special charcaters and other bad stuff striped out. 
            case 'slug':

                // sanitizing our data. 
                // https://developer.wordpress.org/reference/functions/sanitize_title_with_dashes/
                // this function removes special characters, and adds hyphens to white space, so if we have no values after sanitization i would like to keep our value at the default value of faqs.
                $value = sanitize_title_with_dashes($this->user_input);
                //making sure that our input value is not empty after we have sanitized it otherwise i will display an error.
                if( !empty($value) ){   
                    $this->return_our_data = array('has_an_error' => false,  'value' => $value );
                }

                // default value to use when user passes invalid values or an empty input.
                else{
                    $this->return_our_data = $this->error_message( 'empty', 'faqs'); 
                }

            break;

            // Used to clean the check input fields. 
            // Making sure that we only allow 0 or 1 to be added to the db.
            // have a default that we revert back to upon an empty value being entered
            case 'checkbox':
                // i will not display errors for checkboxs.
                //clearing bad values
                $value = sanitize_text_field($this->user_input);
                //if we have no value then we make the value 0 instead of adding no value too the db for the checkbox.
                if( !empty( $value ) ){  
                    if($value == 1){
                        $this->return_our_data = array('has_an_error' => false,  'value' => 1 );
                    }
                }

                // default value to use when user passes invalid values or an empty input.
                else{
                    $this->return_our_data = array('has_an_error' => false,  'value' => 0 );
                }

            break;
            
            // removes numbers and cleans the users input.
            // have a default that we revert back to upon an empty value being entered
            case 'strings_only':
                $value = sanitize_text_field($this->user_input);
                //if we have no value then we make the value 0 instead of adding no value too the db for the checkbox.
                if( !empty( $value ) ){  

                    // excutes when the users has added somthing other than an alphabetical character in the input.
                    if( preg_match('/[^a-zA-Z]+/', $value) ){

                        //strip out the numbers from the input.
                        //remove excess white space
                        //remove whitespace at the end of the string.
                        $value = $this->remove_excess_white_space(preg_replace('/[^a-zA-Z]/', ' ', $value), true);
                        $this->return_our_data = array('has_an_error' => false,  'value' => $value );
                    }

                    //excutes when the users has no numbers in there input
                    else{
                        $this->return_our_data = array('has_an_error' => false,  'value' => $value );
                    }

                }
                
                // default value to use when user passes invalid values or an empty input.
                else{
                    $this->return_our_data = $this->error_message( 'empty', 'Frequently Asked Questions');
                }

            break;
            
            // clean the post input
            // makes sure it's a number
            // make sure it's a positive number, unless it's equal too -1 
            // have a default that we revert back to upon an empty value being entered
            case 'post_count':

                //cleans input value
                $value = sanitize_text_field($this->user_input);

                // make sure that the value is not empty after sanitizing the data. 
                if( !empty( $value ) ){  

                    //excutes when the users have added letters too the input field.
                    if( preg_match('/[a-zA-Z]/', $value) ){

                        //strips out strings and whitespace.
                        $value = $this->remove_all_white_space_an_parse_as_int( preg_replace('/[a-zA-Z]/', '', $value) );

                        //make sure that the input is a positive number unless it's equal too -1
                        if( $value == -1 ){
                            $this->return_our_data = array('has_an_error' => false,  'value' => $value );
                        } 
                        // Makes sure that we pass a positive integer and that its not a float
                        // - https://developer.wordpress.org/reference/functions/absint/
                        else{
                            $this->return_our_data = array('has_an_error' => false,  'value' => absint($value) );
                        }

                    }

                    //excutes when the users have no letters in there input.
                    else{
                        //strips out whitespace.
                        $value = $this->remove_all_white_space_an_parse_as_int($value);
                        //make sure that the input is a positive number unless it's equal too -1
                        if( $value == -1 ){
                            $this->return_our_data = array('has_an_error' => false,  'value' => $value );
                        } 
                        // Makes sure that we pass a positive integer and that its not a float 
                        // - https://developer.wordpress.org/reference/functions/absint/
                        else{
                            $this->return_our_data = array('has_an_error' => false,  'value' => absint($value) );
                        }

                    }
                }

                // default value to use when user passes invalid values or an empty input.
                else{
                    $this->return_our_data = $this->error_message( 'empty', 10);
                }
        }

        return $this->return_our_data; 
    }
}