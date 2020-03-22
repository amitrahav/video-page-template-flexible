<?php

function vpt_youtube_id_to_url($id){
	/*
	* type1: http://www.youtube.com/watch?v=9Jr6OtgiOIw
	* type2: http://www.youtube.com/watch?v=9Jr6OtgiOIw&feature=related
	* type3: http://youtu.be/9Jr6OtgiOIw
	*/
	$url = '';
	if(isset($id) && !empty($id)){
        $url = 'http://www.youtube.com/watch?v='. $id;
	}
	return $url;
}

function vpt_vimeo_id_to_url($id){
	
	$url = '';
	if(isset($id) && !empty($id)){
        $url = 'https://vimeo.com/' . $id;
	}
	return $url;
}

function vpt_youtube_thumbnail($id, $res){
    // By https://gist.github.com/protrolium/8831763
    $url = '';
	if(isset($id) && !empty($id)){

        switch ($res) {
            case 'sddefault':
                $res_option = 'sddefault';
                break;
            
            case 'hqdefault':
                $res_option = 'hqdefault';
                break;
        
            case 'mqdefault':
                $res_option = 'mqdefault';
                break;
            
            case 'maxresdefault':
                $res_option = 'maxresdefault';
                break;
                
            default:
                $res_option = 'default';
                break;
        }

        $url = "http://img.youtube.com/vi/$id/$res_option.jpg";
	}
	return $url;
}

function vpt_vimeo_thumbnail($id){
    $url = '';
	if(isset($id) && !empty($id)){
        $http_response = file_get_contents("http://vimeo.com/api/v2/video/$id.json");
        $json_data = json_decode($http_response);
        $url = $json_data[0]->thumbnail_medium;
	}
	return $url;
}