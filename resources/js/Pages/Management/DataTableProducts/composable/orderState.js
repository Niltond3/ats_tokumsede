import { reactive, ref, shallowRef } from "vue";
export const useOrderState = () => {
    const formaPagamento = ref("1");
    const trocoPara = ref(0);
    const agendado = ref(0);
    const dataAgendada = ref("");
    const horaInicio = ref("");
    const horaFim = ref("");
    const obs = ref("");
    const observacao = ref("");
    const taxaEntrega = ref(0);
    const totalProdutos = ref(0);
    const total = ref(0);
    const idEndereco = ref(0);
    const idDistribuidor = ref(0);
    const itens = ref([{}]);
    const origem = ref(null);

    const payload = reactive({
        formaPagamento,
        trocoPara,
        agendado,
        dataAgendada,
        horaInicio,
        horaFim,
        obs,
        observacao,
        taxaEntrega,
        totalProdutos,
        total,
        idEndereco,
        idDistribuidor,
        itens,
        origem,
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
