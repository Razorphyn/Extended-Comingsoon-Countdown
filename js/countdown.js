(function (a) {
    a.fn.countdown = function (c, d, f, g, h, k, l, m, n, p) {
        function e() {
            eventDate = Date.parse(b.date) / 1E3;
            currentDate = Math.floor(a.now() / 1E3);
            eventDate <= currentDate ? (null != d && d.call(this), "undefined" != typeof interval && clearInterval(interval)) : (seconds = eventDate - currentDate, days = Math.floor(seconds / 86400), seconds -= 86400 * days, hours = Math.floor(seconds / 3600), seconds -= 3600 * hours, minutes = Math.floor(seconds / 60), seconds -= 60 * minutes, 1 == days ? thisEl.find(".timeRefDays").text(f) : thisEl.find(".timeRefDays").text(g), 1 == hours ? thisEl.find(".timeRefHours").text(h) : thisEl.find(".timeRefHours").text(k), 1 == minutes ? thisEl.find(".timeRefMinutes").text(l) : thisEl.find(".timeRefMinutes").text(m), 1 == seconds ? thisEl.find(".timeRefSeconds").text(n) : thisEl.find(".timeRefSeconds").text(p), "on" == b.format && (days = 2 <= String(days).length ? days : "0" + days, hours = 2 <= String(hours).length ? hours : "0" + hours, minutes = 2 <= String(minutes).length ? minutes : "0" + minutes, seconds = 2 <= String(seconds).length ? seconds : "0" + seconds), isNaN(eventDate) ? (alert("Invalid date. Here's an example: 12 Tuesday 2012 17:30:00"), clearInterval(interval)) : (thisEl.find(".days").text(days), thisEl.find(".hours").text(hours), thisEl.find(".minutes").text(minutes), thisEl.find(".seconds").text(seconds)))
        }
        thisEl = a(this);
        var b = {
            date: null,
            format: null
        };
        c && a.extend(b, c);
        e();
        interval = setInterval(e, 1E3)
    }
})(jQuery);