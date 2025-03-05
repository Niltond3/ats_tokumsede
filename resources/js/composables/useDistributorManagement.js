import { ref, nextTick } from 'vue';
import * as z from 'zod';
import validator from 'validator';
import { dialogState } from '@/composables/useToggleDialog';
// Importando os endpoints atualizados e fazendo alias no delete para evitar conflito
import {
    getDistribuidores,
    createDistributor,
    updateDistributor,
    deleteDistributor as deleteDistributorAPI
} from '@/services/api/distributors';
import renderToast from '@/components/renderPromiseToast';

/**
 * Composable para gerenciar as operações de CRUD dos distribuidores.
 */
export function useDistributorManagement() {
    // Dados para edição (quando um distribuidor é selecionado)
    const distributorDetails = ref({});

    // Lista de distribuidores
    const distributorsList = ref([]);

    // Estado do botão para evitar múltiplos cliques
    const disabledButton = ref(false);

    // Gerenciamento do estado do diálogo
    const { isOpen, toggleDialog } = dialogState('ManageDistributor');

    // Esquema de validação utilizando Zod para os campos do distribuidor
    const formSchema = z.object({
        nome: z.string({ required_error: 'Informe o nome do distribuidor' }),
        cnpj: z.string({ required_error: 'Informe o CNPJ' }),
        email: z.string({ required_error: 'Informe o email' }).email('Email inválido'),
        telefonePrincipal: z.string({ required_error: 'Informe o telefone' }).refine(validator.isMobilePhone, { message: 'Número de telefone inválido' }),
        // Adicione outros campos conforme necessário
    });

    // Reseta os valores do formulário quando o diálogo é fechado
    const resetDialogValues = () => {
        distributorDetails.value = {};
    };

    // Abre ou fecha o diálogo e reseta valores, se necessário
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
        distributorDetails.value = { ...distributor };
    };

    return {
        isOpen,
        distributorDetails,
        distributorsList,
        disabledButton,
        formSchema,
        handleDialogOpen,
        handleSubmit,
        fetchDistributors,
        deleteDistributor,
        editDistributor,
    };
}
