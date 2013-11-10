(function (a) {
    a.fn.countdown = function (c, d, f, g, h, k, l, m, n, p,offset) {
        function e() {
            currentDate = Math.floor((new Date().getTime()-offset)/1E3);
            if(eventDate < currentDate){
				null != d && d.call(this), 
				"undefined" != typeof interval && clearInterval(interval)
			}
			else{
				seconds = (eventDate - currentDate), 
				days = Math.floor(seconds / 86400), 
				seconds -= 86400 * days, 
				hours = Math.floor(seconds / 3600), 
				seconds -= 3600 * hours, 
				minutes = Math.floor(seconds / 60), 
				seconds -= 60 * minutes, 
				1 == days ? thisEl.find(".timeRefDays").text(f) : thisEl.find(".timeRefDays").text(g), 
				1 == hours ? thisEl.find(".timeRefHours").text(h) : thisEl.find(".timeRefHours").text(k), 
				1 == minutes ? thisEl.find(".timeRefMinutes").text(l) : thisEl.find(".timeRefMinutes").text(m), 
				1 == seconds ? thisEl.find(".timeRefSeconds").text(n) : thisEl.find(".timeRefSeconds").text(p);
				if("on" == b.format){
					days = 2 <= String(days).length ? days : "0" + days, 
					hours = 2 <= String(hours).length ? hours : "0" + hours, 
					minutes = 2 <= String(minutes).length ? minutes : "0" + minutes, 
					seconds = 2 <= String(seconds).length ? seconds : "0" + seconds
				}
				thisEl.find(".days").text(days), 
				thisEl.find(".hours").text(hours), 
				thisEl.find(".minutes").text(minutes), 
				thisEl.find(".seconds").text(seconds)
			}
        }
        var b = {
            date: null,
            format: null
        };
        c && a.extend(b, c);
		b.date=b.date.split(' ');
		b.date[0]=b.date[0].split('/');
		b.date[1]=b.date[1].split(':');
		eventDate = (new Date(b.date[0][2], b.date[0][0]-1, b.date[0][1],b.date[1][0], b.date[1][1], b.date[1][2]).getTime())/1E3;
		offset=offset-new Date().getTimezoneOffset()*60*1000;
        thisEl = a(this);
        e();
        interval = setInterval(e, 1E3);
		if(isNaN(eventDate)){
			alert("Invalid date. Here's an example: 12 Tuesday 2012 17:30:00"), clearInterval(interval)
		}
    }
})(jQuery);