<?php defined('IN_TS') or die('Access Denied.');switch($ts){		case "":				$reviewid = intval($_GET['reviewid']);		//$new['apple']->isReview();				$strReview = $db->once_fetch_assoc("select * from ".dbprefix."apple_review where `reviewid`='$reviewid'");		$strReview['user'] = aac('user')->getOneUser($strReview['userid']);				//苹果机		$strApple = $db->once_fetch_assoc("select * from ".dbprefix."apple where `appleid`='".$strReview['appleid']."'");		$index = $db->fetch_all_assoc("select * from ".dbprefix."apple_index where `appleid`='".$strReview['appleid']."'");		foreach($index as $key=>$item){			$strApple['model'][] = array(				'virtuename'	=> $new['apple']->getVirtueName($item['virtueid']),				'parameter'	=> $item['parameter'],			);		}				$title = $strReview['title'];		include template('review');		break;			//添加点评	case "add":		$userid = intval($TS_USER['user']['userid']);				if($userid == 0){			header("Location: ".SITE_URL.tsurl('user','login'));			exit;		}				$appleid = intval($_GET['appleid']);				$new['apple']->isApple($appleid);				$title = '添加点评';		include template('review_add');				break;			case "add_do":		$userid = intval($TS_USER['user']['userid']);				if($userid == 0){			header("Location: ".SITE_URL);			exit;		}				$appleid = intval($_POST['appleid']);		$new['apple']->isApple($appleid);				$title = trim($_POST['title']);		$content = trim($_POST['content']);				if($title=='' || $content == ''){			header("Location: ".SITE_URL);			exit;		}				$addtime = time();				$db->query("insert into ".dbprefix."apple_review (`appleid`,`userid`,`title`,`content`,`addtime`) values ('$appleid','$userid','$title','$content','$addtime')");				$reviewid=$db->insert_id();				header("Location: ".SITE_URL.tsurl('apple','review',array('reviewid'=>$reviewid)));				break;		//修改点评 	case "edit":		$reviewid = intval($_GET['reviewid']);				$userid = intval($TS_USER['user']['userid']);				if($userid == 0){			header("Location: ".SITE_URL);			exit;		}				$new['apple']->isReview($reviewid);		$strReview = $db->once_fetch_assoc("select * from ".dbprefix."apple_review where `reviewid`='$reviewid'");				if($strReview['userid'] != $userid || intval($TS_USER['user']['isadmin'])!=1){			header("Location: ".SITE_URL);			eixt;		}				$title = '修改点评';		include template('review_edit');				break;			case "edit_do":		$reviewid = intval($_POST['reviewid']);				$userid = intval($TS_USER['user']['userid']);				if($userid == 0){			header("Location: ".SITE_URL);			exit;		}				$new['apple']->isReview($reviewid);				$title = trim($_POST['title']);		$content = trim($_POST['content']);				$db->query("update ".dbprefix."apple_review set `title`='$title',`content`='$content' where `reviewid`='$reviewid'");				header("Location: ".SITE_URL.tsurl('apple','review',array('reviewid'=>$reviewid)));				break;		//删除点评 	case "del":		$reviewid = intval($_GET['reviewid']);				$userid = intval($TS_USER['user']['userid']);				if($userid == 0){			header("Location: ".SITE_URL);			exit;		}				$new['apple']->isReview($reviewid);				$strReview = $db->once_fetch_assoc("select * from ".dbprefix."apple_review where `reviewid`='$reviewid'");				if($strReview['userid']==$userid || intval($TS_USER['user']['isadmin'])==1){			$db->query("delete from ".dbprefix."apple_review where `reviewid`='$reviewid'");		}				header("Location: ".SITE_URL.tsurl('apple','show',array('appleid'=>$strReview['appleid'])));				break;			//点评列表 	case "list":		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;		$url = SITE_URL.tsurl('apple','review',array('ts'=>'list','page'=>''));		$lstart = $page*20-20;				$arrReviews = $db->fetch_all_assoc("select * from ".dbprefix."apple_review order by addtime desc limit $lstart,20");				$reviewNum = $db->once_fetch_assoc("select count(*) from ".dbprefix."apple_review");		$pageUrl = pagination($reviewNum['count(*)'], 20, $page, $url);				foreach($arrReviews as $key=>$item){			$arrReview[] = $item;			$arrReview[$key]['content'] = getsubstrutf8(t($item['content']),0,130);			$arrReview[$key]['user'] = aac('user')->getOneUser($item['userid']);			$arrReview[$key]['apple'] = $new['apple']->getApple($item['appleid']);		}				$title = '点评';				include template('review_list');				break;	}