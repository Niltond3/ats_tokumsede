import { utf8Decode } from '@/util'

export const sortProductsByName = (products) => {
    const PRIORITY_PRODUCT_PREFIX = "Alkalina"
    return products.sort((a, b) => {
        const isAAlkalina = a.nome.startsWith(PRIORITY_PRODUCT_PREFIX)
        const isBAlkalina = b.nome.startsWith(PRIORITY_PRODUCT_PREFIX)

        if (isAAlkalina && !isBAlkalina) return -1
        if (!isAAlkalina && isBAlkalina) return 1
        return a.nome.localeCompare(b.nome)
    })
}

export const formatProducts = (products) => {
    if (!Array.isArray(products)) return []

    const mapProduct = ({ id, idPreco, img, nome, qtdMin, valor }) => ({
        id,
        idPreco,
        img,
        nome: utf8Decode(nome),
        preco: [{ qtd: qtdMin, val: valor }],
    })

    return sortProductsByName(products.map(mapProduct))
}
