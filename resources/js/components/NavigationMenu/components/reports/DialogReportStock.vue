<script setup>
import { ref } from 'vue'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Form } from '@/components/ui/form'
import { Select } from '@/components/ui/select'
import { Button } from '@/components/ui/button'

const isOpen = ref(false)
const selectedDistributors = ref([])

async function fetchStockReport() {
    const response = await axios.post('/relatorio/estoque', {
        idDistribuidores: selectedDistributors.value.join(',')
    })
    // Handle response data
}
</script>

<template>
    <Dialog v-model:open="isOpen">
        <slot name="trigger" @click="isOpen = true" />
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Relatório de Estoque</DialogTitle>
            </DialogHeader>
            <Form @submit="fetchStockReport">
                <Select v-model="selectedDistributors" multiple />
                <Button type="submit">Gerar Relatório</Button>
            </Form>
        </DialogContent>
    </Dialog>
</template>
