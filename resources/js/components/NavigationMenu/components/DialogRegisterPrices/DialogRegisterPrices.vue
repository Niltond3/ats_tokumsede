<script setup>
// Vue core
import { ref, onMounted } from 'vue'

// UI Components
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select'

// Utilities
import { dialogState } from '@/hooks/useToggleDialog'
import renderToast from '@/components/renderPromiseToast'
import { utf8Decode } from '@/util'
import { Skeleton } from '@/components/ui/skeleton'

const loadingDistributors = ref(true)
const loadingProducts = ref(true)
const awaitingDistributor = ref(true)

const distributors = ref([])
const products = ref([])

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
    console.log(distributorId)
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
        // loadingDistributors.value = false
        console.log(products.value)
    })
}


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
            <DialogHeader>
                <DialogTitle class="leading-none flex gap-3 mr-4 text-lg text-info">
                    <i class="ri-shopping-bag-3-fill"></i>
                    <p class="font-semibold">Preços</p>
                </DialogTitle>
                <div class="flex gap-2">
                    <DialogDescription class="py-2 w-min text-nowrap">
                        Cadastro de preços
                    </DialogDescription>
                    <Skeleton v-if="loadingDistributors" class="w-full h-10" />
                    <Select v-else @update:modelValue="handleUpdateDistributorSelect">
                        <SelectTrigger>
                            <SelectValue placeholder="Selecione um distribuidor" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem v-for="distributor in distributors" :key="distributor.id"
                                    :value="`${distributor.id}`">
                                    {{ distributor.nome }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
            </DialogHeader>
            <div>
                table products with prices, later ....
            </div>
        </DialogContent>
    </Dialog>
</template>
