import { ref } from 'vue';

export const payloadPedido = () => {
    const payload = ref({
        formaPagamento: 0,
        trocoPara: 0,
        agendado: 0,
        dataAgendada: '',
        horaInicio: '',
        horaFim: '',
        obs: '',
        observacao: '',
        taxaEntrega: 0,
        totalProdutos: 0,
        total: 0,
        idEndereco: 0,
        idDistribuidor: 0,
        itens: [{}],
    });

    function setPayload(newPayload) {
        payload.value = newPayload;
    }
    return [payload, setPayload];
};
