import { dateToDayMonthYearFormat } from "@/util";
import { toast } from "vue-sonner";

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
        (orderState.payload = {
            ...orderState.payload,
            idDistribuidor: value.toString(),
        }),
    handleUpdateOrderNote: (value) => {
        console.log(value);
        orderState.payload = { ...orderState.payload, obs: value };
    },
    handleUpdateStatus: (value) => {
        console.log(value);
        const { info, payload, status } = value;
        orderState.status = status;
        orderState.payload = { ...orderState.payload, status: payload };
        return toast.info(info);
    },
});
