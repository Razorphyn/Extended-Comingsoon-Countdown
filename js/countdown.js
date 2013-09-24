(function (e) {
    e.fn.countdown = function (t, n,day,pday,hour,phour,minute,pminute,second,psecond) {
        function i() {
            eventDate = Date.parse(r["date"]) / 1e3;
            currentDate = Math.floor(e.now() / 1e3);
            if (eventDate <= currentDate) {
                n.call(this);
                clearInterval(interval)
            }
            seconds = eventDate - currentDate;
            days = Math.floor(seconds / (60 * 60 * 24));
            seconds -= days * 60 * 60 * 24;
            hours = Math.floor(seconds / (60 * 60));
            seconds -= hours * 60 * 60;
            minutes = Math.floor(seconds / 60);
            seconds -= minutes * 60;
            if (days == 1) {
                thisEl.find(".timeRefDays").text(day)
            } else {
                thisEl.find(".timeRefDays").text(pday)
            } if (hours == 1) {
                thisEl.find(".timeRefHours").text(hour)
            } else {
                thisEl.find(".timeRefHours").text(phour)
            } if (minutes == 1) {
                thisEl.find(".timeRefMinutes").text(minute)
            } else {
                thisEl.find(".timeRefMinutes").text(pminute)
            } if (seconds == 1) {
                thisEl.find(".timeRefSeconds").text(second)
            } else {
                thisEl.find(".timeRefSeconds").text(psecond)
            } if (r["format"] == "on") {
                days = String(days).length >= 2 ? days : "0" + days;
                hours = String(hours).length >= 2 ? hours : "0" + hours;
                minutes = String(minutes).length >= 2 ? minutes : "0" + minutes;
                seconds = String(seconds).length >= 2 ? seconds : "0" + seconds
            }
            if (!isNaN(eventDate)) {
                thisEl.find(".days").text(days);
                thisEl.find(".hours").text(hours);
                thisEl.find(".minutes").text(minutes);
                thisEl.find(".seconds").text(seconds)
            } else {
                alert("Invalid date. Here's an example: 12 Tuesday 2012 17:30:00");
                clearInterval(interval)
            }
        }
        thisEl = e(this);
        var r = {
            date: null,
            format: null
        };
        if (t) {
            e.extend(r, t)
        }
        i();
        interval = setInterval(i, 1e3)
    }
})(jQuery)