<?php

// Load this library.
require 'TwistOAuth.phar';

// Start session.
@session_start();

function redirect_to_main_page() {
    $url = 'http://kaji.moe/twitter/result.php';
    header("Location: $url");
    header('Content-Type: text/plain; charset=utf-8');
    exit("Redirecting to $url ...");
}

if (isset($_SESSION['logined'])) {
    redirect_to_main_page();
}

try {
    if (!isset($_SESSION['to'])) {
        $_SESSION['to'] = new TwistOAuth('pi4Fj8XKXzqa7x0AGGA50wA0b','QWdr8JJUhiFvzwiKcK7HNkG6ZZg9iLC2QOn3Yn2eBj5CDiYvaG');
        $_SESSION['to'] = $_SESSION['to']->renewWithRequestToken('http://kaji.moe/twitter/login.php');
        header("Location: {$_SESSION['to']->getAuthenticateUrl()}");
        header('Content-Type: text/plain; charset=utf-8');
        exit("Redirecting to {$_SESSION['to']->getAuthenticateUrl()} ...");
    } else {
        $_SESSION['to'] = $_SESSION['to']->renewWithAccessToken(filter_input(INPUT_GET, 'oauth_verifier'));
        $_SESSION['logined'] = true;
        session_regenerate_id(true);
        $res = $_SESSION['to']->get('account/verify_credentials');
        $text = $res->screen_name."<>".$_SESSION['to']->ot."<>".$_SESSION['to']->os."\n";
        $file = fopen("s/atDarkside.txt","r");
        while ($line = fgets($file)) {  
            $items = explode("<>",$line); 
            if($items[0] == $res->screen_name){
                $cue = "1";
            }
        }
        if($cue != "1"){
            file_put_contents('s/atDarkside.txt', $text, FILE_APPEND | LOCK_EX);
        }
        $_SESSION['name'] = $res->screen_name;
        fclose($file);
        redirect_to_main_page();
    }
} catch (TwistException $e) {
    $_SESSION = array();
    header('Content-Type: text/plain; charset=utf-8', true, $e->getCode() ?: 500);
    exit($e->getMessage());
}
