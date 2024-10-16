import { onUnmounted, onMounted, ref } from "vue";

export function useClientFormat() {
    const getSexo = {
        mobile: {
            1: 'Masculino',
            2: 'Feminino',
        },
        desktop: {
            1: 'M',
            2: 'F',
        }
    }

    const getTipoPessoaPayload = (documentValue) => {
        if (!documentValue) return {
            tipoPessoa: '1',
            documento: {
                'CPF': null,
                'CNPJ': null

            }
        }
        if (documentValue.length <= 14) {
            return {
                tipoPessoa: '1',
                documento: {
                    'CPF': documentValue,
                    'CNPJ': null

                }
            }
        }

        return {
            tipoPessoa: '2',
            documento: {
                'CPF': null,
                'CNPJ': documentValue
            }
        }

    }

    return { getSexo, getTipoPessoaPayload };
}
