<script setup>
import { ref, watch } from 'vue';
import { Label } from '@/components/ui/label';
import { MoneyUtil } from '@/util';
import { Money3Component } from 'v-money3';
import { twMerge } from 'tailwind-merge';

const { config } = MoneyUtil.formatMoney();

const emits = defineEmits(['update:exchange']);

const props = defineProps(['value', 'class']);

const value = ref(parseFloat(props.value));

const handleBlur = (e) => emits('update:exchange', { value: e.target.value });

watch(
  () => props.value,
  (newValue) => {
    value.value = parseFloat(newValue);
  },
);
// peer focus-visible:ring-info/60 block min-h-[auto] w-full rounded  bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear motion-reduce:transition-none dark:text-neutral-200 dark:autofill:shadow-autofill dark:peer-focus:text-primary text-slate-600
</script>

<template>
  <div
    :class="
      twMerge('relative h-[40px] max-w-[100px] min-w-[100px] items-center group', props.class)
    "
  >
    <Money3Component
      id="exchange"
      type="text"
      placeholder="Troco"
      :class="
        twMerge(
          'h-full border border-input peer focus-visible:ring-info/60 block min-h-[auto] w-full rounded  bg-transparent px-3 py-[0.32rem] leading-[1.6] outline-none transition-all duration-200 ease-linear motion-reduce:transition-none dark:text-neutral-200 dark:autofill:shadow-autofill dark:peer-focus:text-primary text-slate-600',
        )
      "
      v-bind="config"
      :model-value="value"
      @keypress.enter="handleBlur"
      @blur="handleBlur"
    />
    <Label
      class="absolute left-1 -top-2 text-slate-400 text-sm px-1 bg-white group-has-[input:focus]:text-info/80 transition-colors"
      for="exchange"
      >troco para</Label
    >
  </div>
</template>
