<script setup>
import { ref, computed, watch } from 'vue'
import { toast } from 'vue-sonner'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import DataTablePedidos from '@/Pages/Management/DataTablePedidos/DataTablePedidos.vue'
import { Form } from '@/components/ui/form'
import { Skeleton } from '@/components/ui/skeleton'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import { Button } from '@/components/ui/button'
import {
    ComboboxAnchor, ComboboxContent, ComboboxEmpty, ComboboxGroup,
    ComboboxInput, ComboboxItem, ComboboxItemIndicator, ComboboxLabel,
    ComboboxRoot, ComboboxTrigger, ComboboxViewport,
    TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText,
    TagsInputRoot
} from 'radix-vue'
import { listAllDistributors } from '@/services/api/distributors'
import renderToast from '@/components/renderPromiseToast'
import { utf8Decode } from '@/util'

const isOpen = ref(false)
const dateRange = ref(undefined)
const hasDateError = ref(false)
const orderResponse = ref(null)
const searchTerm = ref('')
const selectedDistributors = ref([])
const distributors = ref([])
const isLoading = ref(true)

watch(selectedDistributors, () => {
    searchTerm.value = ''
}, { deep: true })

const presets = computed(() => {
    const today = new Date()
    return [
        {
            label: 'Último dia',
            range: [new Date(today.setDate(today.getDate() - 1)), new Date()]
        },
        {
            label: 'Última semana',
            range: [new Date(today.setDate(today.getDate() - 7)), new Date()]
        },
        {
            label: 'Último mês',
            range: [new Date(today.setMonth(today.getMonth() - 1)), new Date()]
        },
        {
            label: 'Último ano',
            range: [new Date(today.setFullYear(today.getFullYear() - 1)), new Date()]
        }
    ]
})

async function getDistributors() {
    isLoading.value = true
    renderToast(
        listAllDistributors(),
        'Carregando distribuidores...',
        'Distribuidores carregados com sucesso!',
        (response) => {
            distributors.value = response.data.data.map(d => ({ id: d.id, nome: utf8Decode(d.nome) }))
            isLoading.value = false
        }
    )
}

async function fetchOrdersReport() {
    if (!dateRange.value) {
        hasDateError.value = true
        toast.warning('Selecione um período para gerar o relatório')
        return
    }
    hasDateError.value = false
    const [startDate, endDate] = dateRange.value
    const distributorIds = selectedDistributors.value
        .map(name => distributors.value.find(d => d.nome === name)?.id)
        .filter(id => id) // Remove falsy values
        .join(',')
    try {
        const response = await axios.post('/relatorio/pedidos', {
            dataInicial: startDate?.toLocaleDateString('pt-BR'),
            dataFinal: endDate?.toLocaleDateString('pt-BR'),
            idDistribuidores: distributorIds || null // Send null if no distributors selected
        })
        console.log(response.data)
        orderResponse.value = response
    } catch (error) {
        toast.error('Erro ao gerar relatório')
    }
}

watch(dateRange, () => {
    if (dateRange.value) {
        hasDateError.value = false
    }
})

getDistributors()
</script>

<template>
    <Dialog v-model:open="isOpen" :modal="false">
        <slot name="trigger" @click="isOpen = true" />
        <DialogContent class="max-w-[90vw]" @pointerdownOutside="(e) => e.preventDefault()">
            <DialogHeader>
                <DialogTitle>Relatório de Pedidos</DialogTitle>
            </DialogHeader>
            <div v-if="!orderResponse" class="mb-4">
                <Form @submit="fetchOrdersReport">
                    <VueDatePicker v-model="dateRange" range locale="pt-BR" format="dd/MM/yyyy"
                        :enable-time-picker="false" :presets="presets" clearable auto-apply
                        :class="{ 'border-red-500 ring-red-500': hasDateError }" />

                    <ComboboxRoot v-model="selectedDistributors" v-model:search-term="searchTerm" multiple
                        class="my-4 relative">
                        <Skeleton v-if="isLoading" class="w-full h-10 rounded-lg" />
                        <ComboboxAnchor v-else
                            class="w-full inline-flex items-center justify-between rounded-lg p-2 text-[13px] leading-none gap-[5px] bg-white shadow-sm border hover:bg-gray-50">
                            <TagsInputRoot v-slot="{ modelValue: tags }" :model-value="selectedDistributors"
                                delimiter="" class="flex gap-2 items-center rounded-lg flex-wrap">
                                <TagsInputItem v-for="item in tags" :key="item" :value="item"
                                    class="flex items-center gap-2 bg-primary text-primary-foreground rounded px-2 py-1">
                                    <TagsInputItemText class="text-sm" />
                                    <TagsInputItemDelete
                                        @click="selectedDistributors = selectedDistributors.filter(d => d !== item)">
                                        <i class="ri-close-line" />
                                    </TagsInputItemDelete>
                                </TagsInputItem>

                                <ComboboxInput as-child>
                                    <TagsInputInput placeholder="Selecione os distribuidores..."
                                        class="focus:outline-none flex-1 rounded !bg-transparent"
                                        @keydown.enter.prevent />
                                </ComboboxInput>
                            </TagsInputRoot>

                            <ComboboxTrigger>
                                <i class="ri-arrow-down-s-line h-4 w-4" />
                            </ComboboxTrigger>
                        </ComboboxAnchor>

                        <ComboboxContent class="absolute z-10 w-full mt-2 bg-white rounded-md shadow-lg">
                            <ComboboxViewport class="p-1">
                                <ComboboxEmpty class="text-gray-400 text-sm p-2" />
                                <ComboboxGroup>
                                    <ComboboxLabel class="px-2 text-xs text-gray-500">
                                        Distribuidores
                                    </ComboboxLabel>
                                    <ComboboxItem v-for="distributor in distributors" :key="distributor.id"
                                        :value="distributor.nome"
                                        class="relative flex items-center px-2 py-1.5 text-sm rounded-sm hover:bg-gray-100 cursor-pointer">
                                        <ComboboxItemIndicator class="absolute left-1">
                                            <i class="ri-check-line" />
                                        </ComboboxItemIndicator>
                                        <span>{{ distributor.nome }}</span>
                                    </ComboboxItem>
                                </ComboboxGroup>
                            </ComboboxViewport>
                        </ComboboxContent>
                    </ComboboxRoot>

                    <Button type="submit">Gerar Relatório</Button>
                </Form>
            </div>
            <!-- Add DataTable section -->
            <div v-else>
                <Button @click="orderResponse = null" class="mb-4">
                    <i class="ri-arrow-left-line mr-2" />
                    Voltar
                </Button>

                <DataTablePedidos :orderResponse="orderResponse" ajustClass="max-h-[70vh] overflow-auto" />
            </div>
        </DialogContent>
    </Dialog>
</template>
