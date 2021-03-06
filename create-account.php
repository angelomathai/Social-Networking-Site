<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
include('/Applications/MAMP/htdocs/Project1/classes/DB.php');
include('/Applications/MAMP/htdocs/Project1/classes/Mail.php');
    
if(isset($_POST['createaccount']))
    {
        $username=$_POST['username'];
        $password=$_POST['password'];
        $email=$_POST['email'];
        
        if(!DB::query('SELECT username FROM users WHERE username=:username',array(':username'=>$username)))
        {
            if(strlen($username)>=3 && strlen($username)<=32)
            {
                if(preg_match('/[a-zA-Z0-9]+/',$username))
                {
                    if(strlen($password)>=6 && strlen($password)<=60)
                       {
                                if(filter_var($email, FILTER_VALIDATE_EMAIL))
                                {
                                    if(!DB::query('SELECT email FROM users WHERE email=:email',array(':email'=>$email)))
                                    {
                                        DB::query('INSERT INTO users VALUES (null,:username,:password,:email,\'0\',null)',array(':username'=>$username,':password'=>password_hash($password,PASSWORD_BCRYPT),':email'=>$email));
                                        Mail::sendMail('Welcome to our Social Network','Your account has been created!',$email);
                                        echo 'Success!';
                                    }
                                    else
                                    {
                                        echo 'Email already registered';
                                    }
                                }
                                else
                                {
                                    echo 'Invalid email!';
                                }
                       }
                    else
                    {
                        echo 'Invalid password';
                    }
                }
                else
                {
                    echo 'Invalid Username';
                }
            }
            else
            {
                echo "Invalid username";
            }
        }
        else
        {
            echo 'User already exists!';
        }
    }
?>
<h1>Register</h1>
<form action="create-account.php" method="post">
<input type="text" name="username" value="" placeholder="Username ..."><p />
<input type="password" name="password" value="" placeholder="Password ..."><p />
<input type="email" name="email" value="" placeholder="someone@somesite.com"><p />
<input type="submit" name="createaccount" value="Create Account">
</form>
