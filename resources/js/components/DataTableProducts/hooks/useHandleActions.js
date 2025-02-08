import { toast } from "vue-sonner";
import { MoneyUtil } from "@/util";

export const handleActions = (props) => {
    const { toFloat } = MoneyUtil.formatMoney();

    const {
        emit,
        status,
        isUpdate,
        addressNote,
        tableIdentifier,
        payload,
        setPayload,
        setTableData,
        disabledButton,
    } = props;

    const handleCallbackPedido = () => {
        setPayload({ ...payload.value, observacao: addressNote.value });
        disabledButton.value = true;
        emit("callback:payloadPedido", payload.value);
    };

    const handlePayForm = (value) =>
        setPayload({ ...payload.value, formaPagamento: value });

    const handleExchange = ({ value }) =>
        setPayload({
            ...payload.value,
            trocoPara: parseFloat(value.split(" ")[1]),
        });

    const handleScheduling = (date) => {
        if (date) {
            const { date: formattedDate, time } =
                DateUtil.dateToDayMonthYearFormat(date);
            const dataAgendada = formattedDate;
            const horaInicio = time;
            return setPayload({
                ...payload.value,
                agendado: 1,
                dataAgendada,
                horaInicio,
            });
        }
        return setPayload({
            ...payload.value,
            agendado: 0,
            dataAgendada: "",
            horaInicio: "",
        });
    };

    const handleDistributor = (value) =>
        setPayload({ ...payload.value, idDistribuidor: value });

    const handleOrderNote = (value) =>
        setPayload({ ...payload.value, obs: value });

    const handleStatusChange = () => {
        if (status.value.label == "Agendado")
            return toast.info("Pedido Agendado!");
        if (status.value.label == "Pendente" && !status.value.oldStatus)
            return toast.info("Pedido Pendente!");
        if (status.value.label == "Pendente" && status.value.oldStatus) {
            status.value = status.value.oldStatus;
            setPayload({ ...payload.value, status: status.value.statusId });
            return toast.info("Status Restaurado!");
        }

        const pendente = {
            label: "Pendente",
            classes: {
                bg: "bg-warning",
                text: "text-warning",
                icon: "ri-error-warning-fill",
            },
        };

        const oldStatus = {
            ...status.value,
            statusId: payload.value.status,
        };

        status.value = { ...pendente, oldStatus };
        setPayload({ ...payload.value, status: 1 });
        return toast.info("Status Alterado!");
    };

    const handleDataToTable = (data) => {
        const {
            products,
            distributorTaxes: { taxaUnica: taxaEntrega },
            distributor: { id: idDistribuidor, nome: distributorName },
            address: { id: idEndereco, observacao },
        } = data;

        tableIdentifier.value = distributorName;

        addressNote.value = observacao;

        const order = data.order;

        if (order) {
            const {
                obs,
                itensPedido,
                total,
                formaPagamento: { id: formaPagamento },
                trocoPara: orderTroco,
                agendado,
                dataAgendada,
                horaInicio,
                endereco: { observacao },
                idEndereco,
                id: idPedido,
                status: orderStatus,
            } = order;

            isUpdate.value = true;

            const newProducts = products.map((product) => {
                const productToChange = itensPedido.filter(
                    (prod) => prod.idProduto == product.id
                )[0];

                if (productToChange)
                    return {
                        ...product,
                        preco: [
                            {
                                qtd: product.preco[product.preco.length - 1].qtd,
                                val: toFloat(productToChange.preco),
                            },
                        ],
                    };
                return product;
            });

            setTableData(newProducts);

            const itens = itensPedido.map((item) => {
                const {
                    preco: itemPreco,
                    qtd: quantidade,
                    subtotal: itemSubtotal,
                    precoAcertado,
                    idProduto,
                } = item;
                const preco = toFloat(itemPreco);
                const subtotal = toFloat(itemSubtotal);

                return {
                    idProduto,
                    preco,
                    precoAcertado,
                    quantidade,
                    subtotal,
                };
            });

            status.value = orderStatus;
            const trocoPara = toFloat(orderTroco);
            const totalProdutos = itens
                .map((product) => parseFloat(product.subtotal))
                .reduce((curr, prev) => curr + prev);

            setPayload({
                ...payload.value,
                formaPagamento,
                trocoPara,
                agendado,
                dataAgendada,
                horaInicio,
                obs,
                observacao,
                totalProdutos,
                total: toFloat(total),
                idEndereco,
                idDistribuidor,
                itens,
                idPedido,
                status: order.statusId,
            });
            return;
        }

        setTableData(products);

        setPayload({
            ...payload.value,
            taxaEntrega,
            idDistribuidor,
            idEndereco,
        });
    };

    return {
        handleCallbackPedido,
        handlePayForm,
        handleExchange,
        handleScheduling,
        handleDistributor,
        handleOrderNote,
        handleStatusChange,
        handleDataToTable,
    };
};
