// src/composables/useDistributorManagement.js
import { ref, nextTick } from 'vue';
import * as z from 'zod';
import validator from 'validator';
import { dialogState } from '@/composables/useToggleDialog';
import {
    getDistribuidores,
    createDistributor,
    updateDistributor,
    deleteDistributor as deleteDistributorAPI,
    getDistributor
} from '@/services/api/distributors';
import renderToast from '@/components/renderPromiseToast';
import { usePixKeyValidation } from './usePixKeyValidation';

/**
 * Composable para gerenciar as operações de CRUD dos distribuidores
 * e para fornecer os schemas de validação de um formulário multi‐etapas.
 */
export function useDistributorManagement() {
    // Validação do pixKey
    const { validatePixKeyFormat } = usePixKeyValidation();

    // Dados para edição (quando um distribuidor é selecionado)
    const distributorDetails = ref({});

    // Lista de distribuidores (para administradores não Distribuidores)
    const distributorsList = ref([]);

    // Estado do botão para evitar múltiplos cliques
    const disabledButton = ref(false);

    // Gerenciamento do estado do diálogo
    const { isOpen, toggleDialog } = dialogState('ManageDistributor');

    // === Schemas de validação para o formulário multi‐etapas ===
    // Etapa 1: Dados Básicos (omiti o campo dddTelefone)
    const basicInfoSchema = z.object({
        nome: z.string({ required_error: 'Informe o nome do distribuidor' }),
        cnpj: z.string().nullable().optional(),
        email: z.string({ required_error: 'Informe o email' }).email('Email inválido'),
        telefonePrincipal: z
            .string({ required_error: 'Informe o telefone' })
            .refine(validator.isMobilePhone, { message: 'Número de telefone inválido' }),
        outrosContatos: z.string().nullable().optional(),
        pix_key: z.string()
            .nullable()
            .optional()
            .refine(value => !value || validatePixKeyFormat(value), {
                message: "Formato de chave PIX inválido"
            })
    });

    // Etapa 2: Endereço
    const addressSchema = z.object({
        logradouro: z.string({ required_error: 'Informe o logradouro' }),
        numero: z.string({ required_error: 'Informe o número' }),
        bairro: z.string({ required_error: 'Informe o bairro' }),
        complemento: z.string().optional(),
        cep: z.string({ required_error: 'Informe o CEP' }),
        cidade: z.string({ required_error: 'Informe a cidade' }),
        estado: z.string({ required_error: 'Informe o estado' }),
        referencia: z.string().optional(),
    });

    // Etapa 3: Horários e Taxas (apenas campos com prefixo "novo_")
    const scheduleAndRatesSchema = z.object({
        novo_domingo: z.number({ required_error: 'Informe se domingo está ativo (0 ou 1)' }),
        novo_inicioDomingo: z.string({ required_error: 'Informe o horário de início de domingo' }),
        novo_fimDomingo: z.string({ required_error: 'Informe o horário de fim de domingo' }),
        novo_sabado: z.number({ required_error: 'Informe se sábado está ativo (0 ou 1)' }),
        novo_inicioSabado: z.string({ required_error: 'Informe o horário de início de sábado' }),
        novo_fimSabado: z.string({ required_error: 'Informe o horário de fim de sábado' }),
        taxaUnica: z.number().optional(),
        valorTaxaUnica: z.number().optional(),
        taxaDomingo: z.number().optional(),
        valorTaxaDomingo: z.number().optional(),
        taxaCompraMinima: z.number().optional(),
        valorCompraMinima: z.number().optional(),
        taxaEntregaDistante: z.number().optional(),
        distanciaMaxima: z.number().optional(),
        valorKmAdicional: z.number().optional(),
    });

    // Array com os schemas para cada etapa do formulário
    const formSchemas = [basicInfoSchema, addressSchema, scheduleAndRatesSchema];
    // =============================================================

    // Reseta os valores do formulário quando o diálogo é fechado
    const resetDialogValues = () => {
        distributorDetails.value = {};
    };

    // Abre ou fecha o diálogo e reseta os valores, se necessário
    const handleDialogOpen = (openValue) => {
        if (!openValue) {
            resetDialogValues();
        }
        toggleDialog();
    };

    // Busca a lista de distribuidores via API
    const fetchDistributors = async () => {
        try {
            const response = await getDistribuidores();
            distributorsList.value = response;
        } catch (error) {
            console.error('Erro ao buscar distribuidores', error);
        }
    };

    // Busca um distribuidor específico para edição e concatena DDD com telefone
    const fetchDistributor = async (distributorId) => {
        try {
            const response = await getDistributor(distributorId);
            const data = response.data.data;
            // Se existir dddTelefone, concatena com telefonePrincipal no formato (##) ####-#### ou similar
            if (data.dddTelefone && data.telefonePrincipal) {
                data.telefonePrincipal = `(${data.dddTelefone}) ${data.telefonePrincipal}`;
            }
            distributorDetails.value = data;
        } catch (error) {
            console.error('Erro ao buscar distribuidor', error);
        }
    };

    // Função para submeter (criar ou atualizar) os dados do distribuidor
    const onSubmit = async (values) => {
        if (distributorDetails.value && distributorDetails.value.id) {
            renderToast(
                updateDistributor(distributorDetails.value.id, values),
                'Atualizando distribuidor...',
                'Distribuidor atualizado com sucesso',
                'Erro ao atualizar distribuidor'
            ).then(() => {
                fetchDistributors();
                resetDialogValues();
            });
        } else {
            renderToast(
                createDistributor(values),
                'Cadastrando distribuidor...',
                'Distribuidor cadastrado com sucesso',
                'Erro ao cadastrar distribuidor'
            ).then(() => {
                fetchDistributors();
                resetDialogValues();
            });
        }
    };

    // Valida e submete o formulário
    const handleSubmit = async (event, values, validate) => {
        event.preventDefault();
        const isValid = await validate();
        if (isValid.valid) {
            onSubmit(values);
        }
    };

    // Função para deletar um distribuidor
    const deleteDistributor = async (id) => {
        try {
            renderToast(
                deleteDistributorAPI(id),
                'Excluindo distribuidor...',
                'Distribuidor excluído com sucesso',
                'Erro ao excluir distribuidor'
            ).then(() => {
                fetchDistributors();
            });
        } catch (error) {
            console.error(error);
        }
    };

    // Preenche os dados do formulário para edição
    const editDistributor = async (distributor) => {
        distributorDetails.value = {};
        await nextTick();
        console.log(distributor)
        distributorDetails.value = {
            ...distributor,
            // Concatena o DDD com o telefone, se necessário
            telefonePrincipal: distributor.dddTelefone + distributor.telefonePrincipal,
        };
    };

    return {
        isOpen,
        distributorDetails,
        distributorsList,
        disabledButton,
        formSchemas,
        handleDialogOpen,
        handleSubmit,
        fetchDistributor,
        fetchDistributors,
        deleteDistributor,
        editDistributor,
        resetDialogValues,
    };
}
