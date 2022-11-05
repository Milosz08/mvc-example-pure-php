<?php

class BooksService
{
    private $_form_data = array('title', 'authors', 'copies');

    //------------------------------------------------------------------------------------------------------------------

    public function __construct()
    {
        $this->_form_data = array_fill_keys($this->_form_data, array('value' => '', 'error_message' => ''));
    }

    //------------------------------------------------------------------------------------------------------------------

    public function get_form_validatior_book()
    {
        return $this->_form_data;
    }
}