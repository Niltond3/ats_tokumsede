import { ref } from "vue";

export default {
    useGoogleAutocomplete: (inputId) => {
        const place = ref();
        const options = {
            componentRestrictions: { country: "br" },
            strictBounds: true,
        };
        const element = document.getElementById(inputId);
        if (!element) {
            console.error(`Elemento com id ${inputId} não foi encontrado`);
            return { place };
        }
        const autocomplete = new google.maps.places.Autocomplete(element, options);
        autocomplete.setFields(["place_id", "geometry", "address_component", "formatted_address"]);
        const infowindow = new google.maps.InfoWindow();
        autocomplete.addListener("place_changed", function () {
            infowindow.close();
            place.value = autocomplete.getPlace();
        });
        // Garante que a propriedade pointerEvents seja redefinida sem causar efeitos colaterais inesperados
        setTimeout(() => (document.body.style.pointerEvents = ""), 0);
        return { place };
    },
};
