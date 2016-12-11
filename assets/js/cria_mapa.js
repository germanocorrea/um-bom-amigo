/* js/cria_mapa.js*/
var mapa = L.map("mapao").setView([40.671750, -73.963397], 13);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox.satellite',
    accessToken: 'pk.eyJ1IjoibWFyY29jYW5vc3NhIiwiYSI6ImNpc2tyODVrYTA1eGoyem53MmpweDZjY28ifQ.KYReqNWQbAh7J9TyFxJJKQ'
}).addTo(mapa);

