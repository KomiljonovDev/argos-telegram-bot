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
            $bot->sendChatAction('typing', $fromid)->editMessageText("FAQ qo'shish. Savolni yozing.");
            exit();
        }
    }
}

?>