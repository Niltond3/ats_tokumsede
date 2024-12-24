import { dateToDayMonthYearFormat } from "@/util";

export const useEventHandlers = (
    orderState,
    emit,
    addressNote,
    disabledButton
) => ({
    handleCallbackPedido: () => {
        orderState.payload = {
            ...orderState.payload,
            observacao: addressNote.value,
        };
        disabledButton.value = true;
        console.log(orderState.payload);
        emit("callback:payloadPedido", orderState.payload);
    },
    handlePayForm: (value) =>
        (orderState.payload = { ...orderState.payload, formaPagamento: value }),
    handleExchange: ({ value }) =>
        (orderState.payload = {
            ...orderState.payload,
            trocoPara: parseFloat(value.split(" ")[1]),
        }),
    handleScheduling: (date) => {
        if (date) {
            const { date: formattedDate, time } =
                dateToDayMonthYearFormat(date);

            const dataAgendada = formattedDate;

            const horaInicio = time;

            return (orderState.payload = {
                ...orderState.payload,
                agendado: 1,
                dataAgendada,
                horaInicio,
            });
        }
        return (orderState.payload = {
            ...orderState.payload,
            agendado: 0,
            dataAgendada: "",
            horaInicio: "",
        });
    },
    handleDistributor: (value) =>
        (orderState.payload = { ...orderState.payload, idDistribuidor: value }),
    handleUpdateOrderNote: (value) => {
        console.log(value);
        orderState.payload = { ...orderState.payload, obs: value };
    },
    handleUpdateStatus: (value) => {
        console.log(value);
        // if (orderState.status.label == 'Agendado') return toast.info('Pedido Agendado!')
        // if (orderState.status.label == 'Pendente' && !orderState.status.oldStatus) return toast.info('Pedido Pendente!')
        // if (orderState.status.label == 'Pendente' && orderState.status.oldStatus) {
        //     orderState.status = orderState.status.oldStatus
        //     orderState.payload = { ...orderState.payload, status: orderState.status.statusId }
        //     return toast.info('Status Restaurado!')
        // }

        // const pendente = {
        //     label: 'Pendente',
        //     classes: {
        //         bg: 'bg-warning',
        //         text: 'text-warning',
        //         icon: 'ri-error-warning-fill'
        //     }

        // }

        // const oldStatus = {
        //     ...orderState.status,
        //     statusId: orderState.payload.status
        // }

        // orderState.status = { ...pendente, oldStatus }
        // orderState.payload = { ...orderState.payload, status: 1 }
        // return toast.info('Status Alterado!')
    },
});
