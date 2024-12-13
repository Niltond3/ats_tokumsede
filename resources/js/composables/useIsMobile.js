import { ref } from "vue";

const isMobile = ref(false);

export default function () {
    const detectDevice = () => {
        const userAgent =
            navigator.userAgent || navigator.vendor || window.opera;
        // Verifica dispositivos móveis comuns
        isMobile.value =
            /android|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(
                userAgent
            );
    };

    return {
        isMobile,
        detectDevice,
    };
}
