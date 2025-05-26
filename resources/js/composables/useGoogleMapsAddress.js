import { onMounted } from 'vue';

export function useGoogleMapsAddress(onPlaceChanged) {
    const getTypes = {
        street_number: (object, value) => {
            return { ...object, numero: value };
        },
        route: (object, value) => {
            return { ...object, logradouro: value };
        },
        sublocality_level_1: (object, value) => {
            return { ...object, bairro: value };
        },
        administrative_area_level_2: (object, value) => {
            return { ...object, cidade: value };
        },
        administrative_area_level_1: (object, value) => {
            return { ...object, estado: value };
        },
        postal_code: (object, value) => {
            return { ...object, cep: value };
        },
        country: (object, value) => {
            return { ...object, pais: value };
        },
    };

    const setPlace = (place, handleChange) => {

        const { address_components, formatted_address, geometry: { location: { lat, lng } } } = place;

        const addressComp = address_components.reduce((prev, addressItem) => {
            const { short_name, types } = addressItem;
            const type = types[0];
            const typeGetted = getTypes[type];
            if (typeGetted) return typeGetted(prev, short_name);
            return prev;
        }, {});
        handleChange({ ...addressComp, search: formatted_address, latitude: lat(), longitude: lng() });
    };

    const getUserLocation = (handleChange) => {
        const isSupported = 'navigator' in window && 'geolocation' in navigator;
        if (isSupported) {
            navigator.geolocation.getCurrentPosition((position) => {
                const latlng = {
                    lat: parseFloat(position.coords.latitude),
                    lng: parseFloat(position.coords.longitude),
                };
                const geocoder = new google.maps.Geocoder();
                geocoder
                    .geocode({ location: latlng })
                    .then((response) => {
                        if (response.results[0]) setPlace(response.results[0], handleChange);
                        else window.alert('No results found');
                    })
                    .catch((e) => window.alert('Geocoder failed due to: ' + e));
            });
        }
    };

    const handleLocatorButton = (event, handleChange) => {
        event.preventDefault();
        getUserLocation(handleChange);
    };

    onMounted(() => {
        const options = {
            componentRestrictions: {
                country: 'br',
            },
            strictBounds: true,
        };

        const autocompleteInput = document.getElementById('autocomplete');
        if (!autocompleteInput) return;
        const autocomplete = new google.maps.places.Autocomplete(autocompleteInput, options);

        autocomplete.setFields(['place_id', 'geometry', 'address_component', 'formatted_address']);

        const infowindow = new google.maps.InfoWindow();

        autocomplete.addListener('place_changed', function () {
            infowindow.close();
            const place = autocomplete.getPlace();
            if (onPlaceChanged && typeof onPlaceChanged === 'function') {
                setPlace(place, onPlaceChanged);
            }
        });

        setTimeout(() => (document.body.style.pointerEvents = ''), 0);
    });

    return {
        handleLocatorButton,
        getUserLocation,
        setPlace,
    };
}
