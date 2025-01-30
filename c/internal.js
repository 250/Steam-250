(()=>{var t={795:function(t){t.exports=function(){"use strict";var t=1e3,e=6e4,n=36e5,s="millisecond",r="second",i="minute",a="hour",o="day",u="week",c="month",h="quarter",d="year",l="date",f="Invalid Date",m=/^(\d{4})[-/]?(\d{1,2})?[-/]?(\d{0,2})[Tt\s]*(\d{1,2})?:?(\d{1,2})?:?(\d{1,2})?[.:]?(\d+)?$/,$=/\[([^\]]+)]|Y{1,4}|M{1,4}|D{1,2}|d{1,4}|H{1,2}|h{1,2}|a|A|m{1,2}|s{1,2}|Z{1,2}|SSS/g,y={name:"en",weekdays:"Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),months:"January_February_March_April_May_June_July_August_September_October_November_December".split("_"),ordinal:function(t){var e=["th","st","nd","rd"],n=t%100;return"["+t+(e[(n-20)%10]||e[n]||e[0])+"]"}},v=function(t,e,n){var s=String(t);return!s||s.length>=e?t:""+Array(e+1-s.length).join(n)+t},p={s:v,z:function(t){var e=-t.utcOffset(),n=Math.abs(e),s=Math.floor(n/60),r=n%60;return(e<=0?"+":"-")+v(s,2,"0")+":"+v(r,2,"0")},m:function t(e,n){if(e.date()<n.date())return-t(n,e);var s=12*(n.year()-e.year())+(n.month()-e.month()),r=e.clone().add(s,c),i=n-r<0,a=e.clone().add(s+(i?-1:1),c);return+(-(s+(n-r)/(i?r-a:a-r))||0)},a:function(t){return t<0?Math.ceil(t)||0:Math.floor(t)},p:function(t){return{M:c,y:d,w:u,d:o,D:l,h:a,m:i,s:r,ms:s,Q:h}[t]||String(t||"").toLowerCase().replace(/s$/,"")},u:function(t){return void 0===t}},g="en",M={};M[g]=y;var S="$isDayjsObject",w=function(t){return t instanceof O||!(!t||!t[S])},D=function t(e,n,s){var r;if(!e)return g;if("string"==typeof e){var i=e.toLowerCase();M[i]&&(r=i),n&&(M[i]=n,r=i);var a=e.split("-");if(!r&&a.length>1)return t(a[0])}else{var o=e.name;M[o]=e,r=o}return!s&&r&&(g=r),r||!s&&g},k=function(t,e){if(w(t))return t.clone();var n="object"==typeof e?e:{};return n.date=t,n.args=arguments,new O(n)},b=p;b.l=D,b.i=w,b.w=function(t,e){return k(t,{locale:e.$L,utc:e.$u,x:e.$x,$offset:e.$offset})};var O=function(){function y(t){this.$L=D(t.locale,null,!0),this.parse(t),this.$x=this.$x||t.x||{},this[S]=!0}var v=y.prototype;return v.parse=function(t){this.$d=function(t){var e=t.date,n=t.utc;if(null===e)return new Date(NaN);if(b.u(e))return new Date;if(e instanceof Date)return new Date(e);if("string"==typeof e&&!/Z$/i.test(e)){var s=e.match(m);if(s){var r=s[2]-1||0,i=(s[7]||"0").substring(0,3);return n?new Date(Date.UTC(s[1],r,s[3]||1,s[4]||0,s[5]||0,s[6]||0,i)):new Date(s[1],r,s[3]||1,s[4]||0,s[5]||0,s[6]||0,i)}}return new Date(e)}(t),this.init()},v.init=function(){var t=this.$d;this.$y=t.getFullYear(),this.$M=t.getMonth(),this.$D=t.getDate(),this.$W=t.getDay(),this.$H=t.getHours(),this.$m=t.getMinutes(),this.$s=t.getSeconds(),this.$ms=t.getMilliseconds()},v.$utils=function(){return b},v.isValid=function(){return!(this.$d.toString()===f)},v.isSame=function(t,e){var n=k(t);return this.startOf(e)<=n&&n<=this.endOf(e)},v.isAfter=function(t,e){return k(t)<this.startOf(e)},v.isBefore=function(t,e){return this.endOf(e)<k(t)},v.$g=function(t,e,n){return b.u(t)?this[e]:this.set(n,t)},v.unix=function(){return Math.floor(this.valueOf()/1e3)},v.valueOf=function(){return this.$d.getTime()},v.startOf=function(t,e){var n=this,s=!!b.u(e)||e,h=b.p(t),f=function(t,e){var r=b.w(n.$u?Date.UTC(n.$y,e,t):new Date(n.$y,e,t),n);return s?r:r.endOf(o)},m=function(t,e){return b.w(n.toDate()[t].apply(n.toDate("s"),(s?[0,0,0,0]:[23,59,59,999]).slice(e)),n)},$=this.$W,y=this.$M,v=this.$D,p="set"+(this.$u?"UTC":"");switch(h){case d:return s?f(1,0):f(31,11);case c:return s?f(1,y):f(0,y+1);case u:var g=this.$locale().weekStart||0,M=($<g?$+7:$)-g;return f(s?v-M:v+(6-M),y);case o:case l:return m(p+"Hours",0);case a:return m(p+"Minutes",1);case i:return m(p+"Seconds",2);case r:return m(p+"Milliseconds",3);default:return this.clone()}},v.endOf=function(t){return this.startOf(t,!1)},v.$set=function(t,e){var n,u=b.p(t),h="set"+(this.$u?"UTC":""),f=(n={},n[o]=h+"Date",n[l]=h+"Date",n[c]=h+"Month",n[d]=h+"FullYear",n[a]=h+"Hours",n[i]=h+"Minutes",n[r]=h+"Seconds",n[s]=h+"Milliseconds",n)[u],m=u===o?this.$D+(e-this.$W):e;if(u===c||u===d){var $=this.clone().set(l,1);$.$d[f](m),$.init(),this.$d=$.set(l,Math.min(this.$D,$.daysInMonth())).$d}else f&&this.$d[f](m);return this.init(),this},v.set=function(t,e){return this.clone().$set(t,e)},v.get=function(t){return this[b.p(t)]()},v.add=function(s,h){var l,f=this;s=Number(s);var m=b.p(h),$=function(t){var e=k(f);return b.w(e.date(e.date()+Math.round(t*s)),f)};if(m===c)return this.set(c,this.$M+s);if(m===d)return this.set(d,this.$y+s);if(m===o)return $(1);if(m===u)return $(7);var y=(l={},l[i]=e,l[a]=n,l[r]=t,l)[m]||1,v=this.$d.getTime()+s*y;return b.w(v,this)},v.subtract=function(t,e){return this.add(-1*t,e)},v.format=function(t){var e=this,n=this.$locale();if(!this.isValid())return n.invalidDate||f;var s=t||"YYYY-MM-DDTHH:mm:ssZ",r=b.z(this),i=this.$H,a=this.$m,o=this.$M,u=n.weekdays,c=n.months,h=n.meridiem,d=function(t,n,r,i){return t&&(t[n]||t(e,s))||r[n].slice(0,i)},l=function(t){return b.s(i%12||12,t,"0")},m=h||function(t,e,n){var s=t<12?"AM":"PM";return n?s.toLowerCase():s};return s.replace($,(function(t,s){return s||function(t){switch(t){case"YY":return String(e.$y).slice(-2);case"YYYY":return b.s(e.$y,4,"0");case"M":return o+1;case"MM":return b.s(o+1,2,"0");case"MMM":return d(n.monthsShort,o,c,3);case"MMMM":return d(c,o);case"D":return e.$D;case"DD":return b.s(e.$D,2,"0");case"d":return String(e.$W);case"dd":return d(n.weekdaysMin,e.$W,u,2);case"ddd":return d(n.weekdaysShort,e.$W,u,3);case"dddd":return u[e.$W];case"H":return String(i);case"HH":return b.s(i,2,"0");case"h":return l(1);case"hh":return l(2);case"a":return m(i,a,!0);case"A":return m(i,a,!1);case"m":return String(a);case"mm":return b.s(a,2,"0");case"s":return String(e.$s);case"ss":return b.s(e.$s,2,"0");case"SSS":return b.s(e.$ms,3,"0");case"Z":return r}return null}(t)||r.replace(":","")}))},v.utcOffset=function(){return 15*-Math.round(this.$d.getTimezoneOffset()/15)},v.diff=function(s,l,f){var m,$=this,y=b.p(l),v=k(s),p=(v.utcOffset()-this.utcOffset())*e,g=this-v,M=function(){return b.m($,v)};switch(y){case d:m=M()/12;break;case c:m=M();break;case h:m=M()/3;break;case u:m=(g-p)/6048e5;break;case o:m=(g-p)/864e5;break;case a:m=g/n;break;case i:m=g/e;break;case r:m=g/t;break;default:m=g}return f?m:b.a(m)},v.daysInMonth=function(){return this.endOf(c).$D},v.$locale=function(){return M[this.$L]},v.locale=function(t,e){if(!t)return this.$L;var n=this.clone(),s=D(t,e,!0);return s&&(n.$L=s),n},v.clone=function(){return b.w(this.$d,this)},v.toDate=function(){return new Date(this.valueOf())},v.toJSON=function(){return this.isValid()?this.toISOString():null},v.toISOString=function(){return this.$d.toISOString()},v.toString=function(){return this.$d.toUTCString()},y}(),_=O.prototype;return k.prototype=_,[["$ms",s],["$s",r],["$m",i],["$H",a],["$W",o],["$M",c],["$y",d],["$D",l]].forEach((function(t){_[t[1]]=function(e){return this.$g(e,t[0],t[1])}})),k.extend=function(t,e){return t.$i||(t(e,O,k),t.$i=!0),k},k.locale=D,k.isDayjs=w,k.unix=function(t){return k(1e3*t)},k.en=M[g],k.Ls=M,k.p={},k}()},676:function(t){t.exports=function(){"use strict";var t,e,n=1e3,s=6e4,r=36e5,i=864e5,a=/\[([^\]]+)]|Y{1,4}|M{1,4}|D{1,2}|d{1,4}|H{1,2}|h{1,2}|a|A|m{1,2}|s{1,2}|Z{1,2}|SSS/g,o=31536e6,u=2628e6,c=/^(-|\+)?P(?:([-+]?[0-9,.]*)Y)?(?:([-+]?[0-9,.]*)M)?(?:([-+]?[0-9,.]*)W)?(?:([-+]?[0-9,.]*)D)?(?:T(?:([-+]?[0-9,.]*)H)?(?:([-+]?[0-9,.]*)M)?(?:([-+]?[0-9,.]*)S)?)?$/,h={years:o,months:u,days:i,hours:r,minutes:s,seconds:n,milliseconds:1,weeks:6048e5},d=function(t){return t instanceof p},l=function(t,e,n){return new p(t,n,e.$l)},f=function(t){return e.p(t)+"s"},m=function(t){return t<0},$=function(t){return m(t)?Math.ceil(t):Math.floor(t)},y=function(t){return Math.abs(t)},v=function(t,e){return t?m(t)?{negative:!0,format:""+y(t)+e}:{negative:!1,format:""+t+e}:{negative:!1,format:""}},p=function(){function m(t,e,n){var s=this;if(this.$d={},this.$l=n,void 0===t&&(this.$ms=0,this.parseFromMilliseconds()),e)return l(t*h[f(e)],this);if("number"==typeof t)return this.$ms=t,this.parseFromMilliseconds(),this;if("object"==typeof t)return Object.keys(t).forEach((function(e){s.$d[f(e)]=t[e]})),this.calMilliseconds(),this;if("string"==typeof t){var r=t.match(c);if(r){var i=r.slice(2).map((function(t){return null!=t?Number(t):0}));return this.$d.years=i[0],this.$d.months=i[1],this.$d.weeks=i[2],this.$d.days=i[3],this.$d.hours=i[4],this.$d.minutes=i[5],this.$d.seconds=i[6],this.calMilliseconds(),this}}return this}var y=m.prototype;return y.calMilliseconds=function(){var t=this;this.$ms=Object.keys(this.$d).reduce((function(e,n){return e+(t.$d[n]||0)*h[n]}),0)},y.parseFromMilliseconds=function(){var t=this.$ms;this.$d.years=$(t/o),t%=o,this.$d.months=$(t/u),t%=u,this.$d.days=$(t/i),t%=i,this.$d.hours=$(t/r),t%=r,this.$d.minutes=$(t/s),t%=s,this.$d.seconds=$(t/n),t%=n,this.$d.milliseconds=t},y.toISOString=function(){var t=v(this.$d.years,"Y"),e=v(this.$d.months,"M"),n=+this.$d.days||0;this.$d.weeks&&(n+=7*this.$d.weeks);var s=v(n,"D"),r=v(this.$d.hours,"H"),i=v(this.$d.minutes,"M"),a=this.$d.seconds||0;this.$d.milliseconds&&(a+=this.$d.milliseconds/1e3,a=Math.round(1e3*a)/1e3);var o=v(a,"S"),u=t.negative||e.negative||s.negative||r.negative||i.negative||o.negative,c=r.format||i.format||o.format?"T":"",h=(u?"-":"")+"P"+t.format+e.format+s.format+c+r.format+i.format+o.format;return"P"===h||"-P"===h?"P0D":h},y.toJSON=function(){return this.toISOString()},y.format=function(t){var n=t||"YYYY-MM-DDTHH:mm:ss",s={Y:this.$d.years,YY:e.s(this.$d.years,2,"0"),YYYY:e.s(this.$d.years,4,"0"),M:this.$d.months,MM:e.s(this.$d.months,2,"0"),D:this.$d.days,DD:e.s(this.$d.days,2,"0"),H:this.$d.hours,HH:e.s(this.$d.hours,2,"0"),m:this.$d.minutes,mm:e.s(this.$d.minutes,2,"0"),s:this.$d.seconds,ss:e.s(this.$d.seconds,2,"0"),SSS:e.s(this.$d.milliseconds,3,"0")};return n.replace(a,(function(t,e){return e||String(s[t])}))},y.as=function(t){return this.$ms/h[f(t)]},y.get=function(t){var e=this.$ms,n=f(t);return"milliseconds"===n?e%=1e3:e="weeks"===n?$(e/h[n]):this.$d[n],e||0},y.add=function(t,e,n){var s;return s=e?t*h[f(e)]:d(t)?t.$ms:l(t,this).$ms,l(this.$ms+s*(n?-1:1),this)},y.subtract=function(t,e){return this.add(t,e,!0)},y.locale=function(t){var e=this.clone();return e.$l=t,e},y.clone=function(){return l(this.$ms,this)},y.humanize=function(e){return t().add(this.$ms,"ms").locale(this.$l).fromNow(!e)},y.valueOf=function(){return this.asMilliseconds()},y.milliseconds=function(){return this.get("milliseconds")},y.asMilliseconds=function(){return this.as("milliseconds")},y.seconds=function(){return this.get("seconds")},y.asSeconds=function(){return this.as("seconds")},y.minutes=function(){return this.get("minutes")},y.asMinutes=function(){return this.as("minutes")},y.hours=function(){return this.get("hours")},y.asHours=function(){return this.as("hours")},y.days=function(){return this.get("days")},y.asDays=function(){return this.as("days")},y.weeks=function(){return this.get("weeks")},y.asWeeks=function(){return this.as("weeks")},y.months=function(){return this.get("months")},y.asMonths=function(){return this.as("months")},y.years=function(){return this.get("years")},y.asYears=function(){return this.as("years")},m}(),g=function(t,e,n){return t.add(e.years()*n,"y").add(e.months()*n,"M").add(e.days()*n,"d").add(e.hours()*n,"h").add(e.minutes()*n,"m").add(e.seconds()*n,"s").add(e.milliseconds()*n,"ms")};return function(n,s,r){t=r,e=r().$utils(),r.duration=function(t,e){var n=r.locale();return l(t,{$l:n},e)},r.isDuration=d;var i=s.prototype.add,a=s.prototype.subtract;s.prototype.add=function(t,e){return d(t)?g(this,t,1):i.bind(this)(t,e)},s.prototype.subtract=function(t,e){return d(t)?g(this,t,-1):a.bind(this)(t,e)}}}()},610:function(t,e,n){"use strict";var s=this&&this.__awaiter||function(t,e,n,s){return new(n||(n=Promise))((function(r,i){function a(t){try{u(s.next(t))}catch(t){i(t)}}function o(t){try{u(s.throw(t))}catch(t){i(t)}}function u(t){var e;t.done?r(t.value):(e=t.value,e instanceof n?e:new n((function(t){t(e)}))).then(a,o)}u((s=s.apply(t,e||[])).next())}))},r=this&&this.__importDefault||function(t){return t&&t.__esModule?t:{default:t}};Object.defineProperty(e,"__esModule",{value:!0});const i=r(n(795)),a=r(n(676));i.default.extend(a.default);class o{constructor(){this.element=o.createElement(),this.blink=0}start(t){if(!t)return this.showBuilding();this.nextBuild=(0,i.default)(t).add(1,"day");const e=(0,i.default)();this.nextBuild<=e&&(this.nextBuild=this.nextBuild.add(i.default.duration(e.diff(this.nextBuild)).days()+1,"days")),this.monitor()}static createElement(){const t=document.createElement("div");return t.classList.add("countdown"),t.innerHTML="Initializing...",document.getElementById("body").appendChild(t),t}monitor(){this.timer=window.setInterval((()=>this.showNextUpdate()),500),this.showNextUpdate()}showNextUpdate(){const t=this.calculateDuration();if(t.asMilliseconds()<=0)return clearInterval(this.timer),this.showReady();const e=t.format("[<span>]HH:mm.ss[</span>]");this.element.innerHTML="Next update in "+((this.blink^=1)?e:e.replace(/:/," "))}showBuilding(){this.element.classList.add("building"),this.element.innerHTML="Building update"+"<span>.</span>".repeat(3)}showReady(){this.element.classList.add("ready"),this.element.innerHTML='<a onclick="location.reload()">Ready for launch</a>'}calculateDuration(){return i.default.duration(this.nextBuild.diff((0,i.default)()))}}e.default=o,function(){s(this,void 0,void 0,(function*(){const t=new o,e=yield(yield fetch("https://api.github.com/repos/250/Steam-250/actions/workflows/Build.yml/runs?actor=Azure-bot&per_page=1")).json();t.start("completed"===e.workflow_runs[0].status?e.workflow_runs[0].updated_at:null)}))}()},438:()=>{"use strict";new class{constructor(){var t;this.form=document.querySelector(".ranking .filter form"),this.filtersButton=null===(t=this.form)||void 0===t?void 0:t.previousElementSibling,this.checks=[],this.form&&(this.checks=[...this.form.querySelectorAll("input[type=checkbox]")],this.initFilterForm(),this.loadState())}initFilterForm(){this.filtersButton.addEventListener("click",(t=>this.form.classList.toggle("open"))),this.form.querySelector("form > button.ok").addEventListener("click",(t=>{this.saveState(),this.form.classList.remove("open"),t.preventDefault()})),this.form.querySelector("form > button.cancel").addEventListener("click",(t=>{this.loadState(),this.form.classList.remove("open"),t.preventDefault()})),this.form.querySelector("form > button.reset").addEventListener("click",(t=>{setTimeout((()=>this.filterApps()))})),this.checks.forEach((t=>t.addEventListener("change",(t=>this.filterApps()))))}filterApps(){const t=document.querySelectorAll("#body .ranking > div[id]"),e=this.checks.filter((t=>this.form.querySelector("fieldset").contains(t))),n=e.filter((t=>t.checked));let s=0;this.filtersButton.getAttribute("data-filtered")||n.length<e.length||this.form.owned.checked?t.forEach((t=>{let e=!t.querySelector(".platforms")||n.some((e=>!!t.querySelector(".platforms > ."+e.name)));this.form.owned.checked&&e&&(e=!t.querySelector("a.owned")),t.classList.toggle("filtered",!e),e&&(t.classList.remove("primary","secondary"),t.classList.add(1&++s?"secondary":"primary"))})):s=t.length;const r=t.length-s;this.filtersButton.setAttribute("data-filtered",r?`[-${r}]`:"")}saveState(){let t={};return this.checks.forEach((e=>t[e.name]=e.checked)),localStorage.setItem("filter",JSON.stringify(t)),t}loadState(){let t;(t=this.validateState())||(t=this.saveState());for(const[e,n]of Object.entries(t))this.form[e]&&(this.form[e].checked=n);this.filterApps()}validateState(){try{return JSON.parse(localStorage.getItem("filter"))}catch(t){return!1}}}}},e={};function n(s){var r=e[s];if(void 0!==r)return r.exports;var i=e[s]={exports:{}};return t[s].call(i.exports,i,i.exports,n),i.exports}n(610);n(438)})();