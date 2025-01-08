<script setup>
import { ref } from 'vue'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Form } from '@/components/ui/form'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import { Button } from '@/components/ui/button'

const isOpen = ref(false)
const dateRange = ref([null, null])

async function fetchOrdersReport() {
    const [startDate, endDate] = dateRange.value
    const response = await axios.post('/relatorio/pedidos', {
        dataInicial: startDate?.toLocaleDateString('pt-BR'),
        dataFinal: endDate?.toLocaleDateString('pt-BR')
    })
    console.log(response)
    // Handle response data
}
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
                    auto-apply />
                <Button type="submit">Gerar Relatório</Button>
            </Form>
        </DialogContent>
    </Dialog>
</template>
