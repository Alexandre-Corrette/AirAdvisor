/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
//import 'jquery';
//import $ from 'jquery';
const $ = require('jquery');
global.$ = global.jQuery = $;


import './js/jquery-ui.js';
import '@popperjs/core';
// start the Stimulus application

import './bootstrap';
window.bootstrap = require('bootstrap');






var keyAccess = 'b064ee-021d46';
$(document).ready(function(){
 var item
  $('.js-user-autocomplete').autocomplete({
    source: function(request, response){$.getJSON("https://aviation-edge.com/v2/public/autocomplete?key=" + keyAccess + '&city=' +request.term
    ,
     
    function(data){ 
      if(data) {
  
        var airportData ;
       $.map(data, function(value, key){
         
         airportData = data.airportsByCities
         
        return airportData
            
          })

          item = $.map(airportData, function(airport,key){
            console.log(airport)
            
            return { 
  
              value: airport.nameAirport + '-' + airport.codeIataAirport }

        
        }) 
        item.unshift({'': ""})
  
        response(item);

        
        
      }
      
    }
    
    )},
 
    
  })._renderItemData = function( input, item ) {
    console.log(item)
    if(typeof item !== undefined) {
      return $( "<option></option>" )
        .data( "item.autocomplete-item", item )
        .append(  item.value   )
        .appendTo( input );
    }
    
};})

var myModal = new bootstrap.Modal(document.getElementById('myModal'))

















