<script setup>
import { cn } from '@/lib/utils';
import { NumberFieldRoot, useForwardPropsEmits } from 'radix-vue';
import { computed, ref } from 'vue';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip'

const props = defineProps({
    row: { type: null, required: false },
    table: { type: null, required: false },
});

const emits = defineEmits(['update:modelValue', 'callback:editedRow']);

const tableMeta = ref(props.table.options.meta)



const setEditedRows = (e) => {
    const elName = e.currentTarget.name;
    const { editedRows } = tableMeta.value

    if (editedRows) editedRows = false
    else editedRows = true


    emits('callback:editedRow', editedRows);

    if (elName !== "edit") {
        tableMeta.value?.revertData(row.index, e.currentTarget.name === "cancel");
    }
};



const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>

<template>

    <TooltipProvider v-bind="forwarded">
        <Tooltip>
            <TooltipTrigger as-child>
                <Button variant="outline" name="edit" v-on:click="setEditedRows">
                    <slot />
                </Button>
            </TooltipTrigger>
            <TooltipContent>
                <div class="">
                    <div v-if="tableMeta?.editedRows[row.id]" className="edit-cell">
                        <button v-on:click="setEditedRows" name="cancel">
                            X
                        </button>
                        <button v-on:click="setEditedRows" name="done">
                            ✔
                        </button>
                    </div>
                    <button v-else v-on:click="setEditedRows" name="edit">
                        ✐
                    </button>
                </div>
            </TooltipContent>
        </Tooltip>
    </TooltipProvider>
</template>
