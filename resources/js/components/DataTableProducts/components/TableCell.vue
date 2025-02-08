<script setup>
import { ref, onMounted } from 'vue';
import { MoneyUtil} from '@/util';

const props = defineProps({
  cellValue: { type: [Number, String], required: false },
  cellkey: { type: String, required: false },
  offer: { type: Boolean, required: false, default: false },
});

const { toCurrency, config } =MoneyUtil.formatMoney();
const inputElement = ref();
const initialValue = ref(toCurrency(parseFloat(props.cellValue).toFixed(2)));
const showInput = ref(false);
const originalValue = ref(null);
const valueChange = ref(false);

const emits = defineEmits(['changed']);

function handleClick() {
  showInput.value = true;
  setTimeout(() => {
    inputElement.value.focus();
  }, 200);
}

const handleValueChange = () =>
  initialValue.value !== toCurrency(originalValue.value)
    ? (valueChange.value = true)
    : (valueChange.value = false);

function handleBlur() {
  showInput.value = false;
  handleValueChange();
  emits('changed', { key: props.cellkey, value: initialValue.value });
}

onMounted(() => (originalValue.value = props.cellValue));
</script>

<template>
  <div
    class="relative cursor-pointer font-semibold"
    :class="valueChange ? 'text-warning' : 'text-info'"
    @click="handleClick"
  >
    <p v-if="!showInput">
      <slot />
    </p>

    <div v-else class="relative items-center flex">
      <input
        ref="inputElement"
        v-model.lazy="initialValue"
        v-money3="config"
        type="text"
        class="focus:ring-transparent focus:outline-none w-20 border border-gray-200 rounded-md p-1"
        @blur="handleBlur"
        @keypress.enter="handleBlur"
      />
    </div>
    <i
      v-if="offer"
      class="ri-hand-coin-fill absolute right-0 2xsm:right-4 xsm:-left-5 top-1/2 -translate-y-1/2"
    ></i>
  </div>
</template>

<script>
import { Money3Directive } from 'v-money3';
import { boolean } from 'zod';
export default {
  directives: { money3: Money3Directive },
};
</script>
