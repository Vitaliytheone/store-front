"use strict";var precacheConfig=[["/index.html","ae41494bd704f21ae2763a58c0da0e7c"],["/static/css/main.a2361a3a.css","32900a263f6c7b353338c07fdc39ce17"],["/static/js/main.b3bfa56e.js","ea5389953990b7b93ad9f1f299371f32"],["/static/media/fa-brands-400.0938cd3c.woff2","0938cd3c86ec4da5c54846583b3e2494"],["/static/media/fa-brands-400.19d26f4e.ttf","19d26f4e4b4fc52d03b87f6d292548d0"],["/static/media/fa-brands-400.4e9e8847.eot","4e9e8847bd8eadca8d81a5708d527fcc"],["/static/media/fa-brands-400.9ddd9f9a.woff","9ddd9f9a879326c121c065956c344ddf"],["/static/media/fa-brands-400.b8f15b31.svg","b8f15b3131082831d10b7accc1f6c098"],["/static/media/fa-light-300.01422b95.eot","01422b95c73840588205b92d2da91f29"],["/static/media/fa-light-300.0d5e9bb8.ttf","0d5e9bb82917c5d65c72718b22bf3679"],["/static/media/fa-light-300.128ba6b5.woff2","128ba6b5a380ea45e4a2616ed029f50e"],["/static/media/fa-light-300.79f0a37f.svg","79f0a37f306d2fd0c8e303cddf1a0965"],["/static/media/fa-light-300.d3aef04f.woff","d3aef04ff0c1bf731298bd2a598e8ca7"],["/static/media/fa-regular-400.691aadc3.woff","691aadc3c6817363ca48bf5a0b87a180"],["/static/media/fa-regular-400.94e84d7e.woff2","94e84d7efab99a5bb7065f5b0a6df7f1"],["/static/media/fa-regular-400.aeabb944.eot","aeabb944b103a5e0e032d2c0659f16b8"],["/static/media/fa-regular-400.cd0e119e.svg","cd0e119ec3bbfb6f902c867f62a2af9a"],["/static/media/fa-regular-400.ff9aece3.ttf","ff9aece3d4cade92f2fd273290ca8bf0"],["/static/media/fa-solid-900.4d496c9d.svg","4d496c9d7dd88333d8c3a0f979cf7a19"],["/static/media/fa-solid-900.56cf83dc.ttf","56cf83dc307672a2d5d0ab227e4098ec"],["/static/media/fa-solid-900.8c3a8870.eot","8c3a8870b9d83594cf831bcc77989eba"],["/static/media/fa-solid-900.b093fcb2.woff","b093fcb2c86d52309e28681421029d5c"],["/static/media/fa-solid-900.f0938640.woff2","f093864079d44808c60ddb4907478b3e"]],cacheName="sw-precache-v3-sw-precache-webpack-plugin-"+(self.registration?self.registration.scope:""),ignoreUrlParametersMatching=[/^utm_/],addDirectoryIndex=function(e,a){var t=new URL(e);return"/"===t.pathname.slice(-1)&&(t.pathname+=a),t.toString()},cleanResponse=function(a){return a.redirected?("body"in a?Promise.resolve(a.body):a.blob()).then(function(e){return new Response(e,{headers:a.headers,status:a.status,statusText:a.statusText})}):Promise.resolve(a)},createCacheKey=function(e,a,t,n){var c=new URL(e);return n&&c.pathname.match(n)||(c.search+=(c.search?"&":"")+encodeURIComponent(a)+"="+encodeURIComponent(t)),c.toString()},isPathWhitelisted=function(e,a){if(0===e.length)return!0;var t=new URL(a).pathname;return e.some(function(e){return t.match(e)})},stripIgnoredUrlParameters=function(e,t){var a=new URL(e);return a.hash="",a.search=a.search.slice(1).split("&").map(function(e){return e.split("=")}).filter(function(a){return t.every(function(e){return!e.test(a[0])})}).map(function(e){return e.join("=")}).join("&"),a.toString()},hashParamName="_sw-precache",urlsToCacheKeys=new Map(precacheConfig.map(function(e){var a=e[0],t=e[1],n=new URL(a,self.location),c=createCacheKey(n,hashParamName,t,/\.\w{8}\./);return[n.toString(),c]}));function setOfCachedUrls(e){return e.keys().then(function(e){return e.map(function(e){return e.url})}).then(function(e){return new Set(e)})}self.addEventListener("install",function(e){e.waitUntil(caches.open(cacheName).then(function(n){return setOfCachedUrls(n).then(function(t){return Promise.all(Array.from(urlsToCacheKeys.values()).map(function(a){if(!t.has(a)){var e=new Request(a,{credentials:"same-origin"});return fetch(e).then(function(e){if(!e.ok)throw new Error("Request for "+a+" returned a response with status "+e.status);return cleanResponse(e).then(function(e){return n.put(a,e)})})}}))})}).then(function(){return self.skipWaiting()}))}),self.addEventListener("activate",function(e){var t=new Set(urlsToCacheKeys.values());e.waitUntil(caches.open(cacheName).then(function(a){return a.keys().then(function(e){return Promise.all(e.map(function(e){if(!t.has(e.url))return a.delete(e)}))})}).then(function(){return self.clients.claim()}))}),self.addEventListener("fetch",function(a){if("GET"===a.request.method){var e,t=stripIgnoredUrlParameters(a.request.url,ignoreUrlParametersMatching),n="index.html";(e=urlsToCacheKeys.has(t))||(t=addDirectoryIndex(t,n),e=urlsToCacheKeys.has(t));var c="/index.html";!e&&"navigate"===a.request.mode&&isPathWhitelisted(["^(?!\\/__).*"],a.request.url)&&(t=new URL(c,self.location).toString(),e=urlsToCacheKeys.has(t)),e&&a.respondWith(caches.open(cacheName).then(function(e){return e.match(urlsToCacheKeys.get(t)).then(function(e){if(e)return e;throw Error("The cached response that was expected is missing.")})}).catch(function(e){return console.warn('Couldn\'t serve response for "%s" from cache: %O',a.request.url,e),fetch(a.request)}))}});