# instagram Api

You need PHP >= 7.0 and curl library

Get the last 12 posts just for public profiles to private profiles just work profile details

you need to add the username or profile id in the constructor

use examples below:

require 'instagram.php';
$instagramDetails = new InstagramDetails('fabio_henrique127');

$posts = $instagramDetails->getPosts();
var_dump($posts);
    
$profile = $instagramDetails->getProfileDetails();
var_dump($profile);
    
$all = $instagramDetails->getAll();
var_dump($all);  
