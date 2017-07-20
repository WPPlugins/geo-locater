jQuery(document).ready(function(){
    
    lmkey = jQuery('#locater_google_api_key').val();
    jQuery('#get_lat_long').on('click',function(){ 
    
    city  = jQuery('#address_city').val();
    country  = jQuery('#address_country').val();
    address  = jQuery('#address_loc').val();
    str = address+" "+city+" "+country;
    
    jQuery.ajax({
      url:"https://maps.googleapis.com/maps/api/geocode/json?address="+str+"&sensor=false&key="+lmkey,
      type: "POST",
      success:function(res){
          jQuery('#address_long').val(res.results[0].geometry.location.lng);
          jQuery('#address_lat').val(res.results[0].geometry.location.lat);

      }
    });
})    
})

