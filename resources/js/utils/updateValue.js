export default (updaterOrValue, refValue) => {
    // Verifica se o parâmetro é uma função (atualizador) ou um valor simples.
    refValue.value =
        typeof updaterOrValue === "function"
            ? updaterOrValue(refValue.value)
            : updaterOrValue;
}
