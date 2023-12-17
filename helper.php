<?php

use App\Models\Category;

    function getCategories(){
       return Category::where('status','1')->orderBy('display_order', 'ASC')->get();
    }
    //blog limit
    function getBlogDetail($description)
    {
        $limit = 20;
        $words = explode(' ', $description);
        $showDescription = implode(' ', array_slice($words, 0, $limit));
        return $showDescription;
    }
    //datetime
    function formatBlogDate($date)
    {
        $format = 'M d, Y';
        return date($format, strtotime($date));
    }
?>