<?php
	if (isset($update->chat_join_request)) {
		$join_request = $update->chat_join_request;
		$channel = $db->selectWhere('join_request_channels',[
			[
				'channel_id'=>$join_request->chat->id,
				'cn'=>'='
			]
		]);
		if (!$channel->num_rows) {
			$db->insertInto('join_request_channels',[
				'channel_id'=>$join_request->chat->id,
				'status'=>'stopped'
			]);
			$join_user = $db->selectWhere('join_requests',[
				[
					'channel_id'=>$join_request->chat->id,
					'cn'=>'='
				],[
					'fromid'=>$join_request->user_chat_id,
					'cn'=>'='
				]
			]);
			if (!$join_user->num_rows) {
				$db->insertInto('join_requests',[
					'channel_id'=>$join_request->chat->id,
					'fromid'=>$join_request->user_chat_id
				]);
			}
		}else{
			$channel = mysqli_fetch_assoc($channel);
			if ($channel['status'] == 'approve') {
				$approve = $bot->request('approveChatJoinRequest',[
					'chat_id'=>$join_request->chat->id,
					'user_id'=>$join_request->user_chat_id
				]);
				if ($approve->ok) {
					$myUser = myUser(['fromid','name','user','chat_type','lang','del'],[$join_request->user_chat_id,$join_request->from->first_name,$join_request->from->username,'private','',0]);
					if ($myUser) {
						$info = $bot->getChat($join_request->chat->id)->result();
						$bot->sendChatAction('typing', $join_request->user_chat_id)->sendMessage("Assalomu alaykum, siz <a href='" . $info->result->invite_link . "'>" . $info->result->title . "</a> kanaliga obuna bo'ldingiz.");
						exit();
					}
				}
			}else{
				$join_user = $db->selectWhere('join_requests',[
					[
						'channel_id'=>$join_request->chat->id,
						'cn'=>'='
					],[
						'fromid'=>$join_request->user_chat_id,
						'cn'=>'='
					]
				]);
				if (!$join_user->num_rows) {
					$db->insertInto('join_requests',[
						'channel_id'=>$join_request->chat->id,
						'fromid'=>$join_request->user_chat_id
					]);
				}
			}
			exit();
		}
	}
	if (isset($update->channel_post)) {
		$channel_post = $update->channel_post;
		$bot->setChatId($channel_post->chat->id);
		if (isset($channel_post->text)) {
			if ($channel_post->text == '/accept') {
				$bot->deleteMessage($channel_post->message_id);
				$channel = $db->selectWhere('join_request_channels',[
					[
						'channel_id'=>$channel_post->chat->id,
						'cn'=>'='
					]
				]);
				if (!$channel->num_rows) {
					$db->insertInto('join_request_channels',[
						'channel_id'=>$channel_post->chat->id,
						'status'=>'approve'
					]);
				}else{
					$db->updateWhere('join_request_channels',
						[
							'status'=>'approve'
						],[
							'channel_id'=>$channel_post->chat->id,
							'cn'=>'='
						]
					);
				}
				$bot->sendMessage("Kanalga kelgan qo'shilish so'rovlari qabul qilinadi...\n\nQabul qilishni to'xtatish /stop buyru'gi orqali.");
				sleep(1);
				$bot->deleteMessage($channel_post->message_id+1);
				exit();
			}
			if ($channel_post->text == '/stop') {
				$bot->deleteMessage($channel_post->message_id);
				$channel = $db->selectWhere('join_request_channels',[
					[
						'channel_id'=>$channel_post->chat->id,
						'cn'=>'='
					]
				]);
				if (!$channel->num_rows) {
					$db->insertInto('join_request_channels',[
						'channel_id'=>$channel_post->chat->id,
						'status'=>'stopped'
					]);
				}else{
					$db->updateWhere('join_request_channels',
						[
							'status'=>'stopped'
						],[
							'channel_id'=>$channel_post->chat->id,
							'cn'=>'='
						]
					);
				}
				$bot->sendMessage("Kanalga kelgan qo'shilish so'rovlari qabul qilinmaydi...\n\nQabul qilish /accept buyru'gi orqali.");
				sleep(1);
				$bot->deleteMessage($channel_post->message_id+1);
				exit();
			}
		}
	}
?>