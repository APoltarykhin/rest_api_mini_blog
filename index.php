<?php
//header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json'); 

    require 'connect_db.php';

    require 'functions.php';


$q = $_GET['q'];

$metod = $_SERVER['REQUEST_METHOD'];

$postsIdentity = explode('/',$q);



$type = $postsIdentity[0];


$postsNumber = isset($postsIdentity[1]) ? $postsIdentity[1] : null;



switch($metod){
    case 'GET':
        if($type === 'posts'){

            if(isset($postsNumber)){
               
                get_one_post($database,$postsNumber);
        
           }else{
            
                get_posts($database);
           }
        
        }
        break;
    case 'POST':
        if($type === 'posts'){

            add_posts($database,$_POST);
            
        }
        break;
    case 'PATCH':
        if($type === 'posts'){
            if(isset($postsNumber)){
               
                $data_json = file_get_contents('php://input');

                $data_array = json_decode($data_json,true);

                update_path($database,$postsNumber,$data_array);

            }
        }   
        break;
    case 'DELETE':
        if($type === 'posts'){
            if(isset($postsNumber)){

                delete_post($database,$postsNumber);

            }
        }
        break;
}


