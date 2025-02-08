<script setup>
import DataTableProducts from '@/components/dataTables/DataTableProducts/components/DataTableProducts';

const props = defineProps({
  table: { type: null, Required: true },
  resizebleColumns: { type: Array, Required: true },
});
</script>

<template>
  <div>
    <DataTableProducts.Table :table="table" :resizeble-columns="resizebleColumns" />
  </div>
</template>

<style scoped>
.space-y-2 > * + * {
  margin-top: 0.5rem;
}
</style>
