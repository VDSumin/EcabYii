let interval = null;
let timeout = null;

async function activateBell() {
    let count = await getNotificationCount();
    let timerId = null;
    if (count) {
        timerId = await switchNotificationBell(1500, count);
        interval = timerId;
    }
    timeout = setTimeout(() => {
        if (timerId)
            clearInterval(timerId);
        activateBell();
    }, 1 * 60 * 1000);
}

function disableNotesUpdate() {
    clearInterval(interval);
    clearTimeout(timeout);
}

async function getNotificationCount() {
    let response = null;
    response = await fetch('/index.php?r=notification/note/count');
    if (response.ok) {
        let json = await response.json();
        return json['count'];
    }
}

async function switchNotificationBell(timeout, count) {
    function getCountSpan() {
        return `<span id="notify_span" class="notify_span">${count}</span>`
    }

    return setInterval(() => {
        document.getElementById('notificationLink').innerHTML = `<img style="width: 20px" src="/themes/bootstrap/images/notify-bell-yellow-2.png">` + getCountSpan();
        setTimeout(() => {
            document.getElementById('notificationLink').innerHTML = '<img style="width: 20px" src="/themes/bootstrap/images/notify-bell.svg">' + getCountSpan();
        }, timeout)
    }, timeout * 2)
}

activateBell();