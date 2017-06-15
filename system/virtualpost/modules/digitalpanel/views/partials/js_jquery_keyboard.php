<script>
    /*!
     jQuery UI Virtual Keyboard
     Version 1.18.12 minified (MIT License)
     Caret code modified from jquery.caret.1.02.js (MIT License)
     */
    ;(function(e){e.keyboard=function(b,l){var a=this,d;a.version="1.18.12";a.$el=e(b);a.el=b;a.$el.data("keyboard",a);a.init=function(){a.options=d=e.extend(!0,{},e.keyboard.defaultOptions,l);a.shiftActive=a.altActive=a.metaActive=a.sets=a.capsLock=!1;a.lastKeyset=[!1,!1,!1];a.rows=["","-shift","-alt","-alt-shift"];e('\x3c!--[if lte IE 8]><script>jQuery("body").addClass("oldie");\x3c/script><![endif]--\x3e\x3c!--[if IE]><script>jQuery("body").addClass("ie");\x3c/script><![endif]--\x3e').appendTo("body").remove(); a.msie=e("body").hasClass("oldie");a.allie=e("body").hasClass("ie");a.inPlaceholder=a.$el.attr("placeholder")||"";a.watermark="undefined"!==typeof document.createElement("input").placeholder&&""!==a.inPlaceholder;a.regex=e.keyboard.comboRegex;a.decimal=/^\./.test(d.display.dec)?!0:!1;a.repeatTime=1E3/(d.repeatRate||20);d.preventDoubleEventTime=d.preventDoubleEventTime||100;a.isOpen=!1;a.wheel=e.isFunction(e.fn.mousewheel);a.alwaysAllowed=[20,33,34,35,36,37,38,39,40,45,46];a.$keyboard=[];a.temp=e('<input style="position:absolute;left:-9999em;top:-9999em;" type="text" value="testing">').appendTo("body").caret(3, 3);a.checkCaret=d.lockInput||3!==a.temp.hide().show().caret().start?!0:!1;a.temp.remove();a.lastCaret={start:0,end:0};a.temp=["",0,0];e.each("initialized beforeVisible visible hidden canceled accepted beforeClose".split(" "),function(k,c){e.isFunction(d[c])&&a.$el.bind(c+".keyboard",d[c])});d.alwaysOpen&&(d.stayOpen=!0);e(document).bind(["mousedown","keyup","touchstart","checkkeyboard",""].join(".keyboard "),function(d){a.opening||(a.escClose(d),d.target&&e(d.target).hasClass("ui-keyboard-input")&& (d=e(d.target).data("keyboard"),d===a&&d.options.openOn&&d.focusOn()))});a.$el.addClass("ui-keyboard-input "+d.css.input).attr({"aria-haspopup":"true",role:"textbox"});(a.$el.is(":disabled")||a.$el.attr("readonly")&&!a.$el.hasClass("ui-keyboard-lockedinput"))&&a.$el.addClass("ui-keyboard-nokeyboard");d.openOn&&a.$el.bind(d.openOn+".keyboard",function(){a.focusOn()});a.watermark||""!==a.$el.val()||""===a.inPlaceholder||""===a.$el.attr("placeholder")||a.$el.addClass("ui-keyboard-placeholder").val(a.inPlaceholder); a.$el.trigger("initialized.keyboard",[a,a.el]);d.alwaysOpen&&a.reveal()};a.setCurrent=function(){e(".ui-keyboard-has-focus").removeClass("ui-keyboard-has-focus");e(".ui-keyboard-input-current").removeClass("ui-keyboard-input-current");a.$el.addClass("ui-keyboard-input-current");a.$keyboard.addClass("ui-keyboard-has-focus");a.isCurrent(!0);a.isOpen=!0};a.isCurrent=function(d){var c=e.keyboard.currentKeyboard||!1;d?c=e.keyboard.currentKeyboard=a.el:!1===d&&c===a.el&&(c=e.keyboard.currentKeyboard=""); return c===a.el};a.isVisible=function(){return a.$keyboard&&a.$keyboard.length?a.$keyboard.is(":visible"):!1};a.focusOn=function(){a.$el.is(":visible")&&setTimeout(function(){"number"!=a.$el.attr("type")&&(a.lastCaret=a.$el.caret())},20);a.isVisible()||(clearTimeout(a.timer),a.reveal());d.alwaysOpen&&a.setCurrent()};a.reveal=function(){var k;a.opening=!0;e(".ui-keyboard").not(".ui-keyboard-always-open").remove();if(a.$el.is(":disabled")||a.$el.attr("readonly")&&!a.$el.hasClass("ui-keyboard-lockedinput"))a.$el.addClass("ui-keyboard-nokeyboard"); else return a.$el.removeClass("ui-keyboard-nokeyboard"),d.openOn&&a.$el.unbind(d.openOn+".keyboard"),a.$keyboard&&(!a.$keyboard||a.$keyboard.length&&!e.contains(document.body,a.$keyboard[0]))||a.startup(),a.watermark||a.el.value!==a.inPlaceholder||a.$el.removeClass("ui-keyboard-placeholder").val(""),a.originalContent=a.$el.val(),a.$preview.val(a.originalContent),d.acceptValid&&a.checkValid(),d.resetDefault&&(a.shiftActive=a.altActive=a.metaActive=!1,a.showKeySet()),d.appendLocally||"body"!==d.appendTo|| a.$keyboard.css({position:"absolute",left:0,top:0}),a.$el.trigger("beforeVisible.keyboard",[a,a.el]),a.setCurrent(),a.$keyboard.show(),d.usePreview&&a.msie&&("undefined"===typeof a.width&&(a.$preview.hide(),a.width=Math.ceil(a.$keyboard.width()),a.$preview.show()),a.$preview.width(a.width)),a.position=d.position,e.ui&&e.ui.position&&!e.isEmptyObject(a.position)&&(a.position.of=a.position.of||a.$el.data("keyboardPosition")||a.$el,a.position.collision=a.position.collision||"flipfit flipfit",a.$keyboard.position(a.position)), a.checkDecimal(),a.lineHeight=parseInt(a.$preview.css("lineHeight"),10)||parseInt(a.$preview.css("font-size"),10)+4,d.caretToEnd&&(k=a.originalContent.length,a.lastCaret={start:k,end:k}),a.allie&&(k=a.lastCaret.start||a.originalContent.length,k={start:k,end:k},a.lastCaret||(a.lastCaret=k),0===a.lastCaret.end&&0<a.lastCaret.start&&(a.lastCaret.end=a.lastCaret.start),0>a.lastCaret.start&&(a.lastCaret=k)),setTimeout(function(){a.opening=!1;d.initialFocus&&a.$preview.focus().caret(a.lastCaret);a.$el.trigger("visible.keyboard", [a,a.el])},10),a};a.startup=function(){a.$keyboard&&a.$keyboard.length||("custom"===d.layout&&(d.layoutHash="custom"+a.customHash()),a.layout="custom"===d.layout?d.layoutHash:d.layout,"undefined"===typeof e.keyboard.builtLayouts[a.layout]&&(e.isFunction(d.create)&&d.create(a),a.$keyboard.length||a.buildKeyboard()),a.$keyboard=e.keyboard.builtLayouts[a.layout].$keyboard.clone(),d.usePreview?(a.$preview=a.$el.clone(!1).removeAttr("id").removeClass("ui-keyboard-placeholder ui-keyboard-input").addClass("ui-keyboard-preview "+ d.css.input).removeAttr("aria-haspopup").attr("tabindex","-1").show(),"number"==a.$preview.attr("type")&&a.$preview.attr("type","text"),e("<div />").addClass("ui-keyboard-preview-wrapper").append(a.$preview).prependTo(a.$keyboard)):(a.$preview=a.$el,e.isEmptyObject(a.position)||(d.position.at=d.position.at2)));a.preview=a.$preview[0];a.$decBtn=a.$keyboard.find(".ui-keyboard-dec");(d.enterNavigation||"TEXTAREA"===a.el.tagName)&&a.alwaysAllowed.push(13);d.lockInput&&a.$preview.addClass("ui-keyboard-lockedinput").attr({readonly:"readonly"}); a.bindKeyboard();a.$keyboard.appendTo(d.appendLocally?a.$el.parent():d.appendTo||"body");a.bindKeys();e.ui&&e.ui.position&&!e.isEmptyObject(a.position)&&e(window).bind("resize.keyboard",function(){a.isVisible()&&a.$keyboard.position(a.position)})};a.bindKeyboard=function(){var k=e.keyboard.builtLayouts[a.layout];a.$preview.unbind("keypress keyup keydown mouseup touchend ".split(" ").join(".keyboard ")).bind("keypress.keyboard",function(c){var g=a.lastKey=String.fromCharCode(c.charCode||c.which);a.$lastKey= [];a.checkCaret&&(a.lastCaret=a.$preview.caret());a.capsLock=65<=g&&90>=g&&!c.shiftKey||97<=g&&122>=g&&c.shiftKey?!0:!1;if(d.restrictInput){if((8===c.which||0===c.which)&&e.inArray(c.keyCode,a.alwaysAllowed))return;-1===e.inArray(g,k.acceptedKeys)&&c.preventDefault()}else if((c.ctrlKey||c.metaKey)&&(97===c.which||99===c.which||118===c.which||120<=c.which&&122>=c.which))return;k.hasMappedKeys&&k.mappedKeys.hasOwnProperty(g)&&(a.lastKey=k.mappedKeys[g],a.insertText(a.lastKey),c.preventDefault());a.checkMaxLength()}).bind("keyup.keyboard", function(c){switch(c.which){case 9:if(a.tab&&d.tabNavigation&&!d.lockInput){if(a.shiftActive=c.shiftKey,c=e.keyboard.keyaction.tab(a),a.tab=!1,!c)return!1}else c.preventDefault();break;case 27:return a.close(),!1}clearTimeout(a.throttled);a.throttled=setTimeout(function(){a.isVisible()&&a.checkCombos()},100);a.checkMaxLength();e.isFunction(d.change)&&d.change(e.Event("change"),a,a.el);a.$el.trigger("change.keyboard",[a,a.el])}).bind("keydown.keyboard",function(c){switch(c.which){case 8:e.keyboard.keyaction.bksp(a, null,c);c.preventDefault();break;case 9:return a.tab=!0,!1;case 13:e.keyboard.keyaction.enter(a,null,c);break;case 20:a.shiftActive=a.capsLock=!a.capsLock;a.showKeySet(this);break;case 86:if(c.ctrlKey||c.metaKey){if(d.preventPaste){c.preventDefault();break}a.checkCombos()}}}).bind("mouseup.keyboard touchend.keyboard",function(){a.checkCaret&&(a.lastCaret=a.$preview.caret())});a.$keyboard.bind("mousedown.keyboard click.keyboard touchstart.keyboard",function(c){c.stopPropagation();a.isCurrent()||(a.reveal(), e(document).trigger("checkkeyboard.keyboard"))});d.preventPaste&&(a.$preview.bind("contextmenu.keyboard",function(a){a.preventDefault()}),a.$el.bind("contextmenu.keyboard",function(a){a.preventDefault()}))};a.bindKeys=function(){var k=(d.keyBinding+" repeater mouseenter mouseleave touchstart mousewheel mouseup click ").split(" ").join(".keyboard ")+"mouseleave.kb mousedown.kb touchstart.kb touchend.kb touchmove.kb touchcancel.kb ";a.$allKeys=a.$keyboard.find("button.ui-keyboard-button").unbind(k).bind(d.keyBinding.split(" ").join(".keyboard ")+ ".keyboard repeater.keyboard",function(c){if(!a.$keyboard.is(":visible"))return!1;var g;g=e(this);var k=g.attr("data-action"),b=(new Date).getTime(),k=":"===k?":":k.split(":")[0];if(!(b-(a.lastEventTime||0)<d.preventDoubleEventTime)){a.lastEventTime=b;a.$preview.focus();a.$lastKey=g;a.lastKey=g.attr("data-curtxt");a.checkCaret&&a.$preview.caret(a.lastCaret);k.match("meta")&&(k="meta");if(e.keyboard.keyaction.hasOwnProperty(k)&&e(this).hasClass("ui-keyboard-actionkey")){if(!1===e.keyboard.keyaction[k](a, this,c))return!1}else"undefined"!==typeof k&&(g=a.lastKey=a.wheel&&!e(this).hasClass("ui-keyboard-actionkey")?a.lastKey:k,a.insertText(g),a.capsLock||d.stickyShift||c.shiftKey||(a.shiftActive=!1,a.showKeySet(this)));a.$preview.focus().caret(a.lastCaret);a.checkCombos();a.checkMaxLength();e.isFunction(d.change)&&d.change(e.Event("change"),a,a.el);a.$el.trigger("change.keyboard",[a,a.el]);c.preventDefault()}}).bind("mouseenter.keyboard mouseleave.keyboard touchstart.keyboard",function(c){if(a.isCurrent()){var k= e(this),b=k.data("layers")||a.getLayers(k);k.data("layers",b=e.grep(b,function(a,c){return e.inArray(a,b)===c}));"mouseenter"!==c.type&&"touchstart"!==c.type||"password"===a.el.type||k.hasClass(d.css.buttonDisabled)||k.addClass(d.css.buttonHover).attr("title",function(k,e){return a.wheel&&""===e&&a.sets&&1<b.length&&"touchstart"!==c.type?d.wheelMessage:e});"mouseleave"===c.type&&(k.data({curtxt:k.data("original"),curnum:0}),k.removeClass("password"===a.el.type?"":d.css.buttonHover).attr("title",function(a, c){return c===d.wheelMessage?"":c}).find("span").html(k.data("original")))}}).bind("mousewheel.keyboard",function(c,d){if(a.wheel){d=d||c.deltaY;var k,b,f=e(this);b=f.data("layers")||a.getLayers(f);1<b.length?(k=f.data("curnum")+(0<d?-1:1),k>b.length-1&&(k=0),0>k&&(k=b.length-1)):k=0;f.data({curnum:k,layers:b,curtxt:b[k]});f.find("span").html(b[k]);return!1}}).bind("mouseup.keyboard mouseleave.kb touchend.kb touchmove.kb touchcancel.kb",function(c){/(mouseleave|touchend|touchcancel)/.test(c.type)? e(this).removeClass(d.css.buttonHover):(a.isVisible()&&a.isCurrent()&&a.$preview.focus(),a.checkCaret&&a.$preview.caret(a.lastCaret));a.mouseRepeat=[!1,""];clearTimeout(a.repeater);return!1}).bind("click.keyboard",function(){return!1}).not(".ui-keyboard-actionkey").add(".ui-keyboard-tab, .ui-keyboard-bksp, .ui-keyboard-space, .ui-keyboard-enter",a.$keyboard).bind("mousedown.kb touchstart.kb",function(){if(0!==d.repeatRate){var c=e(this);a.mouseRepeat=[!0,c];setTimeout(function(){a.mouseRepeat[0]&& a.mouseRepeat[1]===c&&a.repeatKey(c)},d.repeatDelay)}return!1})};a.insertText=function(d){var c,b;b=a.$preview.val();var e=a.$preview.caret(),h=a.$preview.scrollLeft();c=a.$preview.scrollTop();var f=b.length;e.end<e.start&&(e.end=e.start);e.start>f&&(e.end=e.start=f);"TEXTAREA"===a.preview.tagName&&(a.msie&&"\n"===b.substr(e.start,1)&&(e.start+=1,e.end+=1),b=b.split("\n").length-1,a.preview.scrollTop=0<b?a.lineHeight*b:c);c="bksp"===d&&e.start===e.end?!0:!1;d="bksp"===d?"":d;b=e.start+(c?-1:d.length); h+=parseInt(a.$preview.css("fontSize"),10)*("bksp"===d?-1:1);a.$preview.val(a.$preview.val().substr(0,e.start-(c?1:0))+d+a.$preview.val().substr(e.end)).scrollLeft(h).caret(b,b);a.lastCaret={start:b,end:b}};a.checkMaxLength=function(){var k,c=a.$preview.val();!1!==d.maxLength&&c.length>d.maxLength&&(k=Math.min(a.$preview.caret().start,d.maxLength),a.$preview.val(c.substring(0,d.maxLength)),a.$preview.caret(k,k),a.lastCaret={start:k,end:k});a.$decBtn.length&&a.checkDecimal()};a.repeatKey=function(d){d.trigger("repeater.keyboard"); a.mouseRepeat[0]&&(a.repeater=setTimeout(function(){a.repeatKey(d)},a.repeatTime))};a.showKeySet=function(b){var c="",e=(a.shiftActive?1:0)+(a.altActive?2:0);a.shiftActive||(a.capsLock=!1);if(a.metaActive){if(c=b&&b.name&&/meta/.test(b.name)?b.name:"",""===c?c=!0===a.metaActive?"":a.metaActive:a.metaActive=c,!d.stickyShift&&a.lastKeyset[2]!==a.metaActive||(a.shiftActive||a.altActive)&&!a.$keyboard.find(".ui-keyboard-keyset-"+c+a.rows[e]).length)a.shiftActive=a.altActive=!1}else!d.stickyShift&&a.lastKeyset[2]!== a.metaActive&&a.shiftActive&&(a.shiftActive=a.altActive=!1);e=(a.shiftActive?1:0)+(a.altActive?2:0);c=0!==e||a.metaActive?""===c?"":"-"+c:"-default";a.$keyboard.find(".ui-keyboard-keyset"+c+a.rows[e]).length?(a.$keyboard.find(".ui-keyboard-alt, .ui-keyboard-shift, .ui-keyboard-actionkey[class*=meta]").removeClass(d.css.buttonAction).end().find(".ui-keyboard-alt")[a.altActive?"addClass":"removeClass"](d.css.buttonAction).end().find(".ui-keyboard-shift")[a.shiftActive?"addClass":"removeClass"](d.css.buttonAction).end().find(".ui-keyboard-lock")[a.capsLock? "addClass":"removeClass"](d.css.buttonAction).end().find(".ui-keyboard-keyset").hide().end().find(".ui-keyboard-keyset"+c+a.rows[e]).show().end().find(".ui-keyboard-actionkey.ui-keyboard"+c).addClass(d.css.buttonAction),a.lastKeyset=[a.shiftActive,a.altActive,a.metaActive]):(a.shiftActive=a.lastKeyset[0],a.altActive=a.lastKeyset[1],a.metaActive=a.lastKeyset[2])};a.checkCombos=function(){if(!a.isVisible())return a.$preview.val();var b,c,g,n,h=a.$preview.val(),f=a.$preview.caret(),l=e.keyboard.builtLayouts[a.layout], p=h.length;f.end<f.start&&(f.end=f.start);f.start>p&&(f.end=f.start=p);a.msie&&"\n"===h.substr(f.start,1)&&(f.start+=1,f.end+=1);d.useCombos&&(a.msie?h=h.replace(a.regex,function(a,c,b){return d.combos.hasOwnProperty(c)?d.combos[c][b]||a:a}):a.$preview.length&&(g=f.start-(0<=f.start-2?2:0),a.$preview.caret(g,f.end),n=(a.$preview.caret().text||"").replace(a.regex,function(a,c,b){return d.combos.hasOwnProperty(c)?d.combos[c][b]||a:a}),a.$preview.val(a.$preview.caret().replace(n)),h=a.$preview.val())); if(d.restrictInput&&""!==h){g=h;c=l.acceptedKeys.length;for(b=0;b<c;b++)""!==g&&(n=l.acceptedKeys[b],0<=h.indexOf(n)&&(/[\[|\]|\\|\^|\$|\.|\||\?|\*|\+|\(|\)|\{|\}]/g.test(n)&&(n="\\"+n),g=g.replace(new RegExp(n,"g"),"")));""!==g&&(h=h.replace(g,""))}f.start+=h.length-p;f.end+=h.length-p;a.$preview.val(h);a.$preview.caret(f.start,f.end);a.preview.scrollTop=a.lineHeight*(h.substring(0,f.start).split("\n").length-1);a.lastCaret={start:f.start,end:f.end};d.acceptValid&&a.checkValid();return h};a.checkValid= function(){var b=!0;d.validate&&"function"===typeof d.validate&&(b=d.validate(a,a.$preview.val(),!1));a.$keyboard.find(".ui-keyboard-accept")[b?"removeClass":"addClass"]("ui-keyboard-invalid-input")[b?"addClass":"removeClass"]("ui-keyboard-valid-input")};a.checkDecimal=function(){a.decimal&&/\./g.test(a.preview.value)||!a.decimal&&/\,/g.test(a.preview.value)?a.$decBtn.attr({disabled:"disabled","aria-disabled":"true"}).removeClass(d.css.buttonDefault+" "+d.css.buttonHover).addClass(d.css.buttonDisabled): a.$decBtn.removeAttr("disabled").attr({"aria-disabled":"false"}).addClass(d.css.buttonDefault).removeClass(d.css.buttonDisabled)};a.getLayers=function(a){var c;c=a.attr("data-pos");return a.closest(".ui-keyboard").find('button[data-pos="'+c+'"]').map(function(){return e(this).find("> span").html()}).get()};a.switchInput=function(b,c){if("function"===typeof d.switchInput)d.switchInput(a,b,c);else{a.$keyboard.hide();var g;g=!1;var l=e("button, input, textarea, a").filter(":visible"),h=l.index(a.$el)+ (b?1:-1);a.$keyboard.show();h>l.length-1&&(g=d.stopAtEnd,h=0);0>h&&(g=d.stopAtEnd,h=l.length-1);if(!g){c=a.close(c);if(!c)return;(g=l.eq(h).data("keyboard"))&&g.options.openOn.length?g.focusOn():l.eq(h).focus()}}return!1};a.close=function(b){if(a.isOpen){clearTimeout(a.throttled);var c=b?a.checkCombos():a.originalContent;if(b&&d.validate&&"function"===typeof d.validate&&!d.validate(a,c,!0)&&(c=a.originalContent,b=!1,d.cancelClose))return;a.isCurrent(!1);a.isOpen=!1;a.$preview.val(c);a.$el.removeClass("ui-keyboard-input-current ui-keyboard-autoaccepted").addClass(b? !0===b?"":"ui-keyboard-autoaccepted":"").trigger(d.alwaysOpen?"":"beforeClose.keyboard",[a,a.el,b||!1]).val(c).scrollTop(a.el.scrollHeight).trigger(b?"accepted.keyboard":"canceled.keyboard",[a,a.el]).trigger(d.alwaysOpen?"inactive.keyboard":"hidden.keyboard",[a,a.el]).blur();d.openOn&&(a.timer=setTimeout(function(){a.$el.bind(d.openOn+".keyboard",function(){a.focusOn()});e(":focus")[0]===a.el&&a.$el.blur()},500));!d.alwaysOpen&&a.$keyboard&&(a.$keyboard.remove(),a.$keyboard=[]);a.watermark||""!== a.el.value||""===a.inPlaceholder||a.$el.addClass("ui-keyboard-placeholder").val(a.inPlaceholder);a.$el.trigger("change")}return!!b};a.accept=function(){return a.close(!0)};a.escClose=function(b){if(b&&"keyup"===b.type)return 27===b.which?a.close():"";a.isOpen&&(!a.isCurrent()&&a.isOpen||a.isOpen&&b.target!==a.el&&!d.stayOpen)&&(a.allie&&b.preventDefault(),a.close(d.autoAccept?"true":!1))};a.keyBtn=e("<button />").attr({role:"button",type:"button","aria-disabled":"false",tabindex:"-1"}).addClass("ui-keyboard-button"); a.addKey=function(b,c,g){var l,h,f;c=!0===g?b:d.display[c]||b;var m=!0===g?b.charCodeAt(0):b;/\(.+\)/.test(c)&&(h=c.replace(/\(([^()]+)\)/,""),l=c.match(/\(([^()]+)\)/)[1],c=h,f=h.split(":"),h=""!==f[0]&&1<f.length?f[0]:h,e.keyboard.builtLayouts[a.layout].mappedKeys[l]=h);f=c.split(":");""===f[0]&&""===f[1]&&(c=":");c=""!==f[0]&&1<f.length?e.trim(f[0]):c;l=1<f.length?e.trim(f[1]).replace(/_/g," ")||"":"";h=1<c.length?" ui-keyboard-widekey":"";h+=g?"":" ui-keyboard-actionkey";return a.keyBtn.clone().attr({"data-value":c, name:m,"data-pos":a.temp[1]+","+a.temp[2],title:l,"data-action":b,"data-original":c,"data-curtxt":c,"data-curnum":0}).addClass((""===m?"":"ui-keyboard-"+m+h+" ")+d.css.buttonDefault).html("<span>"+c+"</span>").appendTo(a.temp[0])};a.customHash=function(){var a,c,b,e;c=d.customLayout;b=[];var h=[];for(a in c)c.hasOwnProperty(a)&&b.push(c[a]);h=h.concat.apply(h,b).join(" ");c=0;e=h.length;if(0===e)return c;for(a=0;a<e;a++)b=h.charCodeAt(a),c=(c<<5)-c+b,c&=c;return c};a.buildKeyboard=function(){var b, c,g,l,h,f,m,p,q,s=0,r=e.keyboard.builtLayouts[a.layout]={mappedKeys:{},acceptedKeys:[]},t=r.acceptedKeys=[],u=e("<div />").addClass("ui-keyboard "+d.css.container+(d.alwaysOpen?" ui-keyboard-always-open":"")).attr({role:"textbox"}).hide();"custom"!==d.layout&&e.keyboard.layouts.hasOwnProperty(d.layout)||(d.layout="custom",e.keyboard.layouts.custom=d.customLayout||{"default":["{cancel}"]});e.each(e.keyboard.layouts[d.layout],function(r,v){if(""!==r)for(s++,l=e("<div />").attr("name",r).addClass("ui-keyboard-keyset ui-keyboard-keyset-"+ r).appendTo(u)["default"===r?"show":"hide"](),g=0;g<v.length;g++){f=e.trim(v[g]).replace(/\{(\.?)[\s+]?:[\s+]?(\.?)\}/g,"{$1:$2}");p=f.split(/\s+/);for(m=0;m<p.length;m++)if(a.temp=[l,g,m],h=!1,0!==p[m].length)if(/^\{\S+\}$/.test(p[m]))if(c=p[m].match(/^\{(\S+)\}$/)[1].toLowerCase(),/\!\!/.test(c)&&(c=c.replace("!!",""),h=!0),/^sp:((\d+)?([\.|,]\d+)?)(em|px)?$/.test(c)&&(q=parseFloat(c.replace(/,/,".").match(/^sp:((\d+)?([\.|,]\d+)?)(em|px)?$/)[1]||0),e("<span>&nbsp;</span>").width(c.match("px")? q+"px":2*q+"em").addClass("ui-keyboard-button ui-keyboard-spacer").appendTo(l)),/^empty(:((\d+)?([\.|,]\d+)?)(em|px)?)?$/.test(c)&&(q=/:/.test(c)?parseFloat(c.replace(/,/,".").match(/^empty:((\d+)?([\.|,]\d+)?)(em|px)?$/)[1]||0):"",a.addKey(""," ").addClass(d.css.buttonDisabled+" "+d.css.buttonEmpty).attr("aria-disabled",!0).width(q?c.match("px")?q+"px":2*q+"em":"")),/^meta\d+\:?(\w+)?/.test(c))a.addKey(c,c);else switch(c){case "a":case "accept":a.addKey("accept",c).addClass(d.css.buttonAction);break; case "alt":case "altgr":a.addKey("alt","alt");break;case "b":case "bksp":a.addKey("bksp",c);break;case "c":case "cancel":a.addKey("cancel",c).addClass(d.css.buttonAction);break;case "combo":a.addKey("combo","combo").addClass(d.css.buttonAction);break;case "dec":t.push(a.decimal?".":",");a.addKey("dec","dec");break;case "e":case "enter":a.addKey("enter",c).addClass(d.css.buttonAction);break;case "s":case "shift":a.addKey("shift",c);break;case "sign":t.push("-");a.addKey("sign","sign");break;case "space":t.push(" "); a.addKey("space","space");break;case "t":case "tab":a.addKey("tab",c);break;default:if(e.keyboard.keyaction.hasOwnProperty(c))a.addKey(c,c)[h?"addClass":"removeClass"](d.css.buttonAction)}else b=p[m],t.push(":"===b?b:b.split(":")[0]),a.addKey(b,b,!0);l.find(".ui-keyboard-button:last").after('<br class="ui-keyboard-button-endrow">')}});1<s&&(a.sets=!0);r.hasMappedKeys=!e.isEmptyObject(r.mappedKeys);return r.$keyboard=u};a.destroy=function(){e(document).unbind("mousedown.keyboard keyup.keyboard touchstart.keyboard"); a.$keyboard.length&&a.$keyboard.remove();var b=e.trim(d.openOn+" accepted beforeClose canceled change contextmenu hidden initialized keydown keypress keyup visible ").split(" ").join(".keyboard ");a.$el.removeClass("ui-keyboard-input ui-keyboard-lockedinput ui-keyboard-placeholder ui-keyboard-notallowed ui-keyboard-always-open "+d.css.input).removeAttr("aria-haspopup").removeAttr("role").unbind(b+".keyboard").removeData("keyboard")};a.init()};e.keyboard.keyaction={accept:function(b){b.close(!0);return!1}, alt:function(b,e){b.altActive=!b.altActive;b.showKeySet(e)},bksp:function(b){b.insertText("bksp")},cancel:function(b){b.close();return!1},clear:function(b){b.$preview.val("")},combo:function(b){var e=!b.options.useCombos;b.options.useCombos=e;b.$keyboard.find(".ui-keyboard-combo").toggleClass(b.options.css.buttonAction,e);e&&b.checkCombos();return!1},dec:function(b){b.insertText(b.decimal?".":",")},"default":function(b,e){b.shiftActive=b.altActive=b.metaActive=!1;b.showKeySet(e)},enter:function(b, l,a){l=b.el.tagName;var d=b.options;if(a.shiftKey)return d.enterNavigation?b.switchInput(!a[d.enterMod],!0):b.close(!0);if(d.enterNavigation&&("TEXTAREA"!==l||a[d.enterMod]))return b.switchInput(!a[d.enterMod],d.autoAccept?"true":!1);"TEXTAREA"===l&&e(a.target).closest("button").length&&b.insertText(" \n")},lock:function(b,e){b.lastKeyset[0]=b.shiftActive=b.capsLock=!b.capsLock;b.showKeySet(e)},left:function(b){var e=b.$preview.caret();0<=e.start-1&&(b.lastCaret={start:e.start-1,end:e.start-1})}, meta:function(b,l){b.metaActive=e(l).hasClass(b.options.css.buttonAction)?!1:!0;b.showKeySet(l)},next:function(b){b.switchInput(!0,b.options.autoAccept);return!1},prev:function(b){b.switchInput(!1,b.options.autoAccept);return!1},right:function(b){var e=b.$preview.caret();e.start+1<=b.$preview.val().length&&(b.lastCaret={start:e.start+1,end:e.start+1})},shift:function(b,e){b.lastKeyset[0]=b.shiftActive=!b.shiftActive;b.showKeySet(e)},sign:function(b){/^\-?\d*\.?\d*$/.test(b.$preview.val())&&b.$preview.val(-1* b.$preview.val())},space:function(b){b.insertText(" ")},tab:function(b){var e=b.options;if("INPUT"===b.el.tagName)return e.tabNavigation?b.switchInput(!b.shiftActive,!0):!1;b.insertText("\t")}};e.keyboard.builtLayouts={};e.keyboard.layouts={alpha:{"default":["` 1 2 3 4 5 6 7 8 9 0 - = {bksp}","{tab} a b c d e f g h i j [ ] \\","k l m n o p q r s ; ' {enter}","{shift} t u v w x y z , . / {shift}","{accept} {space} {cancel}"],shift:["~ ! @ # $ % ^ & * ( ) _ + {bksp}","{tab} A B C D E F G H I J { } |", 'K L M N O P Q R S : " {enter}',"{shift} T U V W X Y Z < > ? {shift}","{accept} {space} {cancel}"]},qwerty:{"default":["` 1 2 3 4 5 6 7 8 9 0 - = {bksp}","{tab} q w e r t y u i o p [ ] \\","a s d f g h j k l ; ' {enter}","{shift} z x c v b n m , . / {shift}","{accept} {space} {cancel}"],shift:["~ ! @ # $ % ^ & * ( ) _ + {bksp}","{tab} Q W E R T Y U I O P { } |",'A S D F G H J K L : " {enter}',"{shift} Z X C V B N M < > ? {shift}","{accept} {space} {cancel}"]},international:{"default":["` 1 2 3 4 5 6 7 8 9 0 - = {bksp}", "{tab} q w e r t y u i o p [ ] \\","a s d f g h j k l ; ' {enter}","{shift} z x c v b n m , . / {shift}","{accept} {alt} {space} {alt} {cancel}"],shift:["~ ! @ # $ % ^ & * ( ) _ + {bksp}","{tab} Q W E R T Y U I O P { } |",'A S D F G H J K L : " {enter}',"{shift} Z X C V B N M < > ? {shift}","{accept} {alt} {space} {alt} {cancel}"],alt:["~ \u00a1 \u00b2 \u00b3 \u00a4 \u20ac \u00bc \u00bd \u00be \u2018 \u2019 \u00a5 \u00d7 {bksp}","{tab} \u00e4 \u00e5 \u00e9 \u00ae \u00fe \u00fc \u00fa \u00ed \u00f3 \u00f6 \u00ab \u00bb \u00ac", "\u00e1 \u00df \u00f0 f g h j k \u00f8 \u00b6 \u00b4 {enter}","{shift} \u00e6 x \u00a9 v b \u00f1 \u00b5 \u00e7 > \u00bf {shift}","{accept} {alt} {space} {alt} {cancel}"],"alt-shift":["~ \u00b9 \u00b2 \u00b3 \u00a3 \u20ac \u00bc \u00bd \u00be \u2018 \u2019 \u00a5 \u00f7 {bksp}","{tab} \u00c4 \u00c5 \u00c9 \u00ae \u00de \u00dc \u00da \u00cd \u00d3 \u00d6 \u00ab \u00bb \u00a6","\u00c4 \u00a7 \u00d0 F G H J K \u00d8 \u00b0 \u00a8 {enter}","{shift} \u00c6 X \u00a2 V B \u00d1 \u00b5 \u00c7 . \u00bf {shift}", "{accept} {alt} {space} {alt} {cancel}"]},colemak:{"default":["` 1 2 3 4 5 6 7 8 9 0 - = {bksp}","{tab} q w f p g j l u y ; [ ] \\","{bksp} a r s t d h n e i o ' {enter}","{shift} z x c v b k m , . / {shift}","{accept} {space} {cancel}"],shift:["~ ! @ # $ % ^ & * ( ) _ + {bksp}","{tab} Q W F P G J L U Y : { } |",'{bksp} A R S T D H N E I O " {enter}',"{shift} Z X C V B K M < > ? {shift}","{accept} {space} {cancel}"]},dvorak:{"default":["` 1 2 3 4 5 6 7 8 9 0 [ ] {bksp}","{tab} ' , . p y f g c r l / = \\", "a o e u i d h t n s - {enter}","{shift} ; q j k x b m w v z {shift}","{accept} {space} {cancel}"],shift:["~ ! @ # $ % ^ & * ( ) { } {bksp}",'{tab} " < > P Y F G C R L ? + |',"A O E U I D H T N S _ {enter}","{shift} : Q J K X B M W V Z {shift}","{accept} {space} {cancel}"]},num:{"default":"= ( ) {b};{clear} / * -;7 8 9 +;4 5 6 {sign};1 2 3 %;0 . {a} {c}".split(";")}};e.keyboard.defaultOptions={layout:"qwerty",customLayout:null,position:{of:null,my:"center top",at:"center top",at2:"center bottom"}, usePreview:!0,alwaysOpen:!1,initialFocus:!0,stayOpen:!1,display:{a:"\u2714:Accept (Shift-Enter)",accept:"Accept:Accept (Shift-Enter)",alt:"Alt:\u2325 AltGr",b:"\u232b:Backspace",bksp:"Bksp:Backspace",c:"\u2716:Cancel (Esc)",cancel:"Cancel:Cancel (Esc)",clear:"C:Clear",combo:"\u00f6:Toggle Combo Keys",dec:".:Decimal",e:"\u23ce:Enter",empty:"\u00a0",enter:"Enter:Enter \u23ce",left:"\u2190",lock:"Lock:\u21ea Caps Lock",next:"Next \u21e8",prev:"\u21e6 Prev",right:"\u2192",s:"\u21e7:Shift",shift:"Shift:Shift", sign:"\u00b1:Change Sign",space:"&nbsp;:Space",t:"\u21e5:Tab",tab:"\u21e5 Tab:Tab"},wheelMessage:"Use mousewheel to see other keys",css:{input:"ui-widget-content ui-corner-all",container:"ui-widget-content ui-widget ui-corner-all ui-helper-clearfix",buttonDefault:"ui-state-default ui-corner-all",buttonHover:"ui-state-hover",buttonAction:"ui-state-active",buttonDisabled:"ui-state-disabled",buttonEmpty:"ui-keyboard-empty"},autoAccept:!1,lockInput:!1,restrictInput:!1,acceptValid:!1,cancelClose:!0,tabNavigation:!1, enterNavigation:!1,enterMod:"altKey",stopAtEnd:!0,appendLocally:!1,appendTo:"body",stickyShift:!0,preventPaste:!1,caretToEnd:!1,maxLength:!1,repeatDelay:500,repeatRate:20,resetDefault:!1,openOn:"focus",keyBinding:"mousedown touchstart",useCombos:!0,combos:{"`":{a:"\u00e0",A:"\u00c0",e:"\u00e8",E:"\u00c8",i:"\u00ec",I:"\u00cc",o:"\u00f2",O:"\u00d2",u:"\u00f9",U:"\u00d9",y:"\u1ef3",Y:"\u1ef2"},"'":{a:"\u00e1",A:"\u00c1",e:"\u00e9",E:"\u00c9",i:"\u00ed",I:"\u00cd",o:"\u00f3",O:"\u00d3",u:"\u00fa",U:"\u00da", y:"\u00fd",Y:"\u00dd"},'"':{a:"\u00e4",A:"\u00c4",e:"\u00eb",E:"\u00cb",i:"\u00ef",I:"\u00cf",o:"\u00f6",O:"\u00d6",u:"\u00fc",U:"\u00dc",y:"\u00ff",Y:"\u0178"},"^":{a:"\u00e2",A:"\u00c2",e:"\u00ea",E:"\u00ca",i:"\u00ee",I:"\u00ce",o:"\u00f4",O:"\u00d4",u:"\u00fb",U:"\u00db",y:"\u0177",Y:"\u0176"},"~":{a:"\u00e3",A:"\u00c3",e:"\u1ebd",E:"\u1ebc",i:"\u0129",I:"\u0128",o:"\u00f5",O:"\u00d5",u:"\u0169",U:"\u0168",y:"\u1ef9",Y:"\u1ef8",n:"\u00f1",N:"\u00d1"}},validate:function(b,e,a){return!0}};e.keyboard.comboRegex= /([`\'~\^\"ao])([a-z])/mig;e.keyboard.currentKeyboard="";e.fn.keyboard=function(b){return this.each(function(){e(this).data("keyboard")||new e.keyboard(this,b)})};e.fn.getkeyboard=function(){return this.data("keyboard")}})(jQuery);

    (function(e,b,l,a){e.fn.caret=function(d,e){if("undefined"===typeof this[0]||this.is(":hidden")||"hidden"===this.css("visibility"))return this;var c,g,n,h,f;f=document.selection;var m=this[0],p=m.scrollTop,q=!1,s=!0;try{q="undefined"!==typeof m.selectionStart}catch(r){s=!1}"object"===typeof d&&d.start&&d.end?(g=d.start,h=d.end):"number"===typeof d&&"number"===typeof e&&(g=d,h=e);if(s&&"undefined"!==typeof g)return q?(m.selectionStart=g,m.selectionEnd=h):(f=m.createTextRange(),f.collapse(!0),f.moveStart("character", g),f.moveEnd("character",h-g),f.select()),(this.is(":visible")||"hidden"!==this.css("visibility"))&&this.focus(),m.scrollTop=p,this;q?(c=m.selectionStart,n=m.selectionEnd):f?"TEXTAREA"===m.tagName?(h=this.val(),g=f[l](),f=g[a](),f.moveToElementText(m),f.setEndPoint("EndToEnd",g),c=f.text.replace(/\r/g,"\n")[b],n=c+g.text.replace(/\r/g,"\n")[b]):(h=this.val().replace(/\r/g,"\n"),g=f[l]()[a](),g.moveEnd("character",h[b]),c=""===g.text?h[b]:h.lastIndexOf(g.text),g=f[l]()[a](),g.moveStart("character", -h[b]),n=g.text[b]):(c=0,n=(m.value||"").length);f=(m.value||"").substring(c,n);return{start:c,end:n,text:f,replace:function(a){return m.value.substring(0,c)+a+m.value.substring(n,m.value[b])}}}})(jQuery,"length","createRange","duplicate");
</script>
