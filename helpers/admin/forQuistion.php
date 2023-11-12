<?php

if ($update) {
    if (isset($update->message)) {
        if (isAdmin($fromid)) {
            if (strtolower($text) == '/panel') {
                $bot->sendChatAction('typing', $fromid)->setInlineKeyBoard($panel)->sendMessage("Menyudan birini tanlang:");
                exit();
            }
            $admin = mysqli_fetch_assoc(
                $db->selectWhere('admins',[
                    [
                        'fromid'=>$fromid,
                        'cn'=>'='
                    ]
                ])
            );
            if ($admin['menu'] == 'add_FAQ' && $admin['step'] == '1'){
                if ($text){
                    $db->insertInto('faqs', [
                        'name'=>$text
                    ]);
                    $FAQ_id = mysqli_fetch_assoc($db->withSqlQuery("SELECT MAX(id) as id FROM faqs LIMIT 1"))['id'];
                    $db->updateWhere('admins',
                        [
                            'step'=>'2',
                            'data'=>$FAQ_id
                        ],
                        [
                            'fromid'=>$fromid,
                            'cn'=>'='
                        ]
                    );
                    $bot->sendChatAction('typing', $fromid)->sendMessage("Endi savol uchun javobni yozing.");
                }
                exit();
            }
            if ($admin['menu'] == 'add_FAQ' && $admin['step'] == '2'){
                if ($text){
                    $db->updateWhere('faqs',
                        [
                            'answer'=>$text
                        ],
                        [
                            'id'=>$admin['data'],
                            'cn'=>'='
                        ]
                    );
                    $db->updateWhere('admins',
                        [
                            'menu'=>'',
                            'step'=>''
                        ],
                        [
                            'fromid'=>$fromid,
                            'cn'=>'='
                        ]
                    );
                    $bot->sendChatAction('typing', $fromid)->sendMessage("Muvoffaqiyatli qo'shildi. O'chirish uchun link:\n\n/del_faq_" . $admin['data']);
                }
                exit();
            }

            if ($admin['menu'] == 'add_chapter' && $admin['step'] == '1'){
                if ($text){
                    $db->insertInto('quistion_chapters', [
                        'name'=>$text
                    ]);
                    $chapter_id = mysqli_fetch_assoc($db->withSqlQuery("SELECT MAX(id) as id FROM quistion_chapters LIMIT 1"))['id'];
                    $db->updateWhere('admins',
                        [
                            'menu'=>'',
                            'step'=>'',
                            'data'=>''
                        ],
                        [
                            'fromid'=>$fromid,
                            'cn'=>'='
                        ]
                    );
                    $bot->sendChatAction('typing', $fromid)->sendMessage("Muvoffaqiyatli qo'shildi. O'chirish uchun link:\n\n/del_quistion_chapter_" . $chapter_id);
                }
                exit();
            }
            if ($admin['menu'] == 'answering' && $admin['step'] == '1'){
                if ($text){
                    $db->updateWhere('quistions',
                        [
                            'answer'=>$text
                        ],
                        [
                            'id'=>$admin['data'],
                            'cn'=>'='
                        ]
                    );
                    $db->updateWhere('admins',
                        [
                            'menu'=>'',
                            'step'=>'',
                            'data'=>''
                        ],
                        [
                            'fromid'=>$fromid,
                            'cn'=>'='
                        ]
                    );
                    $quistion = mysqli_fetch_assoc(
                        $db->selectWhere('quistions',[
                            [
                                'id'=>$admin['data'],
                                'cn'=>'='
                            ]
                        ])
                    );
                    $bot->sendChatAction('typing', $quistion['chat_id'])->sendMessage($text);
                    $bot->sendChatAction('typing', $fromid)->sendMessage("Habar yuborildi.");
                }
                exit();
            }

            if (mb_stripos($text, "/start quistion_id_")!==false){
                $quistion_id = explode("/start quistion_id_", $text)[1];
                $db->updateWhere('admins',
                    [
                        'menu'=>'answering',
                        'step'=>'1',
                        'data'=>$quistion_id
                    ],
                    [
                        'fromid'=>$fromid,
                        'cn'=>'='
                    ]
                );
                $bot->sendChatAction('typing', $fromid)->sendMessage("O'z javobingizni yozing.");
                exit();
            }

            if (mb_stripos($text, "/del_faq_")!==false){
                $del_faq_id = explode("/del_faq_", $text)[1];
                $db->deleteWhere('faqs',[
                    [
                        'id'=>$del_faq_id,
                        'cn'=>'='
                    ]
                ]);
                $bot->sendChatAction('typing', $fromid)->sendMessage("Muvoffaqiyatli O'chirildi.");
                exit();
            }
            if (mb_stripos($text, "/del_quistion_chapter_")!==false){
                $quistion_chapter_id = explode("/del_quistion_chapter_", $text)[1];
                $db->deleteWhere('quistion_chapters',[
                    [
                        'id'=>$quistion_chapter_id,
                        'cn'=>'='
                    ]
                ]);
                $bot->sendChatAction('typing', $fromid)->sendMessage("Muvoffaqiyatli O'chirildi.");
                exit();
            }
        }
    }elseif (isset($update->callback_query)){
        if ($data == "add_FAQ"){
            $db->updateWhere('admins',
                [
                    'menu'=>'add_FAQ',
                    'step'=>'1'
                ],
                [
                    'fromid'=>$cbid,
                    'cn'=>'='
                ]
            );
            $bot->sendChatAction('typing', $cbid)->editMessageText("FAQ qo'shish. Savolni yozing.", $mid);
            exit();
        }
        if ($data == "add_chapter"){
            $db->updateWhere('admins',
                [
                    'menu'=>'add_chapter',
                    'step'=>'1'
                ],
                [
                    'fromid'=>$cbid,
                    'cn'=>'='
                ]
            );
            $bot->sendChatAction('typing', $cbid)->editMessageText("Savol bo'lim qo'shish. Bo'lim nomini yozing.", $mid);
            exit();
        }
    }
}

?>