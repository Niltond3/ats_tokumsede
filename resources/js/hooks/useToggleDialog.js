import { ref } from 'vue';

export const dialogState = () => {
    const isOpen = ref(false);

    function toggleDialog() {
        if (isOpen.value) return isOpen.value = false;
        return isOpen.value = true;
    }

    return { isOpen, toggleDialog };
};
