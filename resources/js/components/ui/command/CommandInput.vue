<script setup>
import { computed } from "vue";
import { Search } from "lucide-vue-next";
import { ComboboxInput, useForwardProps } from "radix-vue";
import { cn } from "@/lib/utils";
import Separator from "../separator/Separator.vue";

defineOptions({
    inheritAttrs: false,
});

const props = defineProps({
    type: { type: String, required: false },
    disabled: { type: Boolean, required: false },
    autoFocus: { type: Boolean, required: false },
    asChild: { type: Boolean, required: false },
    as: { type: null, required: false },
    class: { type: null, required: false },
});

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props;

    return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <div class="flex flex-col justify-center px-3" cmdk-input-wrapper>
        <div class="flex items-center ">
            <Search class="mr-2 h-4 w-4 shrink-0 opacity-50" />
            <ComboboxInput v-bind="{ ...forwardedProps, ...$attrs }" auto-focus :class="cn(
                'peer focus-visible:ring-info/60 block min-h-[auto] w-full rounded  bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear motion-reduce:transition-none dark:text-neutral-200 dark:autofill:shadow-autofill dark:peer-focus:text-primary text-slate-600 border-input',
                props.class,
            )
                " />
        </div>
        <Separator></Separator>
    </div>
</template>
