import BuildMonitor from"./BuildMonitor";class S250{constructor(){this.tryParseOpenIdPostback(),this.initLogInOut(),this.syncLogInOutState(),this.initFixedMenu(),this.initMenuScrollbarTransitions(),this.constrainDropdownMenuPositions(),this.scrollToCurrentHash(),this.overrideHashChange(),this.overrideFixedLinks(),this.initSearchValue(),this.initAppLinkMenu(),this.initRankingHoverItems(),this.initCountdown()}initLogInOut(){const e=document.querySelector("#lout form");e?(e["openid.return_to"].value=location.origin+location.pathname,document.querySelector("#lin button").addEventListener("click",(e=>this.logout()))):console.debug("Steam user area unavailable: skipped.")}initFixedMenu(){const e=document.querySelector("ol.menu"),t=e.cloneNode(!0);function n(){t.style.position="fixed",t.style.visibility=e.offsetTop<scrollY?"visible":"hidden"}n(),e.insertAdjacentElement("afterend",t),addEventListener("scroll",n)}initMenuScrollbarTransitions(){const e="t11g";document.querySelectorAll("ol.menu li").forEach((t=>{const n=t.querySelector("ol");n&&(t.addEventListener("mouseenter",(t=>0===n.clientHeight&&n.classList.add(e))),t.addEventListener("mouseleave",(t=>n.classList.add(e))))})),document.querySelectorAll("ol.menu > li ol").forEach((t=>{t.addEventListener("transitionend",(n=>t.classList.remove(e))),t.addEventListener("wheel",(e=>{t.clientHeight+t.scrollTop+e.deltaY>t.scrollHeight?(t.scrollTop=t.scrollHeight,e.preventDefault()):t.scrollTop+e.deltaY<0&&(t.scrollTop=0,e.preventDefault()),e.stopPropagation()}))}))}constrainDropdownMenuPositions(){document.querySelectorAll("ol.menu > li > ol").forEach((e=>{const t=e.getBoundingClientRect();t.left<0&&(e.style.left=`calc(${getComputedStyle(e).left} - ${t.left}px)`),t.right>document.documentElement.clientWidth&&(e.style.left=`calc(${getComputedStyle(e).left} - ${t.right-document.documentElement.clientWidth}px)`)}))}scrollToCurrentHash(){addEventListener("load",(e=>this.scrollToHash(location.hash)))}overrideHashChange(){addEventListener("hashchange",(e=>this.scrollToHash(location.hash)))}overrideFixedLinks(){document.querySelectorAll(".fixedlinks a").forEach((e=>{e.addEventListener("click",(t=>{this.scrollToHash(e.hash),t.preventDefault()}))}))}scrollToHash(e){const t="highlight";if(document.querySelectorAll(".ranking [id]").forEach((e=>e.classList.remove(t))),!e)return;const n=document.querySelector("ol.menu").getBoundingClientRect().height,o=this.resolveHashTarget(e);if(o){let e=o.getBoundingClientRect().top-n;const i=document.querySelector(".ranking");if(i&&i.contains(o)){if(o.classList.add(t),function(e){const t=e.getBoundingClientRect();return t.top>=n&&t.left>=0&&t.bottom<=innerHeight&&t.right<=innerWidth}(o))return;e+=o.getBoundingClientRect().height/2-innerHeight/3}scrollTo(pageXOffset,pageYOffset+Math.ceil(e))}}resolveHashTarget(e){if(e.startsWith("#app/")){let[t,n,o]=e.split("/",3),i=document.querySelector(`.ranking img[src*="/${n}/"]`);return i?i.closest("[id]"):void console.error(`Couldn't find game on this ranking: "${decodeURIComponent(o)}".`)}return document.getElementById(e.substr(1))}syncLogInOutState(){const e=document.getElementById("user").classList;e.remove("lin","lout"),e.add(S250.isLoggedIn()?"lin":"lout"),S250.isLoggedIn()&&(this.markOwnedGames(),this.updateUserBar())}markOwnedGames(){const e=JSON.parse(localStorage.getItem("games")),t=document.querySelectorAll(".ranking > div[id] > div:first-of-type > a");let n,o;t.forEach((t=>{const n=t.firstElementChild.src.match(/\/apps\/(\d+)/)[1];e.hasOwnProperty(n)&&(t.classList.add("owned"),t.setAttribute("data-content",e[n]+" hours"))})),document.querySelector("#user .owned").innerText=t.length?(n=document.querySelectorAll(".ranking .owned").length)+"/"+(o=t.length)+" ("+Math.round(n/o*100)+"%)":"n/a"}unmarkOwnedGames(){document.querySelectorAll(".ranking a.owned").forEach((e=>{e.classList.remove("owned")}))}updateUserBar(){const e=JSON.parse(localStorage.getItem("steam")),t=document.querySelector("#lin .avatar");t.href=`http://steamcommunity.com/profiles/${e.id}/`,t.title=e.name;const n=document.createElement("img");n.src=e.avatar,t.appendChild(n)}static isLoggedIn(){return localStorage.hasOwnProperty("steam")&&localStorage.hasOwnProperty("games")}logout(){localStorage.removeItem("steam"),localStorage.removeItem("games"),this.unmarkOwnedGames(),this.syncLogInOutState()}tryParseOpenIdPostback(){const e=this.parseParam("openid.claimed_id");if(!e)return;const t=e.replace(/.*\//,"");fetch(`https://cors.bridged.cc/https://steamcommunity.com/profiles/${t}/games/?tab=all`).then((e=>e.text())).then((e=>{const n=e.match(/var rgGames = ([^\n]+);/);if(!n||2!==n.length)return void alert("Unable to load your profile. This is usually because your Steam profile is not public.\nTry setting your Steam Community profile visibility to public, then refresh this page to try again.");const o=JSON.parse(n[1]).reduce(((e,t)=>(e[t.appid]=t.hours_forever||0,e)),{});if(!Object.keys(o).length)return void alert("No games found in your account! This is usually because your game details are not public.\nTry setting your game details to public on your Steam Community privacy settings page, then refresh this page to try again.");localStorage.setItem("games",JSON.stringify(o));const i=(new DOMParser).parseFromString(e,"text/html");localStorage.setItem("steam",JSON.stringify({id:t,name:i.querySelector(".profile_small_header_name").innerText,avatar:i.querySelector(".playerAvatar > img").src.replace("_medium","")})),location.replace(location.pathname)}))}initSearchValue(){const e=this.parseParam("q");null!==e&&document.querySelectorAll("input[name=q]").forEach((t=>t.value=e.replace(/\+/g," ")))}initAppLinkMenu(){const e=document.getElementById("linkmenu"),t="show";let n;document.querySelectorAll(".ranking .links").forEach((o=>{o.addEventListener("click",(i=>{e.style.top=o.offsetTop+o.offsetHeight+5+"px",e.style.left=o.offsetLeft+"px",e.querySelector("a:first-of-type > span").innerHTML=o.closest("[id]").id,e.classList.toggle(t,n!==o||void 0),o.classList.toggle(t,e.classList.contains(t)),n=o,i.preventDefault()})),o.addEventListener("blur",(n=>{e.classList.remove(t),o.classList.remove(t)}))})),document.querySelectorAll("#linkmenu a").forEach((e=>{e.addEventListener("click",(t=>{if(e.classList.contains("cp")&&(e.classList.contains("rank")&&this.copyToClipboard(n.href),e.classList.contains("app"))){const e=this.findSteamAppId(n.closest("[id]")),t=encodeURIComponent(this.findSteamAppName(n.closest("div")));this.copyToClipboard(`${location.origin}${location.pathname}#app/${e}/${t}`)}}))}))}initRankingHoverItems(){document.querySelectorAll(".compact.ranking li > .title").forEach((e=>{const t=e.appendChild(e.cloneNode(!0));t.style.pointerEvents="none",e.addEventListener("mouseenter",(e=>{t.classList.remove("animate"),t.offsetWidth,t.classList.add("animate")})),e.addEventListener("animationend",(e=>{t.classList.remove("animate")}))}))}async initCountdown(){const e=new BuildMonitor,t=await(await fetch("https://api.github.com/repos/250/Steam-250/actions/workflows/Build.yml/runs?actor=Azure-bot&status=completed&per_page=1")).json();e.start(t.workflow_runs[0].updated_at)}parseParam(e){const t=RegExp("[?&]"+e+"=([^&]*)").exec(location.search);return t&&decodeURIComponent(t[1])}findSteamAppId(e){const t=e.querySelector("img[src]");if(t)return t.src.match(/\/(\d+)\//)[1]}findSteamAppName(e){return e.querySelector(".title > a").innerText}copyToClipboard(e){if(window.clipboardData&&window.clipboardData.setData)return clipboardData.setData("Text",e);if(document.queryCommandSupported&&document.queryCommandSupported("copy")){const t=document.createElement("textarea");t.textContent=e,t.style.position="fixed",document.body.appendChild(t),t.select();try{return document.execCommand("copy")}catch(e){return!1}finally{document.body.removeChild(t)}}}}new S250;