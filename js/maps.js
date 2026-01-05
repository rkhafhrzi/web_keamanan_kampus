let map;

function initMap() {
    const campusCenter = {
        lat: -6.8904,
        lng: 107.6107
    };

    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 15,
        center: campusCenter,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
    });

    const locations = window.SECURITY_LOCATIONS || [];

    locations.forEach(loc => {
        const marker = new google.maps.Marker({
            position: {
                lat: parseFloat(loc.lat),
                lng: parseFloat(loc.lng)
            },
            map: map,
            title: loc.name,
            icon: loc.type === 'pos'
                ? 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                : 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
        });

        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="font-size:14px">
                    <strong>${loc.name}</strong><br>
                    Status: ${loc.status}
                </div>
            `
        });

        marker.addListener("click", () => {
            infoWindow.open(map, marker);
        });
    });
}
