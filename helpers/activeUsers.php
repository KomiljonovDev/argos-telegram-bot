<?php

	if ($update) {
		if ($update->message) {
			if ($type == 'private') {
				$check_activated = $db->selectWhere('active_users',[
					[
						'fromid'=>$fromid,
						'cn'=>'='
					]
				]);
				if (!$check_activated->num_rows) {
					$db->insertInto('active_users',[
						'fromid'=>$fromid
					]);
				}
			}
		}else if($update->callback_query){
			$check_activated = $db->selectWhere('active_users',[
				[
					'fromid'=>$cbid,
					'cn'=>'='
				]
			]);
			if (!$check_activated->num_rows) {
				$db->insertInto('active_users',[
					'fromid'=>$cbid
				]);
			}
		}
	}

?>