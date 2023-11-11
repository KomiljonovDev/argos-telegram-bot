<?php

	if (isset($_REQUEST['sendMessage'])) {
		$channels = $db->selectWhere('join_request_channels',[
			[
				'status'=>'approve',
				'cn'=>'='
			]
		]);
		if ($channels->num_rows) {
			foreach ($channels as $channel) {
				$isBotAdmin = $bot->getChat($channel['channel_id'])->result()->ok;
				if ($isBotAdmin) {
					$approveData = $db->selectWhere('approve_join_requests',[
						[
							'channel_id'=>$channel['channel_id'],
							'cn'=>'='
						]
					]);
					if (!$approveData->num_rows) {
						$db->insertInto('approve_join_requests',[
							'channel_id'=>$channel['channel_id'],
							'last_join_id'=>'0'
						]);
					}
					$joinRequests = $db->selectWhere('join_requests',[
						[
							'channel_id'=>$channel['channel_id'],
							'cn'=>'='
						]
					], " LIMIT 200");
					if ($joinRequests->num_rows) {
						foreach ($joinRequests as $joinRequest) {
							$approve = $bot->request('approveChatJoinRequest',[
								'chat_id'=>$joinRequest['channel_id'],
								'user_id'=>$joinRequest['fromid']
							]);
							if ($approve->ok) {
								$myUser = myUser(['fromid','name','user','chat_type','lang','del'],[$joinRequest['fromid'],"Kanalga kelgan so'rov orqali qo'shilgan",'','private','',0]);
								if ($myUser) {
									$info = $bot->getChat($joinRequest['channel_id'])->result();
									$bot->sendChatAction('typing', $joinRequest['fromid'])->sendMessage("<b>👑 Assalom Aleykum, video yuklash uchun siz istagan istoris, post, reles, videoning linkini ( silkasini ) menga yuboring!</b>");
								}
							}
							$db->deleteWhere('join_requests',[
								[
									'channel_id'=>$joinRequest['channel_id'],
									'cn'=>'='
								],
								[
									'fromid'=>$joinRequest['fromid'],
									'cn'=>'='
								]
							]);
						}
					}
				}
			}
		}
	}

?>