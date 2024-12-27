import { formatMoney, utf8Decode } from "@/util";

const { toCurrency } = formatMoney();
export const formatProductForClipboard = (product) => {
    const { nome, preco, precoEspecial, quantidade } = product;
    const value = precoEspecial
        ? precoEspecial[precoEspecial.length - 1].val
        : preco[preco.length - 1].val;
    const subtotal = quantidade ? quantidade * value : 0;

    return `${utf8Decode(nome)} un ${toCurrency(value)} ${
        subtotal !== 0 ? `subtotal: ${toCurrency(subtotal)}` : ""
    }`;
};
