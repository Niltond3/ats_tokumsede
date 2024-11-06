import { ref } from 'vue';

export const dataTable = (data) => {
    const tableData = ref(data);

    function setTableData(newTableData) {
        tableData.value = newTableData;
    }

    return [tableData, setTableData];
};
