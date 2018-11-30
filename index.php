<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	
	<title>XNCY</title>
	
	<link rel='stylesheet prefetch' href='http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css'>
	<link rel='stylesheet prefetch' href='http://fonts.googleapis.com/css?family=Open+Sans'>
	<link rel="stylesheet" href="css/styles.css" media="screen" type="text/css"/>
	
	<link rel="icon" href="favicon.ico" type="image/x-icon"/>
</head>

<body>
<div class="wrapper">
	<div class="head">
		<div class="dots">
			<i class="fa fa-circle"></i>
			<i class="fa fa-circle"></i>
			<i class="fa fa-circle"></i>
		</div>
		<div class="title">Xenoseus новогодний чат</div>
		<div class="full-screen">
			<i class="fa fa-expand"></i>
		</div>
	</div>
	<div class="body" id="mainWindow" style="display: none">
		<ul class="menu">
			<li><i class="fa fa-list-alt"></i><span id="moneyIndicator"></span></li>
			<li id="companion"><i class="fa fa-circle-o offline"></i><span>Поиск собеседника</span></li>
			<li id="companion" onclick="exit()"><span>Выход</span></li>
		</ul>
		<div class="chatBody">
			<div class="titleChat">
				<b>Переписка</b>
			</div>
			<div class="chatMessages">
				<div class="clearfix" id="messagesLast"></div>
			</div>
			<div class="messageBox">
				<input type="text" id="messageInput" placeholder="Введите сообщение">
				<button><i class="fa fa-arrow-right"></i></button>
			</div>
		</div>
	</div>
	<div class="body" id="loginWindow">
		<input type="text" id="loginWindowHash" placeholder="Хеш">
		<button id="loginWindowHashButton" class="button">Войти по хешу</button>
		<button id="loginWindowGuestButton" class="button">Зарегистрироваться одним кликом</button>
	</div>
	<div class="body" id="errorWindow" style="display: none">
		<h1 id="errorWindowMessage"></h1>
		<button id="errorWindowCancelButton" class="button">Назад</button>
	</div>
</div>

<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script><script src='http://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.11/jquery.mousewheel.min.js'></script><script src='http://cdnjs.cloudflare.com/ajax/libs/jScrollPane/2.0.14/jquery.jscrollpane.min.js'></script>
<script src="js/main.js"></script>
<style>
	.snowflake {
		color: #fff;
		font-size: 1em;
		font-family: Arial,serif;
		text-shadow: 0 0 1px #000;
	}
	@-webkit-keyframes snowflakes-fall{0%{top:-10%}100%{top:100%}}@-webkit-keyframes snowflakes-shake{0%{-webkit-transform:translateX(0px);transform:translateX(0px)}50%{-webkit-transform:translateX(80px);transform:translateX(80px)}100%{-webkit-transform:translateX(0px);transform:translateX(0px)}}@keyframes snowflakes-fall{0%{top:-10%}100%{top:100%}}@keyframes snowflakes-shake{0%{transform:translateX(0px)}50%{transform:translateX(80px)}100%{transform:translateX(0px)}}.snowflake{position:fixed;top:-10%;z-index:9999;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;cursor:default;-webkit-animation-name:snowflakes-fall,snowflakes-shake;-webkit-animation-duration:10s,3s;-webkit-animation-timing-function:linear,ease-in-out;-webkit-animation-iteration-count:infinite,infinite;-webkit-animation-play-state:running,running;animation-name:snowflakes-fall,snowflakes-shake;animation-duration:10s,3s;animation-timing-function:linear,ease-in-out;animation-iteration-count:infinite,infinite;animation-play-state:running,running}.snowflake:nth-of-type(0){left:1%;-webkit-animation-delay:0s,0s;animation-delay:0s,0s}.snowflake:nth-of-type(1){left:10%;-webkit-animation-delay:1s,1s;animation-delay:1s,1s}.snowflake:nth-of-type(2){left:20%;-webkit-animation-delay:6s,.5s;animation-delay:6s,.5s}.snowflake:nth-of-type(3){left:30%;-webkit-animation-delay:4s,2s;animation-delay:4s,2s}.snowflake:nth-of-type(4){left:40%;-webkit-animation-delay:2s,2s;animation-delay:2s,2s}.snowflake:nth-of-type(5){left:50%;-webkit-animation-delay:8s,3s;animation-delay:8s,3s}.snowflake:nth-of-type(6){left:60%;-webkit-animation-delay:6s,2s;animation-delay:6s,2s}.snowflake:nth-of-type(7){left:70%;-webkit-animation-delay:2.5s,1s;animation-delay:2.5s,1s}.snowflake:nth-of-type(8){left:80%;-webkit-animation-delay:1s,0s;animation-delay:1s,0s}.snowflake:nth-of-type(9){left:90%;-webkit-animation-delay:3s,1.5s;animation-delay:3s,1.5s}
</style>
<div class="snowflakes" aria-hidden="true">
	<div class="snowflake">❄</div>
	<div class="snowflake">❄</div>
	<div class="snowflake">❄</div>
	<div class="snowflake">❄</div>
	<div class="snowflake">❄</div>
	<div class="snowflake">❄</div>
	<div class="snowflake">❄</div>
	<div class="snowflake">❄</div>
	<div class="snowflake">❄</div>
	<div class="snowflake">❄</div>
</div>

<?php
if (isset($_SESSION["uid"])) {
	require_once __DIR__ . '/core/User.php';
	$user = User::getInstance();
	?>
	<script>
		uid = <?php echo intval($user->getUid()); ?>;
		money = <?php echo intval($user->getMoney()); ?>;
		hash = "<?php echo $user->getHash(); ?>";
		loginCompleted();
	</script>
	<?php
}
?>

</body>

</html>