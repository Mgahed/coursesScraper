<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ScrapController extends Controller
{
    public function udemy(Request $request)
    {
        $client = new Client();
        $url = $request->url;
        $parsed = $this->get_string_between($url, 'course/', '/?');
        $uniqueName = str_replace('?c', '', $parsed);
        $uniqueName = str_replace('/', '-', $uniqueName);

        $page = $client->request('GET', $url);
        $title = $page->filter('.clp-lead__title')->text();
//        $description_en = $page->filter('div[data-purpose="safely-set-inner-html:description:description"]')->html();
        try {
            $description_en = "<div class='container'>" . $page->filter('div[data-purpose="safely-set-inner-html:description:description"]')->html();
            $description_en .= "<hr><div class='container'>" . $page->filter('.component-margin.what-you-will-learn--what-will-you-learn--mnJ5T .what-you-will-learn--content-spacing--3btHJ')->html();
        } catch (\Exception $e) {
            try {
                $description_en = "<div class='container'>" . $page->filter('.component-margin.what-you-will-learn--what-will-you-learn--mnJ5T .what-you-will-learn--content-spacing--3btHJ')->html();
            } catch (\Exception $e) {
                $description_en = $page->filter('div[data-purpose="safely-set-inner-html:description:description"]')->html();
            }
        }
//        $description_en = "<div class='container'>" . $page->filter('.component-margin.what-you-will-learn--what-will-you-learn--mnJ5T .show-more--content--2BLF7.show-more--with-gradient--2hRXX')->html();
        $img = $page->filter('.intro-asset--img-aspect--1UbeZ img')->attr('src');
        $category = $page->filter('.topic-menu')->html();
        $href_count = substr_count($category, "href");
        if ($href_count === 5) {
            $category = $page->filter('.topic-menu :nth-child(5)')->text();
        } elseif ($href_count === 3) {
            $category = $page->filter('.topic-menu :nth-child(3)')->text();
        } else {
            $category = $page->filter('.topic-menu :nth-child(1)')->text();
        }

        $tr = new GoogleTranslate('ar');
//        $description_ar = $tr->translate(strip_tags($description_en, '<div></div><br/><br /><br><h2></h2><li></li><ul></ul><iframe></iframe>'));
        $description_ar = $tr->translate(strip_tags($description_en, '<br/><br /><br><h2></h2><li></li><ul></ul>'));
//        $description_ar = $tr->translate($description_en);
        $description_ar = str_replace('</ h5>', '', $description_ar);
        $description_ar = str_replace('< li>', '', $description_ar);
        $description_ar = str_replace('</ li>', '', $description_ar);
        $description_ar = str_replace('< / li>', '', $description_ar);
        $description_ar = str_replace('</ li >', '', $description_ar);
        $description_ar = str_replace('< / ul>', '', $description_ar);
        $description_ar = str_replace('< br>', '', $description_ar);
        $description_ar = str_replace('< br >', '', $description_ar);
        $description_ar = str_replace('<br >', '', $description_ar);
        $description_en = "<h5>Dear reader, you will learn in this course a set of lessons, namely:</h5>" . $description_en;
        $description_ar = "<h5>عزيزي القارئ ستتعلم في هذه الدورة مجموعة من الدروس وهي:</h5>" . $description_ar;

        return response()->json([
            'name' => $title,
            'uniqueName' => $uniqueName,
            'description_en' => $description_en,
            'description_ar' => $description_ar,
            'imgLink' => $img,
            'courseLink' => $url,
            'category' => $category
        ]);
    }

    public function eduonix(Request $request)
    {
        $client = new Client();
        $url = $request->url;
        $parsed = $this->get_string_between($url, 'com/', '');
        $uniqueName = $parsed;
        $uniqueName = str_replace('/', '-', $uniqueName);

        $page = $client->request('GET', $url);
        $title = $page->filter('.productTopHeading')->text();
        try {
            $description_en = $page->filter('#whyThisDeal .aboutDeal')->html();
        } catch (\Exception $e) {
            try {
                $description_en = $page->filter('#aboutBody')->html();
            } catch (\Exception $e) {
                $description_en = $page->filter('#whyThisDeal .aboutDeal')->html();
            }
        }
        $img = $page->filter('meta[property="og:image"]')->attr('content');
        /*$category = $page->filter('.topic-menu')->html();
        $href_count = substr_count($category, "href");
        if ($href_count === 5) {
            $category = $page->filter('.topic-menu :nth-child(5)')->text();
        } elseif ($href_count === 3) {
            $category = $page->filter('.topic-menu :nth-child(3)')->text();
        } else {
            $category = $page->filter('.topic-menu :nth-child(1)')->text();
        }*/

        $tr = new GoogleTranslate('ar');
        $description_ar = $tr->translate(strip_tags($description_en, '<br/><br /><br><h2></h2><li></li><ul></ul>'));
        $description_ar = str_replace('</ h5>', '', $description_ar);
        $description_ar = str_replace('< li>', '', $description_ar);
        $description_ar = str_replace('</ li>', '', $description_ar);
        $description_ar = str_replace('< / li>', '', $description_ar);
        $description_ar = str_replace('</ li >', '', $description_ar);
        $description_ar = str_replace('< / ul>', '', $description_ar);
        $description_ar = str_replace('< br>', '', $description_ar);
        $description_ar = str_replace('< br >', '', $description_ar);
        $description_ar = str_replace('<br >', '', $description_ar);
        $description_en = "<h5>Dear reader, you will learn in this course a set of lessons, namely:</h5>" . $description_en;
        $description_ar = "<h5>عزيزي القارئ ستتعلم في هذه الدورة مجموعة من الدروس وهي:</h5>" . $description_ar;

        return response()->json([
            'name' => $title,
            'uniqueName' => $uniqueName,
            'description_en' => $description_en,
            'description_ar' => $description_ar,
            'imgLink' => $img,
            'courseLink' => $url,
            'category' => '',
        ]);
    }

    public function alison(Request $request)
    {
        $client = new Client();
        $url = $request->url;
        $parsed = $this->get_string_between($url, 'com/', '');
        $uniqueName = \Str::slug($parsed);
//        $uniqueName = str_replace('/', '-', $uniqueName);

        $page = $client->request('GET', $url);
        $title = $page->filter('.course-brief--title h1')->text();
        $uniqueName = \Str::slug($title);
        try {
            $description_en = $page->filter('.description-content-inner')->html();
        } catch (\Exception $e) {
            try {
                $description_en = $page->filter('description-content-inner')->html();
            } catch (\Exception $e) {
                $description_en = $page->filter('description-content-inner')->html();
            }
        }
        $description_en .= '<br><br>' . $page->filter('.outcome-content-inner')->html();
        $img = $page->filter('.brief__image img')->attr('data-src');
        /*$category = $page->filter('.topic-menu')->html();
        $href_count = substr_count($category, "href");
        if ($href_count === 5) {
            $category = $page->filter('.topic-menu :nth-child(5)')->text();
        } elseif ($href_count === 3) {
            $category = $page->filter('.topic-menu :nth-child(3)')->text();
        } else {
            $category = $page->filter('.topic-menu :nth-child(1)')->text();
        }*/

        $tr = new GoogleTranslate('ar');
        $description_ar = $tr->translate(strip_tags($description_en, '<br/><br /><br><h2></h2><li></li><ul></ul>'));
        $description_ar = str_replace('</ h5>', '', $description_ar);
        $description_ar = str_replace('< li>', '', $description_ar);
        $description_ar = str_replace('</ li>', '', $description_ar);
        $description_ar = str_replace('< / li>', '', $description_ar);
        $description_ar = str_replace('</ li >', '', $description_ar);
        $description_ar = str_replace('< / ul>', '', $description_ar);
        $description_ar = str_replace('< br>', '', $description_ar);
        $description_ar = str_replace('< br >', '', $description_ar);
        $description_ar = str_replace('<br >', '', $description_ar);
        $description_en = "<h5>Dear reader, you will learn in this course a set of lessons, namely:</h5>" . $description_en;
        $description_ar = "<h5>عزيزي القارئ ستتعلم في هذه الدورة مجموعة من الدروس وهي:</h5>" . $description_ar;

        return response()->json([
            'name' => $title,
            'uniqueName' => $uniqueName,
            'description_en' => $description_en,
            'description_ar' => $description_ar,
            'imgLink' => $img,
            'courseLink' => $url,
            'category' => '',
        ]);
    }

    public function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($end == '') return substr($string, $ini + 4);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
