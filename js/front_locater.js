var curLat = 0;
var curLong = 0;

function suggestion_fill(value)
{
    jQuery('#locater_input').val(value);
    jQuery('#suggestion_box').hide();
}

function errorHandler(err) {
    if(err.code == 1) {
       alert("Error: Access is denied!");
    }
    
    else if( err.code == 2) {
       alert("Error: Position is unavailable!");
    }
}
         
jQuery(document).ready(function() {


    jQuery('#locater_btn').hide();
    jQuery('#locater_input').hide();

    jQuery("#locater_input").keyup(function() {
        var locater_input_text = jQuery('#locater_input').val();
        if (locater_input_text == "")
        {
            jQuery("#suggestion_box").html("");
        }
        else
        {
            siteurl = jQuery('#locater_site_url').val();
            jQuery.ajax({
                type: "POST",
                url: siteurl + "/wp-admin/admin-ajax.php",
                data: {action: 'locater_suggestion', locater_input_text: locater_input_text},
                success: function(html) {
                    jQuery("#suggestion_box").html(html).show();
                }
            });
        }
    });
    initialize(true, '');
    // Handle Radio event
    jQuery('input[name=locater_search_filter]').on('click', function() {

        if (jQuery(this).is(":checked")) {
            if (jQuery(this).val() == 1) {
                jQuery('#locater_btn').hide();
                jQuery('#locater_input').hide();
                initialize(true, '');
            } else {
                jQuery('#locater_btn').show();
                jQuery('#locater_input').show();
            }
        }
    })

    jQuery('#locater_btn').on('click', function() {
        address = jQuery('#locater_input').val();
        initialize(false, address);
    })
})

contentstring = [];
window.data = '';
var databaseAddr = '';

regionlocation = [];
var markers = [];
var iterator = 0;
var areaiterator = 0;
var map;
var infowindow = [];
geocoder = new google.maps.Geocoder();



function initialize(onloading, addr) {

    //If HTML5 Geolocation Is Supported In This Browser
    if (navigator.geolocation) {///generate_map(onloading, addr, generate_listing);


        //Use HTML5 Geolocation API To Get Current Position
        navigator.geolocation.getCurrentPosition(function(position) {

            //Get Latitude From Geolocation API
            var latitude = currLat = position.coords.latitude;
            //Get Longitude From Geolocation API
            var longitude = currLong = position.coords.longitude;
            //Define New Google Map With Lat / Lon
            var coords = new google.maps.LatLng(latitude, longitude);
            infowindow = [];
            markers = [];
            generate_map(onloading, addr, generate_listing);



        },
        errorHandler,
        {timeout: 10 * 1000}
        );

    } else {

        //Otherwise - Gracefully Fall Back If Not Supported... Probably Best Not To Use A JS Alert Though :)
        alert("Geolocation API is not supported in your browser.");

    }

}

function generate_map(onloading, addr, generate_listing) {

    if (onloading) {
        siteurl = jQuery('#locater_site_url').val();
        var mydata = jQuery.ajax({
            type: 'POST',
            data: {action: 'locater_nearby', lat_long: currLat + "," + currLong},
            url: siteurl + "/wp-admin/admin-ajax.php",
            asynch: false,
            success: generate_listing

        })

    } else {
        lmkey = jQuery('#locater_google_api_key').val();
        specific = true;
        mydata = jQuery.ajax({
            url: "https://maps.googleapis.com/maps/api/geocode/json?address=" + addr + "&key=" + lmkey,
            type: "POST",
            asynch: false,
            success: function(res) {
                if (res.status == 'ZERO_RESULTS' || res.results[0].length < 1) {
                    alert("No record Found");
                    location.reload(true);
                }
                curraddr = res.results[0].geometry.location;
                siteurl = jQuery('#locater_site_url').val();
                mydatai = jQuery.ajax({
                    type: 'POST',
                    data: {action: 'locater_nearby', lat_long: curraddr.lat + "," + curraddr.lng, specific_loc: specific},
                    url: siteurl + "/wp-admin/admin-ajax.php",
                    asynch: false,
                    success: generate_listing

                })
            }
        })

    }
    ;



}

function drop(contentstring, regionlocation) {
    for (var i = 0; i < contentstring.length; i++) {
        setTimeout(function() {
            addMarker(contentstring, regionlocation);
        }, 800);
    }
}

function addMarker(contentstring, regionlocation) {
    var lmimg = jQuery('#locater_map_marker_img').val();
    var address = contentstring[areaiterator];

    var icons = lmimg;
    var templat = regionlocation[areaiterator].split(',')[0];
    var templong = regionlocation[areaiterator].split(',')[1];
    var temp_latLng = new google.maps.LatLng(templat, templong);
    markers.push(new google.maps.Marker(
            {
                position: temp_latLng,
                map: map,
                icon: icons,
                draggable: false
            }));
    iterator++;
    info(iterator, contentstring, regionlocation);
    areaiterator++;
}

function info(i, contentstring, regionlocation) {
    infowindow[i] = new google.maps.InfoWindow({
        content: contentstring[i - 1]
    });
    infowindow[i].content = contentstring[i - 1];
    google.maps.event.addListener(markers[i - 1], 'click', function() {
        for (var j = 1; j < contentstring.length + 1; j++) {
            infowindow[j].close();
        }
        infowindow[i].open(map, markers[i - 1]);
    });
}



function generate_listing(listing) {

    contentstring = [];
    regionlocation =[];
    listing = JSON.parse(listing);
    if (!Array.isArray(listing) || listing.length < 1) {
        alert('No entry found near to your location');
        return;
    }
    for (i = 0; i < listing.length; i++)
    {
        contentstring[i] = listing[i].address_loc + ", " + listing[i].address_city + ", " + listing[i].address_country;
        regionlocation[i] = listing[i].latitude + ", " + listing[i].longitude;
    }
    iterator = 0;
    areaiterator = 0;
    lmzs = parseInt(document.getElementById('locater_map_zoom_size').value);
    region = '';
    map = '';
    region = new google.maps.LatLng(regionlocation[areaiterator].split(',')[0], regionlocation[areaiterator].split(',')[1]);
    map = new google.maps.Map(document.getElementById("locater_map"), {
        zoom: lmzs,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: region,
    });
    drop(contentstring, regionlocation);
}

