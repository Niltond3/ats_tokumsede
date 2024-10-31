import { ref } from 'vue';

export const editRow = () => {
    const editedRows = ref(false);

    function setEditedRows() {
        if (editedRows.value) return editedRows.value = false;
        return editedRows.value = true;
    }

    return [editedRows, setEditedRows];
};
