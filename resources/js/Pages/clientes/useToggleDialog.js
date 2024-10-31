import { ref } from 'vue';

export const dialogState = () => {
    const isOpen = ref(false);

    function toggleDialog() {
        console.log(isOpen.value)
        if (isOpen.value) return isOpen.value = false;
        return isOpen.value = true;
    }

    return [isOpen, toggleDialog];
};
