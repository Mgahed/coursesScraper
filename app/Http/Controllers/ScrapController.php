<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ScrapController extends Controller
{
    public function getImg($imgUrl)
    {
        // make post request to the url with 2 keys and values using guzzle
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://thumbsnap.com/api/upload', [
            'multipart' => [
                [
                    'name' => 'key',
                    'contents' => '00002372f4f4ff7148f857ec88f74ec0',
                ],
                [//'media' => fopen('https://img-c.udemycdn.com/course/240x135/3540520_a25f_7.jpg', 'r'),//$img,
                    'name' => 'media',
                    'contents' => fopen($imgUrl, 'r'),
                ],
            ],
        ]);
        // get the response body
        $body = json_decode($response->getBody()->getContents(), true)['data']['media'];
        return $body;
    }

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
            $description_en = "<div class='container'>" . $page->filter('.component-margin.what-you-will-learn--what-will-you-learn--mnJ5T .what-you-will-learn--content-spacing--3btHJ')->html();
            $description_en .= "<hr><hr><div class='container'>" . $page->filter('div[data-purpose="safely-set-inner-html:description:description"]')->html();
        } catch (\Exception $e) {
            try {
                $description_en = "<div class='container'>" . $page->filter('.component-margin.what-you-will-learn--what-will-you-learn--mnJ5T .what-you-will-learn--content-spacing--3btHJ')->html();
            } catch (\Exception $e) {
                $description_en = $page->filter('div[data-purpose="safely-set-inner-html:description:description"]')->html();
            }
        }
//        $description_en = "<div class='container'>" . $page->filter('.component-margin.what-you-will-learn--what-will-you-learn--mnJ5T .show-more--content--2BLF7.show-more--with-gradient--2hRXX')->html();
        $img = $page->filter('.intro-asset--img-aspect--1UbeZ img')->attr('src');
        $img = $this->getImg($img);
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
        $description_en = "<p>Welcome dear follower of the (Journey of Learn) website, we offer courses with coupons available for the first 1000 free registration only and other coupons for a limited time. We explain the short and long description of the courses in order to know the lessons that you will learn easily and easily:</p>" . $description_en . "<p>What are the basic requirements to enter the course and register for it on our website? Learning Journey:</p><p>I press the green button (Get the course to enter the site and register)</p><p>You must have an email (mail account) that you remember for yourself and also you must remember the password of the email that you will register with, and if you do not have an email account, it is better to create an account (Gmail)</p>";
        $description_ar = "<p>أهلا بك عزيزي المتابع لموقع (Journey of Learn) نقدم دورات بكوبونات متاحة لاول 1000 تسجيل مجاني فقط وكوبونات اخري لفترة محدودة فاذا كنت تريد ان تحصل علي كل الكورسات علي موقعنا وان تكون اول المسجلين في الكورسات المجانية قم بتسجيل الدخول أوقم بالدخول علي وسائل التواصل الاجتماعي وخصوصا التليجرام نوضح الوصف المختصر والطويل  للدورات لكي تعرف الدروس التي سوف تتعلمها بسهولة ويسر :</p>" . $description_ar . "<p>ما هي المتطلبات الأساسية لدخول الدورة والتسجيل فيها على موقعنا؟ رحلة تعليمية:</p><p>أضغط على الزر الأخضر (احصل على الدورة للدخول إلى الموقع والتسجيل)</p><p>يجب أن يكون لديك بريد إلكتروني (حساب بريد) تتذكره لنفسك وأيضًا يجب أن تتذكر كلمة مرور البريد الإلكتروني الذي ستسجل به ، وإذا لم يكن لديك حساب بريد إلكتروني ، فمن الأفضل إنشاء حساب (Gmail)</p>";

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
        $img = $this->getImg($img);
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
        $description_en = "<p>Welcome dear follower of the (Journey of Learn) website, we offer courses with coupons available for the first 1000 free registration only and other coupons for a limited time. We explain the short and long description of the courses in order to know the lessons that you will learn easily and easily:</p>" . $description_en . "<p>What are the basic requirements to enter the course and register for it on our website? Learning Journey:</p><p>I press the green button (Get the course to enter the site and register)</p><p>You must have an email (mail account) that you remember for yourself and also you must remember the password of the email that you will register with, and if you do not have an email account, it is better to create an account (Gmail)</p>";
        $description_ar = "<p>أهلا بك عزيزي المتابع لموقع (Journey of Learn) نقدم دورات بكوبونات متاحة لاول 1000 تسجيل مجاني فقط وكوبونات اخري لفترة محدودة فاذا كنت تريد ان تحصل علي كل الكورسات علي موقعنا وان تكون اول المسجلين في الكورسات المجانية قم بتسجيل الدخول أوقم بالدخول علي وسائل التواصل الاجتماعي وخصوصا التليجرام نوضح الوصف المختصر والطويل  للدورات لكي تعرف الدروس التي سوف تتعلمها بسهولة ويسر :</p>" . $description_ar . "<p>ما هي المتطلبات الأساسية لدخول الدورة والتسجيل فيها على موقعنا؟ رحلة تعليمية:</p><p>أضغط على الزر الأخضر (احصل على الدورة للدخول إلى الموقع والتسجيل)</p><p>يجب أن يكون لديك بريد إلكتروني (حساب بريد) تتذكره لنفسك وأيضًا يجب أن تتذكر كلمة مرور البريد الإلكتروني الذي ستسجل به ، وإذا لم يكن لديك حساب بريد إلكتروني ، فمن الأفضل إنشاء حساب (Gmail)</p>";

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
        $img = $this->getImg($img);
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
        $description_en = "<p>Welcome dear follower of the (Journey of Learn) website, we offer courses with coupons available for the first 1000 free registration only and other coupons for a limited time. We explain the short and long description of the courses in order to know the lessons that you will learn easily and easily:</p>" . $description_en . "<p>What are the basic requirements to enter the course and register for it on our website? Learning Journey:</p><p>I press the green button (Get the course to enter the site and register)</p><p>You must have an email (mail account) that you remember for yourself and also you must remember the password of the email that you will register with, and if you do not have an email account, it is better to create an account (Gmail)</p>";
        $description_ar = "<p>أهلا بك عزيزي المتابع لموقع (Journey of Learn) نقدم دورات بكوبونات متاحة لاول 1000 تسجيل مجاني فقط وكوبونات اخري لفترة محدودة فاذا كنت تريد ان تحصل علي كل الكورسات علي موقعنا وان تكون اول المسجلين في الكورسات المجانية قم بتسجيل الدخول أوقم بالدخول علي وسائل التواصل الاجتماعي وخصوصا التليجرام نوضح الوصف المختصر والطويل  للدورات لكي تعرف الدروس التي سوف تتعلمها بسهولة ويسر :</p>" . $description_ar . "<p>ما هي المتطلبات الأساسية لدخول الدورة والتسجيل فيها على موقعنا؟ رحلة تعليمية:</p><p>أضغط على الزر الأخضر (احصل على الدورة للدخول إلى الموقع والتسجيل)</p><p>يجب أن يكون لديك بريد إلكتروني (حساب بريد) تتذكره لنفسك وأيضًا يجب أن تتذكر كلمة مرور البريد الإلكتروني الذي ستسجل به ، وإذا لم يكن لديك حساب بريد إلكتروني ، فمن الأفضل إنشاء حساب (Gmail)</p>";

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
