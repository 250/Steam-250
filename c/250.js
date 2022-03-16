(()=>{var t={351:(t,e,n)=>{"use strict";var o=n(772),i=n(181),a=n.n(i);class r{constructor(){this.initMenuScrollbarTransitions(),this.constrainDropdownMenuPositions(),this.initLogInOut(),a().syncLoginUi(),r.tryRemoveAds(),this.scrollToCurrentHash(),this.overrideHashChange(),this.overrideFixedLinks(),this.initSearchValue(),this.initAppLinkMenu(),this.initRankingHoverItems(),this.initBoxLinkForm()}initLogInOut(){const t=document.querySelector("#lout form");t?(t["openid.realm"].value="https://club.steam250.com",t["openid.return_to"].value=`https://club.steam250.com/steam/login?r=${location.origin+location.pathname}`,document.querySelector("#lout button").addEventListener("click",(t=>localStorage.setItem("login","sync"))),document.querySelector("#lin button").addEventListener("click",(t=>a().logout())),"sync"===localStorage.getItem("login")&&(localStorage.removeItem("login"),a().syncLogin())):console.debug("Steam user area unavailable: skipped.")}initMenuScrollbarTransitions(){const t="t11g";document.querySelectorAll("ol.menu li").forEach((e=>{const n=e.querySelector("ol");n&&(e.addEventListener("mouseenter",(e=>0===n.clientHeight&&n.classList.add(t))),e.addEventListener("mouseleave",(e=>n.classList.add(t))))})),document.querySelectorAll("ol.menu > li ol").forEach((e=>{e.addEventListener("transitionend",(n=>e.classList.remove(t))),e.addEventListener("wheel",(t=>{e.clientHeight+e.scrollTop+t.deltaY>e.scrollHeight?(e.scrollTop=e.scrollHeight,t.preventDefault()):e.scrollTop+t.deltaY<0&&(e.scrollTop=0,t.preventDefault()),t.stopPropagation()}))}))}constrainDropdownMenuPositions(){document.querySelectorAll("ol.menu > li > ol").forEach((t=>{const e=t.getBoundingClientRect();e.left<0&&(t.style.left=`calc(${getComputedStyle(t).left} - ${e.left}px)`),e.right>document.documentElement.clientWidth&&(t.style.left=`calc(${getComputedStyle(t).left} - ${e.right-document.documentElement.clientWidth}px)`)}))}scrollToCurrentHash(){addEventListener("load",(t=>this.scrollToHash(location.hash)))}overrideHashChange(){addEventListener("hashchange",(t=>this.scrollToHash(location.hash)))}overrideFixedLinks(){document.querySelectorAll(".fixedlinks a").forEach((t=>{t.addEventListener("click",(e=>{this.scrollToHash(t.hash),e.preventDefault()}))}))}scrollToHash(t){const e="highlight";if(document.querySelectorAll(".ranking [id]").forEach((t=>t.classList.remove(e))),!t)return;const n=document.querySelector("ol.menu").getBoundingClientRect().height,o=this.resolveHashTarget(t);if(o){let t=o.getBoundingClientRect().top-n;const i=document.querySelector(".applist, .main.ranking");if(i&&i.contains(o)){if(o.classList.add(e),function(t){const e=t.getBoundingClientRect();return e.top>=n&&e.left>=0&&e.bottom<=innerHeight&&e.right<=innerWidth}(o))return;t+=o.getBoundingClientRect().height/2-innerHeight/3}scrollTo(scrollX,scrollY+Math.ceil(t))}}resolveHashTarget(t){if(t.startsWith("#app/")){let[,e,n]=t.split("/",3),o=document.querySelector(`.applist a[href$="/${e}"], .ranking a[href$="/${e}"]`);return o?o.closest("[id]"):void console.error(`Couldn't find game on this ranking: "${decodeURIComponent(n)}".`)}return document.getElementById(t.substr(1))}static isLoggedIn(){return a().isLoggedIn()}static syncLogin(){return a().syncLogin()}static syncLogout(){return a().syncLogout()}static showAds(){const t=localStorage.getItem("user");if(t){const e=JSON.parse(t);return!(e.hasOwnProperty("noads")&&e.noads)}return!0}static tryRemoveAds(){r.showAds()||document.querySelectorAll("ins").forEach((t=>t.remove()))}initSearchValue(){const t=(0,o.t)("q");null!==t&&document.querySelectorAll("input[name=q]").forEach((e=>e.value=t.replace(/\+/g," ")))}initAppLinkMenu(){const t=document.getElementById("linkmenu"),e="show";let n;document.querySelectorAll(".ranking .links").forEach((o=>{o.addEventListener("click",(i=>{t.style.top=o.offsetTop+o.offsetHeight+5+"px",t.style.left=o.offsetLeft+"px",t.querySelector("a:first-of-type > span").innerHTML=o.closest("[id]").id,t.classList.toggle(e,n!==o||void 0),o.classList.toggle(e,t.classList.contains(e)),n=o,i.preventDefault()})),o.addEventListener("blur",(n=>{t.classList.remove(e),o.classList.remove(e)}))})),document.querySelectorAll("#linkmenu a").forEach((t=>{t.addEventListener("click",(e=>{if(t.classList.contains("cp")&&(t.classList.contains("rank")&&this.copyToClipboard(n.href),t.classList.contains("app"))){const t=this.findSteamAppId(n.closest("[id]")),e=encodeURIComponent(this.findSteamAppName(n.closest("div")));this.copyToClipboard(`${location.origin}${location.pathname}#app/${t}/${e}`)}}))}))}initRankingHoverItems(){document.querySelectorAll(".compact.ranking li > .title").forEach((t=>{const e=t.appendChild(t.cloneNode(!0));e.style.pointerEvents="none",t.addEventListener("mouseenter",(t=>{e.classList.remove("animate"),e.offsetWidth,e.classList.add("animate")})),t.addEventListener("animationend",(t=>{e.classList.remove("animate")}))}))}initBoxLinkForm(){document.querySelectorAll(".boxlink input").forEach((t=>t.addEventListener("click",(t=>t.preventDefault()))))}findSteamAppId(t){const e=t.querySelector("img[src]");if(e)return e.src.match(/\/(\d+)\//)[1]}findSteamAppName(t){return t.querySelector(".title > a").innerText}copyToClipboard(t){if(window.clipboardData&&window.clipboardData.setData)return clipboardData.setData("Text",t);if(document.queryCommandSupported&&document.queryCommandSupported("copy")){const e=document.createElement("textarea");e.textContent=t,e.style.position="fixed",document.body.appendChild(e),e.select();try{return document.execCommand("copy")}catch(t){return console.debug("Failed to copy clipboard data."),!1}finally{document.body.removeChild(e)}}}}new r,window.S250=r},804:()=>{new class{constructor(){!1!==this.initDom()&&(this.initVideo(),this.initVideoLinks(),this.initKeyboard(),this.initFooterScroll())}initDom(){if(!document.body)return console.debug("Video player aborted: body not ready."),!1;this.container=document.body.appendChild(document.createElement("div")),this.frame=this.container.appendChild(document.createElement("div")),this.header=this.frame.appendChild(document.createElement("header")),this.video=this.frame.appendChild(document.createElement("video")),this.footer=this.frame.appendChild(document.createElement("footer")),this.page=document.querySelector("#page"),this.container.id="video-container",this.container.addEventListener("click",(t=>t.target===this.container&&this.deactivate()))}initVideo(){this.video.controls=this.video.autoplay=!0;const t=this.loadVolumeState();this.video.volume=t.volume,this.video.muted=t.muted,this.video.addEventListener("volumechange",(t=>this.saveVolumeState())),this.video.addEventListener("loadedmetadata",(t=>{this.video.removeAttribute("width"),this.video.removeAttribute("height"),this.frame.style.maxWidth=this.video.videoWidth+"px"}))}initVideoLinks(){document.querySelectorAll("[data-video]").forEach((t=>t.addEventListener("click",(e=>{"A"===e.target.tagName&&e.target!==t||(this.loadThumbs(t.getAttribute("data-video").split(",")),this.header.innerHTML=t.getAttribute("data-title"),"href"in t&&(this.header.innerHTML=`<a href="${t.href}">${this.header.innerHTML}</a>`),this.footer.firstChild.click(),e.stopPropagation(),e.preventDefault())}))))}initKeyboard(){document.addEventListener("keydown",(t=>"Escape"===t.key&&this.deactivate()))}initFooterScroll(){this.footer.addEventListener("mousemove",(t=>{const e=this.footer.getBoundingClientRect();this.footer.scroll({left:(this.footer.scrollWidth-e.width)*((t.clientX-e.x)/e.width*1.2-.1),behavior:"undefined"!=typeof InstallTrigger?"smooth":"auto"})}))}loadThumbs(t){for(;this.footer.lastChild;)this.footer.removeChild(this.footer.lastChild);for(let e=0;e<t.length;++e){const n=this.footer.appendChild(document.createElement("div"));n.setAttribute("data-index",e+1);const o=n.appendChild(document.createElement("img"));o.src=`https://cdn.cloudflare.steamstatic.com/steam/apps/${t[e]}/movie.184x123.jpg`,n.addEventListener("click",(i=>{this.play(t[e]),n.parentNode.querySelectorAll("img").forEach((t=>t.classList.remove("active"))),o.classList.add("active"),this.header.setAttribute("data-video-id",e+1),this.header.setAttribute("data-videos",t.length)}))}}loadVolumeState(){return localStorage.hasOwnProperty("video.volume")?JSON.parse(localStorage.getItem("video.volume")):{volume:.5,muted:!1}}saveVolumeState(){localStorage.setItem("video.volume",JSON.stringify({volume:this.video.volume,muted:this.video.muted}))}activate(){this.container.classList.add("active"),this.page.classList.add("video")}deactivate(){this.video.pause(),this.container.classList.remove("active"),this.page.classList.remove("video")}play(t){for(this.video.width=this.video.clientWidth,this.video.height=this.video.clientHeight;this.video.lastChild;)this.video.removeChild(this.video.lastChild);const e=this.video.appendChild(document.createElement("source"));e.src=`https://steamcdn-a.akamaihd.net/steam/apps/${t}/movie480.webm`,e.type="video/webm";const n=this.video.appendChild(document.createElement("source"));n.src=`https://steamcdn-a.akamaihd.net/steam/apps/${t}/movie480.mp4`,n.type="video/mp4",this.activate(),this.video.load()}}},927:()=>{},984:()=>{},791:function(t,e,n){"use strict";var o=this&&this.__importDefault||function(t){return t&&t.__esModule?t:{default:t}};Object.defineProperty(e,"__esModule",{value:!0});new(o(n(108)).default)},772:(t,e)=>{"use strict";e.t=void 0,e.t=function(t){const e=RegExp("[?&]"+t+"=([^&]*)").exec(location.search);return e&&decodeURIComponent(e[1])}},181:function(t,e){"use strict";var n=this&&this.__awaiter||function(t,e,n,o){return new(n||(n=Promise))((function(i,a){function r(t){try{c(o.next(t))}catch(t){a(t)}}function s(t){try{c(o.throw(t))}catch(t){a(t)}}function c(t){var e;t.done?i(t.value):(e=t.value,e instanceof n?e:new n((function(t){t(e)}))).then(r,s)}c((o=o.apply(t,e||[])).next())}))};Object.defineProperty(e,"__esModule",{value:!0});e.default=class{static isLoggedIn(){return localStorage.hasOwnProperty("user")}static logout(){document.body.insertAdjacentHTML("beforeend",`\n            <form method="post" action="https://club.steam250.com/logout" name="logout">\n                <input type="hidden" name="r" value="${location.origin+location.pathname}">\n            </form>\n        `),this.syncLogout(),document.forms.namedItem("logout").submit()}static syncLogin(){return n(this,void 0,void 0,(function*(){const t=yield fetch("https://club.steam250.com/api/whoami",{credentials:"include"}),e=yield t.text();if(!t.ok)return console.error("Login sync failed:",t.status,t.statusText,e);const[n,o,i]=e.split("\0",3);localStorage.setItem("user",JSON.stringify({id:n,name:o.substring(40),avatar:o.substring(0,40),noads:"1"===i})),this.postClub250Message("login synced"),S250.tryRemoveAds(),this.syncLoginUi(),this.syncGames()}))}static syncLogout(){localStorage.removeItem("user"),this.postClub250Message("logout synced")}static syncGames(){return n(this,void 0,void 0,(function*(){const t=localStorage.getItem("user");if(!t)return;const e=JSON.parse(t).id,n=(yield(yield fetch(encodeURI(`https://cors.bridged.cc/https://steamcommunity.com/profiles/[U:1:${e}]/games/?tab=all`),{headers:{"x-cors-grida-api-key":"5e64a881-1bca-4be2-9d93-2443d53458b5"}})).text()).match(/var rgGames = ([^\n]+);/);if(!n||2!==n.length)return void alert("Unable to load your profile. This is usually because your Steam profile is not public.\nTry setting your Steam Community profile visibility to public, then refresh this page to try again.");const o=JSON.parse(n[1]).reduce(((t,e)=>(t[e.appid]=e.hours_forever||0,t)),{});Object.keys(o).length?(localStorage.setItem("games",JSON.stringify(o)),this.markOwnedGames()):alert("No games found in your account! This is usually because your game details are not public.\nTry setting your game details to public on your Steam Community privacy settings page, then refresh this page to try again.")}))}static syncLoginUi(){const t=document.getElementById("user");if(!t)return;const e=t.classList;e.remove("lin","lout"),e.add(this.isLoggedIn()?"lin":"lout"),this.isLoggedIn()&&this.updateUserBar()}static updateUserBar(){const t=localStorage.getItem("user");if(!t)return;const e=JSON.parse(t),n=document.querySelector("#lin .avatar");if(!n)return;n.href="https://club.steam250.com/me";const o=n.appendChild(document.createElement("img"));o.alt=o.title=e.name,o.src=`https://cdn.cloudflare.steamstatic.com/steamcommunity/public/images/avatars/${e.avatar.substring(0,2)}/${e.avatar}.jpg'`,this.markOwnedGames()}static markOwnedGames(){const t=localStorage.getItem("games"),e=document.querySelector("#user .owned");if(!t||!e)return;const n=JSON.parse(t),o=document.querySelectorAll(".main.ranking > div[id] > div:first-of-type > a");o.forEach((t=>{const e=t.href.match(/\/app\/(\d+)/)[1];n.hasOwnProperty(e)&&(t.classList.add("owned"),t.setAttribute("data-content",n[e]+" hours"))}));const i=document.querySelectorAll(".main.ranking .owned").length,a=o.length;e.innerText=a?`${i}/${a} (${Math.round(i/a*100)}%)`:"n/a"}static postClub250Message(t){parent!==window&&parent.postMessage(t,"https://club.steam250.com")}}},108:function(t){t.exports=function(){"use strict";function t(){return t=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var o in n)Object.prototype.hasOwnProperty.call(n,o)&&(t[o]=n[o])}return t},t.apply(this,arguments)}var e="undefined"!=typeof window,n=e&&!("onscroll"in window)||"undefined"!=typeof navigator&&/(gle|ing|ro)bot|crawl|spider/i.test(navigator.userAgent),o=e&&"IntersectionObserver"in window,i=e&&"classList"in document.createElement("p"),a=e&&window.devicePixelRatio>1,r={elements_selector:".lazy",container:n||e?document:null,threshold:300,thresholds:null,data_src:"src",data_srcset:"srcset",data_sizes:"sizes",data_bg:"bg",data_bg_hidpi:"bg-hidpi",data_bg_multi:"bg-multi",data_bg_multi_hidpi:"bg-multi-hidpi",data_poster:"poster",class_applied:"applied",class_loading:"loading",class_loaded:"loaded",class_error:"error",class_entered:"entered",class_exited:"exited",unobserve_completed:!0,unobserve_entered:!1,cancel_on_exit:!0,callback_enter:null,callback_exit:null,callback_applied:null,callback_loading:null,callback_loaded:null,callback_error:null,callback_finish:null,callback_cancel:null,use_native:!1},s=function(e){return t({},r,e)},c=function(t,e){var n,o="LazyLoad::Initialized",i=new t(e);try{n=new CustomEvent(o,{detail:{instance:i}})}catch(t){(n=document.createEvent("CustomEvent")).initCustomEvent(o,!1,!1,{instance:i})}window.dispatchEvent(n)},l="src",d="srcset",u="sizes",h="poster",m="llOriginalAttrs",f="loading",p="loaded",g="applied",v="error",y="native",b="data-",_="ll-status",L=function(t,e){return t.getAttribute(b+e)},E=function(t){return L(t,_)},S=function(t,e){return function(t,e,n){var o="data-ll-status";null!==n?t.setAttribute(o,n):t.removeAttribute(o)}(t,0,e)},k=function(t){return S(t,null)},I=function(t){return null===E(t)},C=function(t){return E(t)===y},w=[f,p,g,v],A=function(t,e,n,o){t&&(void 0===o?void 0===n?t(e):t(e,n):t(e,n,o))},x=function(t,e){i?t.classList.add(e):t.className+=(t.className?" ":"")+e},O=function(t,e){i?t.classList.remove(e):t.className=t.className.replace(new RegExp("(^|\\s+)"+e+"(\\s+|$)")," ").replace(/^\s+/,"").replace(/\s+$/,"")},T=function(t){return t.llTempImage},q=function(t,e){if(e){var n=e._observer;n&&n.unobserve(t)}},M=function(t,e){t&&(t.loadingCount+=e)},N=function(t,e){t&&(t.toLoadCount=e)},$=function(t){for(var e,n=[],o=0;e=t.children[o];o+=1)"SOURCE"===e.tagName&&n.push(e);return n},H=function(t,e){var n=t.parentNode;n&&"PICTURE"===n.tagName&&$(n).forEach(e)},R=function(t,e){$(t).forEach(e)},D=[l],P=[l,h],V=[l,d,u],B=function(t){return!!t[m]},U=function(t){return t[m]},G=function(t){return delete t[m]},F=function(t,e){if(!B(t)){var n={};e.forEach((function(e){n[e]=t.getAttribute(e)})),t[m]=n}},j=function(t,e){if(B(t)){var n=U(t);e.forEach((function(e){!function(t,e,n){n?t.setAttribute(e,n):t.removeAttribute(e)}(t,e,n[e])}))}},z=function(t,e,n){x(t,e.class_loading),S(t,f),n&&(M(n,1),A(e.callback_loading,t,n))},J=function(t,e,n){n&&t.setAttribute(e,n)},W=function(t,e){J(t,u,L(t,e.data_sizes)),J(t,d,L(t,e.data_srcset)),J(t,l,L(t,e.data_src))},Y={IMG:function(t,e){H(t,(function(t){F(t,V),W(t,e)})),F(t,V),W(t,e)},IFRAME:function(t,e){F(t,D),J(t,l,L(t,e.data_src))},VIDEO:function(t,e){R(t,(function(t){F(t,D),J(t,l,L(t,e.data_src))})),F(t,P),J(t,h,L(t,e.data_poster)),J(t,l,L(t,e.data_src)),t.load()}},K=["IMG","IFRAME","VIDEO"],X=function(t,e){!e||function(t){return t.loadingCount>0}(e)||function(t){return t.toLoadCount>0}(e)||A(t.callback_finish,e)},Q=function(t,e,n){t.addEventListener(e,n),t.llEvLisnrs[e]=n},Z=function(t,e,n){t.removeEventListener(e,n)},tt=function(t){return!!t.llEvLisnrs},et=function(t){if(tt(t)){var e=t.llEvLisnrs;for(var n in e){var o=e[n];Z(t,n,o)}delete t.llEvLisnrs}},nt=function(t,e,n){!function(t){delete t.llTempImage}(t),M(n,-1),function(t){t&&(t.toLoadCount-=1)}(n),O(t,e.class_loading),e.unobserve_completed&&q(t,n)},ot=function(t,e,n){var o=T(t)||t;tt(o)||function(t,e,n){tt(t)||(t.llEvLisnrs={});var o="VIDEO"===t.tagName?"loadeddata":"load";Q(t,o,e),Q(t,"error",n)}(o,(function(i){!function(t,e,n,o){var i=C(e);nt(e,n,o),x(e,n.class_loaded),S(e,p),A(n.callback_loaded,e,o),i||X(n,o)}(0,t,e,n),et(o)}),(function(i){!function(t,e,n,o){var i=C(e);nt(e,n,o),x(e,n.class_error),S(e,v),A(n.callback_error,e,o),i||X(n,o)}(0,t,e,n),et(o)}))},it=function(t,e,n){!function(t){t.llTempImage=document.createElement("IMG")}(t),ot(t,e,n),function(t){B(t)||(t[m]={backgroundImage:t.style.backgroundImage})}(t),function(t,e,n){var o=L(t,e.data_bg),i=L(t,e.data_bg_hidpi),r=a&&i?i:o;r&&(t.style.backgroundImage='url("'.concat(r,'")'),T(t).setAttribute(l,r),z(t,e,n))}(t,e,n),function(t,e,n){var o=L(t,e.data_bg_multi),i=L(t,e.data_bg_multi_hidpi),r=a&&i?i:o;r&&(t.style.backgroundImage=r,function(t,e,n){x(t,e.class_applied),S(t,g),n&&(e.unobserve_completed&&q(t,e),A(e.callback_applied,t,n))}(t,e,n))}(t,e,n)},at=function(t,e,n){!function(t){return K.indexOf(t.tagName)>-1}(t)?it(t,e,n):function(t,e,n){ot(t,e,n),function(t,e,n){var o=Y[t.tagName];o&&(o(t,e),z(t,e,n))}(t,e,n)}(t,e,n)},rt=function(t){t.removeAttribute(l),t.removeAttribute(d),t.removeAttribute(u)},st=function(t){H(t,(function(t){j(t,V)})),j(t,V)},ct={IMG:st,IFRAME:function(t){j(t,D)},VIDEO:function(t){R(t,(function(t){j(t,D)})),j(t,P),t.load()}},lt=function(t,e){(function(t){var e=ct[t.tagName];e?e(t):function(t){if(B(t)){var e=U(t);t.style.backgroundImage=e.backgroundImage}}(t)})(t),function(t,e){I(t)||C(t)||(O(t,e.class_entered),O(t,e.class_exited),O(t,e.class_applied),O(t,e.class_loading),O(t,e.class_loaded),O(t,e.class_error))}(t,e),k(t),G(t)},dt=["IMG","IFRAME","VIDEO"],ut=function(t){return t.use_native&&"loading"in HTMLImageElement.prototype},ht=function(t,e,n){t.forEach((function(t){return function(t){return t.isIntersecting||t.intersectionRatio>0}(t)?function(t,e,n,o){var i=function(t){return w.indexOf(E(t))>=0}(t);S(t,"entered"),x(t,n.class_entered),O(t,n.class_exited),function(t,e,n){e.unobserve_entered&&q(t,n)}(t,n,o),A(n.callback_enter,t,e,o),i||at(t,n,o)}(t.target,t,e,n):function(t,e,n,o){I(t)||(x(t,n.class_exited),function(t,e,n,o){n.cancel_on_exit&&function(t){return E(t)===f}(t)&&"IMG"===t.tagName&&(et(t),function(t){H(t,(function(t){rt(t)})),rt(t)}(t),st(t),O(t,n.class_loading),M(o,-1),k(t),A(n.callback_cancel,t,e,o))}(t,e,n,o),A(n.callback_exit,t,e,o))}(t.target,t,e,n)}))},mt=function(t){return Array.prototype.slice.call(t)},ft=function(t){return t.container.querySelectorAll(t.elements_selector)},pt=function(t){return function(t){return E(t)===v}(t)},gt=function(t,e){return function(t){return mt(t).filter(I)}(t||ft(e))},vt=function(t,n){var i=s(t);this._settings=i,this.loadingCount=0,function(t,e){o&&!ut(t)&&(e._observer=new IntersectionObserver((function(n){ht(n,t,e)}),function(t){return{root:t.container===document?null:t.container,rootMargin:t.thresholds||t.threshold+"px"}}(t)))}(i,this),function(t,n){e&&window.addEventListener("online",(function(){!function(t,e){var n;(n=ft(t),mt(n).filter(pt)).forEach((function(e){O(e,t.class_error),k(e)})),e.update()}(t,n)}))}(i,this),this.update(n)};return vt.prototype={update:function(t){var e,i,a=this._settings,r=gt(t,a);N(this,r.length),!n&&o?ut(a)?function(t,e,n){t.forEach((function(t){-1!==dt.indexOf(t.tagName)&&function(t,e,n){t.setAttribute("loading","lazy"),ot(t,e,n),function(t,e){var n=Y[t.tagName];n&&n(t,e)}(t,e),S(t,y)}(t,e,n)})),N(n,0)}(r,a,this):(i=r,function(t){t.disconnect()}(e=this._observer),function(t,e){e.forEach((function(e){t.observe(e)}))}(e,i)):this.loadAll(r)},destroy:function(){this._observer&&this._observer.disconnect(),ft(this._settings).forEach((function(t){G(t)})),delete this._observer,delete this._settings,delete this.loadingCount,delete this.toLoadCount},loadAll:function(t){var e=this,n=this._settings;gt(t,n).forEach((function(t){q(t,e),at(t,n,e)}))},restoreAll:function(){var t=this._settings;ft(t).forEach((function(e){lt(e,t)}))}},vt.load=function(t,e){var n=s(e);at(t,n)},vt.resetStatus=function(t){k(t)},e&&function(t,e){if(e)if(e.length)for(var n,o=0;n=e[o];o+=1)c(t,n);else c(t,e)}(vt,window.lazyLoadOptions),vt}()}},e={};function n(o){var i=e[o];if(void 0!==i)return i.exports;var a=e[o]={exports:{}};return t[o].call(a.exports,a,a.exports,n),a.exports}n.n=t=>{var e=t&&t.__esModule?()=>t.default:()=>t;return n.d(e,{a:e}),e},n.d=(t,e)=>{for(var o in e)n.o(e,o)&&!n.o(t,o)&&Object.defineProperty(t,o,{enumerable:!0,get:e[o]})},n.o=(t,e)=>Object.prototype.hasOwnProperty.call(t,e),n(351),n(804),n(791),n(927);n(984)})();