<?php

$update = json_decode(file_get_contents('php://input'));
if (isset($update)) {
    if (isset($update->message)) {
        $message = $update->message;
        $chat_id = $message->chat->id;
        $type = $message->chat->type;
        $miid =$message->message_id;
        $name = $message->from->first_name;
        $lname = $message->from->last_name;
        $full_name = $name . " " . $lname;
        $full_name = html($full_name);
        $user = $message->from->username ?? '';
        $fromid = $message->from->id;
        $lang_code = $message->from->language_code;
        $text = html($message->text);
        $title = $message->chat->title;
        $chatuser = $message->chat->username;
        $chatuser = $chatuser ? $chatuser : "Shaxsiy Guruh!";
        $caption = $message->caption;
        $entities = $message->entities;
        $entities = $entities[0];
        $left_chat_member = $message->left_chat_member;
        $new_chat_member = $message->new_chat_member;
        $photo = $message->photo;
        $video = $message->video;
        $audio = $message->audio;
        $voice = $message->voice;
        $reply = $message->reply_markup;
        $fchat_id = $message->forward_from_chat->id;
        $fid = $message->forward_from_message_id;
    }else if(isset($update->callback_query)){
        $callback = $update->callback_query;
        $qid = $callback->id;
        $mes = $callback->message;
        $mid = $mes->message_id;
        $cmtx = $mes->text;
        $cid = $callback->message->chat->id;
        $ctype = $callback->message->chat->type;
        $cbid = $callback->from->id;
        $cbuser = $callback->from->username;
        $data = $callback->data;
    }
}

$regions  = array_chunk([
    ['text'=>"Andijon", 'callback_data'=>'region_andijon'],
    ['text'=>"Namangan", 'callback_data'=>'region_namangan'],
    ['text'=>"Farg'ona", 'callback_data'=>'region_fargona'],
    ['text'=>"Toshkent Viloyati", 'callback_data'=>'region_toshkentv'],
    ['text'=>"Toshkent Shahar", 'callback_data'=>'region_toshkentsh'],
    ['text'=>"Buxoro", 'callback_data'=>'region_buxoro'],
    ['text'=>"Jizzax", 'callback_data'=>'region_jizzax'],
    ['text'=>"Navoiy", 'callback_data'=>'region_navoiy'],
    ['text'=>"Qashqadaryo", 'callback_data'=>'region_qashqadaryo'],
    ['text'=>"Samarqand", 'callback_data'=>'region_samarqand'],
    ['text'=>"Sirdaryo", 'callback_data'=>'region_sardaryo'],
    ['text'=>"Surxandaryo", 'callback_data'=>'region_surxandaryo'],
    ['text'=>"Xorazm", 'callback_data'=>'region_xorazm'],
    ['text'=>"Qoraqalpoq", 'callback_data'=>'region_qoraqalpoq']
], 2);

$districts  = [
    "namangan"=>[
        ['text'=>"Namangan Shahar", 'callback_data'=>'district_namangansh'],
        ['text'=>"Yangi Namangan tumani", 'callback_data'=>'district_namangany'],
        ['text'=>"Eski Namangan tumani", 'callback_data'=>'district_namangane'],
        ['text'=>"Davlatobod tumani", 'callback_data'=>'district_davlatobod'],
        ['text'=>"Kosonsoy tumani", 'callback_data'=>'district_kosonsoy'],
        ['text'=>"Uychi tumani", 'callback_data'=>'district_uychi'],
        ['text'=>"Uchqo'rg'on tumani", 'callback_data'=>'district_uchqorgon'],
        ['text'=>"Yangiqo'rg'on tumani", 'callback_data'=>'district_yangiqorgon'],
        ['text'=>"To'raqo'rg'on tumani", 'callback_data'=>'district_toraqorgon'],
        ['text'=>"Chust tumani", 'callback_data'=>'district_chust'],
        ['text'=>"Pop tumani", 'callback_data'=>'district_pop'],
        ['text'=>"Chortoq tumani", 'callback_data'=>'district_chortoq'],
        ['text'=>"Mingbuloq tumani", 'callback_data'=>'district_mingbuloq'],
        ['text'=>"Norin tumani", 'callback_data'=>'district_norin']
    ]
];
$answer_option = [
   [
       ['text'=>"Ko'p beriladigan savollar", 'callback_data'=>'FAQ'],
   ],
    [
        ['text'=>"Boshqa", 'callback_data'=>'other_question']
    ]
];
$resize_keyoard = [
    [
        ['text'=>"✅ Yuborish"],
        ['text'=>"✍️ Tahrirlash"],
    ]
];
    
?>