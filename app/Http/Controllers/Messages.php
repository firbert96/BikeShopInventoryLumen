<?php
namespace App\Http\Controllers;

class Messages extends controller
{
    public $email_taken = ["email"=>["The email has already been taken."]];
    public $user_not_found = ["users"=>["User not found"]];
    public $product_not_found = ["inventory"=>["Product not found"]];
}