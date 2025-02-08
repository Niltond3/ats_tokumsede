// Utilities
import { MoneyUtil } from "@/util";

const { toFloat } = MoneyUtil.formatMoney();

const getPrice = (product, orderItem) => {
    return {
        ...product,
        preco: [
            {
                qtd: orderItem.qtd,
                val: toFloat(orderItem.preco),
            },
        ],
    }
};
const mapProductPrice = (product, orderItem) => {
    const price = getPrice(product, orderItem);
    const specialPrice = product.precoEspecial;
    if (specialPrice) {
        return {
            ...price,
            precoEspecial: [specialPrice[specialPrice.length - 1]],
        };
    }
    return price;
};

const mapProductsWithPrices = (products, orderItems) => {
    return products.map((product) => {
        const productToChange = orderItems.find(
            (prod) => prod.idProduto === product.id
        );
        if (!productToChange) return product;

        return mapProductPrice(product, productToChange);
    });
};
const mapOrderItems = (itensPedido) => {
    return itensPedido.map((item) => {
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
};

const handleOrderData = (order, products) => {
    const {
        obs,
        itensPedido,
        total,
        endereco,
        distribuidor,
        formaPagamento: paymentString,
        trocoPara: orderTroco,
        agendado,
        dataAgendada,
        horaInicio,
        idEndereco,
        id: idPedido,
        status: orderStatus,
    } = order;
    const { id: idDistribuidor } = distribuidor;
    const { observacao } = endereco;
    const trocoPara = toFloat(orderTroco);

    const paymentFormToIndex = {
        Dinheiro: 1,
        Cartão: 2,
        Pix: 3,
        Transferência: 4,
    };
    const formaPagamento = paymentFormToIndex[paymentString];
    const productsWithPrice = mapProductsWithPrices(products, itensPedido);
    const itens = mapOrderItems(itensPedido);
    const totalProdutos = itens
        .map((product) => parseFloat(product.subtotal))
        .reduce((curr, prev) => curr + prev);

    const orderPayload = {
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
        itens,
        idPedido,
        idDistribuidor,
        status: order.statusId,
    };

    return {
        updateOrder: true,
        products: productsWithPrice,
        orderStatus,
        orderPayload,
    };
};
const useDataToTableFormat = (
    products,
    taxaEntrega,
    idDistribuidor,
    idEndereco,
    order
) => {

    if (order) return handleOrderData(order, products);

    const orderPayload = {
        taxaEntrega,
        idDistribuidor,
        idEndereco,
    };
    return {
        updateOrder: false,
        products,
        orderStatus: null,
        orderPayload,
    };
};

export default useDataToTableFormat;
