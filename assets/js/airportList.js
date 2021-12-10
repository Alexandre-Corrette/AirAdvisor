import { data, makeArray } from 'jquery';
import './jquery-ui'


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
            
            return { 
  
              value: airport.nameAirport }

        
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
$( function() {
  $( ".datepicker" ).datepicker({ 
  dateFormat: 'yy-m-d',
  showOtherMonths: true,
  selectOtherMonths: true });
} );
 