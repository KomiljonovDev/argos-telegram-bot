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

	if ($update) {
		if (isset($update->message)) {
			if ($type == 'private') {
				if (removeBotUserName($text) == "/start") {
					$myUser = myUser(['fromid','name','user','chat_type','lang','del'],[$fromid,$full_name,$user ?? null,'private','',0]);
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
                        $faq_keyboard[] = ['text'=>$i, 'callback_data'=>'quistion_' . $faq['id']];
                    }
                    $bot->sendChatAction('typing', $cbid)->sendMessage($FAQ_text);
                    exit();
                }
                if ($data == 'few'){
                    $bot->sendChatAction('typing', $cbid)->editMessageText("Other quistion chapter.", $mid);
                    exit();
                }
			}
		}
	}

	include 'helpers/admin/admin.php';
?>