<?php

/**
 *
 * @author https://github.com/KomiljonovDev/,
 * @author https://t.me/GoldCoderUz,
 * @author https://t.me/uzokdeveloper,
 * @author https://t.me/komiljonovdev,
 *
 */
ini_set('error_reporting', 1);
ini_set('display_errors', 1);

require 'Telegram/TelegramBot.php';
require 'db_config/db_config.php';
require 'helpers/functions.php';

use TelegramBot as Bot;

$token = "6816699025:AAGr-s99vKIrKFVwsa_q8NrTxSusvk5BSV4";
$dataSet = ['botToken'=>$token];

$bot = new Bot($dataSet);

require_once 'helpers/variebles.php';
$db = new db_config;

include 'helpers/sendMessageToUsers.php';

include 'helpers/activeUsers.php';
include 'helpers/admin/forQuistion.php.php';

if ($update) {
    if (isset($update->message)) {
        if ($type == 'private') {
            if (removeBotUserName($text) == "/start") {
                $myUser = myUser(['fromid','name','user','chat_type','lang','del'],[$fromid,$full_name,$user ?? null,'private',$lang_code,0]);
            }
            if (channel($fromid)) {
                $user = mysqli_fetch_assoc(
                    $db->selectWhere('users',[
                        [
                            'fromid'=>$fromid,
                            'cn'=>'='
                        ]
                    ])
                );
                $user_data = json_decode($user['data']);
                if (removeBotUserName($text) == "/start") {
                    $db->updateWhere('users',
                        [
                            'data'=>'register',
                            'step'=>1
                        ],
                        [
                            'fromid'=>$fromid,
                            'cn'=>'='
                        ]
                    );
                    if ($myUser) {
                        $bot->sendChatAction('typing', $chat_id)->sendMessage("<b>Assalomu alaykum, " . $full_name ."\nRo'yxatdan o'tish boshlandi.\n\nF.I.SH ni kiriting.\nQuyidagi formatda: Komiljonov Obidjon Komiljon O'g'li</b>");
                        exit();
                    }
                    $bot->sendChatAction('typing', $chat_id)->sendMessage("<b>Assalomu alaykum, " . $full_name ."\nRo'yxatdan o'tish boshlandi.\n\nF.I.SH ni kiriting.\nQuyidagi formatda: Komiljonov Obidjon Komiljon O'g'li</b>");
                    exit();
                }
                if ($user['data'] == 'register' && $user['step'] == '1') {
                    if ($text) {
                        $db->updateWhere('users',
                            [
                                'step'=>2,
                                'full_name'=>$text
                            ],
                            [
                                'fromid'=>$fromid,
                                'cn'=>'='
                            ]
                        );

                        $bot->sendChatAction('typing', $fromid)->sendMessage("Bog'lanish uchun telefon raqamingizni yuboring:");
                    }
                }
                if ($user['data'] == 'register' && $user['step'] == '2') {
                    if ($text) {
                        $db->updateWhere('users',
                            [
                                'step'=>3,
                                'phone'=>$text
                            ],
                            [
                                'fromid'=>$fromid,
                                'cn'=>'='
                            ]
                        );
                        $bot->sendChatAction('typing', $fromid)->setInlineKeyBoard($regions)->sendMessage("Viloyatingizni tanlang:");
                    }
                }
                if ($user['data'] == 'quistion' && $user['step'] == '2') {
                    if ($text) {
                        $bot->sendChatAction('typing', $fromid)->sendMessage("Habaringiz adminlarimizga yuborildi. Javobini kuting.");
                        $sended = $bot->request('copyMessage',[
                            'chat_id'=>$quistion_channel_id,
                            'from_chat_id'=>$fromid,
                            'message_id'=>$miid,
                            'reply_markup'=>json_encode([
                                'inline_keyboard'=>[
                                    [
                                        ['text'=>'Javob berish', 'url'=>'https://t.me/ArgosMurojaat_Bot?start=quistion_id_' . $user['quistion_id']]
                                    ]
                                ]
                            ])
                        ]);
                        $db->updateWhere('quistions',
                            [
                                'quistion'=>$text,
                                'channel_id'=>$quistion_channel_id,
                                'message_id'=>$sended->result->message_id
                            ],
                            [
                                'id'=>$user['quistion_id'],
                                'cn'=>'='
                            ]
                        );
                        $bot->sendChatAction('typing', $fromid)->sendMessage(json_encode($sended));
                    }
                }
            }
        }else{
            if (removeBotUserName($text) == "/start") {
                $myUser = myUser(['fromid','name','user','chat_type','lang','del'],[$fromid,$full_name,$user,'group','',0]);

                if ($myUser) {
                    $bot->sendChatAction('typing', $chat_id)->sendMessage('Assalomu alaykum, xush kelibsiz!');
                    exit();
                }
                $bot->sendChatAction('typing', $chat_id)->sendMessage('Assalomu alaykum, qayata xush kelibsiz!');
                exit();
            }
        }
    }
    else if (isset($update->callback_query)) {
        if (channel($cbid)) {
            $user = mysqli_fetch_assoc(
                $db->selectWhere('users',[
                    [
                        'fromid'=>$cbid,
                        'cn'=>'='
                    ]
                ])
            );
            if ($data == 'res') {
                $bot->sendChatAction('typing', $cbid)->editMessageText("Assalomu alaykum, xush kelibsiz!", $mid);
                exit();
            }
            if (mb_stripos($data, "region_")!==false && $user['data'] == 'register' && $user['step'] == 3) {
                $region = explode("region_", $data)[1];
                $db->updateWhere('users',
                    [
                        'step'=>4,
                        'region'=>ucwords($region)
                    ],
                    [
                        'fromid'=>$cbid,
                        'cn'=>'='
                    ]
                );
                $bot->sendChatAction('typing', $cbid)->setInlineKeyBoard(array_chunk($districts[$region],2))->editMessageText("Tumanni tanlang:", $mid);
                exit();
            }
            if (mb_stripos($data, "district_")!==false && $user['data'] == 'register' && $user['step'] == 4) {
                $district = explode("district_", $data)[1];
                $db->updateWhere('users',
                    [
                        'step'=>5,
                        'district'=>ucwords($district)
                    ],
                    [
                        'fromid'=>$cbid,
                        'cn'=>'='
                    ]
                );
                $bot->sendChatAction('typing', $cbid)->setInlineKeyBoard($answer_option)->editMessageText("Tuman tanlandi. Savolingiz yo'nalishini tanlang:", $mid);
                exit();
            }
            if ($data == 'FAQ'){
                $db->updateWhere('users',
                    [
                        'step'=>0,
                        'data'=>''
                    ],
                    [
                        'fromid'=>$cbid,
                        'cn'=>'='
                    ]
                );
                $FAQs = $db->selectWhere('faqs',[
                    [
                        'id'=>1,
                        'cn'=>'>='
                    ]
                ]);
                $i = 0;
                $FAQ_text = "Tez-tez beriladigan savollar:\n";
                $faq_keyboard = [];
                foreach ($FAQs as $faq){
                    $i++;
                    $FAQ_text .= "\n" . $i . ") " . $faq['name'];
                    $faq_keyboard[] = ['text'=>$i, 'callback_data'=>'faq_quistion_' . $faq['id']];
                }
                $bot->sendChatAction('typing', $cbid)->setInlineKeyBoard(array_chunk($faq_keyboard,5))->editMessageText($FAQ_text, $mid);
                exit();
            }
            if ($data == 'other_question'){
                $db->updateWhere('users',
                    [
                        'data'=>'quistion',
                        'step'=>1
                    ],
                    [
                        'fromid'=>$cbid,
                        'cn'=>'='
                    ]
                );
                $other_chapters = $db->selectWhere('quistion_chapters',[
                    [
                        'id'=>1,
                        'cn'=>'>='
                    ]
                ]);
                $i = 0;
                $chapter_text = "Savolingiz qaysi bo'limga yaqinroq? Agar topa olmasangiz boshqa tugmasini bosing.\n";
                $faq_keyboard = [];
                foreach ($other_chapters as $other_chapter){
                    $i++;
                    $chapter_text .= "\n" . $i . ") " . $other_chapter['name'];
                    $chapter_keyboard[] = ['text'=>$i, 'callback_data'=>'chapter_quistion_' . $other_chapter['id']];
                }
                $chapter_keyboard = array_chunk($chapter_keyboard,5);
                $chapter_keyboard[][] = ['text'=>"Boshqa", 'callback_data'=>'other_other_quistion'];
                $bot->sendChatAction('typing', $cbid)->setInlineKeyBoard($chapter_keyboard)->editMessageText($chapter_text, $mid);
                exit();
            }
            if ((mb_stripos($data, "chapter_quistion_")!==false || $data == "other_other_quistion") && $user['data'] == 'quistion' && $user['step'] == 1) {
                $quistion_chapter_id = explode("chapter_quistion_", $data)[1] ?? 'other';
                $db->insertInto('quistions',[
                    'quistion_chapters_id'=>$quistion_chapter_id,
                    'chat_id'=>$cbid
                ]);
                $quistion_id = mysqli_fetch_assoc($db->withSqlQuery("SELECT MAX(id) as id FROM quistions LIMIT 1"))['id'];
                $db->updateWhere('users',
                    [
                        'step'=>2,
                        'quistion_id'=>$quistion_id
                    ],
                    [
                        'fromid'=>$cbid,
                        'cn'=>'='
                    ]
                );
                $bot->sendChatAction('typing', $cbid)->editMessageText("Savolingizni yozib qoldiring. Marhamat", $mid);
                exit();
            }
        }
    }
}

include 'helpers/admin/admin.php';
?>