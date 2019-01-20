<?php 
    require 'instagram.php';

    $instagramDetails = new InstagramDetails('fabio_henrique127');
    $posts = $instagramDetails->getPosts();
    var_dump($posts);
    $profile = $instagramDetails->getProfileDetails();
    var_dump($profile);
    $all = $instagramDetails->getAll();
    var_dump($all);
    
?>