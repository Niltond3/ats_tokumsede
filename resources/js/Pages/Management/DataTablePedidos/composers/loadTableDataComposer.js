export const useLoadTableDataComposer = (orders, entregadores = null) => {
    const compose = (response) => {
        entregadores ? entregadores.value = response.data[7] : null

        const concatArray = [].concat(
            response.data[0],
            response.data[1],
            response.data[2],
            response.data[3],
            response.data[4]
        );
        const today = new Date();

        const orderMap = new Map(concatArray.map(p => [p.id, p]));

        const scheduledOrders = response.data[5].filter(pedido => {
            if (orderMap.has(pedido.id)) return false;
            const scheduleDate = dateToISOFormat(`${pedido.dataAgendada} ${pedido.horaInicio}`);
            return scheduleDate >= today;
        })

        orders.value = [].concat(concatArray, scheduledOrders);
        return orders.value;
    }

    return {
        compose
    }
}
