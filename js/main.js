const loginWindow = $("#loginWindow");
const errorWindow = $("#errorWindow");
const mainWindow = $("#mainWindow");

let activeWindow = loginWindow;
let allWindows = [
	loginWindow,
	errorWindow,
	mainWindow
];

/**
 * Устанавливает текущее активное окно
 * @param window активное окно
 */
function setActiveWindow(window) {
	allWindows.forEach(function(item) {
		item.hide();
	});
	
	activeWindow = window;
	activeWindow.show();
	
	if (activeWindow === loginWindow) {
		$("#loginWindowHash").focus();
	}
}

// Ссылка на окно, которое было активно до отображения ошибки
let beforeErrorWindow;

/**
 * Показать ошибку на экране
 * @param message текст ошибки
 */
function error(message) {
	$("#errorWindowMessage").html(message);
	beforeErrorWindow = activeWindow;
	setActiveWindow(errorWindow);
}

/**
 * Слушатель окончания входа в чат<br>
 * Инициализирует основные параметры чата
 */
function loginCompleted() {
	setActiveWindow(mainWindow);
	
	$("#moneyIndicator").html("Монет: " + money);
	addMessage("[Система]<br>Ваш хеш равен:<br>" + hash + "<br>Для помощи наберите /help", false);
	
	$.ajax({
		url: '/ajax/postlogin.php',
		method: 'post',
		success: function(data) {
			if (data.state === 0) {
				error(data.message);
			} else {
				longPoll();
			}
		},
		error: function (jqXHR, exception) {
			error(exception);
		}
	});
}

/**
 * Добавить сообщение в чат
 * @param text текст сообщения
 * @param self я ли отправитель этого сообщения?
 */
function addMessage(text, self) {
	let date = new Date();
	let time = date.toLocaleTimeString();
	if (self) {
		$("#messagesLast").before(
			"<div class=\"divMessage\"><div class=\"message-main-sender\"><div class=\"message-text\">" + text + "</div><span class=\"message-time pull-right\">" + time + "</span></div></div>"
		);
	} else {
		$("#messagesLast").before(
			"<div class=\"divMessage\"><div class=\"message\"><div class=\"message-text\">" + text + "</div><span class=\"message-time pull-right\">" + time + "</span></div></div>"
		);
	}
	
	$('.chatMessages').animate({
		scrollTop: 100000
	}, 500);
}

/**
 * Запуск и обработка результата LongPoll
 */
function longPoll() {
	$.ajax({
		url: '/ajax/longpoll.php',
		method: 'post',
		success: function(data) {
			if (data.state === 0) {
				error(data.message);
			} else if (data.state === 2) {
				let messages = JSON.parse(data.message);
				
				for (let index in messages) {
					// noinspection JSUnfilteredForInLoop
					let item = JSON.parse(messages[index]);
					
					if (item.type === 1) {
						let companion = $("#companion");
						companion.find("i").removeClass("offline");
						companion.find("i").addClass("online");
						companion.find("span").html("Собеседник");
					} else if (item.type === 0) {
						addMessage(item.message, false);
					} else if (item.type === 5) {
						addMessage(item.message, true);
					} else if (item.type === 2) {
						let companion = $("#companion");
						companion.find("i").removeClass("online");
						companion.find("i").addClass("offline");
						companion.find("span").html("Собеседник вышел из сети");
						error(item.message);
					} else if (item.type === 3) {
						$("#moneyIndicator").html("Монет: " + item.money);
					}
				}
			}
			longPoll();
		},
		error: function (jqXHR, exception) {
			error(exception);
		}
	});
}

// Флаг блокировки чата для предотвращения спама сообщениями
let chatTimeoutBlocked = false;

/**
 * Отправка сообщения в чат (текст берётся из поля для ввода)
 */
function sendMessage() {
	if (chatTimeoutBlocked) {
		return;
	}
	chatTimeoutBlocked = true;
	
	let input = $("#messageInput");
	let text = input.val();
	input.val('');
	addMessage(text, true);
	
	$.ajax({
		url: '/ajax/message.php',
		method: 'post',
		data: {
			"message": text
		},
		error: function (jqXHR, exception) {
			error(exception);
		}
	});
	
	setTimeout(function() {
		chatTimeoutBlocked = false;
	}, 1000);
}
$('#messageInput').on('keypress', function (e) {
	if (e.which === 13) {
		sendMessage();
	}
});

/**
 * Завершение сессии и выход в главное меню
 */
function exit() {
	uid = -1;
	$.ajax({
		url: '/ajax/logout.php',
		method: 'post',
		success: function(data) {
			window.location.reload();
		}
	});
}


//=========USER=============
let uid = -1;
let hash = "";
let money = 0;
//==========================

$("#errorWindowCancelButton").click(function () {
	setActiveWindow(beforeErrorWindow);
});

/**
 * Обработчик результата входа в систему
 * @param data объект результата
 */
function processLoginResponse(data) {
	if (data.state === 1) {
		uid = data.uid;
		money = data.money;
		hash = data.hash;
		loginCompleted();
	} else {
		error(data.message);
	}
}

$("#loginWindowGuestButton:not(.disabled)").click(function() {
	$.ajax({
		url: '/ajax/guest.php',
		method: 'post',
		success: function(data) {
			processLoginResponse(data);
		}
	});
});

$('#loginWindowHash').on('keypress', function (e) {
	if (e.which === 13) {
		loginByHash();
	}
});
$("#loginWindowHashButton:not(.disabled)").click(function() {
	loginByHash();
});

/**
 * Вход по хешу уже созданного аккаунта
 */
function loginByHash() {
	const hash = $("#loginWindowHash").val();
	
	$.ajax({
		url: '/ajax/login.php',
		method: 'post',
		data: {
			"hash": hash
		},
		success: function(data) {
			processLoginResponse(data);
		}
	});
}




setActiveWindow(loginWindow);