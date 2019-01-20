<?php

    /**
     * Get the last 12 posts just for public profiles to private profiles just work profile details
     */
    class InstagramDetails {
        private $instagramBase = 'https://www.instagram.com/';
        private $username = ''; //Define vazio

        function __construct($username) {
            $this->username = $username;
        }
        /**
         * Returns posts and profile details
         *
         * @return object
         */
        function getAll()
        {
            if ($this->username != ''){
                $listObject = $this->getCurl();
                $userGet = $listObject->entry_data->ProfilePage[0]->graphql->user;
                $edges = ($userGet->edge_owner_to_timeline_media->edges);
                $posts = [];
                foreach ($edges as $key => $value) {
                    $posts[] = $this->createPostObject($value);;
                }
                $returnObject = (object) [
                    'user' => $this->createUserObject($userGet),
                    'posts' => $posts
                ];
                return $returnObject;
            }else{
                return $this->returnError('Você precisa definir o username no construtor');
            }
        }

        /**
         * return profile details
         *
         * @return object
         */
        function getProfileDetails()
        {
            if ($this->username != ''){
                $listObject = $this->getCurl();
                $userGet = $listObject->entry_data->ProfilePage[0]->graphql->user;
                return $this->createUserObject($userGet);
            }else{
                return $this->returnError('Você precisa definir o username no construtor');
            }
        }

        /**
         * return last 12 posts just to public profiles
         *
         * @return array
         */
        function getPosts()
        {
            if ($this->username != ''){
                $listObject = $this->getCurl();
                $userGet = $listObject->entry_data->ProfilePage[0]->graphql->user;
                $edges = ($userGet->edge_owner_to_timeline_media->edges);
                $posts = [];
                foreach ($edges as $key => $value) {
                    $posts[] = $this->createPostObject($value);
                }
                return $posts;
            }else{
                return $this->returnError('Você precisa definir o username no construtor');
            }
        }

        private function getCurl()
        {
            // Iniciamos a função do CURL:
            $ch = curl_init($this->instagramBase . $this->username . '/');
            curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
            "Content-Type:" => "text/plain; charset=utf-8"
            ],
            CURLOPT_RETURNTRANSFER => 1,
            ]);

            $resposta = curl_exec($ch);
            curl_close($ch);
            $dom = new DOMDocument;
            @$dom->loadHTML($resposta);
            $dom->getElementsByTagName('script');
            $xpath = new DOMXPath($dom);
            $script_tags = $xpath->query('//body//script[not(@src)]');

            foreach ($script_tags as $tag) {
                $moveme[] = $tag;
            }
            $jsonEnd = str_replace('window._sharedData = ','',$moveme[0]->nodeValue);
            $jsonEnd = str_replace(';','',$jsonEnd);
            $listObject = json_decode($jsonEnd);
            return $listObject;
        }

        private function createPostObject($value)
        {
            $post = (object) [
                'id' => $value->node->id,
                'text' => $value->node->edge_media_to_caption->edges[0]->node->text,
                'shortcode' => $value->node->shortcode,
                'comments_count' => $value->node->edge_media_to_comment->count,
                'likes_count' => $value->node->edge_liked_by->count,
                'image' => $value->node->display_url,
                'link' => $this->instagramBase . 'p/' . $value->node->shortcode
            ];
            return $post;
        }

        private function createUserObject($userGet)
        {
            $user = (object) [
                'id' => $userGet->id,
                'username' => $userGet->username,
                'biography' => $userGet->biography,
                'followed_count' => $userGet->edge_followed_by->count,
                'follow_count' => $userGet->edge_follow->count,
                'full_name' => $userGet->full_name,
                'category_name' => $userGet->business_category_name,
                'email' => $userGet->business_email,
                'phone_number' => $userGet->business_phone_number,
                'profile_image' => $userGet->profile_pic_url_hd,
                'publish_count' => $userGet->edge_owner_to_timeline_media->count,
                'external_url' => $userGet->external_url,
                'address_info' => json_decode($userGet->business_address_json)
            ];
            return $user;
        }

        private function returnError($message)
        {
            $object = (object) [
                'message' => $message,
                'result' => 0
            ];
            return $object;
        }
        
    }
?>