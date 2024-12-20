import { reactive, ref, shallowRef } from "vue";
export const useOrderState = () => {
    const payload = reactive({
        formaPagamento: ref("1"),
        trocoPara: ref(0),
        agendado: ref(0),
        dataAgendada: ref(""),
        horaInicio: ref(""),
        horaFim: ref(""),
        obs: ref(""),
        observacao: ref(""),
        taxaEntrega: ref(0),
        totalProdutos: ref(0),
        total: ref(0),
        idEndereco: ref(0),
        idDistribuidor: ref(0),
        itens: ref([{}]),
        origem: ref(null),
    });
    const tableData = shallowRef([]);
    const status = ref(null);
    const editedRows = ref(null);

    const orderState = reactive({
        tableData,
        payload,
        status,
        editedRows,
    });
    return orderState;
};
