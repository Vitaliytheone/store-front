"use strict";var precacheConfig=[["./index.html","0868d439648e0f13d0e0e3afb023ea62"],["./static/css/main.css","e362aacec09e11e74b6631ae57566f17"],["./static/js/main.js","6bd14ce4b1c1a9bc0fb90fb67d228880"],["./static/media/Sommerce.eot","6c3989529794b993d34bc5088bdc3c21"],["./static/media/Sommerce.svg","e714619d29dec10085225b8a2732d834"],["./static/media/Sommerce.ttf","48e87bc78cf9103cb1d5054a70838797"],["./static/media/Sommerce.woff","2e62bd50cbbea6bc111b441daa4becce"],["./static/media/add-submenu.svg","332b3f4e454da0b36e6128137a1a5b22"],["./static/media/back.svg","473de025670e139e2575cedf9f54bfa9"],["./static/media/caret.svg","0ce3e73a7026259c0014824b5867b97e"],["./static/media/close.svg","51178b10efe5b0294b1e362ddf2a8f2d"],["./static/media/copy.svg","c616226320e0be006b8ce6733e56b3d2"],["./static/media/empty-image.svg","96ec7ed2d6939683317762cae53d707e"],["./static/media/fa-brands-400.eot","586bdc7d039fe13bda883d81bc822a0e"],["./static/media/fa-brands-400.svg","277706fedcf440dfa9fd535ef7285c82"],["./static/media/fa-brands-400.ttf","6a004ddb259f02543b250c8467669ea6"],["./static/media/fa-brands-400.woff","8270e503193150ebf94304681b327961"],["./static/media/fa-brands-400.woff2","68a68036d1804de9dd28565a4b860933"],["./static/media/fa-light-300.eot","fd4e45701b8f0fcdb5b958e191075437"],["./static/media/fa-light-300.svg","7fc88777fc3ee282f4bda2ff4a6fff9a"],["./static/media/fa-light-300.ttf","80329a35cee0268dc6cf640911ad9bab"],["./static/media/fa-light-300.woff","5024e9b25bf8074067819efc65c4bd66"],["./static/media/fa-light-300.woff2","23fbd7c6763eca9c0d6079b3c56c5b1f"],["./static/media/fa-regular-400.eot","b2d220369f80715b91aa3f75447f5895"],["./static/media/fa-regular-400.svg","dc1995da1b48ebf442e874cc63d9c989"],["./static/media/fa-regular-400.ttf","bdd14bcd205c27612b5bfe322ea6598a"],["./static/media/fa-regular-400.woff","559f4a5f8ad7c7b28626743cadc72a27"],["./static/media/fa-regular-400.woff2","2fa6bcb87bcf488cbc93e7391cb3373a"],["./static/media/fa-solid-900.eot","4279e7972a1ec6d434b560eb06e1f83d"],["./static/media/fa-solid-900.svg","5e46e9273987b5d795c439f84526f147"],["./static/media/fa-solid-900.ttf","3a6ff68146d68033d0c4bd44783ee343"],["./static/media/fa-solid-900.woff","a2a8932315d40d4fa55cf32162177ca3"],["./static/media/fa-solid-900.woff2","893b3ef2bc8b4d8979798fb3b28132bd"],["./static/media/link-active.svg","8429b295203262adaef2ac30d5036b95"],["./static/media/link.svg","5bd322fab69c217d101f333a492b15db"],["./static/media/menu-arrow-bottom.svg","052556235c8054f0fe36dec581825d6a"],["./static/media/menu-arrow-left.svg","4fc7b8f6031bd7a964960804a2c7f0e5"],["./static/media/menu-arrow-right.svg","046d5cfc85b2d21aefe638b6ba762782"],["./static/media/menu-arrow-top.svg","c5dd5273857625c77705e85cad664716"],["./static/media/plus.svg","a39e781808ceda6f8b13c216b065cd3f"],["./static/media/text-align-center.svg","b4b40252a3d751df75f2c6140f7510f3"],["./static/media/text-align-justify.svg","16572559d2c057c5db65c0c6df97373b"],["./static/media/text-align-left.svg","e815407f1d5c03712e015c4dad067ad8"],["./static/media/text-align-right.svg","9ba562283a52d2a6a7a5a27e1029d13d"],["./static/media/text-crossed-active.svg","99f645842078c1bd8a4b7597074a1296"],["./static/media/text-crossed.svg","3650af66207760d91c5ce7df8dc127cf"],["./static/media/text-italic-active.svg","2df6f5618ce9fbf80b49fc09a90d9594"],["./static/media/text-italic.svg","8d8b470c62623a2b0af56aaa5023ab58"],["./static/media/text-size.svg","686ee1469594ce9f7d949a6c496b5afe"],["./static/media/text-strong-active.svg","821fe50b3dfeaed9c6e07c5c293ea4ae"],["./static/media/text-strong.svg","b9462356060ae654cff8946e65344f11"],["./static/media/text-underline-active.svg","94a88e59c7ca644bcc55417f61f0d13d"],["./static/media/text-underline.svg","870a4c727745122b47edf41f0ecd4939"],["./static/media/trash.svg","3b26d8d3f77a95a084f295483bb3ec8a"]],cacheName="sw-precache-v3-sw-precache-webpack-plugin-"+(self.registration?self.registration.scope:""),ignoreUrlParametersMatching=[/^utm_/],addDirectoryIndex=function(e,a){var t=new URL(e);return"/"===t.pathname.slice(-1)&&(t.pathname+=a),t.toString()},cleanResponse=function(a){return a.redirected?("body"in a?Promise.resolve(a.body):a.blob()).then(function(e){return new Response(e,{headers:a.headers,status:a.status,statusText:a.statusText})}):Promise.resolve(a)},createCacheKey=function(e,a,t,c){var i=new URL(e);return c&&i.pathname.match(c)||(i.search+=(i.search?"&":"")+encodeURIComponent(a)+"="+encodeURIComponent(t)),i.toString()},isPathWhitelisted=function(e,a){if(0===e.length)return!0;var t=new URL(a).pathname;return e.some(function(e){return t.match(e)})},stripIgnoredUrlParameters=function(e,t){var a=new URL(e);return a.hash="",a.search=a.search.slice(1).split("&").map(function(e){return e.split("=")}).filter(function(a){return t.every(function(e){return!e.test(a[0])})}).map(function(e){return e.join("=")}).join("&"),a.toString()},hashParamName="_sw-precache",urlsToCacheKeys=new Map(precacheConfig.map(function(e){var a=e[0],t=e[1],c=new URL(a,self.location),i=createCacheKey(c,hashParamName,t,/\.\w{8}\./);return[c.toString(),i]}));function setOfCachedUrls(e){return e.keys().then(function(e){return e.map(function(e){return e.url})}).then(function(e){return new Set(e)})}self.addEventListener("install",function(e){e.waitUntil(caches.open(cacheName).then(function(c){return setOfCachedUrls(c).then(function(t){return Promise.all(Array.from(urlsToCacheKeys.values()).map(function(a){if(!t.has(a)){var e=new Request(a,{credentials:"same-origin"});return fetch(e).then(function(e){if(!e.ok)throw new Error("Request for "+a+" returned a response with status "+e.status);return cleanResponse(e).then(function(e){return c.put(a,e)})})}}))})}).then(function(){return self.skipWaiting()}))}),self.addEventListener("activate",function(e){var t=new Set(urlsToCacheKeys.values());e.waitUntil(caches.open(cacheName).then(function(a){return a.keys().then(function(e){return Promise.all(e.map(function(e){if(!t.has(e.url))return a.delete(e)}))})}).then(function(){return self.clients.claim()}))}),self.addEventListener("fetch",function(a){if("GET"===a.request.method){var e,t=stripIgnoredUrlParameters(a.request.url,ignoreUrlParametersMatching),c="index.html";(e=urlsToCacheKeys.has(t))||(t=addDirectoryIndex(t,c),e=urlsToCacheKeys.has(t));var i="./index.html";!e&&"navigate"===a.request.mode&&isPathWhitelisted(["^(?!\\/__).*"],a.request.url)&&(t=new URL(i,self.location).toString(),e=urlsToCacheKeys.has(t)),e&&a.respondWith(caches.open(cacheName).then(function(e){return e.match(urlsToCacheKeys.get(t)).then(function(e){if(e)return e;throw Error("The cached response that was expected is missing.")})}).catch(function(e){return console.warn('Couldn\'t serve response for "%s" from cache: %O',a.request.url,e),fetch(a.request)}))}});