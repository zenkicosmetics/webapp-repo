function setCookie(cname, cvalue, minutes) {
    var d = new Date();
    d.setTime(d.getTime() + (minutes*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

//get new referrer
var new_document_referrer = document.referrer;
var document_referrer_old = getCookie('partner_referrer_website');
if(new_document_referrer.indexOf('clevvermail.com') == -1  ){
	// set timeout 30 minutes.
	setCookie('partner_referrer_website', new_document_referrer, 30);
}
