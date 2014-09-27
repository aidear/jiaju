<?php
namespace BackEnd\Model\User;

class User
{
    public $ID;
    public $UserName;
    public $Password;
    public $Status;
    public $Role;

    function exchangeArray(Array $data){
        $this->ID = isset($data['ID']) ? $data['ID'] : '';
        $this->UserName = isset($data['UserName']) ? $data['UserName'] : '';
        $this->Password = isset($data['Password']) ? $data['Password'] : '';
        $this->Status = isset($data['Status']) ? $data['Status'] : '';
        $this->Role = isset($data['Role']) ? $data['Role'] : '';
    }

    function getArrayCopy(){
        return get_object_vars($this);
    }
}