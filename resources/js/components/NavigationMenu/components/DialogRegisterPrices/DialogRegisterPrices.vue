<script setup>
// Vue core
import { ref, watch, computed } from 'vue'
import { useVueuse } from '@vueuse/core'

// UI Components
import {
    Dialog,
    DialogContent,
    DialogTrigger,
} from '@/components/ui/dialog'
import DialogHeader from './components/DialogHeader.vue'
import DialogBody from './components/DialogBody.vue'
import DialogFooter from './components/DialogFooter.vue'

// Table related
import { useVueTable } from "@tanstack/vue-table"
import { useUpdateData } from "@/Pages/Management/DataTableProducts/composable/useUpdateData"
import { createTableOptions } from "@/Pages/Management/DataTableProducts/config/tableConfig"
import { columns } from "@/Pages/Management/DataTableProducts/config/Columns"
import { useTableProductsState } from "@/composables/tableProductsState"

// Utilities
import { dialogState } from '@/hooks/useToggleDialog'
import renderToast from '@/components/renderPromiseToast'
import { utf8Decode } from '@/util'


const resizebleColumns = ref(
    columns.filter(
        (column) => column.id !== "quantidade" && column.id !== "actions"
    )
);
const loadingDistributors = ref(true)
const loadingProducts = ref(true)

const sorting = ref([]);
const distributors = ref([])
const globalFilter = ref(null);
const selectedDistributorId = ref(null)

const { width } = useWindowSize();
const tableProductsState = useTableProductsState();
const { updateData } = useUpdateData(tableProductsState);


const { isOpen, toggleDialog } = dialogState()

const disabledButton = ref(false)

const emits = defineEmits(["on:create", 'update:dialogOpen']);

const sortProductsByName = (products) => {
    const PRIORITY_PRODUCT_PREFIX = "Alkalina";
    return products.sort((a, b) => {
        const isAAlkalina = a.nome.startsWith(PRIORITY_PRODUCT_PREFIX);
        const isBAlkalina = b.nome.startsWith(PRIORITY_PRODUCT_PREFIX);

        if (isAAlkalina && !isBAlkalina) return -1;
        if (!isAAlkalina && isBAlkalina) return 1;
        return a.nome.localeCompare(b.nome);
    });
};

const formatProducts = (products) => {
    if (!Array.isArray(products)) return [];

    const mapProduct = ({ id, idPreco, img, nome, qtdMin, valor }) => ({
        id,
        idPreco,
        img,
        nome,
        preco: [{ qtd: qtdMin, val: valor }],
    });

    return sortProductsByName(products.map(mapProduct));
};

const getDistributors = () => {
    const url = 'distribuidores/todosOsDistribuidores'
    const promise = axios.get(url)
    renderToast(promise, 'carregando distribuidores ...', 'Distribuidores carregados', (response) => {
        const distributorsRawArray = response.data.data

        distributors.value = distributorsRawArray.map((distributor) => {
            return {
                ...distributor,
                nome: utf8Decode(distributor.nome)
            }
        })
        loadingDistributors.value = false
    })
}

const handleUpdateDistributorSelect = (distributorId) => {
    console.log(distributorId)
    selectedDistributorId.value = distributorId
    loadingProducts.value = true

    tableProductsState.tableData = [];

    const url = `produtos/listarPorDistribuidor/${distributorId}`
    const promise = axios.get(url)
    renderToast(promise, 'carregando produtos ...', 'Produtos carregadoss', (response) => {
        const productsResponseArray = response.data

        const products = productsResponseArray.map((product) => ({
            ...product,
            nome: utf8Decode(product.nome)
        }))

        tableProductsState.tableData = formatProducts(products);

        loadingProducts.value = false
    })
}

const handleUpdateTableData = (tableDataValue) => tableDataProducts.value = tableDataValue

const handleDialogOpen = (op) => {
    op && getDistributors()
    !op && emits('update:dialogOpen', false)
    toggleDialog()
}

const tableOptions = reactive(
    createTableOptions(
        tableProductsState,
        globalFilter,
        sorting,
        resizebleColumns,
        { value: null },
        updateData
    )
);

const table = useVueTable(tableOptions);

watch(
    () => tableProductsState.tableData,
    (newTableData) => {
        console.log(newTableData)
    }
)

watch(
    () => width.value,
    (newWidth) =>
    (resizebleColumns.value = useResponsiveColumns(
        columns,
        newWidth
    ).value)
);

</script>

<template>
    <Dialog :open="isOpen" @update:open="handleDialogOpen">
        <DialogTrigger as-child>
            <button class="text-info flex items-center justify-center flex-col">
                <div class="select-none rounded-full bg-info h-6 w-6 flex items-center justify-center">
                    <span class="font-bold text-white text-sm">R$</span>
                </div>
                <span class="hidden min-[426px]:block">Preço</span>
            </button>
        </DialogTrigger>
        <DialogContent class="flex flex-col gap-2">
            <DialogHeader :loading-distributors="loadingDistributors" :distributors="distributors"
                :global-filter="globalFilter" @update:distributor="handleUpdateDistributorSelect" />
            <DialogBody :table="table" :resizeble-columns="resizebleColumns"
                @update:table-data="handleUpdateTableData" />
            <DialogFooter :loading-products="loadingProducts" :products="tableProductsState.tableData"
                :distributor-id="selectedDistributorId" />
        </DialogContent>
    </Dialog>
</template>
