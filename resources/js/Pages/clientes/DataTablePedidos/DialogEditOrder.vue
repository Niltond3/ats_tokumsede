<script setup>
import { useForwardPropsEmits } from "radix-vue";
import {
    Dialog,
    DialogContent,
} from '@/components/ui/dialog'
import { ScrollArea } from '@/components/ui/scroll-area'
import { Separator } from '@/components/ui/separator'
import { utf8Decode } from '../../../util';

const props = defineProps({
    open: { type: Boolean, required: false },
    toggleDialog: { type: Function, required: false },
    payloadData: { type: Object, required: true },
});

const forwarded = useForwardPropsEmits(props);

const handleDialogOpen = () => props.open

</script>

<template>
    <Dialog v-bind="forwarded" :open="handleDialogOpen()" @update:open="(op) => toggleDialog()">
        <DialogTrigger as-child>
            <button variant="outline">
                Visualizar Pedido
            </button>
        </DialogTrigger>
        <DialogContent class="gap-2 sm:max-w-min">
            <DialogHeader class=" text-md font-medium text-info leading-none flex gap-3">
                <DialogTitle class=" font-medium text-info leading-none flex gap-3 justify-between mr-4">>
                    <i class="ri-e-bike-2-line"></i>
                    Edite o pedido
                </DialogTitle>
            </DialogHeader>
            Dialog edição do pedido
        </DialogContent>
    </Dialog>
</template>
