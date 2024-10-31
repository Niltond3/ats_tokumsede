<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useForwardPropsEmits } from "radix-vue";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { columns } from './Columns'
import DataTable from './DataTable.vue'
import { utf8Decode } from './DataTableUtil';

const props = defineProps({
    open: { type: Boolean, required: false },
    toggleDialog: { type: Function, required: false },
    idClienteAddress: { type: String, required: false },
});
const emits = defineEmits(["update:modelValue"]);

const forwarded = useForwardPropsEmits(props, emits);


const tableData = ref([])
const distributorInfo = ref()
const whenDialogOpen = async () => {
    const url = `produtos/${props.idClienteAddress}`
    const response = await axios.get(url)
    tableData.value = response.data[0]
    distributorInfo.value = {
        distributor: response.data[1],
        address: response.data[2],
        distributorExpedient: response.data[6],
        distributorTaxes: response.data[4],
    }
}

const handleDialogOpen = () => {
    props.open && whenDialogOpen()
    return props.open
}

const handleRealizarPedido = async (payload) => {
    var url = "pedidos";
    console.log(payload)
    const response = await axios.post(url, payload)
    console.log(response)

}
</script>

<template>
    <Dialog v-bind="forwarded" :open="handleDialogOpen()" @update:open="(op) => toggleDialog()">
        <DialogContent class="sm:max-w-3xl">
            <DataTable :columns="columns" :table-data="tableData" :distributor-payload="distributorInfo"
                @callback:payload-pedido="handleRealizarPedido" />
        </DialogContent>
    </Dialog>
</template>
