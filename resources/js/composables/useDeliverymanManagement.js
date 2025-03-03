// Import necessary functions and libraries from Vue and external modules
import { ref, nextTick } from 'vue';
import * as z from 'zod';
import { dialogState } from '@/composables/useToggleDialog';
import {
    createDeliveryman,
    updateDeliveryman,
    deleteDeliverymanAPI,
    getDeliverymen,
} from '@/services/api/deliveryman';
import { getDistribuidores } from '@/services/api/distributors';
import renderToast from '@/components/renderPromiseToast';
import { StringUtil } from '@/util'

/**
 * Composable to handle deliveryman management logic.
 * It includes CRUD operations and form validation.
 */
export function useDeliverymanManagement() {
    // Data for editing (when a deliveryman is selected)
    const deliverymanDetails = ref({});

    // List of deliverymen
    const deliverymenList = ref([]);

    // List of distributors for association
    const distribuidoresList = ref([]);

    // Button state to prevent multiple clicks during operations
    const disabledButton = ref(false);

    // Management of dialog state using a toggle
    const { isOpen, toggleDialog } = dialogState('ManageDeliveryman');

    // Validation schema with Zod for form fields
    const formSchema = z.object({
        nome: z.string({ required_error: 'Informe o nome do entregador' }),
        telefone: z.string({ required_error: 'Informe o telefone do entregador' }),
        placaVeiculo: z.string({ required_error: 'Informe a placa do veículo' }),
        // The idDistribuidor field can be empty (not selected) or a valid number or string, and transforms it to a number
        idDistribuidor: z.union([
            z.string().transform(val => val === "" ? undefined : Number(val)),
            z.number()
        ]).optional(),
    });

    // Resets the form values when the dialog is closed
    const resetDialogValues = () => {
        deliverymanDetails.value = {};
    };

    // Opens or closes the dialog and resets values if necessary
    const handleDialogOpen = (openValue) => {
        if (!openValue) {
            resetDialogValues();
        }
        toggleDialog();
    };

    // Fetches the list of deliverymen via API
    const fetchDeliverymen = async () => {
        try {
            const response = await getDeliverymen();
            console.log(response)
            deliverymenList.value = response.map((deliveryman) => ({
                ...deliveryman,
                nome: StringUtil.utf8Decode(deliveryman.nome),
                distribuidor: {
                    ...deliveryman.distribuidor,
                    nome: StringUtil.utf8Decode(deliveryman.distribuidor.nome),
                }

            }));
        } catch (error) {
            console.error('Erro ao buscar entregadores', error);
        }
    };

    // Fetches the list of distributors via API
    const fetchDistribuidores = async () => {
        try {
            const response = await getDistribuidores();
            distribuidoresList.value = response.map((distributor) => ({
                ...distributor,
                nome: StringUtil.utf8Decode(distributor.nome),
            }));
        } catch (error) {
            console.error('Erro ao buscar distribuidores', error);
        }
    };

    // Initial call to fetch distributors
    fetchDistribuidores();

    // Function to submit (create or update) the deliveryman data
    const onSubmit = async (values) => {
        console.log(values)
        if (deliverymanDetails.value && deliverymanDetails.value.id) {
            // Update existing deliveryman
            renderToast(
                updateDeliveryman(deliverymanDetails.value.id, values),
                'Atualizando entregador...',
                'Entregador atualizado com sucesso',
                'Erro ao atualizar entregador'
            ).then(() => {
                fetchDeliverymen();
                resetDialogValues();
            });
        } else {
            // Create new deliveryman
            renderToast(
                createDeliveryman(values),
                'Cadastrando entregador...',
                'Entregador cadastrado com sucesso',
                'Erro ao cadastrar entregador'
            ).then(() => {
                fetchDeliverymen();
                resetDialogValues();
            });
        }
    };

    // Validates and submits the form
    const handleSubmit = async (event, values, validate) => {
        event.preventDefault();
        const isValid = await validate();
        if (isValid.valid) {
            const formattedValues = {
                ...values,
                idDistribuidor: values.idDistribuidor ? Number(values.idDistribuidor) : undefined
            };
            onSubmit(formattedValues);
        }
    };

    // Function to delete a deliveryman
    const deleteDeliveryman = async (id) => {
        try {
            renderToast(
                deleteDeliverymanAPI(id),
                'Excluindo entregador...',
                'Entregador excluído com sucesso',
                'Erro ao excluir entregador'
            ).then(() => {
                fetchDeliverymen();
            });
        } catch (error) {
            console.error(error);
        }
    };

    // Fills the form data for editing a deliveryman
    const editDeliveryman = async (deliveryman) => {
        console.log('Deliveryman details:', deliveryman);
        console.log('idDistribuidor type:', typeof deliveryman.idDistribuidor);

        // Primeiro, limpe o formulário
        deliverymanDetails.value = {};

        // Espere o próximo ciclo de renderização
        await nextTick();

        // Então, defina os novos valores
        deliverymanDetails.value = { ...deliveryman };
    };

    // Return all reactive variables and functions
    return {
        isOpen,
        deliverymanDetails,
        deliverymenList,
        distribuidoresList,
        disabledButton,
        formSchema,
        handleDialogOpen,
        handleSubmit,
        fetchDeliverymen,
        deleteDeliveryman,
        editDeliveryman,
    };
}
