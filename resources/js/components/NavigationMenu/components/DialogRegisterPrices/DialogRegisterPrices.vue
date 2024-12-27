<script setup>
// Vue core
import { ref, onMounted } from 'vue'

// UI Components
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
    Dialog,
    DialogContent,
    DialogTrigger,
} from '@/components/ui/dialog'
// Utilities
import { dialogState } from '@/hooks/useToggleDialog'
import renderToast from '@/components/renderPromiseToast'
import { utf8Decode } from '@/util'
import DialogHeader from './components/DialogHeader.vue'
import DialogBody from './components/DialogBody.vue'
import DialogFooter from './components/DialogFooter.vue'

const loadingDistributors = ref(true)
const loadingProducts = ref(true)

const distributors = ref([])
const products = ref([])
const tableDataProducts = ref([])
const selectedDistributorId = ref(null)

const { isOpen, toggleDialog } = dialogState()

const disabledButton = ref(false)

const emits = defineEmits(["on:create", 'update:dialogOpen']);

const getDistributors = () => {
    const url = 'distribuidores/todosOsDistribuidores'
    const promise = axios.get(url)
    renderToast(promise, 'carregando distribuidores ...', 'Distribuidores carregadoss', (response) => {
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
    selectedDistributorId.value = distributorId
    const url = `produtos/listarPorDistribuidor/${distributorId}`
    const promise = axios.get(url)
    renderToast(promise, 'carregando produtos ...', 'Produtos carregadoss', (response) => {
        const productsResponseArray = response.data

        products.value = productsResponseArray.map((product) => {
            return {
                ...product,
                nome: utf8Decode(product.nome)
            }
        })
        loadingProducts.value = false
        console.log(products.value)
    })
}

const handleUpdateTableData = (tableDataValue) => tableDataProducts.value = tableDataValue

const handleDialogOpen = (op) => {
    op && getDistributors()
    !op && emits('update:dialogOpen', false)
    toggleDialog()
}

onMounted(() => {
})

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
        <DialogContent class="gap-2">
            <DialogHeader :loading-distributors="loadingDistributors" :distributors="distributors"
                @update:distributor="handleUpdateDistributorSelect" />
            <DialogBody :loading-products="loadingProducts" :products="products"
                @update:table-data="handleUpdateTableData" />
            <DialogFooter :loading-products="loadingProducts" :products="tableDataProducts"
                :distributor-id="selectedDistributorId" />
        </DialogContent>
    </Dialog>
</template>
