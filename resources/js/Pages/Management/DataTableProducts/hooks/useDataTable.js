import { shallowRef } from "vue";

export const dataTable = (data) => {
    const tableData = shallowRef(data);

    function setTableData(newTableData) {
        tableData.value = newTableData;
    }

    return [tableData, setTableData];
};
