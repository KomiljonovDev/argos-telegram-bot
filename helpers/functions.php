<?php

	function html($tx){
        return str_replace(['<','>'],['&#60;','&#62;'],$tx);
    }

    function removeBotUserName($tx)
    {
    	return explode('@', $tx)[0];
    }

	function myUser($dbColumns=[],$myUser=[]) {
        global $db;
        $user = $db->selectWhere('users',[
            [
                'fromid'=>$myUser[0],
                'cn'=>'='
            ],
        ]);
        if ($user->num_rows) {
            if (mysqli_fetch_assoc($user)['del'] == '1') {
                $db->updateWhere('users',[
                    'del'=>0,
                ],[
                    'fromid'=>$myUser[0],
                    'cn'=>'='
                ]);
            }
            return false;
        }
        $dbInsert = [];
        foreach ($dbColumns as $key => $value) {
            $dbInsert[$dbColumns[$key]] = $myUser[$key];
        }
        return $db->insertInto('users',$dbInsert);
    }

    function channel($fromid) {
        global $db,$admin,$mid,$miid,$qid, $bot;
        $status = mysqli_fetch_assoc($db->selectWhere('channels',[
            [
                'name'=>"status",
                'cn'=>'='
            ],
        ]));
        if ($status["target"] == "on") {
            $channels = $db->selectWhere('channels',[
                [
                    'name'=>"status",
                    'cn'=>'!='
                ],
            ]);
            if ($num = $channels->num_rows) {
                $res = 0;
                foreach ($channels as $key => $value) {
                    $id = $value['target'];
                    $getchatadmin = $bot->request('getChatMember',[
                        'chat_id'=>$id,
                        'user_id'=>$fromid
                    ]);
                    $status = $getchatadmin->result->status;
                    if ($status == "administrator" or $status == "creator" or $status == "member") {
                        $res++;
                    }
                }
                if ($res == $num) {
                    return true;
                }else{
                    foreach ($channels as $key => $value) {
                        $id = $value['target'];
                        $getchat = $bot->request('getChat',[
                            'chat_id'=>$id
                        ]);
                        $title = $getchat->result->title;
                        $link = $getchat->result->invite_link;
                        if (!empty($link)) {
                            $keyy[]=['text'=>"➕ Obuna boʻlish", 'url'=>"$link"];
                        }
                    }
                    $keyy[] = ['callback_data'=>"res", 'text'=>"✅ Tasdiqlash"];
                    $key = array_chunk($keyy, 1);
                    $admin = $db->selectWhere('admins',[
                        [
                            'fromid'=>$fromid,
                            'cn'=>'='
                        ],
                    ]);
                    if ($admin->num_rows) {
                        return true;
                    }
                    if (!is_null($mid)) {
                        $bot->request('answerCallbackQuery',[
                            'callback_query_id'=>$qid,
                            'text'=>"Barcha kanallarimizga obuna bo'lmadingiz!"
                        ]);
                        $bot->request('editMessageText',[
                            'chat_id'=>$fromid,
                            'text'=>"<b>❗️Botdan foydalanishni davom ettirish uchun quyidagi kanalimizga obuna bo'ling👇🏼</b>",
                            'parse_mode'=>'html',
                            'message_id'=>$mid,
                            'reply_markup'=>json_encode([
                                'inline_keyboard'=>$key
                            ]),
                        ]);
                        return false;
                    }
                    $bot->request('sendMessage',[
                        'chat_id'=>$fromid,
                        'text'=>"<b>❗️Botdan foydalanishni davom ettirish uchun quyidagi kanalimizga obuna bo'ling👇🏼</b>",
                        'parse_mode'=>'html',
                        'reply_to_message_id'=>$miid,
                        'reply_markup'=>json_encode([
                            'inline_keyboard'=>$key
                        ]),
                    ]);
                    if (!is_null($mid)) {
                        $bot->request('editMessageText',[
                            'chat_id'=>$fromid,
                            'text'=>"<b>❗️Botdan foydalanishni davom ettirish uchun quyidagi kanalimizga obuna bo'ling👇🏼</b>",
                            'parse_mode'=>'html',
                            'message_id'=>$mid,
                            'reply_markup'=>json_encode([
                                'inline_keyboard'=>$key
                            ]),
                        ]);
                    }
                    return false;
                }
            }
        }
        return true;
    }

    function check($method,$datas = []) { 
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, 'https://matn.uz/api/v1/' . $method); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 
            'Content-Type: application/json', 
            'Authorization: Bearer ' . "vmTYSQIIyB8kUDAaNy33Asu4jjnQ5qXbsJcIehi7SOmoUmhvmdogxsTlKmM8c6W46AFweVlvflEs0VdK" 
        )); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $datas = str_replace([ 
            "ʻ", "’", "\n", "\t" 
        ], [ 
            "'", "'", " ", " " 
        ], $datas); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([ 
            "text" => json_encode($datas) 
        ]));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
        $result = curl_exec($ch); 
        curl_close($ch);
        return json_decode($result); 
    }

    function translate($source,$target,$text) {
        $url = "https://translate.google.com/translate_a/single?client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&hl=es-ES&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e";
        $fields = array(
            'sl'=>urlencode($source),
            'tl'=>urlencode($target),
            'q'=>urlencode($text)
        );
        $fields_string = "";
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'AndroidTranslate/5.3.0.RC02.130475354-53000263 5.1 phone TRANSLATE_OPM5_TEST_1');
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }
    function imgtotext($filename) { 
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://freeocrapi.com/api',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('file'=> new CURLFILE($filename))));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }
    function getUrlData($url)
    {
        $postdata = http_build_query(
            array(
                'key' => 'b35cd306637291724feda70d76db0f1ad00a9208a5438d98369c64a00125feaf',
                'url' => $url
            )
        );

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );

        $context  = stream_context_create($opts);
        $result = file_get_contents('http://alldownloader.komiljonovdev.uz/get', false, $context);
        $result = json_decode($result, true);
        return $result;
    }

    function getVideo($url,$apiURI="https://api.onlinevideoconverter.pro/api/convert") {
        $headers    = array();
        $headers[]  = 'origin: https://videodownloaderpro.net';
        $headers[]  = 'referer: https://videodownloaderpro.net/';
        $headers[]  = 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.99 Safari/537.36';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiURI);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'url'=>trim($url)
        ));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

    function TikTok($url) {
        $crl = curl_init();
        $headr = array();
        $headr[] = 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36';
        $headr[] = 'referer: https://lovetik.com/';
        $headr[] = 'origin: https://lovetik.com';
        curl_setopt($crl, CURLOPT_URL, "https://lovetik.com/api/ajax/search");
        curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, array('query'=>trim($url),'lang'=>"en"));
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        $rest = curl_exec($crl);
        $json = json_decode($rest);

        $js = [];
        $js['ok'] = true;
        $js['result']["download"] = $json->links[1]->a;
        $js['result']["urlaudio"] = $json->links[4]->a;
        $js['result']["cover"] = $json->cover;
        $js['result']["desc"] = $json->desc;
        $js['result']["autherUser"] = $json->author;
        $js['type'] = " - API 1";

        if($js['result']["download"] == null or $_GET["url"] == null){
            $jsj = [];
            $jsj['ok'] = false;
            return json_decode(json_encode($js));
        }else{
            return json_decode(json_encode($js));
        }
    }

    function isAdmin($fromid)
    {
        global $db;
        $user = $db->selectWhere('admins',[
            [
                'fromid'=>$fromid,
                'cn'=>'='
            ],
        ]);
        return (bool) mysqli_num_rows($user);
    }
?>