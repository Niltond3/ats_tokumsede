<script setup>
import { ref, computed } from 'vue'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Form } from '@/components/ui/form'
import { Combobox, ComboboxContent, ComboboxInput, ComboboxTrigger } from '@/components/ui/combobox'
import { TagsInput, TagsInputTag, TagsInputItem } from '@/components/ui/tags-input'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import { Button } from '@/components/ui/button'

const isOpen = ref(false)
const dateRange = ref([null, null])
const selectedDistributors = ref([])
const distributors = ref([])
const search = ref('')

const filteredDistributors = computed(() => {
    return distributors.value.filter(d =>
        d.nome.toLowerCase().includes(search.value.toLowerCase()) &&
        !selectedDistributors.value.includes(d.id)
    )
})

async function getDistributors() {
    const response = await axios.get('/distribuidores')
    distributors.value = response.data
}

const presets = computed(() => {
    const today = new Date()
    return [
        {
            label: 'Último dia',
            range: [
                new Date(today.setDate(today.getDate() - 1)),
                new Date()
            ]
        },
        {
            label: 'Última semana',
            range: [
                new Date(today.setDate(today.getDate() - 7)),
                new Date()
            ]
        },
        {
            label: 'Último mês',
            range: [
                new Date(today.setMonth(today.getMonth() - 1)),
                new Date()
            ]
        },
        {
            label: 'Último ano',
            range: [
                new Date(today.setFullYear(today.getFullYear() - 1)),
                new Date()
            ]
        }
    ]
})

async function fetchOrdersReport() {
    const [startDate, endDate] = dateRange.value
    const response = await axios.post('/relatorio/pedidos', {
        dataInicial: startDate?.toLocaleDateString('pt-BR'),
        dataFinal: endDate?.toLocaleDateString('pt-BR'),
        idDistribuidores: selectedDistributors.value.join(',')
    })
    console.log(response)
}

function removeDistributor(id) {
    selectedDistributors.value = selectedDistributors.value.filter(d => d !== id)
}

getDistributors()
</script>

<template>
    <Dialog v-model:open="isOpen">
        <slot name="trigger" @click="isOpen = true" />
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Relatório de Pedidos</DialogTitle>
            </DialogHeader>
            <Form @submit="fetchOrdersReport">
                <VueDatePicker v-model="dateRange" range locale="pt-BR" format="dd/MM/yyyy" :enable-time-picker="false"
                    :presets="presets" auto-apply />

                <TagsInput v-model="selectedDistributors" class="w-full">
                    <TagsInputTag v-for="id in selectedDistributors" :key="id" @remove="removeDistributor(id)">
                        {{ distributors.find(d => d.id === id)?.nome }}
                    </TagsInputTag>
                    <Combobox v-model="search">
                        <ComboboxTrigger>
                            <ComboboxInput placeholder="Selecione os distribuidores..." />
                        </ComboboxTrigger>
                        <ComboboxContent>
                            <TagsInputItem v-for="distributor in filteredDistributors" :key="distributor.id"
                                :value="distributor.id" @click="selectedDistributors.value.push(distributor.id)">
                                {{ distributor.nome }}
                            </TagsInputItem>
                        </ComboboxContent>
                    </Combobox>
                </TagsInput>

                <Button type="submit">Gerar Relatório</Button>
            </Form>
        </DialogContent>
    </Dialog>
</template>
