(()=>{var t={351:(t,e,n)=>{"use strict";var i=n(772),o=n(181),a=n.n(o),r=n(342);class s{constructor(){this.initMenuScrollbarTransitions(),this.constrainDropdownMenuPositions(),r.Z.initTristateCheckboxes(),this.initLogInOut(),a().syncLoginUi(),s.tryRemoveAds(),this.scrollToCurrentHash(),this.overrideHashChange(),this.overrideFixedLinks(),this.initSearchValue(),this.initAppLinkMenu(),this.initRankingHoverItems()}initLogInOut(){const t=document.querySelector("#lout form");t?(t["openid.realm"].value="https://club.steam250.com",t["openid.return_to"].value=`https://club.steam250.com/steam/login?r=${location.origin+location.pathname}`,document.querySelector("#lout button").addEventListener("click",(t=>localStorage.setItem("login","sync"))),document.querySelector("#lin button").addEventListener("click",(t=>a().logout())),"sync"===localStorage.getItem("login")&&(localStorage.removeItem("login"),a().syncLogin())):console.debug("Steam user area unavailable: skipped.")}initMenuScrollbarTransitions(){const t="t11g";document.querySelectorAll("ol.menu li").forEach((e=>{const n=e.querySelector("ol");n&&(e.addEventListener("mouseenter",(e=>0===n.clientHeight&&n.classList.add(t))),e.addEventListener("mouseleave",(e=>n.classList.add(t))))})),document.querySelectorAll("ol.menu > li ol").forEach((e=>{e.addEventListener("transitionend",(n=>e.classList.remove(t))),e.addEventListener("wheel",(t=>{e.clientHeight+e.scrollTop+t.deltaY>e.scrollHeight?(e.scrollTop=e.scrollHeight,t.preventDefault()):e.scrollTop+t.deltaY<0&&(e.scrollTop=0,t.preventDefault()),t.stopPropagation()}))}))}constrainDropdownMenuPositions(){document.querySelectorAll("ol.menu > li > ol").forEach((t=>{const e=t.getBoundingClientRect();e.left<0&&(t.style.left=`calc(${getComputedStyle(t).left} - ${e.left}px)`),e.right>document.documentElement.clientWidth&&(t.style.left=`calc(${getComputedStyle(t).left} - ${e.right-document.documentElement.clientWidth}px)`)}))}scrollToCurrentHash(){addEventListener("load",(t=>this.scrollToHash(location.hash)))}overrideHashChange(){addEventListener("hashchange",(t=>this.scrollToHash(location.hash)))}overrideFixedLinks(){document.querySelectorAll(".fixedlinks a").forEach((t=>{t.addEventListener("click",(e=>{this.scrollToHash(t.hash),e.preventDefault()}))}))}scrollToHash(t){const e="highlight";if(document.querySelectorAll(".ranking [id]").forEach((t=>t.classList.remove(e))),!t)return;const n=document.querySelector("ol.menu").getBoundingClientRect().height,i=this.resolveHashTarget(t);if(i){let t=i.getBoundingClientRect().top-n;const o=document.querySelector(".applist, .main.ranking");if(o&&o.contains(i)){if(i.classList.add(e),function(t){const e=t.getBoundingClientRect();return e.top>=n&&e.left>=0&&e.bottom<=innerHeight&&e.right<=innerWidth}(i))return;t+=i.getBoundingClientRect().height/2-innerHeight/3}scrollTo(scrollX,scrollY+Math.ceil(t))}}resolveHashTarget(t){if(t.startsWith("#app/")){let[,e,n]=t.split("/",3),i=document.querySelector(`.applist a[href$="/${e}"], .ranking a[href$="/${e}"]`);return i?i.closest("[id]"):void console.error(`Couldn't find game on this ranking: "${decodeURIComponent(n)}".`)}return document.getElementById(t.substr(1))}static isLoggedIn(){return a().isLoggedIn()}static syncLogin(){return a().syncLogin()}static syncLogout(){return a().syncLogout()}static showAds(){const t=localStorage.getItem("user");if(t){const e=JSON.parse(t);return!(e.hasOwnProperty("noads")&&e.noads)}return!0}static tryRemoveAds(){s.showAds()||document.querySelectorAll("ins").forEach((t=>t.remove()))}initSearchValue(){const t=(0,i.t)("q");null!==t&&document.querySelectorAll("input[name=q]").forEach((e=>e.value=t.replace(/\+/g," ")))}initAppLinkMenu(){const t=document.getElementById("linkmenu"),e="show";let n;document.querySelectorAll(".ranking .links").forEach((i=>{i.addEventListener("click",(o=>{t.style.top=i.offsetTop+i.offsetHeight+5+"px",t.style.left=i.offsetLeft+"px",t.querySelector("a:first-of-type > span").innerHTML=i.closest("[id]").id,t.classList.toggle(e,n!==i||void 0),i.classList.toggle(e,t.classList.contains(e)),n=i,o.preventDefault()})),i.addEventListener("blur",(n=>{t.classList.remove(e),i.classList.remove(e)}))})),document.querySelectorAll("#linkmenu a").forEach((t=>{t.addEventListener("click",(e=>{if(t.classList.contains("cp")&&(t.classList.contains("rank")&&this.copyToClipboard(n.href),t.classList.contains("app"))){const t=this.findSteamAppId(n.closest("[id]")),e=encodeURIComponent(this.findSteamAppName(n.closest("div")));this.copyToClipboard(`${location.origin}${location.pathname}#app/${t}/${e}`)}}))}))}initRankingHoverItems(){document.querySelectorAll(".compact.ranking li > .title").forEach((t=>{const e=t.appendChild(t.cloneNode(!0));e.style.pointerEvents="none",t.addEventListener("mouseenter",(t=>{e.classList.remove("animate"),e.offsetWidth,e.classList.add("animate")})),t.addEventListener("animationend",(t=>{e.classList.remove("animate")}))}))}findSteamAppId(t){const e=t.querySelector("img[src]");if(e)return e.src.match(/\/(\d+)\//)[1]}findSteamAppName(t){return t.querySelector(".title > a").innerText}copyToClipboard(t){if(window.clipboardData&&window.clipboardData.setData)return clipboardData.setData("Text",t);if(document.queryCommandSupported&&document.queryCommandSupported("copy")){const e=document.createElement("textarea");e.textContent=t,e.style.position="fixed",document.body.appendChild(e),e.select();try{return document.execCommand("copy")}catch(t){return console.debug("Failed to copy clipboard data."),!1}finally{document.body.removeChild(e)}}}}new s,window.S250=s},804:()=>{new class{constructor(){!1!==this.initDom()&&(this.initVideo(),this.initVideoLinks(),this.initKeyboard(),this.initFooterScroll())}initDom(){if(!document.body)return console.debug("Video player aborted: body not ready."),!1;this.container=document.body.appendChild(document.createElement("div")),this.frame=this.container.appendChild(document.createElement("div")),this.header=this.frame.appendChild(document.createElement("header")),this.video=this.frame.appendChild(document.createElement("video")),this.footer=this.frame.appendChild(document.createElement("footer")),this.page=document.querySelector("#page"),this.container.id="video-container",this.container.addEventListener("click",(t=>t.target===this.container&&this.deactivate()))}initVideo(){this.video.controls=this.video.autoplay=!0;const t=this.loadVolumeState();this.video.volume=t.volume,this.video.muted=t.muted,this.video.addEventListener("volumechange",(t=>this.saveVolumeState())),this.video.addEventListener("loadedmetadata",(t=>{this.video.removeAttribute("width"),this.video.removeAttribute("height"),this.frame.style.maxWidth=this.video.videoWidth+"px"}))}initVideoLinks(){document.querySelectorAll("[data-video]").forEach((t=>t.addEventListener("click",(e=>{"A"===e.target.tagName&&e.target!==t||(this.loadThumbs(t.getAttribute("data-video").split(",")),this.header.innerHTML=t.getAttribute("data-title"),"href"in t&&(this.header.innerHTML=`<a href="${t.href}">${this.header.innerHTML}</a>`),this.footer.firstChild.click(),e.stopPropagation(),e.preventDefault())}))))}initKeyboard(){document.addEventListener("keydown",(t=>"Escape"===t.key&&this.deactivate()))}initFooterScroll(){this.footer.addEventListener("mousemove",(t=>{const e=this.footer.getBoundingClientRect();this.footer.scroll({left:(this.footer.scrollWidth-e.width)*((t.clientX-e.x)/e.width*1.2-.1),behavior:"undefined"!=typeof InstallTrigger?"smooth":"auto"})}))}loadThumbs(t){for(;this.footer.lastChild;)this.footer.removeChild(this.footer.lastChild);for(let e=0;e<t.length;++e){const n=this.footer.appendChild(document.createElement("div"));n.setAttribute("data-index",e+1);const i=n.appendChild(document.createElement("img"));i.src=`https://cdn.cloudflare.steamstatic.com/steam/apps/${t[e]}/movie.184x123.jpg`,n.addEventListener("click",(o=>{this.play(t[e]),n.parentNode.querySelectorAll("img").forEach((t=>t.classList.remove("active"))),i.classList.add("active"),this.header.setAttribute("data-video-id",e+1),this.header.setAttribute("data-videos",t.length)}))}}loadVolumeState(){return localStorage.hasOwnProperty("video.volume")?JSON.parse(localStorage.getItem("video.volume")):{volume:.5,muted:!1}}saveVolumeState(){localStorage.setItem("video.volume",JSON.stringify({volume:this.video.volume,muted:this.video.muted}))}activate(){this.container.classList.add("active"),this.page.classList.add("video")}deactivate(){this.video.pause(),this.container.classList.remove("active"),this.page.classList.remove("video")}play(t){for(this.video.width=this.video.clientWidth,this.video.height=this.video.clientHeight;this.video.lastChild;)this.video.removeChild(this.video.lastChild);const e=this.video.appendChild(document.createElement("source"));e.src=`https://steamcdn-a.akamaihd.net/steam/apps/${t}/movie480.webm`,e.type="video/webm";const n=this.video.appendChild(document.createElement("source"));n.src=`https://steamcdn-a.akamaihd.net/steam/apps/${t}/movie480.mp4`,n.type="video/mp4",this.activate(),this.video.load()}}},927:()=>{},984:()=>{},342:(t,e)=>{"use strict";var n;(function(t){t[t.Middle=0]="Middle",t[t.Right=1]="Right",t[t.Left=2]="Left"})(n||(n={}));e.Z=class{static initTristateCheckboxes(){document.querySelectorAll("div.tri").forEach((t=>{var e,i;const o=[...t.querySelectorAll("input")],a=o[0],r=a.parentNode.querySelector("span"),s=parseFloat(getComputedStyle(r,":before").width)/3,c=2*s,l=a.go2=t=>{d=t,o.filter((e=>e.value===t.toString()))[0].checked=!0,a.parentElement.dataset.state=t.toString()};let d=+(null!==(i=null===(e=o.filter((t=>t.checked))[0])||void 0===e?void 0:e.value)&&void 0!==i?i:0);l(d),a.addEventListener("click",(t=>{const e=t.clientX-r.getBoundingClientRect().x;e>=s&&e<=c?l(n.Middle):d===n.Middle?l(e>c?n.Right:n.Left):d===n.Left?l(e<s?n.Middle:n.Right):l(e>c?n.Middle:n.Left)}))}))}}},791:function(t,e,n){"use strict";var i=this&&this.__importDefault||function(t){return t&&t.__esModule?t:{default:t}};Object.defineProperty(e,"__esModule",{value:!0});new(i(n(108)).default)},772:(t,e)=>{"use strict";e.t=void 0,e.t=function(t){const e=RegExp("[?&]"+t+"=([^&]*)").exec(location.search);return e&&decodeURIComponent(e[1])}},181:function(t,e){"use strict";var n=this&&this.__awaiter||function(t,e,n,i){return new(n||(n=Promise))((function(o,a){function r(t){try{c(i.next(t))}catch(t){a(t)}}function s(t){try{c(i.throw(t))}catch(t){a(t)}}function c(t){var e;t.done?o(t.value):(e=t.value,e instanceof n?e:new n((function(t){t(e)}))).then(r,s)}c((i=i.apply(t,e||[])).next())}))};Object.defineProperty(e,"__esModule",{value:!0});e.default=class{static isLoggedIn(){return localStorage.hasOwnProperty("user")}static logout(){document.body.insertAdjacentHTML("beforeend",`\n            <form method="post" action="https://club.steam250.com/logout" name="logout">\n                <input type="hidden" name="r" value="${location.origin+location.pathname}">\n            </form>\n        `),this.syncLogout(),document.forms.namedItem("logout").submit()}static syncLogin(){return n(this,void 0,void 0,(function*(){const t=yield fetch("https://club.steam250.com/api/whoami",{credentials:"include"}),e=yield t.text();if(!t.ok)return console.error("Login sync failed:",t.status,t.statusText,e);const[n,i,o]=e.split("\0",3);localStorage.setItem("user",JSON.stringify({id:n,name:i.substring(40),avatar:i.substring(0,40),noads:"1"===o})),this.postClub250Message("login synced"),S250.tryRemoveAds(),this.syncLoginUi(),this.syncGames()}))}static syncLogout(){localStorage.removeItem("user"),this.postClub250Message("logout synced")}static syncGames(){return n(this,void 0,void 0,(function*(){const t=localStorage.getItem("user");if(!t)return;const e=JSON.parse(t).id,n=(yield(yield fetch(encodeURI(`https://cors.bridged.cc/https://steamcommunity.com/profiles/[U:1:${e}]/games/?tab=all`),{headers:{"x-cors-grida-api-key":"5e64a881-1bca-4be2-9d93-2443d53458b5"}})).text()).match(/var rgGames = ([^\n]+);/);if(!n||2!==n.length)return void alert("Unable to load your profile. This is usually because your Steam profile is not public.\nTry setting your Steam Community profile visibility to public, then refresh this page to try again.");const i=JSON.parse(n[1]).reduce(((t,e)=>(t[e.appid]=e.hours_forever||0,t)),{});Object.keys(i).length?(localStorage.setItem("games",JSON.stringify(i)),this.markOwnedGames()):alert("No games found in your account! This is usually because your game details are not public.\nTry setting your game details to public on your Steam Community privacy settings page, then refresh this page to try again.")}))}static syncLoginUi(){const t=document.getElementById("user");if(!t)return;const e=t.classList;e.remove("lin","lout"),e.add(this.isLoggedIn()?"lin":"lout"),this.isLoggedIn()&&this.updateUserBar()}static updateUserBar(){const t=localStorage.getItem("user");if(!t)return;const e=JSON.parse(t),n=document.querySelector("#lin .avatar");if(!n)return;n.href="https://club.steam250.com/me";const i=n.appendChild(document.createElement("img"));i.alt=i.title=e.name,i.src=`https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/${e.avatar.substring(0,2)}/${e.avatar}.jpg'`,this.markOwnedGames()}static markOwnedGames(){const t=localStorage.getItem("games"),e=document.querySelector("#user .owned");if(!t||!e)return;const n=JSON.parse(t),i=document.querySelectorAll(".main.ranking > div[id] > div:first-of-type > a");i.forEach((t=>{const e=t.href.match(/\/app\/(\d+)/)[1];n.hasOwnProperty(e)&&(t.classList.add("owned"),t.setAttribute("data-content",n[e]+" hours"))}));const o=document.querySelectorAll(".main.ranking .owned").length,a=i.length;e.innerText=a?`${o}/${a} (${Math.round(o/a*100)}%)`:"n/a"}static postClub250Message(t){parent!==window&&parent.postMessage(t,"https://club.steam250.com")}}},108:function(t){t.exports=function(){"use strict";function t(){return t=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var i in n)Object.prototype.hasOwnProperty.call(n,i)&&(t[i]=n[i])}return t},t.apply(this,arguments)}var e="undefined"!=typeof window,n=e&&!("onscroll"in window)||"undefined"!=typeof navigator&&/(gle|ing|ro)bot|crawl|spider/i.test(navigator.userAgent),i=e&&"IntersectionObserver"in window,o=e&&"classList"in document.createElement("p"),a=e&&window.devicePixelRatio>1,r={elements_selector:".lazy",container:n||e?document:null,threshold:300,thresholds:null,data_src:"src",data_srcset:"srcset",data_sizes:"sizes",data_bg:"bg",data_bg_hidpi:"bg-hidpi",data_bg_multi:"bg-multi",data_bg_multi_hidpi:"bg-multi-hidpi",data_bg_set:"bg-set",data_poster:"poster",class_applied:"applied",class_loading:"loading",class_loaded:"loaded",class_error:"error",class_entered:"entered",class_exited:"exited",unobserve_completed:!0,unobserve_entered:!1,cancel_on_exit:!0,callback_enter:null,callback_exit:null,callback_applied:null,callback_loading:null,callback_loaded:null,callback_error:null,callback_finish:null,callback_cancel:null,use_native:!1,restore_on_error:!1},s=function(e){return t({},r,e)},c=function(t,e){var n,i="LazyLoad::Initialized",o=new t(e);try{n=new CustomEvent(i,{detail:{instance:o}})}catch(t){(n=document.createEvent("CustomEvent")).initCustomEvent(i,!1,!1,{instance:o})}window.dispatchEvent(n)},l="src",d="srcset",u="sizes",h="poster",m="llOriginalAttrs",f="data",g="loading",p="loaded",v="applied",y="error",b="native",_="data-",L="ll-status",E=function(t,e){return t.getAttribute(_+e)},S=function(t){return E(t,L)},k=function(t,e){return function(t,e,n){var i="data-ll-status";null!==n?t.setAttribute(i,n):t.removeAttribute(i)}(t,0,e)},C=function(t){return k(t,null)},I=function(t){return null===S(t)},w=function(t){return S(t)===b},A=[g,p,v,y],x=function(t,e,n,i){t&&(void 0===i?void 0===n?t(e):t(e,n):t(e,n,i))},T=function(t,e){o?t.classList.add(e):t.className+=(t.className?" ":"")+e},O=function(t,e){o?t.classList.remove(e):t.className=t.className.replace(new RegExp("(^|\\s+)"+e+"(\\s+|$)")," ").replace(/^\s+/,"").replace(/\s+$/,"")},M=function(t){return t.llTempImage},q=function(t,e){if(e){var n=e._observer;n&&n.unobserve(t)}},H=function(t,e){t&&(t.loadingCount+=e)},N=function(t,e){t&&(t.toLoadCount=e)},R=function(t){for(var e,n=[],i=0;e=t.children[i];i+=1)"SOURCE"===e.tagName&&n.push(e);return n},$=function(t,e){var n=t.parentNode;n&&"PICTURE"===n.tagName&&R(n).forEach(e)},D=function(t,e){R(t).forEach(e)},P=[l],V=[l,h],B=[l,d,u],U=[f],j=function(t){return!!t[m]},G=function(t){return t[m]},J=function(t){return delete t[m]},F=function(t,e){if(!j(t)){var n={};e.forEach((function(e){n[e]=t.getAttribute(e)})),t[m]=n}},z=function(t,e){if(j(t)){var n=G(t);e.forEach((function(e){!function(t,e,n){n?t.setAttribute(e,n):t.removeAttribute(e)}(t,e,n[e])}))}},W=function(t,e,n){T(t,e.class_applied),k(t,v),n&&(e.unobserve_completed&&q(t,e),x(e.callback_applied,t,n))},X=function(t,e,n){T(t,e.class_loading),k(t,g),n&&(H(n,1),x(e.callback_loading,t,n))},Y=function(t,e,n){n&&t.setAttribute(e,n)},K=function(t,e){Y(t,u,E(t,e.data_sizes)),Y(t,d,E(t,e.data_srcset)),Y(t,l,E(t,e.data_src))},Z={IMG:function(t,e){$(t,(function(t){F(t,B),K(t,e)})),F(t,B),K(t,e)},IFRAME:function(t,e){F(t,P),Y(t,l,E(t,e.data_src))},VIDEO:function(t,e){D(t,(function(t){F(t,P),Y(t,l,E(t,e.data_src))})),F(t,V),Y(t,h,E(t,e.data_poster)),Y(t,l,E(t,e.data_src)),t.load()},OBJECT:function(t,e){F(t,U),Y(t,f,E(t,e.data_src))}},Q=["IMG","IFRAME","VIDEO","OBJECT"],tt=function(t,e){!e||function(t){return t.loadingCount>0}(e)||function(t){return t.toLoadCount>0}(e)||x(t.callback_finish,e)},et=function(t,e,n){t.addEventListener(e,n),t.llEvLisnrs[e]=n},nt=function(t,e,n){t.removeEventListener(e,n)},it=function(t){return!!t.llEvLisnrs},ot=function(t){if(it(t)){var e=t.llEvLisnrs;for(var n in e){var i=e[n];nt(t,n,i)}delete t.llEvLisnrs}},at=function(t,e,n){!function(t){delete t.llTempImage}(t),H(n,-1),function(t){t&&(t.toLoadCount-=1)}(n),O(t,e.class_loading),e.unobserve_completed&&q(t,n)},rt=function(t,e,n){var i=M(t)||t;it(i)||function(t,e,n){it(t)||(t.llEvLisnrs={});var i="VIDEO"===t.tagName?"loadeddata":"load";et(t,i,e),et(t,"error",n)}(i,(function(o){!function(t,e,n,i){var o=w(e);at(e,n,i),T(e,n.class_loaded),k(e,p),x(n.callback_loaded,e,i),o||tt(n,i)}(0,t,e,n),ot(i)}),(function(o){!function(t,e,n,i){var o=w(e);at(e,n,i),T(e,n.class_error),k(e,y),x(n.callback_error,e,i),n.restore_on_error&&z(e,B),o||tt(n,i)}(0,t,e,n),ot(i)}))},st=function(t,e,n){!function(t){return Q.indexOf(t.tagName)>-1}(t)?function(t,e,n){!function(t){t.llTempImage=document.createElement("IMG")}(t),rt(t,e,n),function(t){j(t)||(t[m]={backgroundImage:t.style.backgroundImage})}(t),function(t,e,n){var i=E(t,e.data_bg),o=E(t,e.data_bg_hidpi),r=a&&o?o:i;r&&(t.style.backgroundImage='url("'.concat(r,'")'),M(t).setAttribute(l,r),X(t,e,n))}(t,e,n),function(t,e,n){var i=E(t,e.data_bg_multi),o=E(t,e.data_bg_multi_hidpi),r=a&&o?o:i;r&&(t.style.backgroundImage=r,W(t,e,n))}(t,e,n),function(t,e,n){var i=E(t,e.data_bg_set);if(i){var o=i.split("|"),a=o.map((function(t){return"image-set(".concat(t,")")}));t.style.backgroundImage=a.join(),""===t.style.backgroundImage&&(a=o.map((function(t){return"-webkit-image-set(".concat(t,")")})),t.style.backgroundImage=a.join()),W(t,e,n)}}(t,e,n)}(t,e,n):function(t,e,n){rt(t,e,n),function(t,e,n){var i=Z[t.tagName];i&&(i(t,e),X(t,e,n))}(t,e,n)}(t,e,n)},ct=function(t){t.removeAttribute(l),t.removeAttribute(d),t.removeAttribute(u)},lt=function(t){$(t,(function(t){z(t,B)})),z(t,B)},dt={IMG:lt,IFRAME:function(t){z(t,P)},VIDEO:function(t){D(t,(function(t){z(t,P)})),z(t,V),t.load()},OBJECT:function(t){z(t,U)}},ut=function(t,e){(function(t){var e=dt[t.tagName];e?e(t):function(t){if(j(t)){var e=G(t);t.style.backgroundImage=e.backgroundImage}}(t)})(t),function(t,e){I(t)||w(t)||(O(t,e.class_entered),O(t,e.class_exited),O(t,e.class_applied),O(t,e.class_loading),O(t,e.class_loaded),O(t,e.class_error))}(t,e),C(t),J(t)},ht=["IMG","IFRAME","VIDEO"],mt=function(t){return t.use_native&&"loading"in HTMLImageElement.prototype},ft=function(t,e,n){t.forEach((function(t){return function(t){return t.isIntersecting||t.intersectionRatio>0}(t)?function(t,e,n,i){var o=function(t){return A.indexOf(S(t))>=0}(t);k(t,"entered"),T(t,n.class_entered),O(t,n.class_exited),function(t,e,n){e.unobserve_entered&&q(t,n)}(t,n,i),x(n.callback_enter,t,e,i),o||st(t,n,i)}(t.target,t,e,n):function(t,e,n,i){I(t)||(T(t,n.class_exited),function(t,e,n,i){n.cancel_on_exit&&function(t){return S(t)===g}(t)&&"IMG"===t.tagName&&(ot(t),function(t){$(t,(function(t){ct(t)})),ct(t)}(t),lt(t),O(t,n.class_loading),H(i,-1),C(t),x(n.callback_cancel,t,e,i))}(t,e,n,i),x(n.callback_exit,t,e,i))}(t.target,t,e,n)}))},gt=function(t){return Array.prototype.slice.call(t)},pt=function(t){return t.container.querySelectorAll(t.elements_selector)},vt=function(t){return function(t){return S(t)===y}(t)},yt=function(t,e){return function(t){return gt(t).filter(I)}(t||pt(e))},bt=function(t,n){var o=s(t);this._settings=o,this.loadingCount=0,function(t,e){i&&!mt(t)&&(e._observer=new IntersectionObserver((function(n){ft(n,t,e)}),function(t){return{root:t.container===document?null:t.container,rootMargin:t.thresholds||t.threshold+"px"}}(t)))}(o,this),function(t,n){e&&(n._onlineHandler=function(){!function(t,e){var n;(n=pt(t),gt(n).filter(vt)).forEach((function(e){O(e,t.class_error),C(e)})),e.update()}(t,n)},window.addEventListener("online",n._onlineHandler))}(o,this),this.update(n)};return bt.prototype={update:function(t){var e,o,a=this._settings,r=yt(t,a);N(this,r.length),!n&&i?mt(a)?function(t,e,n){t.forEach((function(t){-1!==ht.indexOf(t.tagName)&&function(t,e,n){t.setAttribute("loading","lazy"),rt(t,e,n),function(t,e){var n=Z[t.tagName];n&&n(t,e)}(t,e),k(t,b)}(t,e,n)})),N(n,0)}(r,a,this):(o=r,function(t){t.disconnect()}(e=this._observer),function(t,e){e.forEach((function(e){t.observe(e)}))}(e,o)):this.loadAll(r)},destroy:function(){this._observer&&this._observer.disconnect(),e&&window.removeEventListener("online",this._onlineHandler),pt(this._settings).forEach((function(t){J(t)})),delete this._observer,delete this._settings,delete this._onlineHandler,delete this.loadingCount,delete this.toLoadCount},loadAll:function(t){var e=this,n=this._settings;yt(t,n).forEach((function(t){q(t,e),st(t,n,e)}))},restoreAll:function(){var t=this._settings;pt(t).forEach((function(e){ut(e,t)}))}},bt.load=function(t,e){var n=s(e);st(t,n)},bt.resetStatus=function(t){C(t)},e&&function(t,e){if(e)if(e.length)for(var n,i=0;n=e[i];i+=1)c(t,n);else c(t,e)}(bt,window.lazyLoadOptions),bt}()}},e={};function n(i){var o=e[i];if(void 0!==o)return o.exports;var a=e[i]={exports:{}};return t[i].call(a.exports,a,a.exports,n),a.exports}n.n=t=>{var e=t&&t.__esModule?()=>t.default:()=>t;return n.d(e,{a:e}),e},n.d=(t,e)=>{for(var i in e)n.o(e,i)&&!n.o(t,i)&&Object.defineProperty(t,i,{enumerable:!0,get:e[i]})},n.o=(t,e)=>Object.prototype.hasOwnProperty.call(t,e),n(351),n(804),n(791),n(927);n(984)})();