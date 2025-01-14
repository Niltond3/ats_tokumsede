<script setup>
import { ref, computed, watch } from 'vue'
import { toast } from 'vue-sonner'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import DataTablePedidos from '@/Pages/Management/DataTablePedidos/DataTablePedidos.vue'
import { Form } from '@/components/ui/form'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import { Button } from '@/components/ui/button'
import { listAllDistributors } from '@/services/api/distributors'
import renderToast from '@/components/renderPromiseToast'
import { cn, utf8Decode } from '@/util'
import DistributorCombobox from './DistributorCombobox.vue'

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

const createDateRange = (daysBack) => {
    const end = new Date()
    const start = new Date()
    start.setDate(start.getDate() - daysBack)
    start.setHours(0, 0, 0, 0)
    end.setHours(23, 59, 59, 999)
    return [start, end]
}

const presets = computed(() => [
    {
        label: 'Último dia',
        value: createDateRange(1)
    },
    {
        label: 'Última semana',
        value: createDateRange(7)
    },
    {
        label: 'Último mês',
        value: createDateRange(30)
    },
    {
        label: 'Último ano',
        value: createDateRange(365)
    }
])

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
    <Dialog v-model:open="isOpen" :modal="true">
        <slot name="trigger" @click="isOpen = true" />
        <DialogContent class="max-w-[90vw] sm:max-w-3xl" @pointerdownOutside.prevent
            :class="{ 'overflow-auto px-2 overflow-x-hidden': orderResponse, 'overflow-visible': !orderResponse }">
            <DialogHeader class="flex justify-between items-center mb-3">
                <DialogTitle class="text-info">Relatório de Pedidos</DialogTitle>
                <Button v-if="orderResponse" @click="orderResponse = null" variant="ghost" size="sm" :class='cn(
                    [
                        "group h-4 p-0 flex justify-start rounded-sm absolute top-4 font-thin opacity-70 transition-all",
                        "max-[640px]:left-4",
                        "sm:w-6 sm:right-6",
                        "sm:hover:w-[5.5rem] sm:hover:px-2 sm:hover:right-8 hover:text-info hover:opacity-100"
                    ])'>
                    <i class="ri-arrow-left-line" />
                    <span :class='cn([
                        "max-[640px]:sr-only",
                        "sm:pointer-events-none sm:opacity-0 sm:select-none sm:transition-opacity",
                        "sm:group-hover:opacity-100"
                    ])'>Voltar</span>
                </Button>
            </DialogHeader>
            <div v-if="!orderResponse" class="mb-4">
                <Form @submit="fetchOrdersReport">
                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="sm:w-1/2">
                            <VueDatePicker v-model="dateRange" range locale="pt-BR" format="dd/MM/yyyy"
                                :enable-time-picker="false" :preset-dates="presets" clearable auto-apply :class="[
                                    { 'border-red-500 ring-red-500': hasDateError },
                                    '[&_input]:h-14' // Match height with DistributorCombobox
                                ]" />
                        </div>
                        <div class="sm:w-1/2">
                            <DistributorCombobox v-model="selectedDistributors" v-model:search-term="searchTerm"
                                :distributors="distributors" :is-loading="isLoading" class="" />
                        </div>
                    </div>

                    <Button type="submit" class="mt-4">Gerar Relatório</Button>
                </Form>
            </div>
            <!-- Add DataTable section -->
            <div v-else class="flex flex-col h-[80vh]">
                <DataTablePedidos :orderResponse="orderResponse" ajustClass="!top-[100px] md:!top-[90px]" />
            </div>
        </DialogContent>
    </Dialog>
</template>
